<?php

require_once __DIR__ . '/security.php';
require_once __DIR__ . '/news_routing.php';
require_once __DIR__ . '/photo_ordering.php';

function ensureNewsAuthorColumns($conn)
{
    $requiredColumns = array(
        'created_by_user_id' => 'ALTER TABLE news ADD COLUMN created_by_user_id INT NULL AFTER `date`',
        'created_by_login' => 'ALTER TABLE news ADD COLUMN created_by_login VARCHAR(255) NULL AFTER created_by_user_id',
        'created_by_name' => 'ALTER TABLE news ADD COLUMN created_by_name VARCHAR(255) NULL AFTER created_by_login',
    );

    foreach ($requiredColumns as $columnName => $sql) {
        $safeColumnName = preg_replace('/[^a-zA-Z0-9_]/', '', $columnName);
        if ($safeColumnName === '') {
            continue;
        }

        $result = $conn->query("SHOW COLUMNS FROM news LIKE '" . $conn->real_escape_string($safeColumnName) . "'");
        $columnExists = $result instanceof mysqli_result && $result->num_rows > 0;
        if ($result instanceof mysqli_result) {
            $result->free();
        }

        if ($columnExists) {
            continue;
        }

        if (!$conn->query($sql)) {
            throw new RuntimeException('Не удалось подготовить таблицу news для сохранения автора.');
        }
    }
}

function ensureNewsVideoColumn($conn)
{
    $result = $conn->query("SHOW COLUMNS FROM news LIKE 'video_filename'");
    $columnExists = $result instanceof mysqli_result && $result->num_rows > 0;
    if ($result instanceof mysqli_result) {
        $result->free();
    }

    if ($columnExists) {
        return;
    }

    if (!$conn->query('ALTER TABLE news ADD COLUMN video_filename VARCHAR(255) NULL AFTER freim')) {
        throw new RuntimeException('Не удалось подготовить таблицу news для сохранения видео.');
    }
}

function ensureNewsVideoInfrastructure($conn)
{
    static $isPrepared = false;

    if ($isPrepared) {
        return;
    }

    ensureNewsVideoColumn($conn);
    $isPrepared = true;
}

function fetchNewsById($conn, $newsId)
{
    $stmt = $conn->prepare('SELECT * FROM news WHERE id = ? LIMIT 1');
    if (!$stmt) {
        return null;
    }

    $stmt->bind_param('i', $newsId);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result ? $result->fetch_assoc() : null;
    $stmt->close();

    return $row ?: null;
}

function fetchNewsBySlug($conn, $slug)
{
    $normalizedSlug = normalizeNewsSlug($slug);
    if ($normalizedSlug === '') {
        return null;
    }

    $stmt = $conn->prepare('SELECT * FROM news WHERE slug = ? LIMIT 1');
    if (!$stmt) {
        return null;
    }

    $stmt->bind_param('s', $normalizedSlug);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result ? $result->fetch_assoc() : null;
    $stmt->close();

    return $row ?: null;
}

function fetchNewsPhotos($conn, $newsId)
{
    $stmt = $conn->prepare('SELECT id, filename, sort_order FROM photos WHERE news_id = ? ORDER BY sort_order ASC, id ASC');
    if (!$stmt) {
        return array();
    }

    $stmt->bind_param('i', $newsId);
    $stmt->execute();
    $result = $stmt->get_result();
    $photos = $result ? $result->fetch_all(MYSQLI_ASSOC) : array();
    $stmt->close();

    return $photos;
}

function normalizeNewPhotoClientIds($rawClientIds)
{
    if (!is_array($rawClientIds)) {
        return array();
    }

    $clientIds = array();
    foreach ($rawClientIds as $rawClientId) {
        if (is_array($rawClientId)) {
            continue;
        }

        $clientId = trim((string) $rawClientId);
        if ($clientId === '' || !preg_match('/^[a-zA-Z0-9_-]+$/', $clientId)) {
            continue;
        }

        $clientIds[$clientId] = $clientId;
    }

    return array_values($clientIds);
}

