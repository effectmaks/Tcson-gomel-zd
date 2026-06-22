<?php
require_once dirname(__DIR__) . '/lib/security.php';
require_once dirname(__DIR__) . '/lib/auth.php';
require_once dirname(__DIR__) . '/lib/public_page_visibility.php';
require_once __DIR__ . '/data.php';
require_once __DIR__ . '/content.php';
include dirname(__DIR__) . '/db_connection.php';

$departmentId = isset($departmentDetailSlug) ? (string) $departmentDetailSlug : '';
$departmentItem = getDepartmentItemById($departmentId);

if (!is_array($departmentItem)) {
    http_response_code(404);
    $departmentItem = array(
        'title' => 'Отделение не найдено',
        'summary' => 'Запрошенная страница отделения не найдена.',
        'head_role' => '',
        'head_name' => '',
        'phone' => '',
        'phone_href' => '',
        'address' => '',
        'schedule' => array(),
        'purpose' => array(),
        'activities' => array(),
        'audience' => array(),
    );
}

$departmentCenterInfo = getDepartmentCenterInfo();
$departmentItems = getDepartmentItems();
$departmentPageTitle = (string) $departmentItem['title'];
$departmentPageIcon = getDepartmentIconPath($departmentId);
$departmentSiteName = $departmentCenterInfo['site_name'];
$departmentSeoDescription = $departmentPageTitle . ' ТЦСОН Железнодорожного района г. Гомеля: контакты, график работы, цели и направления деятельности.';
$departmentContentHtml = formatDepartmentDetailContentHtml(getDepartmentDetailContentHtml($departmentId), $departmentPageTitle);
$departmentFeatureCards = isset($departmentItem['feature_cards']) && is_array($departmentItem['feature_cards'])
    ? $departmentItem['feature_cards']
    : array();
$departmentDownloadItems = isset($departmentItem['download_items']) && is_array($departmentItem['download_items'])
    ? $departmentItem['download_items']
    : array();

foreach ($departmentDownloadItems as $departmentDownloadIndex => $departmentDownloadItem) {
    $downloadHref = (string) ($departmentDownloadItem['href'] ?? '');
    $downloadPath = '';
    if ($downloadHref !== '' && str_starts_with($downloadHref, '/')) {
        $downloadPath = dirname(__DIR__) . $downloadHref;
    }

    $downloadExtension = pathinfo(parse_url($downloadHref, PHP_URL_PATH) ?? '', PATHINFO_EXTENSION);
    $downloadExtension = $downloadExtension !== '' ? mb_strtoupper($downloadExtension, 'UTF-8') : 'FILE';
    $downloadSize = '';
    if ($downloadPath !== '' && file_exists($downloadPath)) {
        $downloadSize = (string) ceil(filesize($downloadPath) / 1024) . ' КБ';
    }

    $departmentDownloadItems[$departmentDownloadIndex]['meta'] = $downloadExtension . ($downloadSize !== '' ? ', ' . $downloadSize : '');
}

