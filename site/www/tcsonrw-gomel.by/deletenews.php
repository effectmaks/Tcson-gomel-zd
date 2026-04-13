<?php
session_start();

require_once __DIR__ . '/lib/security.php';
require_once __DIR__ . '/lib/auth.php';
require_once __DIR__ . '/lib/news_management.php';
requirePermission('news');
include __DIR__ . '/db_connection.php';
header('X-Robots-Tag: noindex, nofollow', true);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Метод не поддерживается.');
}

requireCsrfToken();

$newsId = getIntFromPost('id');
if ($newsId === null) {
    redirectTo('/listevents.php');
}

$filenames = array();
$videoFilename = null;
$selectStmt = $conn->prepare('SELECT filename FROM photos WHERE news_id = ?');
if ($selectStmt) {
    $selectStmt->bind_param('i', $newsId);
    $selectStmt->execute();
    $result = $selectStmt->get_result();
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            if (isset($row['filename'])) {
                $filenames[] = $row['filename'];
            }
        }
    }
    $selectStmt->close();
}

ensureNewsVideoInfrastructure($conn);
$news = fetchNewsById($conn, $newsId);
if ($news) {
    $videoFilename = getNewsVideoFilename($news);
}

$deletePhotosStmt = $conn->prepare('DELETE FROM photos WHERE news_id = ?');
if ($deletePhotosStmt) {
    $deletePhotosStmt->bind_param('i', $newsId);
    $deletePhotosStmt->execute();
    $deletePhotosStmt->close();
}

$deleteNewsStmt = $conn->prepare('DELETE FROM news WHERE id = ?');
if ($deleteNewsStmt) {
    $deleteNewsStmt->bind_param('i', $newsId);
    $deleteNewsStmt->execute();
    $deleteNewsStmt->close();
}

foreach ($filenames as $filename) {
    $safeFilename = sanitizeStoredFilename($filename);
    if ($safeFilename === '') {
        continue;
    }

    $filePath = __DIR__ . '/photos/' . $safeFilename;
    if (is_file($filePath)) {
        @unlink($filePath);
    }
}

if ($videoFilename !== null) {
    deleteNewsVideoFileByFilename($videoFilename);
}

redirectTo('/listevents.php');