function normalizePhotoSequenceTokens($rawSequence)
{
    if (!is_array($rawSequence)) {
        return array();
    }

    $tokens = array();
    foreach ($rawSequence as $rawToken) {
        if (is_array($rawToken)) {
            continue;
        }

        $token = trim((string) $rawToken);
        if ($token === '' || !preg_match('/^(existing:\d+|new:[a-zA-Z0-9_-]+)$/', $token)) {
            continue;
        }

        $tokens[$token] = $token;
    }

    return array_values($tokens);
}

function buildPhotoSortPlan(array $existingPhotos, array $pendingDeletedPhotoIds, array $photoSequence, array $newPhotoClientIds)
{
    $deletedLookup = array_fill_keys($pendingDeletedPhotoIds, true);
    $existingTokenMap = array();
    $existingTokenOrder = array();

    foreach ($existingPhotos as $photo) {
        $photoId = (int) ($photo['id'] ?? 0);
        if ($photoId <= 0 || isset($deletedLookup[$photoId])) {
            continue;
        }

        $token = 'existing:' . $photoId;
        $existingTokenMap[$token] = $photoId;
        $existingTokenOrder[] = $token;
    }

    $newTokenMap = array();
    $newTokenOrder = array();
    foreach ($newPhotoClientIds as $clientId) {
        $token = 'new:' . $clientId;
        $newTokenMap[$token] = $clientId;
        $newTokenOrder[] = $token;
    }

    $allowedTokens = $existingTokenMap + $newTokenMap;
    $finalSequence = array();
    $usedTokens = array();

    foreach ($photoSequence as $token) {
        if (!isset($allowedTokens[$token]) || isset($usedTokens[$token])) {
            continue;
        }

        $usedTokens[$token] = true;
        $finalSequence[] = $token;
    }

    foreach (array_merge($existingTokenOrder, $newTokenOrder) as $token) {
        if (isset($usedTokens[$token])) {
            continue;
        }

        $usedTokens[$token] = true;
        $finalSequence[] = $token;
    }

    $existingSortOrders = array();
    $newSortOrders = array();
    $position = 1;

    foreach ($finalSequence as $token) {
        if (isset($existingTokenMap[$token])) {
            $existingSortOrders[$existingTokenMap[$token]] = $position;
            $position++;
            continue;
        }

        if (isset($newTokenMap[$token])) {
            $newSortOrders[$newTokenMap[$token]] = $position;
            $position++;
        }
    }

    return array(
        'existing' => $existingSortOrders,
        'new' => $newSortOrders,
    );
}

function applyExistingPhotoSortOrders($conn, $newsId, array $sortOrders)
{
    if (empty($sortOrders)) {
        return;
    }

    $stmt = $conn->prepare('UPDATE photos SET sort_order = ? WHERE id = ? AND news_id = ?');
    if (!$stmt) {
        return;
    }

    foreach ($sortOrders as $photoId => $sortOrder) {
        $safePhotoId = (int) $photoId;
        $safeSortOrder = (int) $sortOrder;
        if ($safePhotoId <= 0 || $safeSortOrder <= 0) {
            continue;
        }

        $stmt->bind_param('iii', $safeSortOrder, $safePhotoId, $newsId);
        $stmt->execute();
    }

    $stmt->close();
}

function getNextPhotoSortOrder($conn, $newsId)
{
    $stmt = $conn->prepare('SELECT COALESCE(MAX(sort_order), 0) AS max_sort_order FROM photos WHERE news_id = ?');
    if (!$stmt) {
        return 1;
    }

    $stmt->bind_param('i', $newsId);
    $stmt->execute();
    $result = $stmt->get_result();
    $maxSortOrder = $result ? (int) ($result->fetch_assoc()['max_sort_order'] ?? 0) : 0;
    $stmt->close();

    return $maxSortOrder + 1;
}

function getUploadedNewsPhotosArray($files = null)
{
    if ($files === null) {
        $files = $_FILES['photos'] ?? null;
    }

    if (!is_array($files) || !array_key_exists('name', $files)) {
        return null;
    }

    if (is_array($files['name'])) {
        return $files;
    }

    return array(
        'name' => array($files['name'] ?? ''),
        'type' => array($files['type'] ?? ''),
        'tmp_name' => array($files['tmp_name'] ?? ''),
        'error' => array($files['error'] ?? UPLOAD_ERR_NO_FILE),
        'size' => array($files['size'] ?? 0),
    );
}

