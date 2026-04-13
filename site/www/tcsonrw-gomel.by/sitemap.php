<?php

require_once __DIR__ . '/lib/news_routing.php';
include __DIR__ . '/db_connection.php';

ensureNewsSlugInfrastructure($conn);

function fetchSitemapRows($conn)
{
    $result = $conn->query('SELECT id, slug, date FROM news ORDER BY date DESC, id DESC');
    if (!$result instanceof mysqli_result) {
        return array();
    }

    $rows = $result->fetch_all(MYSQLI_ASSOC);
    $result->free();

    return $rows;
}

function fetchLatestNewsDate($conn, $type = null)
{
    if ($type !== null) {
        $stmt = $conn->prepare('SELECT MAX(date) AS last_date FROM news WHERE LOWER(type) = ?');
        if (!$stmt) {
            return date('Y-m-d');
        }

        $stmt->bind_param('s', $type);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result ? $result->fetch_assoc() : null;
        $stmt->close();

        return (string) ($row['last_date'] ?? date('Y-m-d'));
    }

    $result = $conn->query('SELECT MAX(date) AS last_date FROM news');
    if (!$result instanceof mysqli_result) {
        return date('Y-m-d');
    }

    $row = $result->fetch_assoc();
    $result->free();

    return (string) ($row['last_date'] ?? date('Y-m-d'));
}

$allNewsRows = fetchSitemapRows($conn);
$siteRootLastmod = fetchLatestNewsDate($conn);
$newsArchiveLastmod = fetchLatestNewsDate($conn, 'новость');
$eventsArchiveLastmod = fetchLatestNewsDate($conn, 'мероприятие');
$staticEntries = array(
    array('path' => '/', 'lastmod' => $siteRootLastmod),
    array('path' => '/listnews.php', 'lastmod' => $newsArchiveLastmod),
    array('path' => '/listevents.php', 'lastmod' => $eventsArchiveLastmod),
);

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
