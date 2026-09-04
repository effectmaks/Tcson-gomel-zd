<?php
require_once __DIR__ . '/lib/security.php';
require_once __DIR__ . '/lib/auth.php';
require_once __DIR__ . '/lib/public_page_visibility.php';
include __DIR__ . '/db_connection.php';

$informationPageTitle = 'Информация';
$informationPageHeading = 'Информация';
$informationSiteName = 'ТЦСОН Железнодорожного района г. Гомеля';

$informationCards = array(
    array(
        'number' => '1.',
        'title' => 'Государственная информация',
        'description' => 'Официальные документы, сведения об учреждении, нормативные акты и справочная информация.',
        'href' => '/structure.php',
    ),
    array(
        'number' => '2.',
        'title' => 'Накопительное страхование «Стравита»',
        'description' => 'Условия участия, преимущества программы, порядок оформления и консультации по накопительному страхованию.',
        'href' => '/nakopitelnaya-pensiya.php',
    ),
    array(
        'number' => '3.',
        'title' => 'Безопасность граждан',
        'description' => 'Информация по защите прав граждан, профилактике насилия и правовой безопасности.',
        'href' => '/anti-corruption.php',
    ),
    array(
        'number' => '4.',
        'title' => 'Помощь в кризисной ситуации',
        'description' => 'Помощь в трудной жизненной ситуации, экстренная поддержка и консультации.',
        'href' => '/domestic-violence-prevention.php',
    ),
    array(
        'number' => '5.',
        'title' => 'Людям с инвалидностью',
        'description' => 'Услуги, реабилитация, абилитация и поддержка людей с инвалидностью.',
        'href' => '/gossocpodderzhka-invalidov.php',
    ),
    array(
        'number' => '6.',
        'title' => 'Семьям и детям',
        'description' => 'Поддержка семей с детьми, социальные выплаты, опека и попечительство.',
        'href' => '/department/guardianship/',
    ),
    array(
        'number' => '7.',
        'title' => 'Пожилым гражданам',
        'description' => 'Уход, социальное обслуживание на дому, досуг и дневное пребывание.',
        'href' => '/department/elderly-day-care/',
    ),
    array(
        'number' => '8.',
        'title' => 'Социальные услуги',
        'description' => 'Перечень социальных услуг, порядок их предоставления и основные направления работы.',
        'href' => '/department/',
    ),
    array(
        'number' => '9.',
        'title' => 'Сотрудничество и проекты',
        'description' => 'Партнеры, совместные проекты, волонтерство и общественно полезные инициативы.',
        'href' => '/listevents.php',
    ),
    array(
        'number' => '10.',
        'title' => 'Ясный язык - ясно и просто',
        'description' => 'Доступный буклет о помощи и услугах отделения социальной реабилитации, абилитации инвалидов.',
        'href' => '/easy-language.php',
    ),
    array(
        'number' => '11.',
        'title' => 'Вопрос-ответ',
        'description' => 'Ответы на часто задаваемые вопросы о социальной поддержке, занятости и социальных услугах.',
        'href' => '/questions-answers.php',
    ),
);

$seoTitleMeta = $informationPageHeading . ' - ' . $informationSiteName;
$seoDescriptionMeta = 'Информация и услуги, доступные на сайте ТЦСОН Железнодорожного района г. Гомеля, сгруппированные по направлениям.';

