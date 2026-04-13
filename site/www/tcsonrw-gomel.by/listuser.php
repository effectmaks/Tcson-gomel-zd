<?php
session_start();

require_once __DIR__ . '/lib/security.php';
require_once __DIR__ . '/lib/auth.php';
require_once __DIR__ . '/lib/messenger.php';
requirePermission('admin');
include __DIR__ . '/db_connection.php';
messengerEnsureUsersMessengerColumn($conn);

function ensureUsersPermissionColumns($conn)
{
    $requiredColumns = array(
        'admin' => 'ALTER TABLE users ADD COLUMN admin TINYINT(1) NOT NULL DEFAULT 0',
        'news' => 'ALTER TABLE users ADD COLUMN news TINYINT(1) NOT NULL DEFAULT 0',
        'docs' => 'ALTER TABLE users ADD COLUMN docs TINYINT(1) NOT NULL DEFAULT 0',
        'people' => 'ALTER TABLE users ADD COLUMN people TINYINT(1) NOT NULL DEFAULT 0',
        'messenger' => 'ALTER TABLE users ADD COLUMN messenger TINYINT(1) NOT NULL DEFAULT 0',
        'tokens' => 'ALTER TABLE users ADD COLUMN tokens TINYINT(1) NOT NULL DEFAULT 0',
    );

    foreach ($requiredColumns as $columnName => $sql) {
        $result = $conn->query("SHOW COLUMNS FROM users LIKE '" . $conn->real_escape_string($columnName) . "'");
        $columnExists = $result instanceof mysqli_result && $result->num_rows > 0;
        if ($result instanceof mysqli_result) {
            $result->free();
        }

        if ($columnExists) {
            continue;
        }

        if (!$conn->query($sql)) {
            throw new RuntimeException('Не удалось добавить колонку роли `' . $columnName . '` в таблицу users.');
        }
    }

    $permissionsColumnResult = $conn->query("SHOW COLUMNS FROM users LIKE 'permissions'");
    $permissionsColumn = $permissionsColumnResult instanceof mysqli_result ? $permissionsColumnResult->fetch_assoc() : null;
    if ($permissionsColumnResult instanceof mysqli_result) {
        $permissionsColumnResult->free();
    }

    if ($permissionsColumn && isset($permissionsColumn['Type'])) {
        $permissionsType = mb_strtolower((string) $permissionsColumn['Type'], 'UTF-8');
        if (strpos($permissionsType, 'enum(') === 0) {
            if (!$conn->query('ALTER TABLE users MODIFY COLUMN permissions VARCHAR(255) NULL DEFAULT NULL')) {
                throw new RuntimeException('Не удалось изменить тип колонки `permissions` для хранения нескольких ролей.');
            }
        }
    }
}

