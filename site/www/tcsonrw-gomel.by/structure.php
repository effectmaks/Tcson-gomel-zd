<?php
require_once __DIR__ . '/lib/security.php';
require_once __DIR__ . '/lib/auth.php';
require_once __DIR__ . '/lib/public_page_visibility.php';
include __DIR__ . '/db_connection.php';

$structurePageTitle = 'Структура учреждения';
$structureSiteName = 'Территориальный центр социального обслуживания населения Железнодорожного района г. Гомеля';
$structureDocTitle = 'Политика государственного учреждения в отношении обработки персональных данных';
$structureDocHref = '/documents/gomel-personal-data-policy-2026.docx';
$structureDocPath = __DIR__ . $structureDocHref;
$structureDocSize = file_exists($structureDocPath) ? (int) ceil(filesize($structureDocPath) / 1024) . ' КБ' : '';

$structurePeople = array(
    'director' => array(
        'role' => 'Директор',
        'name' => 'Забавчик Наталья Александровна',
    ),
    'deputy' => array(
        'role' => 'Заместитель директора',
        'name' => 'Снежкова Екатерина Петровна',
    ),
    'accountant' => array(
        'role' => 'Главный бухгалтер',
        'name' => 'Смагур Ольга Анатольевна',
    ),
    'hr' => array(
        'role' => 'Специалист по кадрам',
        'name' => 'Лысюк Илона Григорьевна',
    ),
    'lawyer' => array(
        'role' => 'Юрисконсульт',
        'name' => 'Анашкина Татьяна Сергеевна',
    ),
    'safety' => array(
        'role' => 'Инженер по охране труда',
        'name' => 'Солдатенко Людмила Григорьевна',
    ),
);

$structureDepartments = array(
    array(
        'role' => 'Отделение первичного приема, информации, анализа и прогнозирования',
        'head' => 'Волчкова Виктория Станиславовна',
        'icon' => 'info',
    ),
    array(
        'role' => 'Отделение социальной поддержки населения',
        'head' => 'Коржова Елена Викторовна',
        'icon' => 'support',
    ),
    array(
        'role' => 'Отделение опеки и попечительства',
        'head' => 'Коржова Карина Валерьевна',
        'icon' => 'guardianship',
    ),
    array(
        'role' => 'Отделение социальной помощи на дому',
        'head' => 'Светюха Наталья Михайловна',
        'icon' => 'home',
    ),
    array(
        'role' => 'Отделение комплексной поддержки в кризисной ситуации',
        'head' => 'Дайнеко Ирина Сергеевна',
        'icon' => 'crisis',
    ),
    array(
        'role' => 'Отделение дневного пребывания для граждан пожилого возраста',
        'head' => 'Усова Лилия Евгеньевна',
        'icon' => 'elderly',
    ),
    array(
        'role' => 'Отделение социальной реабилитации, абилитации инвалидов',
        'head' => 'Кулаковская Алина Егоровна',
        'icon' => 'rehab',
    ),
);

$seoTitleMeta = 'Структура учреждения - ТЦСОН Железнодорожного района г. Гомеля';
$seoDescriptionMeta = 'Структура и руководство ТЦСОН Железнодорожного района г. Гомеля: директор, заместитель директора, отделения и ответственные специалисты.';

