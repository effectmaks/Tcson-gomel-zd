<?php

require_once __DIR__ . '/security.php';

function dbFlagToBool($value)
{
    if (is_bool($value)) {
        return $value;
    }

    if (is_int($value) || is_float($value)) {
        return (int) $value === 1;
    }

    if (!is_string($value)) {
        return false;
    }

    $normalized = mb_strtolower(trim($value), 'UTF-8');
    return in_array($normalized, array('1', 'true', 'yes', 'on', 'admin', 'active', 'enabled'), true);
}

function normalizePermissionName($value)
{
    if (!is_string($value)) {
        return null;
    }

    $normalized = mb_strtolower(trim($value), 'UTF-8');
    if ($normalized === '') {
        return null;
    }

    $map = array(
        'admin' => 'admin',
        'news' => 'news',
        'docs' => 'docs',
        'people' => 'people',
        'messenger' => 'messenger',
        'tokens' => 'tokens',
        'управление пользователями' => 'admin',
        'события' => 'news',
        'документы' => 'docs',
        'учет людей' => 'people',
        'учёт людей' => 'people',
        'чаты' => 'messenger',
        'мессенджер' => 'messenger',
        'токены' => 'tokens',
        'токен' => 'tokens',
        'bearer-токены' => 'tokens',
        'bearer токены' => 'tokens',
    );

    return isset($map[$normalized]) ? $map[$normalized] : null;
}

function isUserBlocked(array $user)
{
    $status = isset($user['status']) ? mb_strtolower(trim((string) $user['status']), 'UTF-8') : '';
    if (in_array($status, array('blocked', 'заблокирован', 'block', 'inactive'), true)) {
        return true;
    }

    if (isset($user['blockuser']) && dbFlagToBool($user['blockuser'])) {
        return true;
    }

    if (isset($user['is_blocked']) && dbFlagToBool($user['is_blocked'])) {
        return true;
    }

    if (isset($user['blocked']) && dbFlagToBool($user['blocked'])) {
        return true;
    }

    return false;
}

function parseUserPermissions(array $user)
{
    $permissions = array();

    if (isset($user['role']) && mb_strtolower(trim((string) $user['role']), 'UTF-8') === 'admin') {
        $permissions[] = 'admin';
    }

    if (isset($user['admin']) && dbFlagToBool($user['admin'])) {
        $permissions[] = 'admin';
    }

    if (isset($user['news']) && dbFlagToBool($user['news'])) {
        $permissions[] = 'news';
    }

    if (isset($user['docs']) && dbFlagToBool($user['docs'])) {
        $permissions[] = 'docs';
    }

    if (isset($user['people']) && dbFlagToBool($user['people'])) {
        $permissions[] = 'people';
    }

    if (isset($user['messenger']) && dbFlagToBool($user['messenger'])) {
        $permissions[] = 'messenger';
    }

    if (isset($user['tokens']) && dbFlagToBool($user['tokens'])) {
        $permissions[] = 'tokens';
    }

    if (isset($user['permissions']) && is_string($user['permissions'])) {
        $rawPermissions = trim($user['permissions']);
        $directPermission = normalizePermissionName($rawPermissions);
        if ($directPermission !== null) {
            $permissions[] = $directPermission;
        }

        $chunks = preg_split('/[;,]+/', $rawPermissions);
        foreach ($chunks as $chunk) {
            $normalizedChunk = trim((string) $chunk);
            if ($normalizedChunk === '') {
                continue;
            }

            $mappedPermission = normalizePermissionName($normalizedChunk);
            if ($mappedPermission !== null) {
                $permissions[] = $mappedPermission;
                continue;
            }

            foreach (preg_split('/\s+/', $normalizedChunk) as $word) {
                $mappedWordPermission = normalizePermissionName((string) $word);
                if ($mappedWordPermission !== null) {
                    $permissions[] = $mappedWordPermission;
                }
            }
        }
    }

    if (in_array('admin', $permissions, true)) {
        $permissions[] = 'news';
        $permissions[] = 'docs';
        $permissions[] = 'people';
        $permissions[] = 'messenger';
        $permissions[] = 'tokens';
    }

    $permissions = array_values(array_unique($permissions));
    return $permissions;
}

function loginUserWithPermissions($login, array $user)
{
    session_regenerate_id(true);
    $_SESSION['login'] = (string) $login;
    $_SESSION['permissions'] = parseUserPermissions($user);
}

function isLoggedIn()
{
    return isset($_SESSION['login']) && is_string($_SESSION['login']) && $_SESSION['login'] !== '';
}

function hasPermission($permission)
{
    if (!isset($_SESSION['permissions']) || !is_array($_SESSION['permissions'])) {
        return false;
    }

    if (in_array('admin', $_SESSION['permissions'], true)) {
        return true;
    }

    return in_array($permission, $_SESSION['permissions'], true);
}

function requireLogin()
{
    if (!isLoggedIn()) {
        redirectTo('/auth.php');
    }
}

function requirePermission($permission)
{
    requireLogin();

    if (!hasPermission($permission)) {
        http_response_code(403);
        exit('Недостаточно прав для выполнения операции.');
    }
}

function getCurrentUserByLogin($conn)
{
    if (!isLoggedIn() || !($conn instanceof mysqli)) {
        return null;
    }

    $login = (string) ($_SESSION['login'] ?? '');
    if ($login === '') {
        return null;
    }

    $stmt = $conn->prepare('SELECT * FROM users WHERE login = ? LIMIT 1');
    if (!$stmt) {
        return null;
    }

    $stmt->bind_param('s', $login);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result ? $result->fetch_assoc() : null;
    $stmt->close();

    return $user ?: null;
}
