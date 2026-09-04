<?php
require_once __DIR__ . '/lib/security.php';
require_once __DIR__ . '/lib/auth.php';
require_once __DIR__ . '/lib/public_page_visibility.php';
include __DIR__ . '/db_connection.php';

$administrativeProceduresPageTitle = 'Административные процедуры';
$administrativeProceduresTablePath = __DIR__ . '/lib/administrative-procedures-2026-table.php';
$seoTitleMeta = 'Административные процедуры - ТЦСОН Железнодорожного района г. Гомеля';
$seoDescriptionMeta = 'Перечень административных процедур, осуществляемых ТЦСОН Железнодорожного района г. Гомеля в 2026 году.';

$administrativeProceduresVisibility = bootstrapPublicPageVisibility(
    $conn,
    '/procedures.php',
    $administrativeProceduresPageTitle
);
?>
<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="format-detection" content="telephone=no">
    <link rel="icon" href="/img/favicon.png" type="image/png" sizes="120x120">
    <link rel="stylesheet" href="/css/cssbootstrap.min.css?v=<?= filemtime($_SERVER['DOCUMENT_ROOT'] . '/css/cssbootstrap.min.css') ?>">
    <link rel="stylesheet" href="/css/smartphoto.min.css?v=<?= filemtime($_SERVER['DOCUMENT_ROOT'] . '/css/smartphoto.min.css') ?>">
    <link rel="stylesheet" href="/css/style.css?v=<?= filemtime($_SERVER['DOCUMENT_ROOT'] . '/css/style.css') ?>">
    <script src="https://lidrekon.ru/slep/js/jquery.js"></script>
    <script src="https://lidrekon.ru/slep/js/uhpv-full.min.js"></script>
    <link rel="stylesheet" href="/css/normalize.css?v=<?= filemtime($_SERVER['DOCUMENT_ROOT'] . '/css/normalize.css') ?>">
    <link rel="stylesheet" href="/css/media.css?v=<?= filemtime($_SERVER['DOCUMENT_ROOT'] . '/css/media.css') ?>">
    <link rel="stylesheet" href="/css/media_mobile.css?v=<?= filemtime($_SERVER['DOCUMENT_ROOT'] . '/css/media_mobile.css') ?>">
    <title><?php echo e($administrativeProceduresPageTitle); ?> - ТЦСОН Железнодорожного района г. Гомеля</title>
    <?php
    $seoScheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $seoHost = $_SERVER['HTTP_HOST'] ?? 'tcsonrw-gomel.by';
    $seoRequestUri = $_SERVER['REQUEST_URI'] ?? '/procedures.php';
    $seoPath = strtok($seoRequestUri, '?');
    $seoCanonical = $seoScheme . '://' . $seoHost . $seoPath;
    $seoOgImageUrl = $seoScheme . '://' . $seoHost . '/img/logo-old-mini.webp';
    ?>
    <meta name="description" content="<?php echo e($seoDescriptionMeta); ?>">
    <meta
        name="robots"
        content="<?php echo !empty($administrativeProceduresVisibility['is_published']) ? 'index,follow' : 'noindex,nofollow'; ?>"
    >
    <link rel="canonical" href="<?php echo e($seoCanonical); ?>">
    <meta property="og:locale" content="ru_RU">
    <meta property="og:type" content="website">
    <meta property="og:title" content="<?php echo e($seoTitleMeta); ?>">
    <meta property="og:description" content="<?php echo e($seoDescriptionMeta); ?>">
    <meta property="og:url" content="<?php echo e($seoCanonical); ?>">
    <meta property="og:image" content="<?php echo e($seoOgImageUrl); ?>">
    <style>
        .administrative-procedures-page {
            scroll-padding-top: calc(var(--administrative-procedures-header-offset, var(--header-height)) + 54px);
        }

        .administrative-procedures-main {
            position: relative;
            isolation: isolate;
            padding-top: calc(var(--administrative-procedures-header-offset, var(--header-height)) + 54px);
            padding-bottom: 74px;
        }

        .administrative-procedures-main::before,
        .administrative-procedures-main::after {
            content: "";
            position: absolute;
            top: 0;
            bottom: -86px;
            width: clamp(0px, calc((100vw - var(--width-container)) / 2), 118px);
            background: url("/img/loop-vert.png") repeat-y center top / 100% auto;
            pointer-events: none;
            z-index: 0;
        }

        .administrative-procedures-main::before {
            left: 0;
            -webkit-mask-image: linear-gradient(270deg, transparent, rgba(0, 0, 0, 0.76));
            mask-image: linear-gradient(270deg, transparent, rgba(0, 0, 0, 0.76));
        }

        .administrative-procedures-main::after {
            right: 0;
            -webkit-mask-image: linear-gradient(90deg, transparent, rgba(0, 0, 0, 0.76));
            mask-image: linear-gradient(90deg, transparent, rgba(0, 0, 0, 0.76));
        }

        .administrative-procedures-layout {
            position: relative;
            z-index: 1;
            display: grid;
            grid-template-columns: 315px minmax(0, 1fr);
            gap: 34px;
            align-items: start;
        }

        .administrative-procedures-content {
            min-width: 0;
        }

        .administrative-procedures-breadcrumbs {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 18px;
            color: #6a766f;
            font-size: 14px;
            line-height: 1.35;
        }

        .administrative-procedures-breadcrumbs__home {
            width: 16px;
            height: 16px;
            flex: 0 0 16px;
            background-color: #20604a;
            -webkit-mask: url("/img/dom.svg") no-repeat center / contain;
            mask: url("/img/dom.svg") no-repeat center / contain;
        }

        .administrative-procedures-breadcrumbs__separator {
            color: #aab0ac;
            font-weight: 700;
        }

        .administrative-procedures-breadcrumbs a {
            color: #20604a;
            text-decoration: none;
        }

        .administrative-procedures-breadcrumbs a:hover,
        .administrative-procedures-breadcrumbs a:focus-visible {
            color: #c62b30;
        }

        .administrative-procedures-head {
            margin-bottom: 24px;
        }

        .administrative-procedures-title {
            position: relative;
            margin: 0 0 14px;
            padding-left: 22px;
            color: #15553d;
            font-size: clamp(30px, 3vw, 40px);
            font-weight: 700;
            line-height: 1.12;
        }

        .administrative-procedures-title::before {
            content: "";
            position: absolute;
            left: 0;
            top: 3px;
            bottom: 5px;
            width: 5px;
            border-radius: 999px;
            background: #d53331;
        }

        .administrative-procedures-intro {
            max-width: 980px;
            margin: 0;
            color: #34443d;
            font-size: 18px;
            line-height: 1.55;
        }

        .administrative-procedures-card {
            overflow: hidden;
            border: 1px solid rgba(32, 96, 74, 0.13);
            border-radius: 22px;
            background: rgba(255, 255, 255, 0.88);
            box-shadow: 0 18px 38px rgba(48, 56, 52, 0.08);
        }

        .administrative-procedures-note {
            display: flex;
            align-items: center;
            gap: 12px;
            margin: 0;
            padding: 17px 22px;
            border-bottom: 1px solid rgba(32, 96, 74, 0.12);
            background: linear-gradient(120deg, rgba(255, 242, 232, 0.92), rgba(255, 250, 246, 0.82));
            color: #34443d;
            font-size: 15px;
            line-height: 1.45;
        }

        .administrative-procedures-note::before {
            content: "↔";
            display: grid;
            width: 34px;
            height: 34px;
            flex: 0 0 34px;
            place-items: center;
            border-radius: 50%;
            background: #d53331;
            color: #fff;
            font-size: 19px;
            font-weight: 700;
        }

        .administrative-procedures-table-wrap {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            scrollbar-color: #d53331 #f0e7e1;
            scrollbar-width: thin;
        }

        .administrative-procedures-table {
            width: 100%;
            min-width: 1760px;
            border: 0;
            border-collapse: separate;
            border-spacing: 0;
            color: #24352e;
            font-size: 15px;
            line-height: 1.48;
        }

        .administrative-procedures-table col:first-child {
            width: 82px;
        }

        .administrative-procedures-table col:nth-child(2) {
            width: 250px;
        }

        .administrative-procedures-table td {
            min-width: 150px;
            padding: 18px 16px;
            border-right: 1px solid rgba(32, 96, 74, 0.16);
            border-bottom: 1px solid rgba(32, 96, 74, 0.16);
            vertical-align: top;
            background: rgba(255, 255, 255, 0.93);
        }

        .administrative-procedures-table tr:first-child td {
            padding: 18px 15px;
            background: #15553d;
            color: #fff;
            font-weight: 700;
            line-height: 1.35;
            vertical-align: middle;
        }

        .administrative-procedures-table tr:nth-child(2) td {
            padding: 9px 14px;
            background: #f7e8de;
            color: #9f292d;
            font-weight: 700;
            text-align: center;
            vertical-align: middle;
        }

        .administrative-procedures-table tr:nth-child(n + 3):nth-child(even) td {
            background: #fbf8f5;
        }

        .administrative-procedures-table tr:nth-child(n + 3) td:first-child {
            color: #c62b30;
            font-weight: 700;
            white-space: nowrap;
        }

        .administrative-procedures-table tr:nth-child(n + 3) td:nth-child(2) {
            color: #15553d;
            font-weight: 650;
        }

        .administrative-procedures-table td:last-child {
            border-right: 0;
        }

        .administrative-procedures-table tr:last-child td {
            border-bottom: 0;
        }

        .administrative-procedures-table p {
            margin: 0 0 10px;
        }

        .administrative-procedures-table p:last-child {
            margin-bottom: 0;
        }

        .administrative-procedures-table a {
            color: #15553d;
            font-weight: 700;
            text-decoration: none;
        }

        .administrative-procedures-table a:hover,
        .administrative-procedures-table a:focus-visible {
            color: #c62b30;
            text-decoration: underline;
        }

        .administrative-procedures-error {
            margin: 0;
            padding: 24px;
            color: #9f292d;
            font-weight: 600;
        }

        @media (max-width: 1100px) {
            .administrative-procedures-layout {
                grid-template-columns: 270px minmax(0, 1fr);
                gap: 24px;
            }
        }

        @media (max-width: 860px) {
            .administrative-procedures-page {
                scroll-padding-top: calc(var(--administrative-procedures-header-offset, var(--header-height)) + 38px);
            }

            .administrative-procedures-main {
                padding-top: calc(var(--administrative-procedures-header-offset, var(--header-height)) + 38px);
                padding-bottom: 54px;
            }

            .administrative-procedures-layout {
                grid-template-columns: 1fr;
            }

            .administrative-procedures-content {
                min-width: 0;
            }
        }

        @media (max-width: 620px) {
            .administrative-procedures-main {
                padding-top: calc(var(--administrative-procedures-header-offset, var(--header-height)) + 28px);
            }

            .administrative-procedures-title {
                padding-left: 18px;
                font-size: 30px;
            }

            .administrative-procedures-intro {
                font-size: 16px;
            }

            .administrative-procedures-card {
                border-radius: 18px;
            }

            .administrative-procedures-note {
                align-items: flex-start;
                padding: 15px 16px;
                font-size: 14px;
            }

            .administrative-procedures-table {
                min-width: 1540px;
                font-size: 14px;
            }

            .administrative-procedures-table td {
                padding: 15px 13px;
            }
        }

        @media print {
            .administrative-procedures-main {
                padding-top: 0;
            }

            .administrative-procedures-layout {
                display: block;
            }

            .section-side-menu,
            .administrative-procedures-breadcrumbs,
            .administrative-procedures-note {
                display: none;
            }

            .administrative-procedures-card {
                border: 0;
                box-shadow: none;
            }

            .administrative-procedures-table-wrap {
                overflow: visible;
            }

            .administrative-procedures-table {
                min-width: 0;
                font-size: 8px;
            }

            .administrative-procedures-table td {
                padding: 5px;
            }
        }
    </style>
