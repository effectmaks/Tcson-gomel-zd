<?php
session_start();

require_once __DIR__ . '/lib/security.php';
require_once __DIR__ . '/lib/auth.php';
requirePermission('admin');
include __DIR__ . '/db_connection.php';
header('X-Robots-Tag: noindex, nofollow', true);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Метод не поддерживается.');
}

requireCsrfToken();

$userId = getIntFromPost('id');
if ($userId === null) {
    redirectTo('/listuser.php');
}

$targetUser = null;
$selectStmt = $conn->prepare('SELECT id, login FROM users WHERE id = ? LIMIT 1');
if ($selectStmt) {
    $selectStmt->bind_param('i', $userId);
    $selectStmt->execute();
    $result = $selectStmt->get_result();
    $targetUser = $result ? $result->fetch_assoc() : null;
    $selectStmt->close();
}

if (!$targetUser) {
    redirectTo('/listuser.php');
}

$currentSessionLogin = (string) ($_SESSION['login'] ?? '');
if ($currentSessionLogin !== '' && isset($targetUser['login']) && (string) $targetUser['login'] === $currentSessionLogin) {
    http_response_code(400);
    exit('Нельзя удалить текущего пользователя.');
}

$deleteStmt = $conn->prepare('DELETE FROM users WHERE id = ?');
if (!$deleteStmt) {
    http_response_code(500);
    exit('Ошибка удаления пользователя.');
}

$deleteStmt->bind_param('i', $userId);
$deleteStmt->execute();
$deleteStmt->close();

redirectTo('/listuser.php');
