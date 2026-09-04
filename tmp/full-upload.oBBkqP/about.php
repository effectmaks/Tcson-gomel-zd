<?php
require_once __DIR__ . '/lib/security.php';
require_once __DIR__ . '/lib/auth.php';
require_once __DIR__ . '/lib/public_page_visibility.php';
include __DIR__ . '/db_connection.php';

$aboutPageTitle = 'О центре';
$aboutOfficialName = 'Государственное учреждение «Территориальный центр социального обслуживания населения Железнодорожного района г. Гомеля»';
$aboutAddress = '246032, Республика Беларусь, г. Гомель, ул. 50 лет БССР, д. 19';
$aboutImage = '/img/about-center-building.webp';
$seoTitleMeta = 'О центре - ТЦСОН Железнодорожного района г. Гомеля';
$seoDescriptionMeta = 'Полное наименование, адрес и основные направления деятельности ТЦСОН Железнодорожного района г. Гомеля.';

bootstrapPublicPageVisibility($conn, '/about.php', $aboutPageTitle);
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
    <title><?php echo e($seoTitleMeta); ?></title>
    <?php
    $seoScheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $seoHost = $_SERVER['HTTP_HOST'] ?? 'tcsonrw-gomel.by';
    $seoRequestUri = $_SERVER['REQUEST_URI'] ?? '/about.php';
    $seoPath = strtok($seoRequestUri, '?');
    $seoCanonical = $seoScheme . '://' . $seoHost . $seoPath;
    $seoOgImageUrl = $seoScheme . '://' . $seoHost . $aboutImage;
    ?>
    <meta name="description" content="<?php echo e($seoDescriptionMeta); ?>">
    <meta name="robots" content="index,follow">
    <link rel="canonical" href="<?php echo e($seoCanonical); ?>">
    <meta property="og:locale" content="ru_RU">
    <meta property="og:type" content="website">
    <meta property="og:title" content="<?php echo e($seoTitleMeta); ?>">
    <meta property="og:description" content="<?php echo e($seoDescriptionMeta); ?>">
    <meta property="og:url" content="<?php echo e($seoCanonical); ?>">
    <meta property="og:image" content="<?php echo e($seoOgImageUrl); ?>">
    <style>
        .about-page {
            scroll-padding-top: calc(var(--about-page-header-offset, var(--header-height)) + 54px);
        }

        .about-page-main {
            position: relative;
            isolation: isolate;
            padding-top: calc(var(--about-page-header-offset, var(--header-height)) + 54px);
            padding-bottom: 74px;
        }

        .about-page-main::before,
        .about-page-main::after {
            content: "";
            position: absolute;
            top: 0;
            bottom: -86px;
            width: clamp(0px, calc((100vw - var(--width-container)) / 2), 118px);
            background: url("/img/loop-vert.png") repeat-y center top / 100% auto;
            pointer-events: none;
            z-index: 0;
        }

        .about-page-main::before {
            left: 0;
            -webkit-mask-image: linear-gradient(270deg, transparent 0%, rgba(0, 0, 0, 0.3) 42%, rgba(0, 0, 0, 0.76) 100%);
            mask-image: linear-gradient(270deg, transparent 0%, rgba(0, 0, 0, 0.3) 42%, rgba(0, 0, 0, 0.76) 100%);
        }

        .about-page-main::after {
            right: 0;
            -webkit-mask-image: linear-gradient(90deg, transparent 0%, rgba(0, 0, 0, 0.3) 42%, rgba(0, 0, 0, 0.76) 100%);
            mask-image: linear-gradient(90deg, transparent 0%, rgba(0, 0, 0, 0.3) 42%, rgba(0, 0, 0, 0.76) 100%);
        }

        .about-page-layout {
            position: relative;
            z-index: 1;
            display: grid;
            grid-template-columns: 315px minmax(0, 1fr);
            gap: 34px;
            align-items: start;
        }

        .about-page-content {
            min-width: 0;
        }

        .about-page-breadcrumbs {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 18px;
            color: #6a766f;
            font-size: 14px;
            line-height: 1.35;
        }

        .about-page-breadcrumbs__home {
            width: 16px;
            height: 16px;
            flex: 0 0 16px;
            background-color: #20604a;
            -webkit-mask: url("/img/dom.svg") no-repeat center / contain;
            mask: url("/img/dom.svg") no-repeat center / contain;
        }

        .about-page-breadcrumbs__separator {
            color: #aab0ac;
            font-weight: 700;
        }

        .about-page-breadcrumbs a {
            color: #20604a;
            text-decoration: none;
        }

        .about-page-breadcrumbs a:hover,
        .about-page-breadcrumbs a:focus-visible {
            color: #c62b30;
        }

        .about-page-title {
            position: relative;
            margin: 0 0 28px;
            padding-left: 22px;
            color: #15553d;
            font-size: clamp(30px, 3vw, 40px);
            font-weight: 700;
            line-height: 1.12;
        }

        .about-page-title::before {
            content: "";
            position: absolute;
            left: 0;
            top: 3px;
            bottom: 5px;
            width: 5px;
            border-radius: 999px;
            background: linear-gradient(180deg, #d53331, #f06f4f);
        }

        .about-page-hero {
            overflow: hidden;
            margin-bottom: 30px;
            border: 1px solid rgba(32, 96, 74, 0.12);
            border-radius: 26px;
            background: rgba(255, 255, 255, 0.84);
            box-shadow: 0 18px 40px rgba(36, 62, 51, 0.09);
        }

        .about-page-photo {
            position: relative;
            margin: 0;
            aspect-ratio: 16 / 9;
            overflow: hidden;
            background: #e8eee8;
        }

        .about-page-photo::after {
            content: "";
            position: absolute;
            inset: auto 0 0;
            height: 34%;
            background: linear-gradient(180deg, transparent, rgba(9, 53, 36, 0.18));
            pointer-events: none;
        }

        .about-page-photo img {
            display: block;
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .about-page-identity {
            display: grid;
            grid-template-columns: minmax(0, 1.35fr) minmax(250px, 0.65fr);
            gap: 18px;
            padding: 24px;
        }

        .about-page-identity-card {
            min-width: 0;
            padding: 20px 22px;
            border-radius: 18px;
            background: linear-gradient(145deg, #fffdfb, #f7f8ef);
            box-shadow: inset 0 0 0 1px rgba(32, 96, 74, 0.08);
        }

        .about-page-identity-label {
            display: block;
            margin-bottom: 9px;
            color: #718178;
            font-size: 13px;
            font-weight: 700;
            letter-spacing: 0.08em;
            line-height: 1.3;
            text-transform: uppercase;
        }

        .about-page-identity-value {
            display: block;
            color: #153f31;
            font-size: 17px;
            font-weight: 700;
            line-height: 1.48;
        }

        .about-page-activity {
            padding: 6px 0 0;
        }

        .about-page-activity-title {
            margin: 0 0 24px;
            color: #15553d;
            font-size: clamp(24px, 2.5vw, 31px);
            font-weight: 700;
            line-height: 1.2;
        }

        .about-page-activity-list {
            display: grid;
            gap: 0;
            margin: 0;
            padding: 0;
            list-style: none;
        }

        .about-page-activity-item {
            position: relative;
            padding: 0 0 18px 32px;
            color: #31473e;
            font-size: 16px;
            font-weight: 500;
            line-height: 1.58;
        }

        .about-page-activity-item::before {
            content: "";
            position: absolute;
            top: 6px;
            left: 2px;
            width: 15px;
            height: 15px;
            background-color: #c62b30;
            -webkit-mask: url("/img/icon-arnament.svg") no-repeat center / contain;
            mask: url("/img/icon-arnament.svg") no-repeat center / contain;
        }

        .about-page-activity-link {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            margin-left: 7px;
            color: #1c6a4d;
            font-weight: 700;
            text-decoration: none;
            white-space: nowrap;
        }

        .about-page-activity-link::after {
            content: "→";
        }

        .about-page-activity-link:hover,
        .about-page-activity-link:focus-visible {
            color: #c62b30;
        }

        .about-page-closing {
            margin: 22px 0 0;
            padding: 18px 20px;
            border-left: 5px solid #d53331;
            border-radius: 0 14px 14px 0;
            background: #f8f2ec;
            color: #29483c;
            font-size: 16px;
            font-weight: 650;
            line-height: 1.55;
        }

        @media (max-width: 1080px) {
            .about-page-layout {
                grid-template-columns: 270px minmax(0, 1fr);
                gap: 26px;
            }

            .about-page-identity {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 850px) {
            .about-page-main {
                padding-top: calc(var(--about-page-header-offset, var(--header-height)) + 30px);
                padding-bottom: 56px;
            }

            .about-page-layout {
                grid-template-columns: 1fr;
            }

            .about-page-layout > .section-side-menu {
                position: static;
            }
        }

        @media (max-width: 620px) {
            .about-page-main {
                padding-top: calc(var(--about-page-header-offset, var(--header-height)) + 22px);
            }

            .about-page-layout {
                gap: 22px;
            }

            .about-page-title {
                margin-bottom: 20px;
                padding-left: 18px;
                font-size: 29px;
            }

            .about-page-hero {
                border-radius: 20px;
            }

            .about-page-identity {
                gap: 12px;
                padding: 16px;
            }

            .about-page-identity-card {
                padding: 17px;
            }

            .about-page-identity-value {
                font-size: 15px;
            }

            .about-page-activity {
                padding-top: 4px;
            }

            .about-page-activity-title {
                margin-bottom: 18px;
                font-size: 24px;
            }

            .about-page-activity-item {
                padding: 0 0 17px 29px;
                font-size: 15px;
            }

            .about-page-activity-item::before {
                top: 5px;
                left: 2px;
                width: 14px;
                height: 14px;
            }
        }
    </style>
</head>

<body class="about-page">
<?php include __DIR__ . '/header.php'; ?>
<main class="main about-page-main">
    <div class="about-page-layout container">
        <?php
        $aboutMenuActive = 'about';
        include __DIR__ . '/about-side-menu.php';
        ?>

        <div class="about-page-content">
            <nav class="about-page-breadcrumbs" aria-label="Хлебные крошки">
                <span class="about-page-breadcrumbs__home" aria-hidden="true"></span>
                <a href="/">Главная</a>
                <span class="about-page-breadcrumbs__separator" aria-hidden="true">›</span>
                <span><?php echo e($aboutPageTitle); ?></span>
            </nav>

            <h1 class="about-page-title"><?php echo e($aboutPageTitle); ?></h1>

            <section class="about-page-hero" aria-label="Информация об учреждении">
                <figure class="about-page-photo">
                    <img
                        src="<?php echo e($aboutImage); ?>"
                        alt="Здание Территориального центра социального обслуживания населения Железнодорожного района г. Гомеля"
                        width="1280"
                        height="720"
                    >
                </figure>
                <div class="about-page-identity">
                    <article class="about-page-identity-card">
                        <span class="about-page-identity-label">Полное наименование</span>
                        <strong class="about-page-identity-value"><?php echo e($aboutOfficialName); ?></strong>
                    </article>
                    <article class="about-page-identity-card">
                        <span class="about-page-identity-label">Адрес</span>
                        <strong class="about-page-identity-value"><?php echo e($aboutAddress); ?></strong>
                    </article>
                </div>
            </section>

            <section class="about-page-activity" aria-labelledby="about-page-activity-title">
                <h2 class="about-page-activity-title" id="about-page-activity-title">Деятельность центра направлена на:</h2>
                <ul class="about-page-activity-list">
                    <li class="about-page-activity-item">
                        предоставление государственной адресной социальной помощи (ГАСП);
                        <a class="about-page-activity-link" href="/department/social-support/">перейти</a>
                    </li>
                    <li class="about-page-activity-item">
                        обеспечение граждан техническими средствами социальной реабилитации, оказание содействия в их получении;
                    </li>
                    <li class="about-page-activity-item">организацию комплексного социального обслуживания населения на территории района путем оказания своевременной и квалифицированной психологической, юридической, экономической, реабилитационной и иной помощи социально-незащищенным слоям населения, гражданам, находящимся в тяжелой жизненной ситуации;</li>
                    <li class="about-page-activity-item">участие в отслеживании социально-демографической ситуации и разработке предложений по совершенствованию социального обслуживания населения на территории соответствующей административно-территориальной единицы;</li>
                    <li class="about-page-activity-item">дифференцированный (по категориям) учет граждан, находящихся в трудной жизненной ситуации, определение необходимых им форм социального обслуживания, видов социальных услуг;</li>
                    <li class="about-page-activity-item">оказание содействия гражданам, находящимся в трудной жизненной ситуации, в преодолении трудной жизненной ситуации и (или) адаптации к ней;</li>
                    <li class="about-page-activity-item">осуществление экспериментальной и инновационной деятельности в сфере социального обслуживания;</li>
                    <li class="about-page-activity-item">проведение информационно-просветительной работы по актуальным вопросам социального обслуживания на территории соответствующей административно-территориальной единицы;</li>
                    <li class="about-page-activity-item">привлечение к сотрудничеству волонтеров для оказания социальных услуг гражданам, находящимся в трудной ситуации;</li>
                    <li class="about-page-activity-item">сотрудничество с организациями различных форм собственности и индивидуальными предпринимателями по вопросам социального обслуживания населения в пределах своей компетенции;</li>
                    <li class="about-page-activity-item">подготовку методических материалов на основе практической деятельности центра;</li>
                    <li class="about-page-activity-item">изучение, обобщение и внедрение в практику лучшего отечественного и зарубежного опыта социального обслуживания;</li>
                    <li class="about-page-activity-item">составление и анализ ведомственной и другой отчетности по направлениям деятельности центра;</li>
                    <li class="about-page-activity-item">подготовку аналитических материалов по вопросам социального обслуживания;</li>
                    <li class="about-page-activity-item">проведение мероприятий по повышению профессионального уровня работников центра;</li>
                    <li class="about-page-activity-item">
                        осуществление функций по опеке и попечительству в отношении совершеннолетних лиц и их имущества.
                        <a class="about-page-activity-link" href="/department/guardianship/">перейти</a>
                    </li>
                </ul>
                <p class="about-page-closing">Центр вправе осуществлять иные функции в соответствии с законодательством.</p>
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

        function syncAboutPageOffset() {
            root.style.setProperty('--about-page-header-offset', header.offsetHeight + 'px');
        }

        syncAboutPageOffset();
        window.addEventListener('load', syncAboutPageOffset, { once: true });
        window.addEventListener('resize', syncAboutPageOffset);
    }());
</script>
</body>
</html>