bootstrapPublicPageVisibility($conn, '/informatsiya.php', $informationPageTitle);
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
    <title><?php echo e($informationPageHeading); ?> - <?php echo e($informationSiteName); ?></title>
    <?php
    $seoScheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $seoHost = $_SERVER['HTTP_HOST'] ?? 'tcsonrw-gomel.by';
    $seoRequestUri = $_SERVER['REQUEST_URI'] ?? '/informatsiya.php';
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
        .information-map-page {
            scroll-padding-top: calc(var(--information-map-page-header-offset, var(--header-height)) + 54px);
        }

        .information-map-main {
            position: relative;
            isolation: isolate;
            padding-top: calc(var(--information-map-page-header-offset, var(--header-height)) + 54px);
            padding-bottom: 74px;
        }

        .information-map-main::before,
        .information-map-main::after {
            content: "";
            position: absolute;
            top: 0;
            bottom: -86px;
            width: clamp(0px, calc((100vw - var(--width-container)) / 2), 118px);
            background: url("/img/loop-vert.png") repeat-y center top / 100% auto;
            pointer-events: none;
            z-index: 0;
        }

        .information-map-main::before {
            left: 0;
            -webkit-mask-image: linear-gradient(270deg, transparent 0%, rgba(0, 0, 0, 0.3) 42%, rgba(0, 0, 0, 0.76) 100%);
            mask-image: linear-gradient(270deg, transparent 0%, rgba(0, 0, 0, 0.3) 42%, rgba(0, 0, 0, 0.76) 100%);
        }

        .information-map-main::after {
            right: 0;
            -webkit-mask-image: linear-gradient(90deg, transparent 0%, rgba(0, 0, 0, 0.3) 42%, rgba(0, 0, 0, 0.76) 100%);
            mask-image: linear-gradient(90deg, transparent 0%, rgba(0, 0, 0, 0.3) 42%, rgba(0, 0, 0, 0.76) 100%);
        }

        .information-map-layout {
            position: relative;
            z-index: 1;
        }

        .information-map-breadcrumbs {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 18px;
            color: #6a766f;
            font-size: 14px;
            line-height: 1.35;
        }

        .information-map-breadcrumbs__home {
            width: 16px;
            height: 16px;
            flex: 0 0 16px;
            background-color: #20604a;
            -webkit-mask: url("/img/dom.svg") no-repeat center / contain;
            mask: url("/img/dom.svg") no-repeat center / contain;
        }

        .information-map-breadcrumbs__separator {
            color: #aab0ac;
            font-weight: 700;
        }

        .information-map-breadcrumbs a {
            color: #20604a;
            text-decoration: none;
        }

        .information-map-breadcrumbs a:hover,
        .information-map-breadcrumbs a:focus-visible {
            color: #c62b30;
        }

        .information-map-head {
            margin-bottom: 28px;
        }

        .information-map-title {
            position: relative;
            display: block;
            margin: 0 0 14px;
            padding-left: 22px;
            color: #15553d;
            font-size: clamp(30px, 4vw, 42px);
            font-weight: 700;
            line-height: 1.12;
        }

        .information-map-title::before {
            content: "";
            position: absolute;
            left: 0;
            top: 4px;
            bottom: 6px;
            width: 5px;
            border-radius: 999px;
            background: #d53331;
        }

        .information-map-lead {
            max-width: none;
            margin: 0;
            color: #445c52;
            font-size: 17px;
            line-height: 1.45;
            white-space: nowrap;
        }

        .information-map-panel {
            position: relative;
            display: flex;
            min-height: max(0px, calc(90dvh - var(--information-map-page-header-offset, var(--header-height))));
            padding: 0;
            border-radius: 0;
            background: transparent;
            border: 0;
            box-shadow: none;
            overflow: visible;
        }

        .information-map-panel::before {
            display: none;
        }

        .information-map-grid {
            position: relative;
            z-index: 1;
            display: grid;
            flex: 1 1 auto;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            align-content: stretch;
            gap: 18px;
            min-height: 100%;
        }

        .information-map-card {
            position: relative;
            display: flex;
            flex-direction: column;
            min-height: 0;
            padding: 22px 20px 18px;
            border-radius: 28px;
            background: rgba(255, 255, 255, 0.98);
            border: 1px solid rgba(221, 227, 218, 0.96);
            box-shadow: 0 12px 26px rgba(26, 53, 45, 0.06);
            text-decoration: none;
            transition: transform .24s ease, border-color .24s ease, box-shadow .24s ease, background-color .24s ease;
        }

        .information-map-card:hover,
        .information-map-card:focus-visible {
            transform: translateY(-4px);
            background: linear-gradient(135deg, rgba(244, 248, 241, 0.98), rgba(251, 247, 239, 0.98));
            border-color: rgba(32, 96, 74, 0.22);
            box-shadow: 0 18px 36px rgba(26, 53, 45, 0.12);
        }

        .information-map-card__badge {
            width: 74px;
            height: 74px;
            border-radius: 50%;
            background: linear-gradient(180deg, #f6f6eb 0%, #efefe1 100%);
            box-shadow: none;
        }

        .information-map-card__body {
            display: flex;
            flex: 1 1 auto;
            flex-direction: column;
            gap: 12px;
            min-width: 0;
            padding-top: 18px;
        }

        .information-map-card__title {
            margin: 0;
            color: #174738;
            font-size: 18px;
            font-weight: 700;
            line-height: 1.25;
        }

        .information-map-card__title-number {
            color: #d53331;
            margin-right: 6px;
        }

        .information-map-card__description {
            display: block;
            flex: 1 1 auto;
            min-width: 0;
            margin: 0;
            color: #43584f;
            font-size: 16px;
            line-height: 1.6;
        }

        .information-map-card__meta {
            display: flex;
            align-items: center;
            gap: 12px;
            min-width: 0;
            margin-top: auto;
        }

        .information-map-card__arrow {
            flex: 0 0 auto;
            align-self: center;
            padding-top: 0;
            color: #d53331;
            font-size: 24px;
            line-height: 1;
            transition: transform .2s ease;
        }

        .information-map-card:hover .information-map-card__arrow,
        .information-map-card:focus-visible .information-map-card__arrow {
            transform: translateX(4px);
        }

        @media (max-width: 1180px) {
            .information-map-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 760px) {
            .information-map-page {
                scroll-padding-top: calc(var(--information-map-page-header-offset, var(--header-height)) + 38px);
            }

            .information-map-main {
                padding-top: calc(var(--information-map-page-header-offset, var(--header-height)) + 38px);
                padding-bottom: 58px;
            }

            .information-map-main::before,
            .information-map-main::after {
                display: none;
            }

            .information-map-title {
                font-size: 32px;
            }

            .information-map-lead {
                white-space: normal;
            }

            .information-map-panel {
                padding: 0;
                border-radius: 0;
            }

            .information-map-grid {
                grid-template-columns: 1fr;
                gap: 14px;
            }

            .information-map-card {
                padding: 20px 18px 18px;
                border-radius: 24px;
            }

            .information-map-card__badge {
                width: 62px;
                height: 62px;
            }

            .information-map-card__title {
                font-size: 17px;
            }

            .information-map-card__description {
                font-size: 15px;
            }

            .information-map-card__meta {
                align-items: flex-start;
            }
        }
    </style>
</head>

<body class="information-map-page">
    <?php include __DIR__ . '/header.php'; ?>
    <main class="information-map-main">
        <div class="information-map-layout container">
            <nav class="information-map-breadcrumbs" aria-label="Хлебные крошки">
                <a href="/" class="information-map-breadcrumbs__home" aria-label="Главная"></a>
                <span class="information-map-breadcrumbs__separator" aria-hidden="true">›</span>
                <span>Информация</span>
            </nav>

            <section class="information-map-head" aria-labelledby="information-map-title">
                <h1 class="information-map-title" id="information-map-title"><?php echo e($informationPageHeading); ?></h1>
                <p class="information-map-lead">Информация и услуги сайта сгруппированы по направлениям.</p>
            </section>

            <section class="information-map-panel" aria-label="Категории информации">
                <div class="information-map-grid">
                    <?php foreach ($informationCards as $card): ?>
                    <a class="information-map-card" href="<?php echo e($card['href']); ?>">
                        <span class="information-map-card__badge" aria-hidden="true"></span>
                        <span class="information-map-card__body">
                            <h2 class="information-map-card__title">
                                <span class="information-map-card__title-number"><?php echo e($card['number']); ?></span>
                                <?php echo e($card['title']); ?>
                            </h2>
                            <span class="information-map-card__meta">
                                <span class="information-map-card__description"><?php echo e($card['description']); ?></span>
                                <span class="information-map-card__arrow" aria-hidden="true">›</span>
                            </span>
                        </span>
                    </a>
                    <?php endforeach; ?>
                </div>
            </section>
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

            function syncInformationMapPageOffset() {
                root.style.setProperty('--information-map-page-header-offset', header.offsetHeight + 'px');
            }

            syncInformationMapPageOffset();
            window.addEventListener('load', syncInformationMapPageOffset);
            window.addEventListener('resize', syncInformationMapPageOffset);
        })();
    </script>
</body>

</html>
