<?php
require_once dirname(__DIR__) . '/lib/security.php';
require_once dirname(__DIR__) . '/lib/auth.php';
require_once dirname(__DIR__) . '/lib/public_page_visibility.php';
require_once __DIR__ . '/data.php';
include dirname(__DIR__) . '/db_connection.php';

$departmentCenterInfo = getDepartmentCenterInfo();
$departmentPageTitle = $departmentCenterInfo['page_title'];
$departmentSiteName = $departmentCenterInfo['site_name'];
$departmentCenterAddress = $departmentCenterInfo['address'];
$departmentCenterPhone = $departmentCenterInfo['phone'];
$departmentCenterPhoneHref = $departmentCenterInfo['phone_href'];
$departmentCenterEmail = $departmentCenterInfo['email'];
$departmentDirector = $departmentCenterInfo['director'];
$departmentIntroParagraphs = getDepartmentIntroParagraphs();
$departmentGoals = getDepartmentGoals();
$departmentPageLinks = getDepartmentPageLinks();
$departmentItems = getDepartmentItems();

$seoTitleMeta = 'Отделения - ТЦСОН Железнодорожного района г. Гомеля';
$seoDescriptionMeta = 'Отделения ТЦСОН Железнодорожного района г. Гомеля: направления работы, руководители, телефоны и адреса подразделений.';