function fetchUserByIdForAdmin($conn, $userId)
{
    $stmt = $conn->prepare('SELECT * FROM users WHERE id = ? LIMIT 1');
    if (!$stmt) {
        return null;
    }

    $stmt->bind_param('i', $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result ? $result->fetch_assoc() : null;
    $stmt->close();

    return $user ?: null;
}

function getUsersTableColumnsMap($conn)
{
    $tableColumns = array();
    $columnsResult = $conn->query('SHOW COLUMNS FROM users');
    if ($columnsResult) {
        while ($column = $columnsResult->fetch_assoc()) {
            if (isset($column['Field'])) {
                $tableColumns[$column['Field']] = true;
            }
        }
    }

    return $tableColumns;
}

function getPermissionFlagsFromParsedUser(array $user, array $permissionKeys)
{
    $flags = array();
    foreach ($permissionKeys as $permissionKey) {
        $flags[$permissionKey] = 0;
    }

    $parsedPermissions = parseUserPermissions($user);
    foreach ($parsedPermissions as $permission) {
        if (isset($flags[$permission])) {
            $flags[$permission] = 1;
        }
    }

    return $flags;
}

function getEmptyPermissionFlags(array $permissionKeys)
{
    $flags = array();
    foreach ($permissionKeys as $permissionKey) {
        $flags[$permissionKey] = 0;
    }

    return $flags;
}

function buildUserFormStateFromUser(array $user, array $permissionKeys)
{
    return array(
        'id' => isset($user['id']) ? (int) $user['id'] : 0,
        'fio' => (string) ($user['full_name'] ?? $user['fio'] ?? ''),
        'login' => (string) ($user['login'] ?? ''),
        'password' => '',
        'permissions' => getPermissionFlagsFromParsedUser($user, $permissionKeys),
        'blocked' => isUserBlocked($user) ? 1 : 0,
    );
}

function buildUserFormStateFromPost(array $permissionKeys)
{
    $permissions = getEmptyPermissionFlags($permissionKeys);
    foreach ($permissionKeys as $permissionKey) {
        $permissions[$permissionKey] = isset($_POST[$permissionKey]) ? 1 : 0;
    }

    return array(
        'id' => isset($_POST['id']) ? (int) $_POST['id'] : 0,
        'fio' => trim((string) ($_POST['fio'] ?? '')),
        'login' => mb_strtolower(trim((string) ($_POST['login'] ?? '')), 'UTF-8'),
        'password' => (string) ($_POST['password'] ?? ''),
        'permissions' => $permissions,
        'blocked' => isset($_POST['blockuser']) ? 1 : 0,
    );
}

function validateUserFormState($conn, array $tableColumns, array $formState, array $permissionKeys, $isEdit, $editingUserId = null)
{
    $errors = array();

    $selectedPermissionsCount = 0;
    foreach ($formState['permissions'] as $permissionValue) {
        if (!empty($permissionValue)) {
            $selectedPermissionsCount++;
        }
    }

    $hasLegacyPermissionColumns = false;
    foreach ($permissionKeys as $permissionColumn) {
        if (isset($tableColumns[$permissionColumn])) {
            $hasLegacyPermissionColumns = true;
            break;
        }
    }

    if ($formState['fio'] === '' || mb_strlen($formState['fio'], 'UTF-8') < 5) {
        $errors[] = 'Введите корректное ФИО (минимум 5 символов).';
    }

    if ($formState['login'] === '' || !preg_match('/^[a-z0-9_]+$/', $formState['login'])) {
        $errors[] = 'Логин должен содержать только латинские буквы, цифры и знак подчеркивания.';
    }

    if ($isEdit) {
        if ($formState['password'] !== '' && mb_strlen($formState['password'], '8bit') < 8) {
            $errors[] = 'Новый пароль должен содержать минимум 8 символов.';
        }
    } else {
        if (mb_strlen($formState['password'], '8bit') < 8) {
            $errors[] = 'Пароль должен содержать минимум 8 символов.';
        }
    }

    if (isset($tableColumns['permissions']) && !$hasLegacyPermissionColumns && $selectedPermissionsCount > 1) {
        $errors[] = 'В текущей конфигурации можно выбрать только одно разрешение для пользователя.';
    }

    if (!isset($tableColumns['login'])) {
        $errors[] = 'Таблица users не содержит обязательное поле login.';
    }

    if (!$isEdit && !isset($tableColumns['password'])) {
        $errors[] = 'Таблица users не содержит обязательное поле password.';
    }

    if (!empty($errors)) {
        return $errors;
    }

    $duplicateConditions = array();
    $duplicateValues = array();
    $duplicateTypes = '';

    if (isset($tableColumns['login'])) {
        $duplicateConditions[] = 'login = ?';
        $duplicateValues[] = $formState['login'];
        $duplicateTypes .= 's';
    }

    if (isset($tableColumns['full_name'])) {
        $duplicateConditions[] = 'full_name = ?';
        $duplicateValues[] = $formState['fio'];
        $duplicateTypes .= 's';
    } elseif (isset($tableColumns['fio'])) {
        $duplicateConditions[] = 'fio = ?';
        $duplicateValues[] = $formState['fio'];
        $duplicateTypes .= 's';
    }

    if (empty($duplicateConditions)) {
        return $errors;
    }

    $duplicateSql = 'SELECT id FROM users WHERE ' . implode(' OR ', $duplicateConditions);
    if ($isEdit) {
        $duplicateSql = 'SELECT id FROM users WHERE (' . implode(' OR ', $duplicateConditions) . ') AND id <> ?';
        $duplicateValues[] = (int) $editingUserId;
        $duplicateTypes .= 'i';
    }
    $duplicateSql .= ' LIMIT 1';

    $duplicateStmt = $conn->prepare($duplicateSql);
    if (!$duplicateStmt) {
        $errors[] = 'Ошибка проверки дубликатов пользователя.';
        error_log('listuser duplicate prepare error: ' . $conn->error);
        return $errors;
    }

    bindDynamicParams($duplicateStmt, $duplicateTypes, $duplicateValues);
    $duplicateStmt->execute();
    $duplicateResult = $duplicateStmt->get_result();
    $duplicateRow = $duplicateResult ? $duplicateResult->fetch_assoc() : null;
    $duplicateStmt->close();

    if ($duplicateRow) {
        $errors[] = 'Пользователь с таким логином или ФИО уже существует.';
    }

    return $errors;
}

function buildUserMutationData(array $tableColumns, array $formState, array $permissionKeys)
{
    $enumPermissionValue = null;
    $selectedPermissions = array();
    foreach ($permissionKeys as $permissionKey) {
        if (!empty($formState['permissions'][$permissionKey])) {
            $selectedPermissions[] = $permissionKey;
            $enumPermissionValue = $permissionKey;
        }
    }

    $data = array();
    if (isset($tableColumns['full_name'])) {
        $data['full_name'] = $formState['fio'];
    } elseif (isset($tableColumns['fio'])) {
        $data['fio'] = $formState['fio'];
    }

    if (isset($tableColumns['login'])) {
        $data['login'] = $formState['login'];
    }

    foreach ($permissionKeys as $permissionKey) {
        if (isset($tableColumns[$permissionKey])) {
            $data[$permissionKey] = (int) $formState['permissions'][$permissionKey];
        }
    }

    if (isset($tableColumns['permissions'])) {
        $data['permissions'] = !empty($selectedPermissions) ? implode(';', $selectedPermissions) : null;
    }

    if (isset($tableColumns['blockuser'])) {
        $data['blockuser'] = (int) $formState['blocked'];
    }

    if (isset($tableColumns['is_blocked'])) {
        $data['is_blocked'] = (int) $formState['blocked'];
    }

    if (isset($tableColumns['blocked'])) {
        $data['blocked'] = (int) $formState['blocked'];
    }

    if (isset($tableColumns['status'])) {
        $data['status'] = !empty($formState['blocked']) ? 'blocked' : 'active';
    }

    return $data;
}

$permissionLabels = array(
    'admin' => 'Управление пользователями',
    'news' => 'События',
    'docs' => 'Документы',
    'people' => 'Учет людей',
    'messenger' => 'Задания',
    'tokens' => 'Токены',
);
$permissionKeys = array_keys($permissionLabels);
ensureUsersPermissionColumns($conn);
$tableColumns = getUsersTableColumnsMap($conn);
$csrfToken = getCsrfToken();
$currentSessionLogin = (string) ($_SESSION['login'] ?? '');
$statusMessages = array(
    'created' => 'Пользователь успешно создан.',
    'updated' => 'Пользователь успешно обновлен.',
);
$flashSuccess = '';
$status = trim((string) ($_GET['status'] ?? ''));
if (isset($statusMessages[$status])) {
    $flashSuccess = $statusMessages[$status];
}

$activeModal = '';
$addErrors = array();
$editErrors = array();
$addFormState = array(
    'id' => 0,
    'fio' => '',
    'login' => '',
    'password' => '',
    'permissions' => getEmptyPermissionFlags($permissionKeys),
    'blocked' => 0,
);
$editFormState = $addFormState;
$editModalUserLabel = '';
$pageError = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    requireCsrfToken();
    $formAction = trim((string) ($_POST['form_action'] ?? ''));

    if ($formAction === 'add_user') {
        $addFormState = buildUserFormStateFromPost($permissionKeys);
        $addErrors = validateUserFormState($conn, $tableColumns, $addFormState, $permissionKeys, false);

        if (empty($addErrors)) {
            $insertData = buildUserMutationData($tableColumns, $addFormState, $permissionKeys);
            if (isset($tableColumns['password'])) {
                $insertData['password'] = password_hash($addFormState['password'], PASSWORD_DEFAULT);
            }

            $columns = array_keys($insertData);
            $placeholders = implode(', ', array_fill(0, count($columns), '?'));
            $sql = 'INSERT INTO users (' . implode(', ', $columns) . ') VALUES (' . $placeholders . ')';
            $types = '';
            $values = array();
            foreach ($insertData as $value) {
                if (is_int($value)) {
                    $types .= 'i';
                } else {
                    $types .= 's';
                }
                $values[] = $value;
            }

            $insertStmt = $conn->prepare($sql);
            if (!$insertStmt) {
                $addErrors[] = 'Ошибка подготовки создания пользователя.';
                error_log('listuser add prepare error: ' . $conn->error);
            } else {
                bindDynamicParams($insertStmt, $types, $values);
                if ($insertStmt->execute()) {
                    $insertStmt->close();
                    redirectTo('/listuser.php?status=created');
                }

                $addErrors[] = 'Ошибка создания пользователя.';
                error_log('listuser add execute error: ' . $insertStmt->error);
                $insertStmt->close();
            }
        }

        if (!empty($addErrors)) {
            $activeModal = 'add';
        }
    } elseif ($formAction === 'edit_user') {
        $editFormState = buildUserFormStateFromPost($permissionKeys);
        $editingUserId = (int) $editFormState['id'];
        $existingUser = $editingUserId > 0 ? fetchUserByIdForAdmin($conn, $editingUserId) : null;
        if (!$existingUser) {
            $editErrors[] = 'Пользователь не найден.';
            $activeModal = 'edit';
        } else {
            $editModalUserLabel = (string) ($existingUser['full_name'] ?? $existingUser['fio'] ?? $existingUser['login'] ?? '');
            $editErrors = validateUserFormState($conn, $tableColumns, $editFormState, $permissionKeys, true, $editingUserId);

            if (empty($editErrors)) {
                $updateData = buildUserMutationData($tableColumns, $editFormState, $permissionKeys);
                if ($editFormState['password'] !== '' && isset($tableColumns['password'])) {
                    $updateData['password'] = password_hash($editFormState['password'], PASSWORD_DEFAULT);
                }

                if (empty($updateData)) {
                    $editErrors[] = 'Нет полей для обновления в таблице users.';
                } else {
                    $setParts = array();
                    $types = '';
                    $values = array();
                    foreach ($updateData as $column => $value) {
                        $setParts[] = $column . ' = ?';
                        if (is_int($value)) {
                            $types .= 'i';
                        } else {
                            $types .= 's';
                        }
                        $values[] = $value;
                    }

                    $values[] = $editingUserId;
                    $types .= 'i';

                    $sql = 'UPDATE users SET ' . implode(', ', $setParts) . ' WHERE id = ?';
                    $updateStmt = $conn->prepare($sql);
                    if (!$updateStmt) {
                        $editErrors[] = 'Ошибка подготовки обновления пользователя.';
                        error_log('listuser edit prepare error: ' . $conn->error);
                    } else {
                        bindDynamicParams($updateStmt, $types, $values);
                        if ($updateStmt->execute()) {
                            $updateStmt->close();
                            redirectTo('/listuser.php?status=updated');
                        }

                        $editErrors[] = 'Ошибка обновления пользователя.';
                        error_log('listuser edit execute error: ' . $updateStmt->error);
                        $updateStmt->close();
                    }
                }
            }

            if (!empty($editErrors)) {
                $activeModal = 'edit';
            }
        }
    }
}

