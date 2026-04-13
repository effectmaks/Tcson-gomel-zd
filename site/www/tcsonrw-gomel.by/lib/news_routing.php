<?php

function normalizeNewsSlug($value)
{
    $rawValue = trim((string) $value);
    if ($rawValue === '') {
        return '';
    }

    $slug = mb_strtolower($rawValue, 'UTF-8');

    $transliterationMap = array(
        'а' => 'a',
        'б' => 'b',
        'в' => 'v',
        'г' => 'g',
        'д' => 'd',
        'е' => 'e',
        'ё' => 'yo',
        'ж' => 'zh',
        'з' => 'z',
        'и' => 'i',
        'й' => 'y',
        'к' => 'k',
        'л' => 'l',
        'м' => 'm',
        'н' => 'n',
        'о' => 'o',
        'п' => 'p',
        'р' => 'r',
        'с' => 's',
        'т' => 't',
        'у' => 'u',
        'ф' => 'f',
        'х' => 'h',
        'ц' => 'ts',
        'ч' => 'ch',
        'ш' => 'sh',
        'щ' => 'sch',
        'ъ' => '',
        'ы' => 'y',
        'ь' => '',
        'э' => 'e',
        'ю' => 'yu',
        'я' => 'ya',
        'і' => 'i',
        'ї' => 'yi',
        'є' => 'ye',
        'ў' => 'u',
    );

    $slug = strtr($slug, $transliterationMap);
    $slug = preg_replace('/[^a-z0-9]+/', '-', $slug);
    $slug = preg_replace('/-+/', '-', (string) $slug);
    $slug = trim((string) $slug, '-');

    if (strlen($slug) > 220) {
        $slug = rtrim(substr($slug, 0, 220), '-');
    }

    return $slug;
}

function buildNewsSlugFromTitle($title)
{
    $slug = normalizeNewsSlug($title);

    return $slug !== '' ? $slug : 'material';
}

function isValidNewsSlug($slug)
{
    return (bool) preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', (string) $slug);
}

function buildUniqueNewsSlugFromUsed(array $usedSlugs, $baseSlug)
{
    $baseSlug = normalizeNewsSlug($baseSlug);
    if ($baseSlug === '') {
        $baseSlug = 'material';
    }

    $candidate = $baseSlug;
    $suffix = 2;

    while (isset($usedSlugs[$candidate])) {
        $suffixPart = '-' . $suffix;
        $maxBaseLength = max(1, 220 - strlen($suffixPart));
        $trimmedBase = rtrim(substr($baseSlug, 0, $maxBaseLength), '-');
        if ($trimmedBase === '') {
            $trimmedBase = 'material';
        }

        $candidate = $trimmedBase . $suffixPart;
        $suffix++;
    }

    return $candidate;
}

function newsSlugExists($conn, $slug, $excludeId = null)
{
    $slug = normalizeNewsSlug($slug);
    if ($slug === '') {
        return false;
    }

    if ($excludeId !== null) {
        $stmt = $conn->prepare('SELECT id FROM news WHERE slug = ? AND id <> ? LIMIT 1');
        if (!$stmt) {
            return false;
        }

        $stmt->bind_param('si', $slug, $excludeId);
    } else {
        $stmt = $conn->prepare('SELECT id FROM news WHERE slug = ? LIMIT 1');
        if (!$stmt) {
            return false;
        }

        $stmt->bind_param('s', $slug);
    }

    $stmt->execute();
    $result = $stmt->get_result();
    $exists = $result instanceof mysqli_result && $result->num_rows > 0;
    if ($result instanceof mysqli_result) {
        $result->free();
    }
    $stmt->close();

    return $exists;
}