function getUploadedNewsVideo($files = null)
{
    if ($files === null) {
        $files = $_FILES['video'] ?? null;
    }

    if (!is_array($files) || !array_key_exists('name', $files)) {
        return null;
    }

    if (is_array($files['name'])) {
        $fileCount = count($files['name']);
        for ($index = 0; $index < $fileCount; $index++) {
            $candidateName = trim((string) ($files['name'][$index] ?? ''));
            if ($candidateName === '') {
                continue;
            }

            return array(
                'name' => $candidateName,
                'type' => (string) ($files['type'][$index] ?? ''),
                'tmp_name' => (string) ($files['tmp_name'][$index] ?? ''),
                'error' => (int) ($files['error'][$index] ?? UPLOAD_ERR_NO_FILE),
                'size' => (int) ($files['size'][$index] ?? 0),
            );
        }

        return null;
    }

    return array(
        'name' => (string) ($files['name'] ?? ''),
        'type' => (string) ($files['type'] ?? ''),
        'tmp_name' => (string) ($files['tmp_name'] ?? ''),
        'error' => (int) ($files['error'] ?? UPLOAD_ERR_NO_FILE),
        'size' => (int) ($files['size'] ?? 0),
    );
}

function getUploadedFileErrorMessage($errorCode, $subjectLabel)
{
    $messages = array(
        UPLOAD_ERR_INI_SIZE => 'Файл "' . $subjectLabel . '" превышает лимит сервера.',
        UPLOAD_ERR_FORM_SIZE => 'Файл "' . $subjectLabel . '" превышает лимит формы.',
        UPLOAD_ERR_PARTIAL => 'Файл "' . $subjectLabel . '" загружен не полностью.',
        UPLOAD_ERR_NO_TMP_DIR => 'На сервере отсутствует временная папка для загрузки "' . $subjectLabel . '".',
        UPLOAD_ERR_CANT_WRITE => 'Не удалось записать файл "' . $subjectLabel . '" на диск.',
        UPLOAD_ERR_EXTENSION => 'Загрузка файла "' . $subjectLabel . '" остановлена расширением PHP.',
    );

    return isset($messages[$errorCode]) ? $messages[$errorCode] : 'Ошибка загрузки файла "' . $subjectLabel . '".';
}

function detectUploadedVideoExtension(array $video)
{
    $declaredMime = strtolower(trim((string) ($video['type'] ?? '')));
    $originalName = strtolower(trim((string) ($video['name'] ?? '')));
    $tmpName = (string) ($video['tmp_name'] ?? '');

    $allowedMimeTypes = array(
        'video/mp4' => 'mp4',
        'video/webm' => 'webm',
        'video/ogg' => 'ogv',
        'application/ogg' => 'ogv',
    );
    $allowedExtensions = array(
        'mp4' => 'mp4',
        'm4v' => 'mp4',
        'webm' => 'webm',
        'ogv' => 'ogv',
        'ogg' => 'ogv',
    );

    if ($declaredMime !== '' && isset($allowedMimeTypes[$declaredMime])) {
        return $allowedMimeTypes[$declaredMime];
    }

    if (is_file($tmpName) && function_exists('finfo_open')) {
        $finfo = @finfo_open(FILEINFO_MIME_TYPE);
        if ($finfo) {
            $detectedMime = @finfo_file($finfo, $tmpName);
            @finfo_close($finfo);
            $detectedMime = strtolower(trim((string) $detectedMime));
            if ($detectedMime !== '' && isset($allowedMimeTypes[$detectedMime])) {
                return $allowedMimeTypes[$detectedMime];
            }
        }
    }

    $extension = strtolower((string) pathinfo($originalName, PATHINFO_EXTENSION));
    if ($extension !== '' && isset($allowedExtensions[$extension])) {
        return $allowedExtensions[$extension];
    }

    throw new InvalidArgumentException('Допускаются только видео в формате MP4, WebM или OGV.');
}

function getNewsVideoFilename($news)
{
    $filename = sanitizeStoredFilename(is_array($news) ? ($news['video_filename'] ?? '') : '');
    return $filename !== '' ? $filename : null;
}