</head>

<body class="administrative-procedures-page">
<?php include __DIR__ . '/header.php'; ?>
<main class="main administrative-procedures-main">
    <div class="administrative-procedures-layout container">
        <?php
        $aboutMenuActive = 'administrative-procedures';
        include __DIR__ . '/about-side-menu.php';
        ?>

        <div class="administrative-procedures-content">
            <nav class="administrative-procedures-breadcrumbs" aria-label="Хлебные крошки">
                <span class="administrative-procedures-breadcrumbs__home" aria-hidden="true"></span>
                <a href="/">Главная</a>
                <span class="administrative-procedures-breadcrumbs__separator" aria-hidden="true">›</span>
                <a href="/about.php">О центре</a>
                <span class="administrative-procedures-breadcrumbs__separator" aria-hidden="true">›</span>
                <span><?php echo e($administrativeProceduresPageTitle); ?></span>
            </nav>

            <header class="administrative-procedures-head">
                <h1 class="administrative-procedures-title"><?php echo e($administrativeProceduresPageTitle); ?></h1>
                <p class="administrative-procedures-intro">
                    Перечень административных процедур, осуществляемых учреждением
                    «Территориальный центр социального обслуживания населения
                    Железнодорожного района г. Гомеля» в 2026 году.
                </p>
            </header>

            <section class="administrative-procedures-card" aria-label="Административные процедуры учреждения">
                <p class="administrative-procedures-note">
                    Таблица содержит полный перечень документов, сроки, размеры платы и ответственных
                    исполнителей. Для просмотра всех колонок прокрутите таблицу по горизонтали.
                </p>
                <div class="administrative-procedures-table-wrap" tabindex="0" aria-label="Прокручиваемая таблица административных процедур">
                    <?php if (is_file($administrativeProceduresTablePath)): ?>
                        <?php
                        define('TCSON_ADMINISTRATIVE_PROCEDURES_PAGE', true);
                        include $administrativeProceduresTablePath;
                        ?>
                    <?php else: ?>
                        <p class="administrative-procedures-error">Данные административных процедур временно недоступны.</p>
                    <?php endif; ?>
                </div>
            </section>
        </div>
    </div>
</main>
<?php include __DIR__ . '/footer.php'; ?>
<script>
    (function () {
        var root = document.documentElement;
        var header = document.querySelector('.header-down');

        if (!root || !header) {
            return;
        }

        function syncAdministrativeProceduresOffset() {
            root.style.setProperty(
                '--administrative-procedures-header-offset',
                header.offsetHeight + 'px'
            );
        }

        syncAdministrativeProceduresOffset();
        window.addEventListener('load', syncAdministrativeProceduresOffset);
        window.addEventListener('resize', syncAdministrativeProceduresOffset);
    })();
</script>
</body>

</html>
