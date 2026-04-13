<?php
session_start();

require_once __DIR__ . '/lib/security.php';
require_once __DIR__ . '/lib/auth.php';
include __DIR__ . '/db_connection.php';

$loggedIn = isLoggedIn();
$login = $loggedIn ? $_SESSION['login'] : '';
$welcomeName = $login;
$error = '';

if ($loggedIn) {
    $currentUser = getCurrentUserByLogin($conn);
    if (is_array($currentUser)) {
        $welcomeName = (string) ($currentUser['full_name'] ?? $currentUser['fio'] ?? $login);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    requireCsrfToken();

    $loginInput = mb_strtolower(trim((string) ($_POST['login'] ?? '')), 'UTF-8');
    $passwordInput = (string) ($_POST['password'] ?? '');

    if ($loginInput === '' || $passwordInput === '') {
        $error = 'Введите логин и пароль';
    } else {
        $stmt = $conn->prepare('SELECT * FROM users WHERE login = ? LIMIT 1');
        if (!$stmt) {
            error_log('Login prepare error: ' . $conn->error);
            $error = 'Ошибка сервера. Повторите позже.';
        } else {
            $stmt->bind_param('s', $loginInput);
            $stmt->execute();
            $result = $stmt->get_result();
            $user = $result ? $result->fetch_assoc() : null;
            $stmt->close();

            if ($user && isset($user['password']) && password_verify($passwordInput, $user['password'])) {
                if (isUserBlocked($user)) {
                    $error = 'Учетная запись заблокирована';
                } else {
                    loginUserWithPermissions($loginInput, $user);
                    redirectTo('/auth.php');
                }
            } else {
                $error = 'Неверное имя пользователя или пароль';
            }
        }
    }

    $loggedIn = isLoggedIn();
    $login = $loggedIn ? $_SESSION['login'] : '';
    $welcomeName = $login;
    if ($loggedIn) {
        $currentUser = getCurrentUserByLogin($conn);
        if (is_array($currentUser)) {
            $welcomeName = (string) ($currentUser['full_name'] ?? $currentUser['fio'] ?? $login);
        }
    }
}

$csrfToken = getCsrfToken();
$seoTitleMeta = 'Авторизация — ТЦСОН Железнодорожного района г. Гомеля';
$seoDescriptionMeta = 'Вход в личный кабинет ТЦСОН Железнодорожного района г. Гомеля.';
$seoRobotsMeta = 'noindex,nofollow';
?>
<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="img/favicon.png" type="image/x-icon">
    <link rel="stylesheet" href="/css/normalize.css?v=<?= filemtime($_SERVER['DOCUMENT_ROOT'] . '/css/normalize.css') ?>">
    <link rel="stylesheet" href="/css/cssbootstrap.min.css?v=<?= filemtime($_SERVER['DOCUMENT_ROOT'] . '/css/cssbootstrap.min.css') ?>">
    <link rel="stylesheet" type="text/css" href="/css/slick.css?v=<?= filemtime($_SERVER['DOCUMENT_ROOT'] . '/css/slick.css') ?>" />
    <link rel="stylesheet" href="/css/smartphoto.min.css?v=<?= filemtime($_SERVER['DOCUMENT_ROOT'] . '/css/smartphoto.min.css') ?>">
    <link rel="stylesheet" href="/css/style.css?v=<?= filemtime($_SERVER['DOCUMENT_ROOT'] . '/css/style.css') ?>">
    <script src="https://lidrekon.ru/slep/js/jquery.js"></script>
    <script src="https://lidrekon.ru/slep/js/uhpv-full.min.js"></script>
    <link rel="stylesheet" href="/css/media.css?v=<?= filemtime($_SERVER['DOCUMENT_ROOT'] . '/css/media.css') ?>">
    <link rel="stylesheet" href="/css/media_mobile.css?v=<?= filemtime($_SERVER['DOCUMENT_ROOT'] . '/css/media_mobile.css') ?>">
    <script src="/script/jquery-3.6.0.min.js"></script>
    <script src="/script/slick.min.js"></script>
    <title>Авторизация — ТЦСОН</title>
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

<body class="auth-page">
    <script type="text/javascript">
        (function(m,e,t,r,i,k,a){
            m[i]=m[i]||function(){(m[i].a=m[i].a||[]).push(arguments)};
            m[i].l=1*new Date();
            for (var j = 0; j < document.scripts.length; j++) {if (document.scripts[j].src === r) { return; }}
            k=e.createElement(t),a=e.getElementsByTagName(t)[0],k.async=1,k.src=r,a.parentNode.insertBefore(k,a)
        })(window, document,'script','https://mc.yandex.ru/metrika/tag.js?id=106899778', 'ym');

        ym(106899778, 'init', {ssr:true, webvisor:true, clickmap:true, ecommerce:"dataLayer", referrer: document.referrer, url: location.href, accurateTrackBounce:true, trackLinks:true});
    </script>
    <noscript><div><img src="https://mc.yandex.ru/watch/106899778" style="position:absolute; left:-9999px;" alt="" /></div></noscript>
    <div class="button_btneay">
        <button id="specialButton" class="header-top-buttons__impaired-button" aria-label="Версия для слабовидящих">
            <span class="header-top-buttons__impaired-icon" aria-hidden="true"></span>
            <span class="header-top-buttons__impaired-text">
                <span>Версия для</span>
                <span>слабовидящих</span>
            </span>
        </button>
    </div>
    <main class="auth-page__shell">
        <section class="auth-card">
            <div class="auth-card__top">
                <a href="/" class="admin-primary-button" aria-label="На главную" title="На главную"><img class="admin-primary-button__icon" src="/img/dom.svg" alt=""></a>
                <?php if ($loggedIn): ?>
                <a href="logout.php" class="admin-primary-button" aria-label="Выйти" title="Выйти"><img class="admin-primary-button__icon" src="/img/vihod.svg" alt=""></a>
                <?php endif; ?>
            </div>

            <?php if ($loggedIn): ?>
            <div class="auth-card__intro">
                <p class="auth-card__eyebrow">Личный кабинет</p>
                <h1 class="auth-card__title">Добро пожаловать, <?php echo e($welcomeName); ?></h1>
                <p class="auth-card__text">Выберите раздел, с которым хотите работать.</p>
            </div>
            <div class="auth-link-row">
                <?php if (hasPermission('news')): ?>
                <a href="addnews.php" class="auth-link-chip">Добавить событие</a>
                <?php endif; ?>
                <?php if (hasPermission('admin')): ?>
                <a href="listuser.php" class="auth-link-chip">Пользователи</a>
                <?php endif; ?>
                <?php if (hasPermission('messenger')): ?>
                <a href="messenger.php" class="auth-link-chip">Задания</a>
                <?php endif; ?>
            </div>
            <?php else: ?>
            <div class="auth-card__intro">
                <p class="auth-card__eyebrow">Личный кабинет</p>
                <h1 class="auth-card__title">Авторизация</h1>
                <p class="auth-card__text">Войдите в административный раздел сайта, используя логин и пароль пользователя.</p>
            </div>
            <?php if ($error !== ''): ?>
            <div class="auth-alert"><?php echo e($error); ?></div>
            <?php endif; ?>
            <form method="POST" action="" class="auth-form">
                <input type="hidden" name="csrf_token" value="<?php echo e($csrfToken); ?>">
                <div class="auth-field">
                    <label class="auth-label" for="login">Имя пользователя</label>
                    <input class="auth-input" type="text" id="login" name="login" autocomplete="username" required>
                </div>
                <div class="auth-field">
                    <label class="auth-label" for="password">Пароль</label>
                    <input class="auth-input" type="password" id="password" name="password" autocomplete="current-password" required>
                </div>
                <button class="auth-submit" type="submit">Войти</button>
            </form>
            <?php endif; ?>
        </section>
    </main>
</body>

</html>
