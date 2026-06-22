<?php

require_once __DIR__ . '/../lib/security.php';
require_once __DIR__ . '/../lib/auth.php';
require_once __DIR__ . '/../lib/public_page_visibility.php';
include __DIR__ . '/../db_connection.php';

header('Content-Type: application/json; charset=UTF-8');

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
    http_response_code(405);
    echo json_encode(
        array('ok' => false, 'message' => 'Метод не поддерживается.'),
        JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
    );
    exit();
}

requireLogin();
requireCsrfToken();

$pageKey = normalizePublicPageVisibilityKey($_POST['page_key'] ?? '');
$rawPublishedValue = trim((string) ($_POST['is_published'] ?? ''));
$actorLogin = trim((string) ($_SESSION['login'] ?? ''));

if ($pageKey === null || !isManagedPublicPageKey($pageKey)) {
    http_response_code(422);
    echo json_encode(
        array('ok' => false, 'message' => 'Не удалось определить страницу.'),
        JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
    );
    exit();
}

if ($rawPublishedValue === '') {
    http_response_code(422);
    echo json_encode(
        array('ok' => false, 'message' => 'Не передан флаг публикации.'),
        JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
    );
    exit();
}

$isPublished = in_array($rawPublishedValue, array('1', 'true', 'on', 'yes'), true);

try {
    $state = updatePublicPageVisibility($conn, $pageKey, $isPublished, $actorLogin);
} catch (Throwable $exception) {
    http_response_code(500);
    echo json_encode(
        array('ok' => false, 'message' => 'Не удалось сохранить настройки страницы.'),
        JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
    );
    exit();
}

echo json_encode(
    array(
        'ok' => true,
        'page_key' => $state['key'],
        'is_published' => $state['is_published'],
        'published_by_login' => $state['published_by_login'],
        'updated_by_login' => $state['updated_by_login'],
        'updated_at' => $state['updated_at'],
        'status_text' => $state['status_text'],
    ),
    JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
);