bootstrapPublicPageVisibility($conn, '/department/', 'Отделения');
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
    <title><?php echo e($departmentPageTitle); ?> - <?php echo e($departmentSiteName); ?></title>
    <?php
    $seoScheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $seoHost = $_SERVER['HTTP_HOST'] ?? 'tcsonrw-gomel.by';
    $seoCanonical = $seoScheme . '://' . $seoHost . (string) ($departmentItem['detail_url'] ?? '/department/');
    $seoOgImage = '/img/logo-old-mini.webp';
    $seoOgImageUrl = $seoScheme . '://' . $seoHost . $seoOgImage;
    ?>
    <meta name="description" content="<?php echo e($departmentSeoDescription); ?>">
    <meta name="robots" content="index,follow">
    <link rel="canonical" href="<?php echo e($seoCanonical); ?>">
    <meta property="og:locale" content="ru_RU">
    <meta property="og:type" content="website">
    <meta property="og:title" content="<?php echo e($departmentPageTitle); ?>">
    <meta property="og:description" content="<?php echo e($departmentSeoDescription); ?>">
    <meta property="og:url" content="<?php echo e($seoCanonical); ?>">
    <meta property="og:image" content="<?php echo e($seoOgImageUrl); ?>">
    <style>
        .department-detail-page {
            scroll-padding-top: calc(var(--department-detail-header-offset, var(--header-height)) + 54px);
        }

        .department-detail-main {
            position: relative;
            isolation: isolate;
            padding-top: calc(var(--department-detail-header-offset, var(--header-height)) + 54px);
            padding-bottom: 74px;
        }

        .department-detail-main::before,
        .department-detail-main::after {
            content: "";
            position: absolute;
            top: 0;
            bottom: -88px;
            width: clamp(0px, calc((100vw - var(--width-container)) / 2), 118px);
            background: url("/img/loop-vert.png") repeat-y center top / 100% auto;
            pointer-events: none;
            z-index: 0;
        }

        .department-detail-main::before {
            left: 0;
            -webkit-mask-image: linear-gradient(270deg, transparent 0%, rgba(0, 0, 0, 0.3) 42%, rgba(0, 0, 0, 0.76) 100%);
            mask-image: linear-gradient(270deg, transparent 0%, rgba(0, 0, 0, 0.3) 42%, rgba(0, 0, 0, 0.76) 100%);
        }

        .department-detail-main::after {
            right: 0;
            -webkit-mask-image: linear-gradient(90deg, transparent 0%, rgba(0, 0, 0, 0.3) 42%, rgba(0, 0, 0, 0.76) 100%);
            mask-image: linear-gradient(90deg, transparent 0%, rgba(0, 0, 0, 0.3) 42%, rgba(0, 0, 0, 0.76) 100%);
        }

        .department-detail-layout {
            position: relative;
            z-index: 1;
            display: grid;
            grid-template-columns: 315px minmax(0, 1fr);
            gap: 36px;
            align-items: start;
        }

        .department-detail-content {
            min-width: 0;
        }

        .department-detail-breadcrumbs {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 18px;
            color: #6a766f;
            font-size: 14px;
            line-height: 1.35;
        }

        .department-detail-breadcrumbs__home {
            width: 16px;
            height: 16px;
            flex: 0 0 16px;
            background-color: #20604a;
            -webkit-mask: url("/img/dom.svg") no-repeat center / contain;
            mask: url("/img/dom.svg") no-repeat center / contain;
        }

        .department-detail-breadcrumbs__separator {
            color: #aab0ac;
            font-weight: 700;
        }

        .department-detail-breadcrumbs a {
            color: #20604a;
            text-decoration: none;
        }

        .department-detail-breadcrumbs a:hover,
        .department-detail-breadcrumbs a:focus-visible {
            color: #c62b30;
        }

        .department-detail-hero {
            margin-bottom: 26px;
            padding: 28px;
            border-radius: 30px;
            background: linear-gradient(135deg, rgba(26, 84, 64, 0.08), rgba(214, 181, 107, 0.2));
            border: 1px solid rgba(32, 96, 74, 0.14);
            box-shadow: 0 22px 44px rgba(17, 52, 40, 0.08);
        }

        .department-detail-title-row {
            display: flex;
            align-items: center;
            gap: 18px;
            margin-bottom: 16px;
        }

        .department-detail-title-icon-shell {
            width: 64px;
            height: 64px;
            border-radius: 20px;
            background: linear-gradient(180deg, #f8f8ef 0%, #f0f1e4 100%);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex: 0 0 64px;
        }

        .department-detail-title-icon {
            width: 34px;
            height: 34px;
            background-color: #1f5f47;
            -webkit-mask: var(--department-icon) no-repeat center / contain;
            mask: var(--department-icon) no-repeat center / contain;
            opacity: 0.96;
        }

        .department-detail-title {
            margin: 0;
            color: #14352b;
            font-size: clamp(30px, 4vw, 42px);
            line-height: 1.1;
        }

        .department-detail-lead {
            max-width: 820px;
            margin: 0;
            color: #355448;
            font-size: 17px;
            line-height: 1.65;
        }

        .department-detail-meta {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 14px;
            margin-bottom: 28px;
        }

        .department-detail-meta__item {
            min-width: 0;
            padding: 16px 18px;
            border-radius: 18px;
            background: #ffffff;
            border: 1px solid rgba(32, 96, 74, 0.12);
            box-shadow: 0 14px 30px rgba(17, 52, 40, 0.06);
        }

        .department-detail-meta__label {
            display: block;
            margin-bottom: 6px;
            color: #6e7f76;
            font-size: 12px;
            line-height: 1.4;
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }

        .department-detail-meta__value,
        .department-detail-meta__value a {
            color: #14352b;
            font-size: 15px;
            font-weight: 600;
            line-height: 1.55;
            text-decoration: none;
        }

        .department-detail-section {
            margin-bottom: 28px;
        }

        .department-detail-section__title {
            margin: 0 0 14px;
            color: #14352b;
            font-size: 26px;
            line-height: 1.2;
        }

        .department-detail-source {
            overflow-x: auto;
            padding: 24px;
            border-radius: 24px;
            background: rgba(255, 255, 255, 0.96);
            border: 1px solid rgba(32, 96, 74, 0.12);
            box-shadow: 0 16px 34px rgba(17, 52, 40, 0.07);
            color: #243f35;
            font-size: 16px;
            line-height: 1.65;
        }

        .department-detail-source p {
            margin: 0 0 14px;
        }

        .department-detail-source b,
        .department-detail-source strong {
            color: #14352b;
            font-weight: 800;
        }

        .department-detail-source__intro {
            padding: 18px 20px;
            border-radius: 18px;
            background: rgba(242, 247, 244, 0.96);
            border: 1px solid rgba(32, 96, 74, 0.12);
            color: #14352b;
            font-size: 17px;
            font-weight: 600;
        }

        .department-detail-source__heading {
            position: relative;
            margin: 28px 0 14px !important;
            padding-left: 18px;
            color: #14352b;
            font-size: 24px;
            font-weight: 800;
            line-height: 1.25;
        }

        .department-detail-source__heading::before {
            content: "";
            position: absolute;
            left: 0;
            top: 5px;
            bottom: 5px;
            width: 5px;
            border-radius: 999px;
            background: #c62b30;
        }

        .department-detail-source__strong-heading {
            display: inline-block;
            margin-top: 16px;
            color: #14352b !important;
            font-size: 20px;
            line-height: 1.3;
        }

        .department-detail-source__list-line {
            position: relative;
            padding-left: 22px;
        }

        .department-detail-source__list-line::before {
            content: "";
            position: absolute;
            left: 0;
            top: 11px;
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #c62b30;
        }

        .department-detail-source ul,
        .department-detail-source ol {
            margin: 0 0 16px 22px;
            padding: 0;
        }

        .department-detail-source li {
            margin-bottom: 8px;
        }

        .department-detail-source table {
            width: 100%;
            min-width: 680px;
            margin: 16px 0;
            border-collapse: collapse;
            background: #ffffff;
            color: #243f35;
            font-size: 15px;
            line-height: 1.5;
        }

        .department-detail-source th,
        .department-detail-source td {
            padding: 10px 12px;
            border: 1px solid rgba(32, 96, 74, 0.18);
            vertical-align: top;
        }

        .department-detail-source th {
            background: rgba(32, 96, 74, 0.08);
            color: #14352b;
            font-weight: 800;
        }

        .department-detail-source a {
            color: #20604a;
        }

        .department-detail-list {
            display: grid;
            gap: 10px;
            padding: 0;
            margin: 0;
            list-style: none;
        }

        .department-detail-list li {
            position: relative;
            padding: 15px 18px 15px 46px;
            border-radius: 18px;
            background: rgba(255, 255, 255, 0.94);
            border: 1px solid rgba(32, 96, 74, 0.12);
            color: #385447;
            line-height: 1.6;
        }

        .department-detail-list li::before {
            content: "";
            position: absolute;
            left: 18px;
            top: 21px;
            width: 9px;
            height: 9px;
            border-radius: 50%;
            background: #c62b30;
        }

        .department-detail-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin-top: 30px;
        }

        .department-detail-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 44px;
            padding: 0 20px;
            border-radius: 999px;
            background: #20604a;
            color: #ffffff;
            font-weight: 700;
            text-decoration: none;
            transition: transform 0.2s ease, background-color 0.2s ease;
        }

        .department-detail-button:hover,
        .department-detail-button:focus-visible {
            transform: translateY(-1px);
            background: #c62b30;
            color: #ffffff;
        }

        .department-detail-info-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 16px;
        }

        .department-detail-info-card {
            min-width: 0;
            padding: 24px;
            border-radius: 22px;
            background: rgba(255, 255, 255, 0.96);
            border: 1px solid rgba(32, 96, 74, 0.12);
            box-shadow: 0 16px 34px rgba(17, 52, 40, 0.07);
        }

        .department-detail-info-card__title {
            margin: 0 0 12px;
            color: #14352b;
            font-size: 22px;
            line-height: 1.25;
        }

        .department-detail-info-card__text {
            margin: 0 0 14px;
            color: #355448;
            line-height: 1.65;
        }

        .department-detail-info-card__list {
            margin: 0;
            padding-left: 20px;
            color: #355448;
            line-height: 1.6;
        }

        .department-detail-info-card__list li + li {
            margin-top: 8px;
        }

        .department-detail-info-card__links {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 16px;
        }

        .department-detail-info-card__link {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 40px;
            padding: 0 16px;
            border-radius: 999px;
            background: rgba(32, 96, 74, 0.1);
            color: #15553d;
            font-size: 14px;
            font-weight: 700;
            text-decoration: none;
            transition: transform 0.2s ease, background-color 0.2s ease, color 0.2s ease;
        }

        .department-detail-info-card__link:hover,
        .department-detail-info-card__link:focus-visible {
            transform: translateY(-1px);
            background: rgba(198, 43, 48, 0.12);
            color: #c62b30;
        }

        .department-detail-docs {
            display: grid;
            gap: 14px;
        }

        .department-detail-doc-card {
            display: grid;
            grid-template-columns: 52px minmax(0, 1fr) auto;
            gap: 18px;
            align-items: center;
            padding: 22px;
            border-radius: 18px;
            background: rgba(255, 255, 255, 0.96);
            border: 1px solid rgba(32, 96, 74, 0.12);
            box-shadow: 0 14px 30px rgba(48, 56, 52, 0.08);
        }

        .department-detail-doc-card__icon {
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

        .department-detail-doc-card__title {
            display: block;
            color: #15553d;
            font-size: 17px;
            font-weight: 700;
            line-height: 1.35;
            text-decoration: none;
        }

        .department-detail-doc-card__title:hover,
        .department-detail-doc-card__title:focus-visible {
            color: #c62b30;
        }

        .department-detail-doc-card__meta {
            display: block;
            margin-top: 4px;
            color: #5f6b63;
            font-size: 14px;
            line-height: 1.35;
        }

        .department-detail-doc-card__description {
            display: block;
            margin-top: 8px;
            color: #355448;
            font-size: 15px;
            line-height: 1.5;
        }

        .department-detail-doc-card__button {
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

        .department-detail-doc-card__button:hover,
        .department-detail-doc-card__button:focus-visible {
            border-color: rgba(213, 51, 49, 0.3);
            color: #c62b30;
            transform: translateY(-1px);
        }

        @media (max-width: 1100px) {
            .department-detail-layout {
                grid-template-columns: minmax(0, 1fr);
            }

            .department-detail-layout > .section-side-menu {
                order: 2;
            }

            .department-detail-content {
                order: 1;
            }
        }

        @media (max-width: 860px) {
            .department-detail-meta {
                grid-template-columns: minmax(0, 1fr);
            }

            .department-detail-info-grid {
                grid-template-columns: minmax(0, 1fr);
            }
        }

        @media (max-width: 620px) {
            .department-detail-main {
                padding-top: calc(var(--department-detail-header-offset, var(--header-height)) + 32px);
                padding-bottom: 56px;
            }

            .department-detail-hero {
                padding: 20px;
                border-radius: 22px;
            }

            .department-detail-title-row {
                gap: 16px;
            }

            .department-detail-title-icon-shell {
                width: 58px;
                height: 58px;
                border-radius: 18px;
                flex-basis: 58px;
            }

            .department-detail-title-icon {
                width: 30px;
                height: 30px;
            }

            .department-detail-title {
                font-size: 28px;
            }

            .department-detail-section__title {
                font-size: 22px;
            }

            .department-detail-info-card,
            .department-detail-doc-card {
                padding: 18px;
            }

            .department-detail-doc-card {
                grid-template-columns: minmax(0, 1fr);
            }

            .department-detail-doc-card__button {
                width: 100%;
            }
        }
    </style>
</head>

<body class="department-detail-page">
<?php include dirname(__DIR__) . '/header.php'; ?>
<main class="main department-detail-main">
    <div class="department-detail-layout container">
        <aside class="section-side-menu" aria-label="Отделения центра">
            <h2 class="section-side-menu__title">Отделения</h2>
            <nav class="section-side-menu__nav">
                <?php foreach ($departmentItems as $menuDepartmentItem): ?>
                    <?php $isActiveDepartment = $menuDepartmentItem['id'] === $departmentId; ?>
                    <a
                        class="section-side-menu__link<?php echo $isActiveDepartment ? ' is-active' : ''; ?>"
                        href="<?php echo e($menuDepartmentItem['detail_url']); ?>"
                        <?php echo $isActiveDepartment ? 'aria-current="page"' : ''; ?>
                    ><?php echo e($menuDepartmentItem['title']); ?></a>
                <?php endforeach; ?>
            </nav>
        </aside>

        <div class="department-detail-content">
            <nav class="department-detail-breadcrumbs" aria-label="Хлебные крошки">
                <span class="department-detail-breadcrumbs__home" aria-hidden="true"></span>
                <a href="/">Главная</a>
                <span class="department-detail-breadcrumbs__separator" aria-hidden="true">›</span>
                <a href="/department/">Отделения</a>
                <span class="department-detail-breadcrumbs__separator" aria-hidden="true">›</span>
                <span><?php echo e($departmentPageTitle); ?></span>
            </nav>

            <section class="department-detail-hero" aria-labelledby="department-detail-title" style="--department-icon: url('<?php echo e($departmentPageIcon); ?>');">
                <div class="department-detail-title-row">
                    <span class="department-detail-title-icon-shell" aria-hidden="true">
                        <span class="department-detail-title-icon"></span>
                    </span>
                    <h1 class="department-detail-title" id="department-detail-title"><?php echo e($departmentPageTitle); ?></h1>
                </div>
                <p class="department-detail-lead"><?php echo e((string) $departmentItem['summary']); ?></p>
            </section>

            <div class="department-detail-meta" aria-label="Контактная информация отделения">
                <article class="department-detail-meta__item">
                    <span class="department-detail-meta__label"><?php echo e((string) $departmentItem['head_role']); ?></span>
                    <span class="department-detail-meta__value"><?php echo e((string) $departmentItem['head_name']); ?></span>
                </article>
                <article class="department-detail-meta__item">
                    <span class="department-detail-meta__label">Телефон</span>
                    <span class="department-detail-meta__value"><a href="tel:<?php echo e((string) $departmentItem['phone_href']); ?>"><?php echo e((string) $departmentItem['phone']); ?></a></span>
                </article>
                <article class="department-detail-meta__item">
                    <span class="department-detail-meta__label">Адрес</span>
                    <span class="department-detail-meta__value"><?php echo e((string) $departmentItem['address']); ?></span>
                </article>
            </div>

            <?php if ($departmentContentHtml !== ''): ?>
                <section class="department-detail-section" aria-label="Информация об отделении">
                    <div class="department-detail-source">
                        <?php echo $departmentContentHtml; ?>
                    </div>
                </section>
            <?php else: ?>
            <?php if (!empty($departmentItem['schedule'])): ?>
                <section class="department-detail-section" aria-labelledby="department-schedule-title">
                    <h2 class="department-detail-section__title" id="department-schedule-title">Режим работы</h2>
                    <ul class="department-detail-list">
                        <?php foreach ($departmentItem['schedule'] as $departmentScheduleLine): ?>
                            <li><?php echo e($departmentScheduleLine); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </section>
            <?php endif; ?>

            <?php if (!empty($departmentItem['purpose'])): ?>
                <section class="department-detail-section" aria-labelledby="department-purpose-title">
                    <h2 class="department-detail-section__title" id="department-purpose-title">Цель деятельности отделения</h2>
                    <ul class="department-detail-list">
                        <?php foreach ($departmentItem['purpose'] as $departmentPurposeLine): ?>
                            <li><?php echo e($departmentPurposeLine); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </section>
            <?php endif; ?>

            <?php if (!empty($departmentItem['activities'])): ?>
                <section class="department-detail-section" aria-labelledby="department-activities-title">
                    <h2 class="department-detail-section__title" id="department-activities-title">Направления деятельности</h2>
                    <ul class="department-detail-list">
                        <?php foreach ($departmentItem['activities'] as $departmentActivityLine): ?>
                            <li><?php echo e($departmentActivityLine); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </section>
            <?php endif; ?>

            <?php if (!empty($departmentItem['audience'])): ?>
                <section class="department-detail-section" aria-labelledby="department-audience-title">
                    <h2 class="department-detail-section__title" id="department-audience-title">Кому может быть полезно</h2>
                    <ul class="department-detail-list">
                        <?php foreach ($departmentItem['audience'] as $departmentAudienceLine): ?>
                            <li><?php echo e($departmentAudienceLine); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </section>
            <?php endif; ?>
            <?php endif; ?>

            <?php if (!empty($departmentFeatureCards)): ?>
                <section class="department-detail-section" aria-labelledby="department-feature-cards-title">
                    <h2 class="department-detail-section__title" id="department-feature-cards-title">Полезная информация</h2>
                    <div class="department-detail-info-grid">
                        <?php foreach ($departmentFeatureCards as $departmentFeatureCard): ?>
                            <article class="department-detail-info-card">
                                <?php if (!empty($departmentFeatureCard['title'])): ?>
                                    <h3 class="department-detail-info-card__title"><?php echo e((string) $departmentFeatureCard['title']); ?></h3>
                                <?php endif; ?>
                                <?php if (!empty($departmentFeatureCard['text'])): ?>
                                    <p class="department-detail-info-card__text"><?php echo e((string) $departmentFeatureCard['text']); ?></p>
                                <?php endif; ?>
                                <?php if (!empty($departmentFeatureCard['items']) && is_array($departmentFeatureCard['items'])): ?>
                                    <ul class="department-detail-info-card__list">
                                        <?php foreach ($departmentFeatureCard['items'] as $departmentFeatureCardItem): ?>
                                            <li><?php echo e((string) $departmentFeatureCardItem); ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php endif; ?>
                                <?php if (!empty($departmentFeatureCard['links']) && is_array($departmentFeatureCard['links'])): ?>
                                    <div class="department-detail-info-card__links">
                                        <?php foreach ($departmentFeatureCard['links'] as $departmentFeatureCardLink): ?>
                                            <?php if (!empty($departmentFeatureCardLink['href']) && !empty($departmentFeatureCardLink['label'])): ?>
                                                <a class="department-detail-info-card__link" href="<?php echo e((string) $departmentFeatureCardLink['href']); ?>" target="_blank" rel="noopener noreferrer"><?php echo e((string) $departmentFeatureCardLink['label']); ?></a>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </article>
                        <?php endforeach; ?>
                    </div>
                </section>
            <?php endif; ?>

            <?php if (!empty($departmentDownloadItems)): ?>
                <section class="department-detail-section" aria-labelledby="department-downloads-title">
                    <h2 class="department-detail-section__title" id="department-downloads-title">Материалы для скачивания</h2>
                    <div class="department-detail-docs">
                        <?php foreach ($departmentDownloadItems as $departmentDownloadItem): ?>
                            <?php if (!empty($departmentDownloadItem['href']) && !empty($departmentDownloadItem['title'])): ?>
                                <article class="department-detail-doc-card">
                                    <span class="department-detail-doc-card__icon" aria-hidden="true"><?php echo e((string) ($departmentDownloadItem['meta'] !== '' ? strtok((string) $departmentDownloadItem['meta'], ',') : 'FILE')); ?></span>
                                    <div>
                                        <a class="department-detail-doc-card__title" href="<?php echo e((string) $departmentDownloadItem['href']); ?>"><?php echo e((string) $departmentDownloadItem['title']); ?></a>
                                        <?php if (!empty($departmentDownloadItem['meta'])): ?>
                                            <span class="department-detail-doc-card__meta"><?php echo e((string) $departmentDownloadItem['meta']); ?></span>
                                        <?php endif; ?>
                                        <?php if (!empty($departmentDownloadItem['description'])): ?>
                                            <span class="department-detail-doc-card__description"><?php echo e((string) $departmentDownloadItem['description']); ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <a class="department-detail-doc-card__button" href="<?php echo e((string) $departmentDownloadItem['href']); ?>" download>Скачать</a>
                                </article>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                </section>
            <?php endif; ?>

            <div class="department-detail-actions">
                <a class="department-detail-button" href="/department/">Вернуться к списку отделений</a>
                <a class="department-detail-button" href="/contacts.php">Контактная информация</a>
            </div>
        </div>
    </div>
</main>
<?php include dirname(__DIR__) . '/footer.php'; ?>
<script>
    (function () {
        var root = document.documentElement;
        var header = document.querySelector('.header-down');

        if (!root || !header) {
            return;
        }

        function syncDepartmentDetailOffset() {
            root.style.setProperty('--department-detail-header-offset', header.offsetHeight + 'px');
        }

        syncDepartmentDetailOffset();
        window.addEventListener('load', syncDepartmentDetailOffset);
        window.addEventListener('resize', syncDepartmentDetailOffset);
    })();
</script>
</body>

</html>