bootstrapPublicPageVisibility($conn, '/structure.php', $structurePageTitle);
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
    <title><?php echo e($structurePageTitle); ?> - ТЦСОН Железнодорожного района г. Гомеля</title>
    <?php
    $seoScheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $seoHost = $_SERVER['HTTP_HOST'] ?? 'tcsonrw-gomel.by';
    $seoRequestUri = $_SERVER['REQUEST_URI'] ?? '/structure.php';
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
        .structure-page {
            scroll-padding-top: calc(var(--structure-page-header-offset, var(--header-height)) + 54px);
        }

        .structure-page-main {
            position: relative;
            isolation: isolate;
            padding-top: calc(var(--structure-page-header-offset, var(--header-height)) + 54px);
            padding-bottom: 74px;
        }

        .structure-page-main::before,
        .structure-page-main::after {
            content: "";
            position: absolute;
            top: 0;
            bottom: -86px;
            width: clamp(0px, calc((100vw - var(--width-container)) / 2), 118px);
            background: url("/img/loop-vert.png") repeat-y center top / 100% auto;
            pointer-events: none;
            z-index: 0;
        }

        .structure-page-main::before {
            left: 0;
            -webkit-mask-image: linear-gradient(270deg, transparent 0%, rgba(0, 0, 0, 0.3) 42%, rgba(0, 0, 0, 0.76) 100%);
            mask-image: linear-gradient(270deg, transparent 0%, rgba(0, 0, 0, 0.3) 42%, rgba(0, 0, 0, 0.76) 100%);
        }

        .structure-page-main::after {
            right: 0;
            -webkit-mask-image: linear-gradient(90deg, transparent 0%, rgba(0, 0, 0, 0.3) 42%, rgba(0, 0, 0, 0.76) 100%);
            mask-image: linear-gradient(90deg, transparent 0%, rgba(0, 0, 0, 0.3) 42%, rgba(0, 0, 0, 0.76) 100%);
        }

        .structure-layout {
            position: relative;
            z-index: 1;
            display: grid;
            grid-template-columns: 315px minmax(0, 1fr);
            gap: 34px;
            align-items: start;
        }

        .structure-content {
            min-width: 0;
        }

        .structure-breadcrumbs {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 18px;
            color: #6a766f;
            font-size: 14px;
            line-height: 1.35;
        }

        .structure-breadcrumbs__home {
            width: 16px;
            height: 16px;
            flex: 0 0 16px;
            background-color: #20604a;
            -webkit-mask: url("/img/dom.svg") no-repeat center / contain;
            mask: url("/img/dom.svg") no-repeat center / contain;
        }

        .structure-breadcrumbs__separator {
            color: #aab0ac;
            font-weight: 700;
        }

        .structure-breadcrumbs a {
            color: #20604a;
            text-decoration: none;
        }

        .structure-breadcrumbs a:hover,
        .structure-breadcrumbs a:focus-visible {
            color: #c62b30;
        }

        .structure-head {
            margin-bottom: 28px;
            text-align: left;
        }

        .structure-title {
            position: relative;
            display: block;
            margin: 0 0 12px;
            padding-left: 22px;
            color: #15553d;
            font-size: clamp(30px, 3vw, 40px);
            font-weight: 700;
            line-height: 1.12;
            text-align: left;
        }

        .structure-title::before {
            content: "";
            position: absolute;
            left: 0;
            top: 3px;
            bottom: 5px;
            width: 5px;
            border-radius: 999px;
            background: #d53331;
        }

        .structure-org {
            --structure-line-color: #e43620;
            --structure-line-size: 3px;
            --structure-line-gap: 34px;
            position: relative;
            overflow: hidden;
            margin-bottom: 34px;
            padding: 30px 26px 26px;
            border-radius: 22px;
            background: rgba(255, 255, 255, 0.78);
            box-shadow: 0 18px 38px rgba(48, 56, 52, 0.08);
        }

        .structure-org__chart {
            position: relative;
            width: 100%;
        }

        .structure-org__top {
            position: relative;
            z-index: 2;
            display: flex;
            justify-content: center;
            margin-bottom: var(--structure-line-gap);
        }

        .structure-org__top::after {
            content: "";
            position: absolute;
            left: 50%;
            bottom: calc(-1 * var(--structure-line-gap));
            width: var(--structure-line-size);
            height: var(--structure-line-gap);
            border-radius: 999px;
            background: var(--structure-line-color);
            transform: translateX(-50%);
            z-index: 0;
        }

        .structure-card {
            position: relative;
            z-index: 2;
            display: flex;
            align-items: flex-start;
            gap: 14px;
            min-width: 0;
            min-height: 76px;
            padding: 16px 18px;
            border: 1px solid rgba(213, 51, 49, 0.72);
            border-radius: 10px;
            background: linear-gradient(180deg, #fff, #fff0f0);
            box-shadow: 0 10px 24px rgba(213, 51, 49, 0.11);
        }

        .structure-card--director {
            width: min(100%, 360px);
            border-color: rgba(213, 51, 49, 0.82);
        }

        .structure-card--department {
            border-color: rgba(6, 120, 88, 0.72);
            background: linear-gradient(180deg, #fff, #e1faec);
            box-shadow: 0 10px 24px rgba(6, 120, 88, 0.1);
        }

        .structure-card__icon {
            width: 28px;
            height: 28px;
            flex: 0 0 28px;
            margin-top: 2px;
            background-color: #0b6f52;
        }

        .structure-card__icon--person {
            -webkit-mask: url("/img/man.svg") no-repeat center / contain;
            mask: url("/img/man.svg") no-repeat center / contain;
        }

        .structure-card__icon--info {
            -webkit-mask: url("/img/info.svg") no-repeat center / contain;
            mask: url("/img/info.svg") no-repeat center / contain;
        }

        .structure-card__icon--support {
            -webkit-mask: url("/img/socialnaya podderzhka.svg") no-repeat center / contain;
            mask: url("/img/socialnaya podderzhka.svg") no-repeat center / contain;
        }

        .structure-card__icon--guardianship {
            -webkit-mask: url("/img/socialnaya podderzhka.svg") no-repeat center / contain;
            mask: url("/img/socialnaya podderzhka.svg") no-repeat center / contain;
        }

        .structure-card__icon--home {
            -webkit-mask: url("/img/dom.svg") no-repeat center / contain;
            mask: url("/img/dom.svg") no-repeat center / contain;
        }

        .structure-card__icon--crisis {
            -webkit-mask: url("/img/glaz.svg") no-repeat center / contain;
            mask: url("/img/glaz.svg") no-repeat center / contain;
        }

        .structure-card__icon--elderly {
            -webkit-mask: url("/img/uslugi.svg") no-repeat center / contain;
            mask: url("/img/uslugi.svg") no-repeat center / contain;
        }

        .structure-card__icon--rehab {
            -webkit-mask: url("/img/gossocpodderzhka invalidov.svg") no-repeat center / contain;
            mask: url("/img/gossocpodderzhka invalidov.svg") no-repeat center / contain;
        }

        .structure-card__body {
            min-width: 0;
        }

        .structure-card__role {
            margin: 0 0 5px;
            color: #a51f2b;
            font-size: 15px;
            font-weight: 800;
            line-height: 1.25;
            text-transform: uppercase;
            overflow-wrap: anywhere;
        }

        .structure-card--department .structure-card__role {
            color: #075a43;
            text-transform: none;
        }

        .structure-card__name {
            display: block;
            color: #2f352f;
            font-size: 15px;
            font-weight: 700;
            line-height: 1.35;
            overflow-wrap: anywhere;
        }

        .structure-org__branches {
            position: relative;
            z-index: 1;
            display: grid;
            grid-template-columns: minmax(0, 0.82fr) minmax(0, 1.12fr) minmax(0, 0.82fr);
            gap: clamp(10px, 2vw, 22px);
            align-items: start;
        }

        .structure-org__branches::before {
            content: "";
            position: absolute;
            top: calc(var(--structure-line-gap) / -2);
            left: 12%;
            right: 12%;
            height: var(--structure-line-size);
            border-radius: 999px;
            background: var(--structure-line-color);
            z-index: 0;
        }

        .structure-org__column {
            position: relative;
            display: grid;
            gap: 14px;
        }

        .structure-org__column::before {
            content: "";
            position: absolute;
            top: calc(var(--structure-line-gap) / -2);
            bottom: 0;
            left: 50%;
            width: var(--structure-line-size);
            border-radius: 999px;
            background: var(--structure-line-color);
            transform: translateX(-50%);
            z-index: 0;
        }

        .structure-org__column--departments {
            gap: 10px;
        }

        .structure-section {
            margin-top: 34px;
        }

        .structure-section__title {
            margin: 0 0 16px;
            color: #196847;
            font-size: 26px;
            font-weight: 700;
            line-height: 1.16;
        }

        .structure-table-wrap {
            overflow-x: auto;
            border: 1px solid rgba(32, 96, 74, 0.12);
            border-radius: 18px;
            background: rgba(255, 255, 255, 0.94);
            box-shadow: 0 14px 30px rgba(48, 56, 52, 0.08);
        }

        .structure-table {
            width: 100%;
            min-width: 720px;
            border-collapse: collapse;
            color: #2c2c2c;
            font-size: 15px;
            line-height: 1.45;
        }

        .structure-table th,
        .structure-table td {
            padding: 14px 16px;
            border-bottom: 1px solid rgba(32, 96, 74, 0.1);
            vertical-align: top;
            text-align: left;
        }

        .structure-table th {
            color: #15553d;
            font-size: 14px;
            font-weight: 800;
            text-transform: uppercase;
            background: rgba(238, 247, 241, 0.9);
        }

        .structure-table tr:last-child td {
            border-bottom: 0;
        }

        .structure-table a {
            color: #15553d;
            font-weight: 700;
            text-decoration: none;
        }

        .structure-table a:hover,
        .structure-table a:focus-visible {
            color: #c62b30;
        }

        .structure-doc-card {
            display: grid;
            grid-template-columns: 52px minmax(0, 1fr) auto;
            gap: 18px;
            align-items: center;
            padding: 22px;
            border: 1px solid rgba(32, 96, 74, 0.1);
            border-radius: 18px;
            background: rgba(255, 255, 255, 0.94);
            box-shadow: 0 14px 30px rgba(48, 56, 52, 0.08);
        }

        .structure-doc-card__icon {
            width: 46px;
            height: 46px;
            border-radius: 10px;
            background: #eef7f1;
            color: #15553d;
            font-size: 12px;
            font-weight: 800;
            line-height: 46px;
            text-align: center;
            box-shadow: inset 0 0 0 1px rgba(21, 85, 61, 0.16);
        }

        .structure-doc-card__title {
            display: block;
            color: #15553d;
            font-size: 17px;
            font-weight: 700;
            line-height: 1.35;
            text-decoration: none;
        }

        .structure-doc-card__title:hover,
        .structure-doc-card__title:focus-visible {
            color: #c62b30;
        }

        .structure-doc-card__meta {
            display: block;
            margin-top: 4px;
            color: #5f6b63;
            font-size: 14px;
            line-height: 1.35;
        }

        .structure-doc-card__button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 44px;
            padding: 0 18px;
            border: 1px solid rgba(32, 96, 74, 0.2);
            border-radius: var(--button-radius);
            color: #15553d;
            font-size: 15px;
            font-weight: 700;
            text-decoration: none;
            transition: border-color .2s ease, color .2s ease, transform .2s ease;
        }

        .structure-doc-card__button:hover,
        .structure-doc-card__button:focus-visible {
            border-color: rgba(213, 51, 49, 0.3);
            color: #c62b30;
            transform: translateY(-1px);
        }

        @media (max-width: 1120px) {
            .structure-layout {
                grid-template-columns: 260px minmax(0, 1fr);
                gap: 24px;
            }
        }

        @media (max-width: 860px) {
            .structure-page {
                scroll-padding-top: calc(var(--structure-page-header-offset, var(--header-height)) + 38px);
            }

            .structure-page-main {
                padding-top: calc(var(--structure-page-header-offset, var(--header-height)) + 38px);
                padding-bottom: 58px;
            }

            .structure-page-main::before,
            .structure-page-main::after {
                display: none;
            }

            .structure-layout {
                grid-template-columns: 1fr;
            }

            .structure-head {
                text-align: left;
            }

            .structure-title {
                display: block;
            }
        }

        @media (max-width: 620px) {
            .structure-title {
                font-size: 30px;
            }

            .structure-org {
                --structure-line-size: 2px;
                --structure-line-gap: 24px;
                padding: 14px 10px 12px;
            }

            .structure-org__chart {
                width: 760px;
                max-width: none;
                zoom: 0.43;
            }

            .structure-org__branches {
                gap: 6px;
            }

            .structure-org__column {
                gap: 8px;
            }

            .structure-org__column--departments {
                gap: 7px;
            }

            .structure-card {
                gap: 6px;
                min-height: 54px;
                padding: 8px 7px;
                border-radius: 8px;
            }

            .structure-card__icon {
                width: 17px;
                height: 17px;
                flex-basis: 17px;
                margin-top: 1px;
            }

            .structure-card__role {
                margin-bottom: 4px;
                font-size: 10px;
                line-height: 1.18;
            }

            .structure-card__name {
                font-size: 11px;
                line-height: 1.2;
            }

            .structure-doc-card {
                grid-template-columns: 44px minmax(0, 1fr);
                align-items: start;
            }

            .structure-doc-card__button {
                grid-column: 1 / -1;
            }
        }

        @media (min-width: 400px) and (max-width: 620px) {
            .structure-org__chart {
                zoom: 0.47;
            }
        }

        @media (max-width: 380px) {
            .structure-org__chart {
                zoom: 0.39;
            }
        }
    </style>
</head>

<body class="structure-page">
<?php include __DIR__ . '/header.php'; ?>
<main class="main structure-page-main">
    <div class="structure-layout container">
        <?php
        $aboutMenuActive = 'structure';
        include __DIR__ . '/about-side-menu.php';
        ?>

        <div class="structure-content">
            <nav class="structure-breadcrumbs" aria-label="Хлебные крошки">
                <span class="structure-breadcrumbs__home" aria-hidden="true"></span>
                <a href="/">Главная</a>
                <span class="structure-breadcrumbs__separator" aria-hidden="true">›</span>
                <a href="/#about-center">О центре</a>
                <span class="structure-breadcrumbs__separator" aria-hidden="true">›</span>
                <span><?php echo e($structurePageTitle); ?></span>
            </nav>

            <section class="structure-head" aria-labelledby="structure-page-title">
                <h1 class="structure-title" id="structure-page-title"><?php echo e($structurePageTitle); ?></h1>
            </section>

            <section class="structure-org" aria-label="Организационная структура учреждения">
                <div class="structure-org__chart">
                    <div class="structure-org__top">
                        <article class="structure-card structure-card--director">
                            <span class="structure-card__icon structure-card__icon--person" aria-hidden="true"></span>
                            <div class="structure-card__body">
                                <h2 class="structure-card__role"><?php echo e($structurePeople['director']['role']); ?></h2>
                                <span class="structure-card__name"><?php echo e($structurePeople['director']['name']); ?></span>
                            </div>
                        </article>
                    </div>

                    <div class="structure-org__branches">
                        <div class="structure-org__column structure-org__column--left">
                            <article class="structure-card">
                                <span class="structure-card__icon structure-card__icon--person" aria-hidden="true"></span>
                                <div class="structure-card__body">
                                    <h3 class="structure-card__role"><?php echo e($structurePeople['accountant']['role']); ?></h3>
                                    <span class="structure-card__name"><?php echo e($structurePeople['accountant']['name']); ?></span>
                                </div>
                            </article>
                        </div>

                        <div class="structure-org__column structure-org__column--departments">
                            <article class="structure-card">
                                <span class="structure-card__icon structure-card__icon--person" aria-hidden="true"></span>
                                <div class="structure-card__body">
                                    <h3 class="structure-card__role"><?php echo e($structurePeople['deputy']['role']); ?></h3>
                                    <span class="structure-card__name"><?php echo e($structurePeople['deputy']['name']); ?></span>
                                </div>
                            </article>

                            <?php foreach ($structureDepartments as $department): ?>
                            <article class="structure-card structure-card--department">
                                <span class="structure-card__icon structure-card__icon--<?php echo e($department['icon']); ?>" aria-hidden="true"></span>
                                <div class="structure-card__body">
                                    <h3 class="structure-card__role"><?php echo e($department['role']); ?></h3>
                                    <span class="structure-card__name"><?php echo e($department['head']); ?></span>
                                </div>
                            </article>
                            <?php endforeach; ?>
                        </div>

                        <div class="structure-org__column structure-org__column--right">
                            <?php foreach (array('hr', 'lawyer', 'safety') as $personKey): ?>
                            <article class="structure-card">
                                <span class="structure-card__icon structure-card__icon--person" aria-hidden="true"></span>
                                <div class="structure-card__body">
                                    <h3 class="structure-card__role"><?php echo e($structurePeople[$personKey]['role']); ?></h3>
                                    <span class="structure-card__name"><?php echo e($structurePeople[$personKey]['name']); ?></span>
                                </div>
                            </article>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </section>

            <section class="structure-section" id="documents" aria-labelledby="structure-documents-title">
                <h2 class="structure-section__title" id="structure-documents-title">Документы учреждения</h2>
                <article class="structure-doc-card">
                    <span class="structure-doc-card__icon" aria-hidden="true">DOCX</span>
                    <div>
                        <a class="structure-doc-card__title" href="<?php echo e($structureDocHref); ?>"><?php echo e($structureDocTitle); ?></a>
                        <span class="structure-doc-card__meta">DOCX<?php echo $structureDocSize !== '' ? ', ' . e($structureDocSize) : ''; ?></span>
                    </div>
                    <a class="structure-doc-card__button" href="<?php echo e($structureDocHref); ?>" download>Скачать</a>
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

        function syncStructurePageOffset() {
            root.style.setProperty('--structure-page-header-offset', header.offsetHeight + 'px');
        }

        syncStructurePageOffset();
        window.addEventListener('load', syncStructurePageOffset);
        window.addEventListener('resize', syncStructurePageOffset);
    })();
</script>
</body>

</html>