function buildUniqueNewsSlug($conn, $title, $excludeId = null, $preferredSlug = null)
{
    $baseSlug = $preferredSlug !== null && $preferredSlug !== ''
        ? normalizeNewsSlug($preferredSlug)
        : buildNewsSlugFromTitle($title);

    if ($baseSlug === '') {
        $baseSlug = 'material';
    }

    $candidate = $baseSlug;
    $suffix = 2;

    while (newsSlugExists($conn, $candidate, $excludeId)) {
        $suffixPart = '-' . $suffix;
        $maxBaseLength = max(1, 220 - strlen($suffixPart));
        $trimmedBase = rtrim(substr($baseSlug, 0, $maxBaseLength), '-');
        if ($trimmedBase === '') {
            $trimmedBase = 'material';
        }

        $candidate = $trimmedBase . $suffixPart;
        $suffix++;
    }

    return $candidate;
}

function ensureNewsSlugColumn($conn)
{
    $result = $conn->query("SHOW COLUMNS FROM news LIKE 'slug'");
    $columnExists = $result instanceof mysqli_result && $result->num_rows > 0;
    if ($result instanceof mysqli_result) {
        $result->free();
    }

    if ($columnExists) {
        return;
    }

    if (!$conn->query('ALTER TABLE news ADD COLUMN slug VARCHAR(255) NULL AFTER title')) {
        throw new RuntimeException('Не удалось добавить колонку slug в таблицу news.');
    }
}

function ensureNewsSlugUniqueIndex($conn)
{
    $result = $conn->query("SHOW INDEX FROM news WHERE Key_name = 'news_slug_unique'");
    $indexExists = $result instanceof mysqli_result && $result->num_rows > 0;
    if ($result instanceof mysqli_result) {
        $result->free();
    }

    if ($indexExists) {
        return;
    }

    if (!$conn->query('ALTER TABLE news ADD UNIQUE INDEX news_slug_unique (slug)')) {
        throw new RuntimeException('Не удалось добавить уникальный индекс для slug.');
    }
}

function syncExistingNewsSlugs($conn)
{
    $result = $conn->query('SELECT id, title, slug FROM news ORDER BY id ASC');
    if (!$result instanceof mysqli_result) {
        return;
    }

    $rows = $result->fetch_all(MYSQLI_ASSOC);
    $result->free();

    $usedSlugs = array();
    $stmt = $conn->prepare('UPDATE news SET slug = ? WHERE id = ?');
    if (!$stmt) {
        throw new RuntimeException('Не удалось подготовить обновление slug.');
    }

    foreach ($rows as $row) {
        $newsId = (int) ($row['id'] ?? 0);
        if ($newsId <= 0) {
            continue;
        }

        $currentSlug = trim((string) ($row['slug'] ?? ''));
        $normalizedSlug = normalizeNewsSlug($currentSlug);
        $targetSlug = $normalizedSlug;

        if ($currentSlug === '' || !isValidNewsSlug($currentSlug) || isset($usedSlugs[$normalizedSlug])) {
            $targetSlug = buildUniqueNewsSlugFromUsed($usedSlugs, buildNewsSlugFromTitle($row['title'] ?? ''));
        }

        if ($targetSlug === '') {
            $targetSlug = buildUniqueNewsSlugFromUsed($usedSlugs, 'material');
        }

        if ($currentSlug !== $targetSlug) {
            $stmt->bind_param('si', $targetSlug, $newsId);
            $stmt->execute();
        }

        $usedSlugs[$targetSlug] = true;
    }

    $stmt->close();
}

function ensureNewsSlugInfrastructure($conn)
{
    static $isPrepared = false;

    if ($isPrepared) {
        return;
    }

    ensureNewsSlugColumn($conn);
    syncExistingNewsSlugs($conn);
    ensureNewsSlugUniqueIndex($conn);

    $isPrepared = true;
}

function buildNewsUrl($news)
{
    if (is_array($news)) {
        $slug = trim((string) ($news['slug'] ?? ''));
        $newsId = isset($news['id']) ? (int) $news['id'] : 0;
    } else {
        $slug = trim((string) $news);
        $newsId = 0;
    }

    if ($slug !== '') {
        return '/news/' . rawurlencode($slug);
    }

    if ($newsId > 0) {
        return '/news?id=' . $newsId;
    }

    return '/news';
}

function buildAbsoluteSiteUrl($path)
{
    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'tcsonrw-gomel.by';

    return $scheme . '://' . $host . $path;
}
