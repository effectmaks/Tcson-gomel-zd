<?php

require_once __DIR__ . '/security.php';
require_once __DIR__ . '/auth.php';

function getManagedStaticPublicPages()
{
    return array(
        '/contacts.php' => 'Контактная информация',
        '/anti-corruption.php' => 'Антикоррупционная деятельность',
        '/gossocpodderzhka-invalidov.php' => 'Льготы, права и гарантии инвалидов',
        '/direct-phone-line.php' => 'Прямая телефонная линия',
        '/electronic-appeals.php' => 'Электронные обращения граждан и юрлиц',
        '/structure.php' => 'Структура учреждения',
        '/department/' => 'Отделения',
        '/listnews.php' => 'Новости',
        '/listevents.php' => 'Мероприятия',
    );
}

function normalizePublicPageVisibilityKey($value)
{
    $key = trim((string) $value);
    if ($key === '') {
        return null;
    }

    $key = preg_replace('/[\x00-\x1F\x7F]+/u', '', $key);
    if (!is_string($key) || $key === '') {
        return null;
    }

    $path = strtok($key, '?#');
    if (!is_string($path) || $path === '') {
        return null;
    }

    if ($path[0] !== '/' || strpos($path, '//') === 0) {
        return null;
    }

    if (!preg_match('#^/[A-Za-z0-9._~!$&()*+,;=:@%/-]+$#', $path)) {
        return null;
    }

    return $path;
}

function isManagedPublicPageKey($pageKey)
{
    $normalizedKey = normalizePublicPageVisibilityKey($pageKey);
    if ($normalizedKey === null) {
        return false;
    }

    $staticPages = getManagedStaticPublicPages();
    if (isset($staticPages[$normalizedKey])) {
        return true;
    }

    return (bool) preg_match('#^/news/[a-z0-9]+(?:-[a-z0-9]+)*$#', $normalizedKey);
}

function ensurePublicPageVisibilityInfrastructure($conn)
{
    static $isPrepared = false;

    if ($isPrepared) {
        return;
    }

    $sql = "CREATE TABLE IF NOT EXISTS public_page_visibility (
        page_key VARCHAR(255) NOT NULL,
        is_published TINYINT(1) NOT NULL DEFAULT 1,
        published_by_login VARCHAR(191) DEFAULT NULL,
        updated_by_login VARCHAR(191) DEFAULT NULL,
        updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (page_key)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

    if (!$conn->query($sql)) {
        throw new RuntimeException('Не удалось подготовить таблицу видимости публичных страниц.');
    }

    $requiredColumns = array(
        'published_by_login' => 'ALTER TABLE public_page_visibility ADD COLUMN published_by_login VARCHAR(191) DEFAULT NULL AFTER is_published',
        'updated_by_login' => 'ALTER TABLE public_page_visibility ADD COLUMN updated_by_login VARCHAR(191) DEFAULT NULL AFTER published_by_login',
        'updated_at' => 'ALTER TABLE public_page_visibility ADD COLUMN updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER updated_by_login',
    );

    foreach ($requiredColumns as $columnName => $alterSql) {
        $result = $conn->query("SHOW COLUMNS FROM public_page_visibility LIKE '" . $conn->real_escape_string($columnName) . "'");
        $columnExists = $result instanceof mysqli_result && $result->num_rows > 0;
        if ($result instanceof mysqli_result) {
            $result->free();
        }

        if ($columnExists) {
            continue;
        }

        if (!$conn->query($alterSql)) {
            throw new RuntimeException('Не удалось обновить структуру таблицы видимости публичных страниц.');
        }
    }

    $isPrepared = true;
}

function getPublicPageVisibilityStatusText($isPublished)
{
    return $isPublished
        ? 'Страница доступна всем посетителям.'
        : 'Страница доступна только авторизованным пользователям.';
}