function deleteNewsVideoFileByFilename($filename)
{
    $safeFilename = sanitizeStoredFilename($filename);
    if ($safeFilename === '') {
        return;
    }

    $filePath = dirname(__DIR__) . '/videos/' . $safeFilename;
    if (is_file($filePath)) {
        @unlink($filePath);
    }
}

function deleteNewsVideo($conn, $newsId)
{
    ensureNewsVideoInfrastructure($conn);
    $news = fetchNewsById($conn, $newsId);
    if (!$news) {
        return;
    }

    $currentFilename = getNewsVideoFilename($news);
    $stmt = $conn->prepare('UPDATE news SET video_filename = NULL WHERE id = ?');
    if ($stmt) {
        $stmt->bind_param('i', $newsId);
        $stmt->execute();
        $stmt->close();
    }

    if ($currentFilename !== null) {
        deleteNewsVideoFileByFilename($currentFilename);
    }
}

function saveNewsVideo($conn, $newsId, $files = null, $deleteExisting = false)
{
    ensureNewsVideoInfrastructure($conn);

    $news = fetchNewsById($conn, $newsId);
    if (!$news) {
        throw new InvalidArgumentException('Событие не найдено.');
    }

    $uploadedVideo = getUploadedNewsVideo($files);
    $currentFilename = getNewsVideoFilename($news);

    if ($uploadedVideo === null || trim((string) ($uploadedVideo['name'] ?? '')) === '') {
        if ($deleteExisting && $currentFilename !== null) {
            deleteNewsVideo($conn, $newsId);
            return null;
        }

        return $currentFilename;
    }

    $uploadError = (int) ($uploadedVideo['error'] ?? UPLOAD_ERR_NO_FILE);
    if ($uploadError !== UPLOAD_ERR_OK) {
        if ($uploadError === UPLOAD_ERR_NO_FILE) {
            if ($deleteExisting && $currentFilename !== null) {
                deleteNewsVideo($conn, $newsId);
                return null;
            }

            return $currentFilename;
        }

        throw new RuntimeException(getUploadedFileErrorMessage($uploadError, 'видео'));
    }

    $tmpName = (string) ($uploadedVideo['tmp_name'] ?? '');
    if ($tmpName === '' || !is_uploaded_file($tmpName)) {
        throw new RuntimeException('Не удалось получить загруженный видеофайл.');
    }

    $extension = detectUploadedVideoExtension($uploadedVideo);
    $videoDir = dirname(__DIR__) . '/videos/';
    if (!is_dir($videoDir)) {
        mkdir($videoDir, 0755, true);
    }

    if (!is_writable($videoDir)) {
        throw new RuntimeException('Папка для видео недоступна для записи.');
    }

    $newFilename = bin2hex(random_bytes(16)) . '.' . $extension;
    $targetPath = $videoDir . $newFilename;
    if (!move_uploaded_file($tmpName, $targetPath)) {
        throw new RuntimeException('Не удалось сохранить видео на сервере.');
    }

    $stmt = $conn->prepare('UPDATE news SET video_filename = ? WHERE id = ?');
    if (!$stmt) {
        @unlink($targetPath);
        throw new RuntimeException('Ошибка подготовки сохранения видео.');
    }

    $stmt->bind_param('si', $newFilename, $newsId);
    if (!$stmt->execute()) {
        $stmt->close();
        @unlink($targetPath);
        throw new RuntimeException('Ошибка сохранения видео в базе данных.');
    }
    $stmt->close();

    if ($currentFilename !== null && $currentFilename !== $newFilename) {
        deleteNewsVideoFileByFilename($currentFilename);
    }

    return $newFilename;
}

function normalizeDeleteVideoFlag($value)
{
    if ($value === null) {
        return false;
    }

    if (is_bool($value)) {
        return $value;
    }

    if (is_int($value) || is_float($value)) {
        return (int) $value === 1;
    }

    $normalized = strtolower(trim((string) $value));
    if ($normalized === '') {
        return false;
    }

    return in_array($normalized, array('1', 'true', 'yes', 'on', 'delete'), true);
}