bootstrapPublicPageVisibility($conn, '/department/', $departmentPageTitle);
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
    <title><?php echo e($departmentPageTitle); ?> - <?php echo e($departmentSiteName); ?></title>
    <?php
    $seoScheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $seoHost = $_SERVER['HTTP_HOST'] ?? 'tcsonrw-gomel.by';
    $seoCanonical = $seoScheme . '://' . $seoHost . '/department/';
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
        .department-page-main {
            position: relative;
            isolation: isolate;
            padding-top: calc(var(--department-page-header-offset, var(--header-height)) + 54px);
            padding-bottom: 74px;
        }

        .department-page-main::before,
        .department-page-main::after {
            content: "";
            position: absolute;
            top: 0;
            bottom: -88px;
            width: clamp(0px, calc((100vw - var(--width-container)) / 2), 118px);
            background: url("/img/loop-vert.png") repeat-y center top / 100% auto;
            pointer-events: none;
            z-index: 0;
        }

        .department-page-main::before {
            left: 0;
            -webkit-mask-image: linear-gradient(270deg, transparent 0%, rgba(0, 0, 0, 0.3) 42%, rgba(0, 0, 0, 0.76) 100%);
            mask-image: linear-gradient(270deg, transparent 0%, rgba(0, 0, 0, 0.3) 42%, rgba(0, 0, 0, 0.76) 100%);
        }

        .department-page-main::after {
            right: 0;
            -webkit-mask-image: linear-gradient(90deg, transparent 0%, rgba(0, 0, 0, 0.3) 42%, rgba(0, 0, 0, 0.76) 100%);
            mask-image: linear-gradient(90deg, transparent 0%, rgba(0, 0, 0, 0.3) 42%, rgba(0, 0, 0, 0.76) 100%);
        }

        .department-page-layout {
            position: relative;
            z-index: 1;
            display: grid;
            grid-template-columns: 315px minmax(0, 1fr);
            gap: 36px;
            align-items: start;
        }

        .department-page-sidebar {
            display: grid;
            gap: 18px;
            align-self: start;
        }

        .department-page-content {
            min-width: 0;
        }

        .department-page-breadcrumbs {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 18px;
            color: #6a766f;
            font-size: 14px;
            line-height: 1.35;
        }

        .department-page-breadcrumbs__home {
            width: 16px;
            height: 16px;
            flex: 0 0 16px;
            background-color: #20604a;
            -webkit-mask: url("/img/dom.svg") no-repeat center / contain;
            mask: url("/img/dom.svg") no-repeat center / contain;
        }

        .department-page-breadcrumbs__separator {
            color: #aab0ac;
            font-weight: 700;
        }

        .department-page-breadcrumbs a {
            color: #20604a;
            text-decoration: none;
        }

        .department-page-breadcrumbs a:hover,
        .department-page-breadcrumbs a:focus-visible {
            color: #c62b30;
        }

        .department-page-title {
            margin: 0 0 18px;
            font-size: clamp(32px, 4vw, 42px);
            line-height: 1.08;
            color: #14352b;
        }

        .department-page-hero {
            display: grid;
            gap: 22px;
            margin-bottom: 32px;
        }

        .department-page-hero__shell {
            position: relative;
            overflow: hidden;
            border-radius: 30px;
            padding: 28px;
            background: linear-gradient(135deg, rgba(26, 84, 64, 0.08), rgba(214, 181, 107, 0.2));
            border: 1px solid rgba(32, 96, 74, 0.14);
            box-shadow: 0 22px 44px rgba(17, 52, 40, 0.08);
        }

        .department-page-hero__shell::after {
            content: "";
            position: absolute;
            right: -48px;
            top: -48px;
            width: 180px;
            height: 180px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(198, 43, 48, 0.14), rgba(198, 43, 48, 0));
            pointer-events: none;
        }

        .department-page-hero__content {
            position: relative;
            z-index: 1;
            display: grid;
            gap: 18px;
        }

        .department-page-hero__lead {
            max-width: 760px;
            margin: 0;
            color: #355448;
            font-size: 17px;
            line-height: 1.65;
        }

        .department-page-hero__director {
            display: inline-flex;
            align-items: center;
            gap: 14px;
            width: fit-content;
            padding: 14px 18px;
            border-radius: 18px;
            background: rgba(255, 255, 255, 0.86);
            box-shadow: 0 14px 28px rgba(17, 52, 40, 0.08);
        }

        .department-page-hero__director-icon {
            width: 52px;
            height: 52px;
            border-radius: 16px;
            background: linear-gradient(135deg, #20604a, #3b8e6b);
            position: relative;
            flex: 0 0 52px;
        }

        .department-page-hero__director-icon::before {
            content: "";
            position: absolute;
            inset: 12px;
            background: #ffffff;
            -webkit-mask: url("/img/cabinet.svg") no-repeat center / contain;
            mask: url("/img/cabinet.svg") no-repeat center / contain;
        }

        .department-page-hero__director-role {
            display: block;
            color: #6b7c73;
            font-size: 12px;
            line-height: 1.4;
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }

        .department-page-hero__director-name {
            display: block;
            color: #14352b;
            font-size: 18px;
            font-weight: 700;
            line-height: 1.35;
        }

        .department-page-facts {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 16px;
        }

        .department-page-fact {
            padding: 18px 20px;
            border-radius: 22px;
            background: #ffffff;
            border: 1px solid rgba(32, 96, 74, 0.12);
            box-shadow: 0 14px 30px rgba(17, 52, 40, 0.06);
        }

        .department-page-fact__label {
            display: block;
            margin-bottom: 8px;
            color: #6c7c74;
            font-size: 12px;
            line-height: 1.4;
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }

        .department-page-fact__value,
        .department-page-fact__value a {
            color: #14352b;
            font-size: 16px;
            font-weight: 600;
            line-height: 1.5;
            text-decoration: none;
        }

        .department-page-section {
            margin-bottom: 32px;
        }

        .department-page-section__title {
            margin: 0 0 16px;
            color: #14352b;
            font-size: 28px;
            line-height: 1.18;
        }

        .department-page-section__text {
            margin: 0 0 14px;
            color: #385447;
            font-size: 16px;
            line-height: 1.7;
        }

        .department-page-goals {
            display: grid;
            gap: 12px;
            padding: 0;
            margin: 0;
            list-style: none;
        }

        .department-page-goals li {
            position: relative;
            padding: 16px 18px 16px 52px;
            border-radius: 18px;
            background: rgba(255, 255, 255, 0.92);
            border: 1px solid rgba(32, 96, 74, 0.12);
            color: #385447;
            line-height: 1.6;
        }

        .department-page-goals li::before {
            content: "";
            position: absolute;
            left: 18px;
            top: 18px;
            width: 20px;
            height: 20px;
            background: #20604a;
            -webkit-mask: url("/img/glaz.svg") no-repeat center / contain;
            mask: url("/img/glaz.svg") no-repeat center / contain;
        }

        .department-page-links {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin-top: 18px;
        }

        .department-page-links a {
            display: inline-flex;
            align-items: center;
            min-height: 42px;
            padding: 0 18px;
            border-radius: 999px;
            background: #ffffff;
            border: 1px solid rgba(32, 96, 74, 0.14);
            color: #1f6049;
            text-decoration: none;
            font-weight: 600;
            transition: transform 0.2s ease, border-color 0.2s ease, color 0.2s ease;
        }

        .department-page-links a:hover,
        .department-page-links a:focus-visible {
            transform: translateY(-1px);
            border-color: rgba(198, 43, 48, 0.28);
            color: #c62b30;
        }

        .department-page-departments {
            display: grid;
            gap: 18px;
        }

        .department-page-card {
            scroll-margin-top: calc(var(--department-page-header-offset, var(--header-height)) + 34px);
            padding: 24px;
            border-radius: 26px;
            background: #ffffff;
            border: 1px solid rgba(32, 96, 74, 0.12);
            box-shadow: 0 18px 36px rgba(17, 52, 40, 0.08);
        }

        .department-page-card__header {
            display: grid;
            gap: 10px;
            margin-bottom: 18px;
        }

        .department-page-card__title-row {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .department-page-card__icon-shell {
            width: 58px;
            height: 58px;
            border-radius: 18px;
            background: linear-gradient(180deg, #f8f8ef 0%, #f0f1e4 100%);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex: 0 0 58px;
        }

        .department-page-card__icon {
            width: 30px;
            height: 30px;
            background-color: #1f5f47;
            -webkit-mask: var(--department-icon) no-repeat center / contain;
            mask: var(--department-icon) no-repeat center / contain;
            opacity: 0.96;
        }

        .department-page-card__title {
            margin: 0;
            color: #14352b;
            font-size: 24px;
            line-height: 1.22;
        }

        .department-page-card__summary {
            margin: 0;
            color: #406055;
            font-size: 16px;
            line-height: 1.65;
        }

        .department-page-card__meta {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 14px;
            margin-bottom: 18px;
        }

        .department-page-card__meta-item {
            min-width: 0;
            padding: 16px 18px;
            border-radius: 18px;
            background: rgba(242, 247, 244, 0.96);
        }

        .department-page-card__meta-label {
            display: block;
            margin-bottom: 6px;
            color: #6e7f76;
            font-size: 12px;
            line-height: 1.4;
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }

        .department-page-card__meta-value,
        .department-page-card__meta-value a {
            color: #14352b;
            font-size: 15px;
            line-height: 1.6;
            text-decoration: none;
        }

        .department-page-card__highlights {
            display: grid;
            gap: 10px;
            padding: 0;
            margin: 0;
            list-style: none;
        }

        .department-page-card__highlights li {
            position: relative;
            padding-left: 18px;
            color: #406055;
            line-height: 1.6;
        }

        .department-page-card__highlights li::before {
            content: "";
            position: absolute;
            left: 0;
            top: 10px;
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #c62b30;
        }

        .department-page-card__actions {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin-top: 20px;
        }

        .department-page-card__button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 44px;
            padding: 0 20px;
            border-radius: 999px;
            background: #20604a;
            color: #ffffff;
            font-weight: 700;
            line-height: 1.2;
            text-decoration: none;
            box-shadow: 0 12px 24px rgba(32, 96, 74, 0.18);
            transition: transform 0.2s ease, background-color 0.2s ease, box-shadow 0.2s ease;
        }

        .department-page-card__button:hover,
        .department-page-card__button:focus-visible {
            transform: translateY(-1px);
            background: #c62b30;
            color: #ffffff;
            box-shadow: 0 14px 28px rgba(198, 43, 48, 0.2);
        }

        @media (max-width: 1100px) {
            .department-page-layout {
                grid-template-columns: minmax(0, 1fr);
            }

            .department-page-sidebar {
                order: 2;
            }

            .department-page-content {
                order: 1;
            }
        }

        @media (max-width: 860px) {
            .department-page-facts,
            .department-page-card__meta {
                grid-template-columns: minmax(0, 1fr);
            }
        }

        @media (max-width: 620px) {
            .department-page-main {
                padding-top: calc(var(--department-page-header-offset, var(--header-height)) + 32px);
                padding-bottom: 56px;
            }

            .department-page-title {
                font-size: 30px;
            }

            .department-page-hero__shell,
            .department-page-card {
                padding: 20px;
                border-radius: 22px;
            }

            .department-page-card__title-row {
                gap: 14px;
            }

            .department-page-card__icon-shell {
                width: 52px;
                height: 52px;
                border-radius: 16px;
                flex-basis: 52px;
            }

            .department-page-card__icon {
                width: 28px;
                height: 28px;
            }

            .department-page-section__title,
            .department-page-card__title {
                font-size: 22px;
            }
        }
    </style>
</head>

<body class="department-page">
<?php include dirname(__DIR__) . '/header.php'; ?>
<main class="main department-page-main">
    <div class="department-page-layout container">
        <div class="department-page-sidebar">
            <aside class="section-side-menu" aria-label="Быстрый переход по отделениям">
                <h2 class="section-side-menu__title">Отделения</h2>
                <nav class="section-side-menu__nav">
                    <?php foreach ($departmentItems as $departmentItem): ?>
                        <a class="section-side-menu__link" href="#<?php echo e($departmentItem['id']); ?>"><?php echo e($departmentItem['title']); ?></a>
                    <?php endforeach; ?>
                </nav>
            </aside>
        </div>

        <div class="department-page-content">
            <nav class="department-page-breadcrumbs" aria-label="Хлебные крошки">
                <span class="department-page-breadcrumbs__home" aria-hidden="true"></span>
                <a href="/">Главная</a>
                <span class="department-page-breadcrumbs__separator" aria-hidden="true">›</span>
                <span><?php echo e($departmentPageTitle); ?></span>
            </nav>

            <section class="department-page-hero" aria-labelledby="department-page-title">
                <div class="department-page-hero__shell">
                    <div class="department-page-hero__content">
                        <h1 class="department-page-title" id="department-page-title"><?php echo e($departmentPageTitle); ?></h1>
                        <p class="department-page-hero__lead">Центр объединяет профильные отделения, которые обеспечивают первичный прием, социальную поддержку, помощь на дому, кризисное сопровождение, программы активного долголетия и реабилитационные услуги для жителей Железнодорожного района г. Гомеля.</p>
                        <div class="department-page-hero__director">
                            <span class="department-page-hero__director-icon" aria-hidden="true"></span>
                            <div>
                                <span class="department-page-hero__director-role"><?php echo e($departmentDirector['role']); ?></span>
                                <span class="department-page-hero__director-name"><?php echo e($departmentDirector['name']); ?></span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="department-page-facts" aria-label="Ключевая информация">
                    <article class="department-page-fact">
                        <span class="department-page-fact__label">Основной адрес</span>
                        <span class="department-page-fact__value"><?php echo e($departmentCenterAddress); ?></span>
                    </article>
                    <article class="department-page-fact">
                        <span class="department-page-fact__label">Телефон</span>
                        <span class="department-page-fact__value"><a href="tel:<?php echo e($departmentCenterPhoneHref); ?>"><?php echo e($departmentCenterPhone); ?></a></span>
                    </article>
                    <article class="department-page-fact">
                        <span class="department-page-fact__label">E-mail</span>
                        <span class="department-page-fact__value"><a href="mailto:<?php echo e($departmentCenterEmail); ?>"><?php echo e($departmentCenterEmail); ?></a></span>
                    </article>
                </div>
            </section>

            <section class="department-page-section" aria-labelledby="department-about-title">
                <h2 class="department-page-section__title" id="department-about-title">О направлениях работы</h2>
                <?php foreach ($departmentIntroParagraphs as $departmentIntroParagraph): ?>
                    <p class="department-page-section__text"><?php echo e($departmentIntroParagraph); ?></p>
                <?php endforeach; ?>

                <ul class="department-page-goals" aria-label="Цели деятельности центра">
                    <?php foreach ($departmentGoals as $departmentGoal): ?>
                        <li><?php echo e($departmentGoal); ?></li>
                    <?php endforeach; ?>
                </ul>

                <div class="department-page-links" aria-label="Полезные ссылки раздела">
                    <?php foreach ($departmentPageLinks as $departmentPageLink): ?>
                        <a href="<?php echo e($departmentPageLink['href']); ?>"><?php echo e($departmentPageLink['label']); ?></a>
                    <?php endforeach; ?>
                </div>
            </section>

            <section class="department-page-section" aria-labelledby="department-list-title">
                <h2 class="department-page-section__title" id="department-list-title">Отделения центра</h2>
                <div class="department-page-departments">
                    <?php foreach ($departmentItems as $departmentItem): ?>
                        <article class="department-page-card" id="<?php echo e($departmentItem['id']); ?>">
                            <?php $departmentCardIcon = getDepartmentIconPath((string) ($departmentItem['id'] ?? '')); ?>
                            <div class="department-page-card__header">
                                <div class="department-page-card__title-row" style="--department-icon: url('<?php echo e($departmentCardIcon); ?>');">
                                    <span class="department-page-card__icon-shell" aria-hidden="true">
                                        <span class="department-page-card__icon"></span>
                                    </span>
                                    <h3 class="department-page-card__title"><?php echo e($departmentItem['title']); ?></h3>
                                </div>
                                <p class="department-page-card__summary"><?php echo e($departmentItem['summary']); ?></p>
                            </div>

                            <div class="department-page-card__meta">
                                <div class="department-page-card__meta-item">
                                    <span class="department-page-card__meta-label"><?php echo e($departmentItem['head_role']); ?></span>
                                    <span class="department-page-card__meta-value"><?php echo e($departmentItem['head_name']); ?></span>
                                </div>
                                <div class="department-page-card__meta-item">
                                    <span class="department-page-card__meta-label">Контакты</span>
                                    <span class="department-page-card__meta-value">
                                        <?php if (isset($departmentItem['phones']) && is_array($departmentItem['phones']) && $departmentItem['phones'] !== array()): ?>
                                            <?php foreach ($departmentItem['phones'] as $departmentPhoneIndex => $departmentPhoneItem): ?>
                                                <?php if ($departmentPhoneIndex > 0): ?>, <?php endif; ?><a href="tel:<?php echo e($departmentPhoneItem['href']); ?>"><?php echo e($departmentPhoneItem['label']); ?></a>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <a href="tel:<?php echo e($departmentItem['phone_href']); ?>"><?php echo e($departmentItem['phone']); ?></a>
                                        <?php endif; ?>
                                        <br>
                                        <?php echo e($departmentItem['address']); ?>
                                    </span>
                                </div>
                            </div>

                            <ul class="department-page-card__highlights">
                                <?php foreach ($departmentItem['highlights'] as $departmentHighlight): ?>
                                    <li><?php echo e($departmentHighlight); ?></li>
                                <?php endforeach; ?>
                            </ul>

                            <div class="department-page-card__actions">
                                <a class="department-page-card__button" href="<?php echo e($departmentItem['detail_url']); ?>">Подробнее</a>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            </section>
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

        function syncDepartmentPageOffset() {
            root.style.setProperty('--department-page-header-offset', header.offsetHeight + 'px');
        }

        syncDepartmentPageOffset();
        window.addEventListener('load', syncDepartmentPageOffset);
        window.addEventListener('resize', syncDepartmentPageOffset);
    })();
</script>
</body>

</html>
