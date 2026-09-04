<?php
require_once __DIR__ . '/lib/security.php';
require_once __DIR__ . '/lib/auth.php';
require_once __DIR__ . '/lib/public_page_visibility.php';
include __DIR__ . '/db_connection.php';

$personalReceptionPageTitle = 'График личного приема';
$personalReceptionSiteName = 'ТЦСОН Железнодорожного района г. Гомеля';
$personalReceptionLead = 'График личного приема граждан, их представителей и представителей юридических лиц.';
$personalReceptionSchedule = array(
    array(
        'period' => 'Каждая среда месяца',
        'person' => 'Забавчик Наталья Александровна',
        'position' => 'директор',
        'time' => '08:00 - 13:00',
    ),
    array(
        'period' => 'Каждый вторник месяца',
        'person' => 'Снежкова Екатерина Петровна',
        'position' => 'заместитель директора',
        'time' => '08:00 - 13:00',
    ),
);

$seoTitleMeta = $personalReceptionPageTitle . ' - ' . $personalReceptionSiteName;
$seoDescriptionMeta = 'График личного приема граждан, их представителей и представителей юридических лиц ТЦСОН Железнодорожного района г. Гомеля.';

bootstrapPublicPageVisibility($conn, '/personal-reception.php', $personalReceptionPageTitle);
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
    <title><?php echo e($personalReceptionPageTitle); ?> - <?php echo e($personalReceptionSiteName); ?></title>
    <?php
    $seoScheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $seoHost = $_SERVER['HTTP_HOST'] ?? 'tcsonrw-gomel.by';
    $seoRequestUri = $_SERVER['REQUEST_URI'] ?? '/personal-reception.php';
    $seoPath = strtok($seoRequestUri, '?');
    $seoCanonical = $seoScheme . '://' . $seoHost . $seoPath;
    $seoOgImage = '/img/logo-old-mini.webp';
    $seoRobotsMeta = 'index,follow';
    $seoOgImageUrl = $seoScheme . '://' . $seoHost . $seoOgImage;
    ?>
    <meta name="description" content="<?php echo e($seoDescriptionMeta); ?>">
    <meta name="robots" content="<?php echo e($seoRobotsMeta); ?>">
    <link rel="canonical" href="<?php echo e($seoCanonical); ?>">
    <meta property="og:locale" content="ru_RU">
    <meta property="og:type" content="website">
    <meta property="og:title" content="<?php echo e($seoTitleMeta); ?>">
    <meta property="og:description" content="<?php echo e($seoDescriptionMeta); ?>">
    <meta property="og:url" content="<?php echo e($seoCanonical); ?>">
    <meta property="og:image" content="<?php echo e($seoOgImageUrl); ?>">
    <style>
        .personal-reception-page {
            scroll-padding-top: calc(var(--personal-reception-page-header-offset, var(--header-height)) + 54px);
        }

        .personal-reception-page-main {
            position: relative;
            isolation: isolate;
            padding-top: calc(var(--personal-reception-page-header-offset, var(--header-height)) + 54px);
            padding-bottom: 74px;
        }

        .personal-reception-page-main::before,
        .personal-reception-page-main::after {
            content: "";
            position: absolute;
            top: 0;
            bottom: -86px;
            width: clamp(0px, calc((100vw - var(--width-container)) / 2), 118px);
            background: url("/img/loop-vert.png") repeat-y center top / 100% auto;
            pointer-events: none;
            z-index: 0;
        }

        .personal-reception-page-main::before {
            left: 0;
            -webkit-mask-image: linear-gradient(270deg, transparent 0%, rgba(0, 0, 0, 0.3) 42%, rgba(0, 0, 0, 0.76) 100%);
            mask-image: linear-gradient(270deg, transparent 0%, rgba(0, 0, 0, 0.3) 42%, rgba(0, 0, 0, 0.76) 100%);
        }

        .personal-reception-page-main::after {
            right: 0;
            -webkit-mask-image: linear-gradient(90deg, transparent 0%, rgba(0, 0, 0, 0.3) 42%, rgba(0, 0, 0, 0.76) 100%);
            mask-image: linear-gradient(90deg, transparent 0%, rgba(0, 0, 0, 0.3) 42%, rgba(0, 0, 0, 0.76) 100%);
        }

        .personal-reception-layout {
            position: relative;
            z-index: 1;
            display: grid;
            grid-template-columns: 315px minmax(0, 1fr);
            gap: 36px;
            align-items: start;
        }

        .personal-reception-content {
            min-width: 0;
        }

        .personal-reception-breadcrumbs {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 18px;
            color: #6a766f;
            font-size: 14px;
            line-height: 1.35;
        }

        .personal-reception-breadcrumbs__home {
            width: 16px;
            height: 16px;
            flex: 0 0 16px;
            background-color: #20604a;
            -webkit-mask: url("/img/dom.svg") no-repeat center / contain;
            mask: url("/img/dom.svg") no-repeat center / contain;
        }

        .personal-reception-breadcrumbs__separator {
            color: #aab0ac;
            font-weight: 700;
        }

        .personal-reception-breadcrumbs a {
            color: #20604a;
            text-decoration: none;
        }

        .personal-reception-breadcrumbs a:hover,
        .personal-reception-breadcrumbs a:focus-visible {
            color: #c62b30;
        }

        .personal-reception-title {
            position: relative;
            margin: 0 0 14px;
            padding-left: 20px;
            color: #15553d;
            font-size: clamp(30px, 3vw, 40px);
            font-weight: 700;
            line-height: 1.12;
        }

        .personal-reception-title::before {
            content: "";
            position: absolute;
            left: 0;
            top: 3px;
            bottom: 5px;
            width: 5px;
            border-radius: 999px;
            background: #d53331;
        }

        .personal-reception-lead {
            max-width: 860px;
            margin: 0 0 22px;
            color: #2f3a35;
            font-size: 18px;
            line-height: 1.55;
        }

        .personal-reception-period-card {
            display: flex;
            align-items: center;
            gap: 20px;
            margin-bottom: 24px;
            padding: 24px;
            border: 1px solid rgba(32, 96, 74, 0.12);
            border-radius: 18px;
            background:
                linear-gradient(135deg, rgba(255, 255, 255, 0.96), rgba(246, 252, 248, 0.94)),
                radial-gradient(circle at 0 0, rgba(213, 51, 49, 0.08), transparent 45%);
            box-shadow: 0 16px 34px rgba(48, 56, 52, 0.08);
        }

        .personal-reception-period-card__icon {
            width: 52px;
            height: 52px;
            flex: 0 0 52px;
            background-color: #15553d;
            -webkit-mask: url("/img/time.svg") no-repeat center / contain;
            mask: url("/img/time.svg") no-repeat center / contain;
        }

        .personal-reception-period-card__title {
            margin: 0 0 5px;
            color: #15553d;
            font-size: 22px;
            font-weight: 800;
            line-height: 1.2;
        }

        .personal-reception-period-card__text {
            margin: 0;
            color: #3f4d47;
            font-size: 16px;
            line-height: 1.45;
        }

        .personal-reception-section {
            margin-top: 30px;
        }

        .personal-reception-section__title {
            margin: 0 0 16px;
            color: #196847;
            font-size: 26px;
            font-weight: 700;
            line-height: 1.16;
        }

        .personal-reception-schedule {
            display: grid;
            gap: 14px;
        }

        .personal-reception-card {
            display: grid;
            grid-template-columns: minmax(170px, 0.55fr) minmax(0, 1fr) minmax(150px, 0.4fr);
            gap: 18px;
            align-items: center;
            padding: 22px;
            border: 1px solid rgba(32, 96, 74, 0.1);
            border-radius: 16px;
            background: rgba(255, 255, 255, 0.94);
            box-shadow: 0 14px 30px rgba(48, 56, 52, 0.08);
        }

        .personal-reception-card__period {
            color: #c62b30;
            font-size: 17px;
            font-weight: 800;
            line-height: 1.35;
        }

        .personal-reception-card__person {
            margin: 0 0 4px;
            color: #15553d;
            font-size: 18px;
            font-weight: 800;
            line-height: 1.28;
        }

        .personal-reception-card__position {
            color: #4a5a53;
            font-size: 15px;
            font-weight: 600;
            line-height: 1.4;
        }

        .personal-reception-card__time {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 46px;
            padding: 10px 14px;
            border: 1px solid rgba(32, 96, 74, 0.12);
            border-radius: 12px;
            background: #eef7f1;
            color: #26322e;
            font-size: 16px;
            font-weight: 800;
            line-height: 1.25;
            white-space: nowrap;
        }

        @media (max-width: 1100px) {
            .personal-reception-layout {
                grid-template-columns: 260px minmax(0, 1fr);
                gap: 24px;
            }

            .personal-reception-card {
                grid-template-columns: 1fr;
                align-items: start;
            }
        }

        @media (max-width: 860px) {
            .personal-reception-page {
                scroll-padding-top: calc(var(--personal-reception-page-header-offset, var(--header-height)) + 38px);
            }

            .personal-reception-page-main {
                padding-top: calc(var(--personal-reception-page-header-offset, var(--header-height)) + 38px);
                padding-bottom: 58px;
            }

            .personal-reception-page-main::before,
            .personal-reception-page-main::after {
                display: none;
            }

            .personal-reception-layout {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 620px) {
            .personal-reception-title {
                font-size: 30px;
            }

            .personal-reception-lead {
                font-size: 16px;
            }

            .personal-reception-period-card,
            .personal-reception-card {
                padding: 20px 16px;
            }

            .personal-reception-period-card {
                display: grid;
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body class="personal-reception-page">
<?php include __DIR__ . '/header.php'; ?>
<main class="main personal-reception-page-main">
    <div class="personal-reception-layout container">
        <?php
        $contactsMenuActive = 'personal-reception';
        include __DIR__ . '/contacts-side-menu.php';
        ?>

        <div class="personal-reception-content">
            <nav class="personal-reception-breadcrumbs" aria-label="Хлебные крошки">
                <span class="personal-reception-breadcrumbs__home" aria-hidden="true"></span>
                <a href="/">Главная</a>
                <span class="personal-reception-breadcrumbs__separator" aria-hidden="true">›</span>
                <a href="/contacts.php">Контакты</a>
                <span class="personal-reception-breadcrumbs__separator" aria-hidden="true">›</span>
                <span><?php echo e($personalReceptionPageTitle); ?></span>
            </nav>

            <section aria-labelledby="personal-reception-page-title">
                <h1 class="personal-reception-title" id="personal-reception-page-title"><?php echo e($personalReceptionPageTitle); ?></h1>
                <p class="personal-reception-lead"><?php echo e($personalReceptionLead); ?></p>

                <article class="personal-reception-period-card">
                    <span class="personal-reception-period-card__icon" aria-hidden="true"></span>
                    <div>
                        <h2 class="personal-reception-period-card__title">График приема</h2>
                        <p class="personal-reception-period-card__text">Личный прием проводится руководством учреждения по утвержденному ежемесячному графику.</p>
                    </div>
                </article>
            </section>

            <section class="personal-reception-section" aria-labelledby="personal-reception-schedule-title">
                <h2 class="personal-reception-section__title" id="personal-reception-schedule-title">Расписание</h2>
                <div class="personal-reception-schedule">
                    <?php foreach ($personalReceptionSchedule as $personalReceptionRow): ?>
                        <article class="personal-reception-card">
                            <div class="personal-reception-card__period"><?php echo e($personalReceptionRow['period']); ?></div>
                            <div>
                                <h3 class="personal-reception-card__person"><?php echo e($personalReceptionRow['person']); ?></h3>
                                <div class="personal-reception-card__position"><?php echo e($personalReceptionRow['position']); ?></div>
                            </div>
                            <div class="personal-reception-card__time"><?php echo e($personalReceptionRow['time']); ?></div>
                        </article>
                    <?php endforeach; ?>
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

        function syncPersonalReceptionPageOffset() {
            root.style.setProperty('--personal-reception-page-header-offset', header.offsetHeight + 'px');
        }

        syncPersonalReceptionPageOffset();
        window.addEventListener('load', syncPersonalReceptionPageOffset);
        window.addEventListener('resize', syncPersonalReceptionPageOffset);
    })();
</script>
</body>

</html>
