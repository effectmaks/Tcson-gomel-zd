<?php
session_start();

require_once dirname(__DIR__) . '/lib/security.php';
require_once dirname(__DIR__) . '/lib/auth.php';
include dirname(__DIR__) . '/db_connection.php';

header('Content-Type: application/json; charset=UTF-8');
header('X-Robots-Tag: noindex, nofollow', true);

if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode(array('error' => 'Требуется авторизация'));
    exit();
}

if (!hasPermission('admin')) {
    http_response_code(403);
    echo json_encode(array('error' => 'Недостаточно прав'));
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(array('error' => 'Метод не поддерживается'));
    exit();
}

$fullName = isset($_POST['fullname']) ? trim((string) $_POST['fullname']) : null;
$login = isset($_POST['login']) ? mb_strtolower(trim((string) $_POST['login']), 'UTF-8') : null;

if (($fullName === null || $fullName === '') && ($login === null || $login === '')) {
    http_response_code(400);
    echo json_encode(array('error' => 'Не переданы fullname или login'));
    exit();
}

function checkUserExistsByField($conn, $field, $value)
{
    $allowedFields = array('full_name', 'login');
    if (!in_array($field, $allowedFields, true)) {
        return false;
    }

    $sql = 'SELECT COUNT(*) AS count FROM users WHERE ' . $field . ' = ?';
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        return false;
    }

    $stmt->bind_param('s', $value);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result ? $result->fetch_assoc() : array('count' => 0);
    $stmt->close();

    return ((int) ($row['count'] ?? 0)) > 0;
}

if ($fullName !== null && $fullName !== '' && ($login === null || $login === '')) {
    echo json_encode(array('exists' => checkUserExistsByField($conn, 'full_name', $fullName)));
    exit();
}

if ($login !== null && $login !== '' && ($fullName === null || $fullName === '')) {
    echo json_encode(array('exists' => checkUserExistsByField($conn, 'login', $login)));
    exit();
}

$response = array(
    'fullname_exists' => checkUserExistsByField($conn, 'full_name', $fullName),
    'login_exists' => checkUserExistsByField($conn, 'login', $login),
);

echo json_encode($response);