function fetchPublicPageVisibilityState($conn, $pageKey, $pageTitle = '')
{
    ensurePublicPageVisibilityInfrastructure($conn);

    $normalizedKey = normalizePublicPageVisibilityKey($pageKey);
    if ($normalizedKey === null || !isManagedPublicPageKey($normalizedKey)) {
        return null;
    }

    $stmt = $conn->prepare('SELECT is_published, published_by_login, updated_by_login, updated_at FROM public_page_visibility WHERE page_key = ? LIMIT 1');
    if (!$stmt) {
        throw new RuntimeException('Не удалось загрузить настройки видимости страницы.');
    }

    $stmt->bind_param('s', $normalizedKey);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result ? $result->fetch_assoc() : null;
    $stmt->close();

    $isPublished = !is_array($row) || (int) ($row['is_published'] ?? 1) === 1;
    $title = trim((string) $pageTitle);

    return array(
        'key' => $normalizedKey,
        'title' => $title !== '' ? $title : $normalizedKey,
        'is_published' => $isPublished,
        'published_by_login' => isset($row['published_by_login']) ? trim((string) $row['published_by_login']) : '',
        'updated_by_login' => isset($row['updated_by_login']) ? trim((string) $row['updated_by_login']) : '',
        'updated_at' => isset($row['updated_at']) ? trim((string) $row['updated_at']) : '',
        'status_text' => getPublicPageVisibilityStatusText($isPublished),
    );
}

function setCurrentPublicPageVisibilityState(array $state)
{
    $GLOBALS['current_public_page_visibility_state'] = $state;
}

function getCurrentPublicPageVisibilityState()
{
    $state = $GLOBALS['current_public_page_visibility_state'] ?? null;

    return is_array($state) ? $state : null;
}

function bootstrapPublicPageVisibility($conn, $pageKey, $pageTitle = '')
{
    $state = fetchPublicPageVisibilityState($conn, $pageKey, $pageTitle);
    if ($state === null) {
        return null;
    }

    setCurrentPublicPageVisibilityState($state);

    if (!$state['is_published'] && !isLoggedIn()) {
        $requestUri = normalizeInternalRedirectPath($_SERVER['REQUEST_URI'] ?? $state['key'], $state['key']);
        redirectTo('/auth.php?return=' . rawurlencode($requestUri));
    }

    return $state;
}

function isPublicPagePublished($conn, $pageKey)
{
    $state = fetchPublicPageVisibilityState($conn, $pageKey);

    return $state === null ? true : (bool) $state['is_published'];
}

function updatePublicPageVisibility($conn, $pageKey, $isPublished, $actorLogin = '')
{
    ensurePublicPageVisibilityInfrastructure($conn);

    $normalizedKey = normalizePublicPageVisibilityKey($pageKey);
    if ($normalizedKey === null || !isManagedPublicPageKey($normalizedKey)) {
        throw new InvalidArgumentException('Недопустимый идентификатор публичной страницы.');
    }

    $publishedFlag = $isPublished ? 1 : 0;
    $actor = trim((string) $actorLogin);
    $publishedByLogin = $publishedFlag ? ($actor !== '' ? $actor : null) : null;
    $updatedByLogin = $actor !== '' ? $actor : null;

    $stmt = $conn->prepare(
        'INSERT INTO public_page_visibility (page_key, is_published, published_by_login, updated_by_login)
         VALUES (?, ?, ?, ?)
         ON DUPLICATE KEY UPDATE
             is_published = VALUES(is_published),
             published_by_login = CASE
                 WHEN VALUES(is_published) = 1 THEN VALUES(published_by_login)
                 ELSE published_by_login
             END,
             updated_by_login = VALUES(updated_by_login),
             updated_at = CURRENT_TIMESTAMP'
    );

    if (!$stmt) {
        throw new RuntimeException('Не удалось сохранить настройки видимости страницы.');
    }

    $stmt->bind_param('siss', $normalizedKey, $publishedFlag, $publishedByLogin, $updatedByLogin);
    $stmt->execute();
    $stmt->close();

    return fetchPublicPageVisibilityState($conn, $normalizedKey);
}

function getPublishedNewsVisibilitySqlCondition($newsTableAlias = 'news')
{
    $alias = preg_replace('/[^A-Za-z0-9_]/', '', (string) $newsTableAlias);
    if ($alias === '') {
        $alias = 'news';
    }

    return "COALESCE((SELECT page_visibility.is_published FROM public_page_visibility page_visibility WHERE page_visibility.page_key COLLATE utf8mb4_unicode_ci = CONCAT('/news/', " . $alias . ".slug) COLLATE utf8mb4_unicode_ci LIMIT 1), 1) = 1";
}
