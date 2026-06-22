<?php

require_once __DIR__ . '/lib/news_routing.php';
require_once __DIR__ . '/lib/public_page_visibility.php';
include __DIR__ . '/db_connection.php';

ensureNewsSlugInfrastructure($conn);

function fetchSitemapRows($conn)
{
    ensurePublicPageVisibilityInfrastructure($conn);

    $result = $conn->query('SELECT id, slug, date FROM news WHERE ' . getPublishedNewsVisibilitySqlCondition('news') . ' ORDER BY date DESC, id DESC');
    if (!$result instanceof mysqli_result) {
        return array();
    }

    $rows = $result->fetch_all(MYSQLI_ASSOC);
    $result->free();

    return $rows;
}

function fetchLatestNewsDate($conn, $type = null, $onlyPublished = false)
{
    $conditions = array();
    $types = '';
    $values = array();

    if ($type !== null) {
        $conditions[] = 'LOWER(type) = ?';
        $types .= 's';
        $values[] = $type;
    }

    if ($onlyPublished) {
        ensurePublicPageVisibilityInfrastructure($conn);
        $conditions[] = getPublishedNewsVisibilitySqlCondition('news');
    }

    $sql = 'SELECT MAX(date) AS last_date FROM news';
    if (!empty($conditions)) {
        $sql .= ' WHERE ' . implode(' AND ', $conditions);
    }

    if ($types === '') {
        $result = $conn->query($sql);
        if (!$result instanceof mysqli_result) {
            return date('Y-m-d');
        }

        $row = $result->fetch_assoc();
        $result->free();

        return (string) ($row['last_date'] ?? date('Y-m-d'));
    }

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        return date('Y-m-d');
    }

    bindDynamicParams($stmt, $types, $values);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result ? $result->fetch_assoc() : null;
    $stmt->close();

    return (string) ($row['last_date'] ?? date('Y-m-d'));
}

$allNewsRows = fetchSitemapRows($conn);
$siteRootLastmod = fetchLatestNewsDate($conn, null, true);
$newsArchiveLastmod = fetchLatestNewsDate($conn, 'новость', true);
$eventsArchiveLastmod = fetchLatestNewsDate($conn, 'мероприятие', true);
$staticEntries = array(
    array('path' => '/', 'lastmod' => $siteRootLastmod),
    array('path' => '/contacts.php', 'lastmod' => date('Y-m-d', filemtime(__DIR__ . '/contacts.php'))),
    array('path' => '/anti-corruption.php', 'lastmod' => date('Y-m-d', filemtime(__DIR__ . '/anti-corruption.php'))),
    array('path' => '/gossocpodderzhka-invalidov.php', 'lastmod' => date('Y-m-d', filemtime(__DIR__ . '/gossocpodderzhka-invalidov.php'))),
    array('path' => '/direct-phone-line.php', 'lastmod' => date('Y-m-d', filemtime(__DIR__ . '/direct-phone-line.php'))),
    array('path' => '/electronic-appeals.php', 'lastmod' => date('Y-m-d', filemtime(__DIR__ . '/electronic-appeals.php'))),
    array('path' => '/structure.php', 'lastmod' => date('Y-m-d', filemtime(__DIR__ . '/structure.php'))),
    array('path' => '/department/', 'lastmod' => date('Y-m-d', filemtime(__DIR__ . '/department/index.php'))),
    array('path' => '/listnews.php', 'lastmod' => $newsArchiveLastmod),
    array('path' => '/listevents.php', 'lastmod' => $eventsArchiveLastmod),
);
$staticEntries = array_values(array_filter($staticEntries, function ($entry) use ($conn) {
    $path = (string) ($entry['path'] ?? '');

    if ($path === '/') {
        return true;
    }

    return isPublicPagePublished($conn, $path);
}));

header('Content-Type: application/xml; charset=UTF-8');
echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
<?php foreach ($staticEntries as $entry): ?>
  <url>
    <loc><?php echo htmlspecialchars(buildAbsoluteSiteUrl($entry['path']), ENT_QUOTES, 'UTF-8'); ?></loc>
    <lastmod><?php echo htmlspecialchars((string) $entry['lastmod'], ENT_QUOTES, 'UTF-8'); ?></lastmod>
  </url>
<?php endforeach; ?>
<?php foreach ($allNewsRows as $row): ?>
  <url>
    <loc><?php echo htmlspecialchars(buildAbsoluteSiteUrl(buildNewsUrl($row)), ENT_QUOTES, 'UTF-8'); ?></loc>
    <lastmod><?php echo htmlspecialchars((string) ($row['date'] ?? date('Y-m-d')), ENT_QUOTES, 'UTF-8'); ?></lastmod>
  </url>
<?php endforeach; ?>
</urlset>