function saveNewsPhotos($conn, $newsId, array $newPhotoSortOrders = array(), array $newPhotoClientIds = array(), $files = null)
{
    $photos = getUploadedNewsPhotosArray($files);
    if (!is_array($photos) || !isset($photos['name']) || !is_array($photos['name']) || empty($photos['name'][0])) {
        return;
    }

    $photoDir = dirname(__DIR__) . '/photos/';
    if (!is_dir($photoDir)) {
        mkdir($photoDir, 0755, true);
    }

    if (!is_writable($photoDir)) {
        throw new RuntimeException('Папка для фотографий недоступна для записи.');
    }

    $allowedTypes = array(
        IMAGETYPE_JPEG => 'jpg',
        IMAGETYPE_PNG => 'png',
        IMAGETYPE_GIF => 'gif',
    );

    $stmtPhoto = $conn->prepare('INSERT INTO photos (news_id, sort_order, filename) VALUES (?, ?, ?)');
    if (!$stmtPhoto) {
        throw new RuntimeException('Ошибка подготовки сохранения фото.');
    }

    $fallbackSortOrder = getNextPhotoSortOrder($conn, $newsId);
    $fileCount = count($photos['name']);
    for ($i = 0; $i < $fileCount; $i++) {
        $uploadError = $photos['error'][$i] ?? UPLOAD_ERR_NO_FILE;
        if ($uploadError !== UPLOAD_ERR_OK) {
            continue;
        }

        $tempFilePath = $photos['tmp_name'][$i] ?? '';
        if ($tempFilePath === '' || !is_uploaded_file($tempFilePath)) {
            continue;
        }

        $imageInfo = @getimagesize($tempFilePath);
        if ($imageInfo === false || !isset($allowedTypes[$imageInfo[2]])) {
            continue;
        }

        $clientId = isset($newPhotoClientIds[$i]) ? (string) $newPhotoClientIds[$i] : '';
        $sortOrder = isset($newPhotoSortOrders[$clientId]) ? (int) $newPhotoSortOrders[$clientId] : $fallbackSortOrder;
        if (!isset($newPhotoSortOrders[$clientId])) {
            $fallbackSortOrder++;
        }

        $extension = $allowedTypes[$imageInfo[2]];
        $filename = bin2hex(random_bytes(16)) . '.' . $extension;
        $targetPath = $photoDir . $filename;

        if (!move_uploaded_file($tempFilePath, $targetPath)) {
            continue;
        }

        $stmtPhoto->bind_param('iis', $newsId, $sortOrder, $filename);
        if (!$stmtPhoto->execute()) {
            @unlink($targetPath);
        }
    }

    $stmtPhoto->close();
}

function deletePhotoById($conn, $newsId, $photoId)
{
    $stmt = $conn->prepare('SELECT filename FROM photos WHERE id = ? AND news_id = ? LIMIT 1');
    if (!$stmt) {
        return;
    }

    $stmt->bind_param('ii', $photoId, $newsId);
    $stmt->execute();
    $result = $stmt->get_result();
    $photo = $result ? $result->fetch_assoc() : null;
    $stmt->close();

    if (!$photo || !isset($photo['filename'])) {
        return;
    }

    $deleteStmt = $conn->prepare('DELETE FROM photos WHERE id = ? AND news_id = ?');
    if (!$deleteStmt) {
        return;
    }

    $deleteStmt->bind_param('ii', $photoId, $newsId);
    $deleteStmt->execute();
    $deleteStmt->close();

    $filename = sanitizeStoredFilename($photo['filename']);
    if ($filename !== '') {
        $filePath = dirname(__DIR__) . '/photos/' . $filename;
        if (is_file($filePath)) {
            @unlink($filePath);
        }
    }
}

function normalizePhotoIdsToDelete($rawPhotoIds)
{
    if (!is_array($rawPhotoIds)) {
        return array();
    }

    $photoIds = array();
    foreach ($rawPhotoIds as $rawPhotoId) {
        if (is_array($rawPhotoId)) {
            continue;
        }

        $photoId = filter_var($rawPhotoId, FILTER_VALIDATE_INT, array('options' => array('min_range' => 1)));
        if ($photoId === false) {
            continue;
        }

        $photoIds[$photoId] = $photoId;
    }

    return array_values($photoIds);
}

