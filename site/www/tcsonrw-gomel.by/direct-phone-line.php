<?php
require_once __DIR__ . '/lib/security.php';

$directLinePageTitle = 'Прямая телефонная линия';
$directLineSiteName = 'ТЦСОН Железнодорожного района г. Гомеля';
$directLinePeriod = 'во 2 квартале 2026 года';
$directLineSchedule = array(
    array(
        'position' => 'Директор',
        'phone' => '8 (0232) 27-72-53',
        'phone_href' => '+375232277253',
        'slots' => array(
            array('date' => '29.04.2026', 'time' => '12:00 - 13:00'),
            array('date' => '10.06.2026', 'time' => '12:00 - 13:00'),
        ),
    ),
    array(
        'position' => 'Заместитель директора',
        'phone' => '8 (0232) 29-45-68',
        'phone_href' => '+375232294568',
        'slots' => array(
            array('date' => '14.04.2026', 'time' => '09:00 - 10:00'),
            array('date' => '19.05.2026', 'time' => '09:00 - 10:00'),
        ),
    ),
);
$directLineHotlinePhone = '8 (0232) 34-99-56';
$directLineHotlineHref = '+375232349956';

$seoTitleMeta = $directLinePageTitle . ' - ' . $directLineSiteName;
$seoDescriptionMeta = 'График работы прямой телефонной линии и горячей линии ТЦСОН Железнодорожного района г. Гомеля.';
?>
<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="format-detection" content="telephone=no">
    <link rel="icon" href="/img/favicon.png" type="image/x-icon">
    <link rel="stylesheet" href="/css/cssbootstrap.min.css?v=<?= filemtime($_SERVER['DOCUMENT_ROOT'] . '/css/cssbootstrap.min.css') ?>">
    <link rel="stylesheet" href="/css/smartphoto.min.css?v=<?= filemtime($_SERVER['DOCUMENT_ROOT'] . '/css/smartphoto.min.css') ?>">
    <link rel="stylesheet" href="/css/style.css?v=<?= filemtime($_SERVER['DOCUMENT_ROOT'] . '/css/style.css') ?>">
    <script src="https://lidrekon.ru/slep/js/jquery.js"></script>
    <script src="https://lidrekon.ru/slep/js/uhpv-full.min.js"></script>
    <link rel="stylesheet" href="/css/normalize.css?v=<?= filemtime($_SERVER['DOCUMENT_ROOT'] . '/css/normalize.css') ?>">
    <link rel="stylesheet" href="/css/media.css?v=<?= filemtime($_SERVER['DOCUMENT_ROOT'] . '/css/media.css') ?>">
    <link rel="stylesheet" href="/css/media_mobile.css?v=<?= filemtime($_SERVER['DOCUMENT_ROOT'] . '/css/media_mobile.css') ?>">
    <title><?php echo e($directLinePageTitle); ?> - <?php echo e($directLineSiteName); ?></title>
    <?php
    $seoScheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $seoHost = $_SERVER['HTTP_HOST'] ?? 'tcsonrw-gomel.by';
    $seoRequestUri = $_SERVER['REQUEST_URI'] ?? '/direct-phone-line.php';
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
        .direct-line-page {
            scroll-padding-top: calc(var(--direct-line-page-header-offset, var(--header-height)) + 54px);
        }

        .direct-line-page-main {
            position: relative;
            isolation: isolate;
            padding-top: calc(var(--direct-line-page-header-offset, var(--header-height)) + 54px);
            padding-bottom: 74px;
        }

        .direct-line-page-main::before,
        .direct-line-page-main::after {
            content: "";
            position: absolute;
            top: 0;
            bottom: -86px;
            width: clamp(0px, calc((100vw - var(--width-container)) / 2), 118px);
            background: url("/img/loop-vert.png") repeat-y center top / 100% auto;
            pointer-events: none;
            z-index: 0;
        }

        .direct-line-page-main::before {
            left: 0;
            -webkit-mask-image: linear-gradient(270deg, transparent 0%, rgba(0, 0, 0, 0.3) 42%, rgba(0, 0, 0, 0.76) 100%);
            mask-image: linear-gradient(270deg, transparent 0%, rgba(0, 0, 0, 0.3) 42%, rgba(0, 0, 0, 0.76) 100%);
        }

        .direct-line-page-main::after {
            right: 0;
            -webkit-mask-image: linear-gradient(90deg, transparent 0%, rgba(0, 0, 0, 0.3) 42%, rgba(0, 0, 0, 0.76) 100%);
            mask-image: linear-gradient(90deg, transparent 0%, rgba(0, 0, 0, 0.3) 42%, rgba(0, 0, 0, 0.76) 100%);
        }

        .direct-line-layout {
            position: relative;
            z-index: 1;
            display: grid;
            grid-template-columns: 315px minmax(0, 1fr);
            gap: 36px;
            align-items: start;
        }

        .direct-line-content {
            min-width: 0;
        }

        .direct-line-breadcrumbs {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 18px;
            color: #6a766f;
            font-size: 14px;
            line-height: 1.35;
        }

        .direct-line-breadcrumbs__home {
            width: 16px;
            height: 16px;
            flex: 0 0 16px;
            background-color: #20604a;
            -webkit-mask: url("/img/dom.svg") no-repeat center / contain;
            mask: url("/img/dom.svg") no-repeat center / contain;
        }

        .direct-line-breadcrumbs__separator {
            color: #aab0ac;
            font-weight: 700;
        }

        .direct-line-breadcrumbs a {
            color: #20604a;
            text-decoration: none;
        }

        .direct-line-breadcrumbs a:hover,
        .direct-line-breadcrumbs a:focus-visible {
            color: #c62b30;
        }

        .direct-line-title {
            position: relative;
            margin: 0 0 14px;
            padding-left: 20px;
            color: #15553d;
            font-size: clamp(30px, 3vw, 40px);
            font-weight: 700;
            line-height: 1.12;
        }

        .direct-line-title::before {
            content: "";
            position: absolute;
            left: 0;
            top: 3px;
            bottom: 5px;
            width: 5px;
            border-radius: 999px;
            background: #d53331;
        }

        .direct-line-lead {
            max-width: 850px;
            margin: 0 0 22px;
            color: #2f3a35;
            font-size: 18px;
            line-height: 1.55;
        }

        .direct-line-period-card {
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

        .direct-line-period-card__icon {
            width: 52px;
            height: 52px;
            flex: 0 0 52px;
            background-color: #15553d;
            -webkit-mask: url("/img/zvonok.svg") no-repeat center / contain;
            mask: url("/img/zvonok.svg") no-repeat center / contain;
        }

        .direct-line-period-card__title {
            margin: 0 0 5px;
            color: #15553d;
            font-size: 22px;
            font-weight: 800;
            line-height: 1.2;
        }

        .direct-line-period-card__text {
            margin: 0;
            color: #3f4d47;
            font-size: 16px;
            line-height: 1.45;
        }

        .direct-line-section {
            margin-top: 30px;
        }

        .direct-line-section__title {
            margin: 0 0 16px;
            color: #196847;
            font-size: 26px;
            font-weight: 700;
            line-height: 1.16;
        }

        .direct-line-schedule {
            display: grid;
            gap: 14px;
        }

        .direct-line-schedule-card {
            display: grid;
            grid-template-columns: minmax(190px, 0.7fr) minmax(0, 1fr) minmax(150px, 0.45fr);
            gap: 18px;
            align-items: center;
            padding: 22px;
            border: 1px solid rgba(32, 96, 74, 0.1);
            border-radius: 16px;
            background: rgba(255, 255, 255, 0.94);
            box-shadow: 0 14px 30px rgba(48, 56, 52, 0.08);
        }

        .direct-line-schedule-card__position {
            margin: 0;
            color: #15553d;
            font-size: 18px;
            font-weight: 800;
            line-height: 1.28;
        }

        .direct-line-slots {
            display: grid;
            gap: 8px;
        }

        .direct-line-slot {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            min-height: 44px;
            padding: 10px 14px;
            border: 1px solid rgba(32, 96, 74, 0.12);
            border-radius: 12px;
            background: #eef7f1;
        }

        .direct-line-slot__date {
            color: #26322e;
            font-size: 16px;
            font-weight: 800;
            line-height: 1.25;
        }

        .direct-line-slot__time {
            color: #c62b30;
            font-size: 16px;
            font-weight: 800;
            line-height: 1.25;
            white-space: nowrap;
        }

        .direct-line-phone {
            color: #15553d;
            font-size: 18px;
            font-weight: 800;
            line-height: 1.2;
            text-decoration: none;
            white-space: nowrap;
        }

        .direct-line-phone:hover,
        .direct-line-phone:focus-visible {
            color: #c62b30;
        }

        .direct-line-hotline-card {
            display: grid;
            grid-template-columns: 64px minmax(0, 1fr);
            gap: 20px;
            align-items: center;
            padding: 26px;
            border: 1px solid rgba(213, 51, 49, 0.16);
            border-radius: 18px;
            background:
                linear-gradient(135deg, rgba(255, 255, 255, 0.96), rgba(255, 247, 243, 0.94)),
                radial-gradient(circle at 0 0, rgba(213, 51, 49, 0.1), transparent 48%);
            box-shadow: 0 16px 34px rgba(48, 56, 52, 0.08);
        }

        .direct-line-hotline-card__icon {
            width: 56px;
            height: 56px;
            background-color: #15553d;
            -webkit-mask: url("/img/servis.svg") no-repeat center / contain;
            mask: url("/img/servis.svg") no-repeat center / contain;
        }

        .direct-line-hotline-card__title {
            margin: 0 0 8px;
            color: #15553d;
            font-size: 22px;
            font-weight: 800;
            line-height: 1.2;
        }

        .direct-line-hotline-card__phone {
            display: inline-block;
            margin-bottom: 8px;
            color: #15553d;
            font-size: 24px;
            font-weight: 800;
            line-height: 1.2;
            text-decoration: none;
        }

        .direct-line-hotline-card__phone:hover,
        .direct-line-hotline-card__phone:focus-visible {
            color: #c62b30;
        }

        .direct-line-hotline-card__text {
            margin: 0;
            color: #2f3a35;
            font-size: 16px;
            line-height: 1.5;
        }

        @media (max-width: 1100px) {
            .direct-line-layout {
                grid-template-columns: 260px minmax(0, 1fr);
                gap: 24px;
            }

            .direct-line-schedule-card {
                grid-template-columns: 1fr;
                align-items: start;
            }
        }

        @media (max-width: 860px) {
            .direct-line-page {
                scroll-padding-top: calc(var(--direct-line-page-header-offset, var(--header-height)) + 38px);
            }

            .direct-line-page-main {
                padding-top: calc(var(--direct-line-page-header-offset, var(--header-height)) + 38px);
                padding-bottom: 58px;
            }

            .direct-line-page-main::before,
            .direct-line-page-main::after {
                display: none;
            }

            .direct-line-layout {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 620px) {
            .direct-line-title {
                font-size: 30px;
            }

            .direct-line-lead {
                font-size: 16px;
            }

            .direct-line-period-card,
            .direct-line-hotline-card {
                grid-template-columns: 1fr;
                padding: 20px 16px;
            }

            .direct-line-period-card {
                display: grid;
            }

            .direct-line-schedule-card {
                padding: 18px 16px;
            }

            .direct-line-slot {
                align-items: flex-start;
                flex-direction: column;
            }
        }
    </style>
</head>

<body class="direct-line-page">
<?php include __DIR__ . '/header.php'; ?>
<main class="main direct-line-page-main">
    <div class="direct-line-layout container">
        <?php
        $contactsMenuActive = 'direct-phone-line';
        include __DIR__ . '/contacts-side-menu.php';
        ?>

        <div class="direct-line-content">
            <nav class="direct-line-breadcrumbs" aria-label="Хлебные крошки">
                <span class="direct-line-breadcrumbs__home" aria-hidden="true"></span>
                <a href="/">Главная</a>
                <span class="direct-line-breadcrumbs__separator" aria-hidden="true">›</span>
                <a href="/contacts.php">Контакты</a>
                <span class="direct-line-breadcrumbs__separator" aria-hidden="true">›</span>
                <span><?php echo e($directLinePageTitle); ?></span>
            </nav>

            <section aria-labelledby="direct-line-page-title">
                <h1 class="direct-line-title" id="direct-line-page-title"><?php echo e($directLinePageTitle); ?></h1>
                <p class="direct-line-lead">График работы прямой телефонной линии учреждения <?php echo e($directLinePeriod); ?>.</p>

                <article class="direct-line-period-card">
                    <span class="direct-line-period-card__icon" aria-hidden="true"></span>
                    <div>
                        <h2 class="direct-line-period-card__title">График работы</h2>
                        <p class="direct-line-period-card__text">Прямая телефонная линия проводится руководством учреждения в установленные даты и время.</p>
                    </div>
                </article>
            </section>

            <section class="direct-line-section" aria-labelledby="direct-line-schedule-title">
                <h2 class="direct-line-section__title" id="direct-line-schedule-title">Расписание</h2>
                <div class="direct-line-schedule">
                    <?php foreach ($directLineSchedule as $scheduleRow): ?>
                        <article class="direct-line-schedule-card">
                            <h3 class="direct-line-schedule-card__position"><?php echo e($scheduleRow['position']); ?></h3>
                            <div class="direct-line-slots">
                                <?php foreach ($scheduleRow['slots'] as $slot): ?>
                                    <div class="direct-line-slot">
                                        <span class="direct-line-slot__date"><?php echo e($slot['date']); ?></span>
                                        <span class="direct-line-slot__time"><?php echo e($slot['time']); ?></span>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <a class="direct-line-phone" href="tel:<?php echo e($scheduleRow['phone_href']); ?>"><?php echo e($scheduleRow['phone']); ?></a>
                        </article>
                    <?php endforeach; ?>
                </div>
            </section>

            <section class="direct-line-section" aria-labelledby="direct-line-hotline-title">
                <h2 class="direct-line-section__title" id="direct-line-hotline-title">Горячая линия</h2>
                <article class="direct-line-hotline-card">
                    <span class="direct-line-hotline-card__icon" aria-hidden="true"></span>
                    <div>
                        <h3 class="direct-line-hotline-card__title">В Центре работает телефон горячей линии</h3>
                        <a class="direct-line-hotline-card__phone" href="tel:<?php echo e($directLineHotlineHref); ?>"><?php echo e($directLineHotlinePhone); ?></a>
                        <p class="direct-line-hotline-card__text">В рабочие дни с 08:30 до 17:30, перерыв с 13:00 до 14:00.</p>
                        <p class="direct-line-hotline-card__text">Горячая линия работает по вопросам справочно-консультационного характера.</p>
                    </div>
                </article>
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

        function syncDirectLinePageOffset() {
            root.style.setProperty('--direct-line-page-header-offset', header.offsetHeight + 'px');
        }

        syncDirectLinePageOffset();
        window.addEventListener('load', syncDirectLinePageOffset);
        window.addEventListener('resize', syncDirectLinePageOffset);
    })();
</script>
</body>

</html>
