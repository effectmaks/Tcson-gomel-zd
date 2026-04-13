<?php

require_once __DIR__ . '/lib/security.php';
require_once __DIR__ . '/lib/auth.php';
requirePermission('news');

$newsId = getIntFromGet('id');
$redirectPath = '/addnews.php';

if ($newsId !== null) {
    $redirectPath .= '?id=' . $newsId;
}

redirectTo($redirectPath);