function validateAndNormalizeNewsPayload(array $input, $titleMaxLength = 60)
{
    $type = normalizeNewsType($input['type'] ?? 'новость');
    $title = sanitizeNewsInput($input['title'] ?? '');
    $description = sanitizeNewsInput($input['description'] ?? '');
    $freim = sanitizeNewsInput($input['freim'] ?? '');
    $date = sanitizeNewsInput($input['date'] ?? '');
    $slug = sanitizeNewsInput($input['slug'] ?? '');

    if ($type === null) {
        throw new InvalidArgumentException('Неверный тип события.');
    }

    if ($title === '' || mb_strlen($title, 'UTF-8') < 3) {
        throw new InvalidArgumentException('Название должно содержать минимум 3 символа.');
    }

    if (mb_strlen($title, 'UTF-8') > $titleMaxLength) {
        throw new InvalidArgumentException('Название должно содержать не более ' . $titleMaxLength . ' символов.');
    }

    if ($description === '') {
        throw new InvalidArgumentException('Описание не может быть пустым.');
    }

    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
        throw new InvalidArgumentException('Некорректная дата.');
    }

    return array(
        'type' => $type,
        'title' => $title,
        'description' => $description,
        'freim' => $freim,
        'date' => $date,
        'slug' => $slug,
    );
}

function validateAndNormalizeNewsUpdatePayload(array $input, array $existingNews, $titleMaxLength = 60)
{
    $hasType = array_key_exists('type', $input);
    $hasTitle = array_key_exists('title', $input);
    $hasDescription = array_key_exists('description', $input);
    $hasFreim = array_key_exists('freim', $input);
    $hasDate = array_key_exists('date', $input);
    $hasSlug = array_key_exists('slug', $input);

    $type = $hasType ? normalizeNewsType($input['type'] ?? '') : (normalizeNewsType($existingNews['type'] ?? '') ?: 'новость');
    $title = $hasTitle ? sanitizeNewsInput($input['title'] ?? '') : sanitizeNewsInput($existingNews['title'] ?? '');
    $description = $hasDescription ? sanitizeNewsInput($input['description'] ?? '') : sanitizeNewsInput($existingNews['description'] ?? '');
    $freim = $hasFreim ? sanitizeNewsInput($input['freim'] ?? '') : sanitizeNewsInput($existingNews['freim'] ?? '');
    $date = $hasDate ? sanitizeNewsInput($input['date'] ?? '') : sanitizeNewsInput($existingNews['date'] ?? '');
    $slug = $hasSlug ? sanitizeNewsInput($input['slug'] ?? '') : sanitizeNewsInput($existingNews['slug'] ?? '');

    if ($type === null) {
        throw new InvalidArgumentException('Неверный тип события.');
    }

    if ($title === '' || mb_strlen($title, 'UTF-8') < 3) {
        throw new InvalidArgumentException('Название должно содержать минимум 3 символа.');
    }

    if (mb_strlen($title, 'UTF-8') > $titleMaxLength) {
        throw new InvalidArgumentException('Название должно содержать не более ' . $titleMaxLength . ' символов.');
    }

    if ($description === '') {
        throw new InvalidArgumentException('Описание не может быть пустым.');
    }

    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
        throw new InvalidArgumentException('Некорректная дата.');
    }

    return array(
        'type' => $type,
        'title' => $title,
        'description' => $description,
        'freim' => $freim,
        'date' => $date,
        'slug' => $slug,
    );
}