$users = array();
$result = $conn->query('SELECT * FROM users ORDER BY id DESC');
if ($result) {
    $users = $result->fetch_all(MYSQLI_ASSOC);
} else {
    $pageError = 'Не удалось загрузить список пользователей.';
    error_log('listuser query error: ' . $conn->error);
}

if ($activeModal === 'edit' && $editModalUserLabel === '' && !empty($editFormState['fio'])) {
    $editModalUserLabel = $editFormState['fio'];
}

$seoTitleMeta = 'Список пользователей — ТЦСОН Железнодорожного района г. Гомеля';
$seoDescriptionMeta = 'Список пользователей административной панели ТЦСОН Железнодорожного района г. Гомеля.';
$seoRobotsMeta = 'noindex,nofollow';
?>

<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="/img/favicon.png" type="image/x-icon">
    <link rel="stylesheet" href="/css/normalize.css?v=<?= filemtime($_SERVER['DOCUMENT_ROOT'] . '/css/normalize.css') ?>">
    <link rel="stylesheet" href="/css/cssbootstrap.min.css?v=<?= filemtime($_SERVER['DOCUMENT_ROOT'] . '/css/cssbootstrap.min.css') ?>">
    <link rel="stylesheet" href="/css/style.css?v=<?= filemtime($_SERVER['DOCUMENT_ROOT'] . '/css/style.css') ?>">
    <script src="https://lidrekon.ru/slep/js/jquery.js"></script>
    <script src="https://lidrekon.ru/slep/js/uhpv-full.min.js"></script>
    <link rel="stylesheet" href="/css/media.css?v=<?= filemtime($_SERVER['DOCUMENT_ROOT'] . '/css/media.css') ?>">
    <link rel="stylesheet" href="/css/media_mobile.css?v=<?= filemtime($_SERVER['DOCUMENT_ROOT'] . '/css/media_mobile.css') ?>">
    <title>Список пользователей — ТЦСОН</title>
    <?php
    $seoScheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $seoHost = $_SERVER['HTTP_HOST'] ?? 'tcsonrw-gomel.by';
    $seoRequestUri = $_SERVER['REQUEST_URI'] ?? '/';
    $seoPath = strtok($seoRequestUri, '?');
    $seoCanonical = $seoScheme . '://' . $seoHost . $seoPath;
    $seoTitleMeta = $seoTitleMeta ?? 'ТЦСОН Железнодорожного района г. Гомеля';
    $seoDescriptionMeta = $seoDescriptionMeta ?? 'Официальный сайт ТЦСОН Железнодорожного района г. Гомеля. Новости, мероприятия, услуги и контакты.';
    $seoOgImage = $seoOgImage ?? '/img/logo-main.png';
    $seoRobotsMeta = $seoRobotsMeta ?? 'index,follow';
    $seoOgImageUrl = preg_match('#^https?://#i', $seoOgImage)
        ? $seoOgImage
        : ($seoScheme . '://' . $seoHost . $seoOgImage);
    ?>
    <meta name="description" content="<?php echo htmlspecialchars($seoDescriptionMeta, ENT_QUOTES, 'UTF-8'); ?>">
    <meta name="robots" content="<?php echo htmlspecialchars($seoRobotsMeta, ENT_QUOTES, 'UTF-8'); ?>">
    <link rel="canonical" href="<?php echo htmlspecialchars($seoCanonical, ENT_QUOTES, 'UTF-8'); ?>">
    <meta property="og:locale" content="ru_RU">
    <meta property="og:type" content="website">
    <meta property="og:title" content="<?php echo htmlspecialchars($seoTitleMeta, ENT_QUOTES, 'UTF-8'); ?>">
    <meta property="og:description" content="<?php echo htmlspecialchars($seoDescriptionMeta, ENT_QUOTES, 'UTF-8'); ?>">
    <meta property="og:url" content="<?php echo htmlspecialchars($seoCanonical, ENT_QUOTES, 'UTF-8'); ?>">
    <meta property="og:image" content="<?php echo htmlspecialchars($seoOgImageUrl, ENT_QUOTES, 'UTF-8'); ?>">
