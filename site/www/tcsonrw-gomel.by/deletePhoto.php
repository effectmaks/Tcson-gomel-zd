<?php
session_start();

require_once __DIR__ . '/lib/security.php';
require_once __DIR__ . '/lib/auth.php';
requirePermission('news');
include __DIR__ . '/db_connection.php';
header('X-Robots-Tag: noindex, nofollow', true);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Метод не поддерживается.');
}

requireCsrfToken();

$photoId = getIntFromPost('photoId');
$newsId = getIntFromPost('newsId');

if ($photoId === null || $newsId === null) {
    redirectTo('/listevents.php');
}

$stmt = $conn->prepare('SELECT filename FROM photos WHERE id = ? AND news_id = ? LIMIT 1');
if ($stmt) {
    $stmt->bind_param('ii', $photoId, $newsId);
    $stmt->execute();
    $result = $stmt->get_result();
    $photo = $result ? $result->fetch_assoc() : null;
    $stmt->close();

    if ($photo && isset($photo['filename'])) {
        $deleteStmt = $conn->prepare('DELETE FROM photos WHERE id = ? AND news_id = ?');
        if ($deleteStmt) {
            $deleteStmt->bind_param('ii', $photoId, $newsId);
            $deleteStmt->execute();
            $deleteStmt->close();
        }

        $safeFilename = sanitizeStoredFilename($photo['filename']);
        if ($safeFilename !== '') {
            $targetPath = __DIR__ . '/photos/' . $safeFilename;
            if (is_file($targetPath)) {
                @unlink($targetPath);
            }
        }
    }
}

redirectTo('/addnews.php?id=' . $newsId);