function createNewsEntry($conn, array $input, array $author = array(), $photoFiles = null, $videoFiles = null)
{
    ensureNewsSlugInfrastructure($conn);
    ensurePhotoSortInfrastructure($conn);
    ensureNewsAuthorColumns($conn);
    ensureNewsVideoInfrastructure($conn);

    $payload = validateAndNormalizeNewsPayload($input);
    $preferredSlug = $payload['slug'] !== '' ? $payload['slug'] : null;
    $slug = buildUniqueNewsSlug($conn, $payload['title'], null, $preferredSlug);

    $authorUserId = isset($author['user_id']) && $author['user_id'] !== null ? (int) $author['user_id'] : null;
    $authorLogin = trim((string) ($author['login'] ?? ''));
    $authorName = trim((string) ($author['name'] ?? ''));

    $stmt = $conn->prepare('INSERT INTO news (type, title, slug, description, freim, date, created_by_user_id, created_by_login, created_by_name) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)');
    if (!$stmt) {
        throw new RuntimeException('Ошибка сервера при создании события.');
    }

    $stmt->bind_param(
        'ssssssiss',
        $payload['type'],
        $payload['title'],
        $slug,
        $payload['description'],
        $payload['freim'],
        $payload['date'],
        $authorUserId,
        $authorLogin,
        $authorName
    );

    if (!$stmt->execute()) {
        $errorMessage = $stmt->error;
        $stmt->close();
        throw new RuntimeException('Ошибка при добавлении события: ' . $errorMessage);
    }

    $newsId = (int) $conn->insert_id;
    $stmt->close();

    saveNewsPhotos($conn, $newsId, array(), array(), $photoFiles);
    saveNewsVideo($conn, $newsId, $videoFiles);
    normalizePhotoSortOrders($conn, $newsId);

    $news = fetchNewsById($conn, $newsId);
    $photos = fetchNewsPhotos($conn, $newsId);

    return array(
        'news' => $news,
        'photos' => $photos,
        'url' => $news ? buildNewsUrl($news) : '/news.php?id=' . $newsId,
        'slug' => $slug,
    );
}

function updateNewsEntry($conn, $newsId, array $input, $photoFiles = null, $videoFiles = null)
{
    ensureNewsSlugInfrastructure($conn);
    ensurePhotoSortInfrastructure($conn);
    ensureNewsAuthorColumns($conn);
    ensureNewsVideoInfrastructure($conn);

    $existingNews = fetchNewsById($conn, $newsId);
    if (!$existingNews) {
        throw new InvalidArgumentException('Событие не найдено.');
    }

    $payload = validateAndNormalizeNewsUpdatePayload($input, $existingNews);
    $preferredSlug = $payload['slug'] !== '' ? $payload['slug'] : sanitizeNewsInput($existingNews['slug'] ?? '');
    $slug = buildUniqueNewsSlug($conn, $payload['title'], $newsId, $preferredSlug);

    $pendingDeletedPhotoIds = normalizePhotoIdsToDelete($input['photos_to_delete'] ?? array());
    $photoSequence = normalizePhotoSequenceTokens($input['photo_sequence'] ?? array());
    $newPhotoClientIds = normalizeNewPhotoClientIds($input['new_photo_client_ids'] ?? array());
    $existingPhotosForSortPlan = fetchNewsPhotos($conn, $newsId);
    $photoSortPlan = buildPhotoSortPlan($existingPhotosForSortPlan, $pendingDeletedPhotoIds, $photoSequence, $newPhotoClientIds);

    $stmt = $conn->prepare('UPDATE news SET type = ?, title = ?, slug = ?, description = ?, freim = ?, date = ? WHERE id = ?');
    if (!$stmt) {
        throw new RuntimeException('Ошибка сервера при обновлении события.');
    }

    $stmt->bind_param(
        'ssssssi',
        $payload['type'],
        $payload['title'],
        $slug,
        $payload['description'],
        $payload['freim'],
        $payload['date'],
        $newsId
    );

    if (!$stmt->execute()) {
        $errorMessage = $stmt->error;
        $stmt->close();
        throw new RuntimeException('Ошибка при обновлении события: ' . $errorMessage);
    }
    $stmt->close();

    foreach ($pendingDeletedPhotoIds as $photoId) {
        deletePhotoById($conn, $newsId, $photoId);
    }

    applyExistingPhotoSortOrders($conn, $newsId, $photoSortPlan['existing']);
    saveNewsPhotos($conn, $newsId, $photoSortPlan['new'], $newPhotoClientIds, $photoFiles);
    normalizePhotoSortOrders($conn, $newsId);

    $deleteVideo = normalizeDeleteVideoFlag($input['delete_video'] ?? null);
    saveNewsVideo($conn, $newsId, $videoFiles, $deleteVideo);

    $news = fetchNewsById($conn, $newsId);
    $photos = fetchNewsPhotos($conn, $newsId);

    return array(
        'news' => $news,
        'photos' => $photos,
        'url' => $news ? buildNewsUrl($news) : '/news.php?id=' . $newsId,
        'slug' => $slug,
    );
}