</head>

<body class="<?php echo $activeModal !== '' ? 'has-admin-modal' : ''; ?>">
    <?php include 'header.php'; ?>
    <main class="admin-page">
            <div class="container">
                <div class="admin-page__shell">
                <section class="admin-card admin-card--action">
                    <div class="admin-card__top admin-card__top--compact">
                        <div class="admin-card__intro admin-card__intro--compact">
                            <p class="admin-card__eyebrow">Администрирование</p>
                            <h2 class="admin-card__subtitle">Список пользователей</h2>
                            <p class="admin-card__text">Управление учетными записями личного кабинета, правами доступа и статусами пользователей.</p>
                        </div>
                        <div class="admin-card__actions">
                            <a href="/auth.php" class="admin-secondary-button">Личный кабинет</a>
                            <button type="button" class="admin-primary-button js-open-user-modal" data-modal-target="adminAddUserModal">Добавить пользователя</button>
                        </div>
                    </div>
                </section>

                <section class="admin-card">
                    <?php if ($pageError !== ''): ?>
                    <div class="admin-alert"><?php echo e($pageError); ?></div>
                    <?php endif; ?>
                    <?php if ($flashSuccess !== ''): ?>
                    <div class="admin-alert admin-alert--success"><?php echo e($flashSuccess); ?></div>
                    <?php endif; ?>

                    <div class="admin-meta-row">
                        <span class="admin-badge">Всего пользователей: <?php echo count($users); ?></span>
                    </div>

                    <div class="admin-table-wrap">
                        <table class="admin-users-table">
                            <thead>
                                <tr>
                                    <th>Пользователь</th>
                                    <th>Разрешения</th>
                                    <th>Статус</th>
                                    <th>Действия</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($users as $user): ?>
                                <?php
                                    $userId = isset($user['id']) ? (int) $user['id'] : 0;
                                    $fullName = (string) ($user['full_name'] ?? $user['fio'] ?? '');
                                    $loginValue = (string) ($user['login'] ?? '');
                                    $permissions = parseUserPermissions($user);
                                    $labels = array();
                                    foreach ($permissions as $permission) {
                                        if (isset($permissionLabels[$permission])) {
                                            $labels[] = $permissionLabels[$permission];
                                        }
                                    }
                                    $permissionsText = !empty($labels) ? implode(', ', $labels) : 'Нет прав';
                                    $blocked = isUserBlocked($user);
                                    $editPayload = array(
                                        'id' => $userId,
                                        'fio' => $fullName,
                                        'login' => $loginValue,
                                        'permissions' => getPermissionFlagsFromParsedUser($user, $permissionKeys),
                                        'blocked' => $blocked ? 1 : 0,
                                    );
                                    $editPayloadJson = htmlspecialchars(json_encode($editPayload, JSON_UNESCAPED_UNICODE | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT | JSON_HEX_TAG), ENT_QUOTES, 'UTF-8');
                                ?>
                                <tr>
                                    <td>
                                        <div class="admin-user-name">
                                            <strong><?php echo e($fullName !== '' ? $fullName : 'Без ФИО'); ?></strong>
                                            <span class="admin-user-login"><?php echo e($loginValue); ?></span>
                                        </div>
                                    </td>
                                    <td class="admin-permissions"><?php echo e($permissionsText); ?></td>
                                    <td>
                                        <span class="admin-status <?php echo $blocked ? 'admin-status--blocked' : 'admin-status--active'; ?>">
                                            <?php echo $blocked ? 'Заблокирован' : 'Активен'; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if ($userId > 0): ?>
                                        <div class="admin-user-actions">
                                            <button
                                                type="button"
                                                class="admin-secondary-button js-open-edit-user-modal"
                                                data-modal-target="adminEditUserModal"
                                                data-user="<?php echo $editPayloadJson; ?>"
                                            >Редактировать</button>
                                            <?php if ($loginValue !== $currentSessionLogin): ?>
                                            <form class="admin-inline-form" method="POST" action="deleteuser.php" onsubmit="return confirm('Удалить пользователя?');">
                                                <input type="hidden" name="csrf_token" value="<?php echo e($csrfToken); ?>">
                                                <input type="hidden" name="id" value="<?php echo $userId; ?>">
                                                <button type="submit" class="admin-text-button admin-text-button--danger">Удалить</button>
                                            </form>
                                            <?php else: ?>
                                            <button type="button" class="admin-text-button" disabled>Текущий пользователь</button>
                                            <?php endif; ?>
                                        </div>
                                        <?php else: ?>
                                        <span class="admin-user-login">Нет действий</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </section>
            </div>
        </div>
    </main>

    <div class="admin-modal" id="adminAddUserModal" <?php echo $activeModal === 'add' ? '' : 'hidden'; ?>>
        <div class="admin-modal__backdrop" data-close-modal></div>
        <section class="admin-modal__dialog">
            <div class="admin-modal__header">
                <div>
                    <h2 class="admin-modal__title">Добавить пользователя</h2>
                    <p class="admin-modal__text">Создание новой учетной записи администратора или сотрудника.</p>
                </div>
                <button type="button" class="admin-close-button" aria-label="Закрыть" data-close-modal>&times;</button>
            </div>

            <?php if (!empty($addErrors)): ?>
            <div class="admin-alert">
                <?php foreach ($addErrors as $error): ?>
                <div><?php echo e($error); ?></div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <form method="POST" action="" class="admin-form">
                <input type="hidden" name="csrf_token" value="<?php echo e($csrfToken); ?>">
                <input type="hidden" name="form_action" value="add_user">

                <div class="admin-form-grid">
                    <div class="admin-form-field">
                        <label class="admin-form-label" for="add-user-fio">ФИО</label>
                        <input class="admin-form-input" type="text" id="add-user-fio" name="fio" value="<?php echo e($addFormState['fio']); ?>" required>
                    </div>

                    <div class="admin-form-field">
                        <label class="admin-form-label" for="add-user-login">Логин</label>
                        <input class="admin-form-input" type="text" id="add-user-login" name="login" value="<?php echo e($addFormState['login']); ?>" required>
                    </div>

                    <div class="admin-form-field">
                        <label class="admin-form-label" for="add-user-password">Пароль</label>
                        <input class="admin-form-input" type="password" id="add-user-password" name="password" autocomplete="new-password" required>
                    </div>

                    <div class="admin-form-field">
                        <span class="admin-form-label">Разрешения</span>
                        <div class="admin-checkbox-grid">
                            <?php foreach ($permissionLabels as $permissionKey => $permissionLabel): ?>
                            <div class="admin-checkbox">
                                <input type="checkbox" id="add-user-<?php echo e($permissionKey); ?>" name="<?php echo e($permissionKey); ?>" value="<?php echo e($permissionKey); ?>" <?php echo !empty($addFormState['permissions'][$permissionKey]) ? 'checked' : ''; ?>>
                                <label for="add-user-<?php echo e($permissionKey); ?>"><?php echo e($permissionLabel); ?></label>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="admin-form-field">
                        <div class="admin-checkbox">
                            <input type="checkbox" id="add-user-blocked" name="blockuser" value="blockuser" <?php echo !empty($addFormState['blocked']) ? 'checked' : ''; ?>>
                            <label for="add-user-blocked">Заблокировать пользователя</label>
                        </div>
                    </div>
                </div>

                <div class="admin-form-actions">
                    <button type="button" class="admin-secondary-button" data-close-modal>Отмена</button>
                    <button type="submit" class="admin-primary-button">Создать</button>
                </div>
            </form>
        </section>
    </div>

    <div class="admin-modal" id="adminEditUserModal" <?php echo $activeModal === 'edit' ? '' : 'hidden'; ?>>
        <div class="admin-modal__backdrop" data-close-modal></div>
        <section class="admin-modal__dialog">
            <div class="admin-modal__header">
                <div>
                    <h2 class="admin-modal__title">Редактировать пользователя</h2>
                    <p class="admin-modal__text"><?php echo e($editModalUserLabel !== '' ? $editModalUserLabel : 'Изменение учетной записи пользователя.'); ?></p>
                </div>
                <button type="button" class="admin-close-button" aria-label="Закрыть" data-close-modal>&times;</button>
            </div>

            <?php if (!empty($editErrors)): ?>
            <div class="admin-alert">
                <?php foreach ($editErrors as $error): ?>
                <div><?php echo e($error); ?></div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <form method="POST" action="" class="admin-form" id="adminEditUserForm">
                <input type="hidden" name="csrf_token" value="<?php echo e($csrfToken); ?>">
                <input type="hidden" name="form_action" value="edit_user">
                <input type="hidden" name="id" id="edit-user-id" value="<?php echo (int) $editFormState['id']; ?>">

                <div class="admin-form-grid">
                    <div class="admin-form-field">
                        <label class="admin-form-label" for="edit-user-fio">ФИО</label>
                        <input class="admin-form-input" type="text" id="edit-user-fio" name="fio" value="<?php echo e($editFormState['fio']); ?>" required>
                    </div>

                    <div class="admin-form-field">
                        <label class="admin-form-label" for="edit-user-login">Логин</label>
                        <input class="admin-form-input" type="text" id="edit-user-login" name="login" value="<?php echo e($editFormState['login']); ?>" required>
                    </div>

                    <div class="admin-form-field">
                        <label class="admin-form-label" for="edit-user-password">Новый пароль</label>
                        <input class="admin-form-input" type="password" id="edit-user-password" name="password" autocomplete="new-password" placeholder="Оставьте пустым, чтобы не менять">
                        <p class="admin-form-help">Поле можно оставить пустым, если пароль менять не нужно.</p>
                    </div>

                    <div class="admin-form-field">
                        <span class="admin-form-label">Разрешения</span>
                        <div class="admin-checkbox-grid">
                            <?php foreach ($permissionLabels as $permissionKey => $permissionLabel): ?>
                            <div class="admin-checkbox">
                                <input type="checkbox" id="edit-user-<?php echo e($permissionKey); ?>" name="<?php echo e($permissionKey); ?>" value="<?php echo e($permissionKey); ?>" <?php echo !empty($editFormState['permissions'][$permissionKey]) ? 'checked' : ''; ?>>
                                <label for="edit-user-<?php echo e($permissionKey); ?>"><?php echo e($permissionLabel); ?></label>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="admin-form-field">
                        <div class="admin-checkbox">
                            <input type="checkbox" id="edit-user-blocked" name="blockuser" value="blockuser" <?php echo !empty($editFormState['blocked']) ? 'checked' : ''; ?>>
                            <label for="edit-user-blocked">Заблокировать пользователя</label>
                        </div>
                    </div>
                </div>

                <div class="admin-form-actions">
                    <button type="button" class="admin-secondary-button" data-close-modal>Отмена</button>
                    <button type="submit" class="admin-primary-button">Сохранить</button>
                </div>
            </form>
        </section>
    </div>

    <?php include 'footer.php'; ?>

    <script>
        (function () {
            const body = document.body;
            const modals = Array.from(document.querySelectorAll('.admin-modal'));
            const addModal = document.getElementById('adminAddUserModal');
            const editModal = document.getElementById('adminEditUserModal');
            const editForm = document.getElementById('adminEditUserForm');

            function setBodyState() {
                const hasOpenModal = modals.some((modal) => !modal.hasAttribute('hidden'));
                body.classList.toggle('has-admin-modal', hasOpenModal);
            }

            function closeAllModals() {
                modals.forEach((modal) => {
                    modal.setAttribute('hidden', '');
                });
                setBodyState();
            }

            function openModal(modal) {
                if (!modal) {
                    return;
                }

                closeAllModals();
                modal.removeAttribute('hidden');
                setBodyState();
            }

            function populateEditModal(user) {
                if (!editForm || !user) {
                    return;
                }

                const idField = editForm.querySelector('#edit-user-id');
                const fioField = editForm.querySelector('#edit-user-fio');
                const loginField = editForm.querySelector('#edit-user-login');
                const passwordField = editForm.querySelector('#edit-user-password');
                const blockedField = editForm.querySelector('#edit-user-blocked');
                const modalText = editModal ? editModal.querySelector('.admin-modal__text') : null;

                if (idField) {
                    idField.value = user.id || '';
                }
                if (fioField) {
                    fioField.value = user.fio || '';
                }
                if (loginField) {
                    loginField.value = user.login || '';
                }
                if (passwordField) {
                    passwordField.value = '';
                }
                if (blockedField) {
                    blockedField.checked = Boolean(Number(user.blocked || 0));
                }
                if (modalText) {
                    modalText.textContent = user.fio || user.login || 'Изменение учетной записи пользователя.';
                }

                const permissions = user.permissions || {};
                ['admin', 'news', 'docs', 'people', 'messenger', 'tokens'].forEach((permissionKey) => {
                    const checkbox = editForm.querySelector('#edit-user-' + permissionKey);
                    if (checkbox) {
                        checkbox.checked = Boolean(Number(permissions[permissionKey] || 0));
                    }
                });
            }

            document.querySelectorAll('.js-open-user-modal').forEach((button) => {
                button.addEventListener('click', () => {
                    const modalId = button.getAttribute('data-modal-target');
                    openModal(document.getElementById(modalId));
                });
            });

            document.querySelectorAll('.js-open-edit-user-modal').forEach((button) => {
                button.addEventListener('click', () => {
                    const modalId = button.getAttribute('data-modal-target');
                    const payload = button.getAttribute('data-user');
                    if (payload) {
                        try {
                            populateEditModal(JSON.parse(payload));
                        } catch (error) {
                            console.error(error);
                        }
                    }
                    openModal(document.getElementById(modalId));
                });
            });

            document.querySelectorAll('[data-close-modal]').forEach((element) => {
                element.addEventListener('click', closeAllModals);
            });

            document.addEventListener('keydown', (event) => {
                if (event.key === 'Escape') {
                    closeAllModals();
                }
            });

            setBodyState();
        })();
    </script>
</body>

</html>
