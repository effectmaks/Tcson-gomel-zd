<?php
require_once __DIR__ . '/lib/security.php';
require_once __DIR__ . '/lib/auth.php';
require_once __DIR__ . '/lib/public_page_visibility.php';
include __DIR__ . '/db_connection.php';

function formatDocumentFileSize($path)
{
    if (!is_file($path)) {
        return '';
    }

    $bytes = filesize($path);
    if ($bytes === false) {
        return '';
    }

    if ($bytes >= 1048576) {
        return str_replace('.', ',', (string) round($bytes / 1048576, 1)) . ' МБ';
    }

    return (string) max(1, (int) ceil($bytes / 1024)) . ' КБ';
}

$pageTitle = 'Дополнительное накопительное пенсионное страхование';
$pageSiteName = 'ТЦСОН Железнодорожного района г. Гомеля';
$pageDocumentHref = '/documents/stravita-dlya-sayta.docx';
$pageDocumentPath = __DIR__ . $pageDocumentHref;
$pageDocumentSize = formatDocumentFileSize($pageDocumentPath);
$pageBannerHref = '/img/banner-nakopitelnaya-pensiya-stravita.png';
$pageOfficialHref = 'https://www.ssf.gov.by/ru/dobrovolnoe-strahovanie-dopolnitelnoj-nakopitelnoj-pensii-ru';

$pageLead = 'С 1 октября 2022 года Указом Президента Республики Беларусь от 27 сентября 2021 года № 367 введен дополнительный вид пенсионного страхования: добровольное дополнительное накопительное пенсионное страхование с финансовой поддержкой государства по программе «3+3».';
$pageDescription = 'Программа «3+3» помогает работающим гражданам заранее сформировать дополнительный источник пенсионных выплат при личном участии и софинансировании со стороны государства.';
$pageFacts = array(
    'Принять участие могут работающие граждане до достижения общеустановленного пенсионного возраста.',
    'Общеустановленный пенсионный возраст: женщины до 58 лет, мужчины до 63 лет.',
    'Личные накопления формируются с привлечением средств бюджета государственного внебюджетного фонда социальной защиты населения Республики Беларусь.',
    'Итог программы - дополнительный источник пенсионных выплат помимо основной пенсии.',
);

$seoTitleMeta = $pageTitle . ' - ' . $pageSiteName;
$seoDescriptionMeta = 'Информация о добровольном дополнительном накопительном пенсионном страховании с финансовой поддержкой государства по программе «3+3».';
$seoOgImage = $pageBannerHref;

bootstrapPublicPageVisibility($conn, '/nakopitelnaya-pensiya.php', $pageTitle);
?>
<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="format-detection" content="telephone=no">
    <link rel="icon" href="/img/favicon.png" type="image/png" sizes="120x120">
    <link rel="stylesheet" href="/css/cssbootstrap.min.css?v=<?= filemtime($_SERVER['DOCUMENT_ROOT'] . '/css/cssbootstrap.min.css') ?>">
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
    $seoRequestUri = $_SERVER['REQUEST_URI'] ?? '/nakopitelnaya-pensiya.php';
    $seoPath = strtok($seoRequestUri, '?');
    $seoCanonical = $seoScheme . '://' . $seoHost . $seoPath;
    $seoOgImageUrl = preg_match('#^https?://#i', $seoOgImage)
        ? $seoOgImage
        : ($seoScheme . '://' . $seoHost . $seoOgImage);
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
        .pension-page {
            scroll-padding-top: calc(var(--pension-header-offset, var(--header-height)) + 42px);
        }

        .pension-main {
            padding-top: calc(var(--pension-header-offset, var(--header-height)) + 42px);
            padding-bottom: 72px;
        }

        .pension-breadcrumbs {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 22px;
            color: #6a766f;
            font-size: 14px;
            line-height: 1.35;
        }

        .pension-breadcrumbs__home {
            width: 16px;
            height: 16px;
            flex: 0 0 16px;
            background-color: #20604a;
            -webkit-mask: url("/img/dom.svg") no-repeat center / contain;
            mask: url("/img/dom.svg") no-repeat center / contain;
        }

        .pension-breadcrumbs a {
            color: #20604a;
            text-decoration: none;
        }

        .pension-breadcrumbs a:hover,
        .pension-breadcrumbs a:focus-visible {
            color: #c62b30;
        }

        .pension-breadcrumbs__separator {
            color: #aab0ac;
            font-weight: 700;
        }

        .pension-title {
            position: relative;
            margin: 0 0 28px;
            padding-left: 20px;
            color: #15553d;
            font-size: 28px;
            font-weight: 700;
            line-height: 1.2;
        }

        .pension-title::before {
            content: "";
            position: absolute;
            top: 3px;
            bottom: 3px;
            left: 0;
            width: 5px;
            border-radius: 999px;
            background: #d53331;
        }

        .pension-layout {
            display: grid;
            gap: 24px;
        }

        .pension-layout__footer {
            margin-top: 0;
        }

        .pension-hero {
            display: grid;
            grid-template-columns: minmax(0, 1.08fr) minmax(320px, 0.92fr);
            gap: 28px;
            align-items: center;
            padding: 28px;
            border: 1px solid rgba(32, 96, 74, 0.12);
            border-radius: 28px;
            background: linear-gradient(180deg, rgba(255, 255, 255, 0.99) 0%, rgba(247, 251, 248, 0.99) 100%);
            box-shadow: 0 16px 36px rgba(48, 56, 52, 0.08);
        }

        .pension-hero__eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 14px;
            color: #20604a;
            font-size: 16px;
            font-weight: 700;
            line-height: 1.4;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        .pension-hero__eyebrow::before {
            content: "";
            width: 32px;
            height: 2px;
            border-radius: 999px;
            background: #d53331;
        }

        .pension-hero__lead {
            margin: 0 0 18px;
            color: #355448;
            font-size: 16px;
            line-height: 1.7;
        }

        .pension-hero__actions {
            display: flex;
            flex-wrap: wrap;
            gap: 14px;
            margin-top: 24px;
        }

        .pension-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 46px;
            padding: 0 22px;
            border: 1px solid #15553d;
            border-radius: var(--button-radius);
            background: #15553d;
            color: #fff;
            font-size: 16px;
            font-weight: 700;
            line-height: 1;
            text-decoration: none;
            transition: border-color .2s ease, background-color .2s ease, transform .2s ease;
        }

        .pension-button:hover,
        .pension-button:focus-visible {
            border-color: #c62b30;
            background: #c62b30;
            color: #fff;
            transform: translateY(-1px);
        }

        .pension-button--secondary {
            background: rgba(255, 255, 255, 0.98);
            color: #15553d;
        }

        .pension-hero__visual {
            display: block;
            overflow: hidden;
            border-radius: 22px;
            border: 1px solid rgba(32, 96, 74, 0.12);
            box-shadow: 0 14px 26px rgba(48, 56, 52, 0.12);
        }

        .pension-hero__visual img {
            display: block;
            width: 100%;
            height: auto;
        }

        .pension-grid {
            display: grid;
            grid-template-columns: minmax(0, 1fr) minmax(300px, 0.72fr);
            gap: 24px;
        }

        .pension-panel {
            padding: 28px;
            border: 1px solid rgba(32, 96, 74, 0.12);
            border-radius: 24px;
            background: rgba(255, 255, 255, 0.97);
            box-shadow: 0 14px 32px rgba(48, 56, 52, 0.08);
        }

        .pension-panel__title {
            margin: 0 0 16px;
            color: #15553d;
            font-size: 18px;
            font-weight: 700;
            line-height: 1.35;
        }

        .pension-panel__text {
            margin: 0;
            color: #355448;
            font-size: 16px;
            line-height: 1.7;
        }

        .pension-facts {
            margin: 0;
            padding: 0;
            list-style: none;
            display: grid;
            gap: 14px;
        }

        .pension-facts li {
            position: relative;
            padding-left: 18px;
            color: #355448;
            font-size: 16px;
            line-height: 1.65;
        }

        .pension-facts li::before {
            content: "";
            position: absolute;
            top: 10px;
            left: 0;
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #d53331;
        }

        .pension-document {
            display: grid;
            grid-template-columns: 52px minmax(0, 1fr);
            gap: 18px;
            align-items: start;
            margin-top: 24px;
            padding: 20px 22px;
            border: 1px solid rgba(32, 96, 74, 0.12);
            border-radius: 18px;
            background: #f7faf7;
        }

        .pension-document__icon {
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

        .pension-document__title {
            display: block;
            color: #15553d;
            font-size: 18px;
            font-weight: 700;
            line-height: 1.35;
            text-decoration: none;
        }

        .pension-document__title:hover,
        .pension-document__title:focus-visible {
            color: #c62b30;
        }

        .pension-document__meta {
            display: block;
            margin-top: 5px;
            color: #5f6b63;
            font-size: 14px;
            line-height: 1.35;
        }

        .pension-document__actions {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin-top: 16px;
        }

        @media (max-width: 991px) {
            .pension-hero,
            .pension-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 767px) {
            .pension-main {
                padding-top: calc(var(--pension-header-offset, var(--header-height)) + 34px);
                padding-bottom: 56px;
            }

            .pension-title {
                font-size: 24px;
            }

            .pension-hero,
            .pension-panel {
                padding: 20px;
                border-radius: 20px;
            }

            .pension-panel__title,
            .pension-document__title {
                font-size: 17px;
            }

            .pension-document {
                grid-template-columns: minmax(0, 1fr);
                padding: 18px;
            }

            .pension-hero__actions,
            .pension-document__actions {
                flex-direction: column;
            }

            .pension-button {
                width: 100%;
            }
        }
    </style>
</head>

<body class="pension-page">
<?php include __DIR__ . '/header.php'; ?>
<main class="main pension-main">
    <div class="container">
        <nav class="pension-breadcrumbs" aria-label="Хлебные крошки">
            <span class="pension-breadcrumbs__home" aria-hidden="true"></span>
            <a href="/">Главная</a>
            <span class="pension-breadcrumbs__separator" aria-hidden="true">›</span>
            <a href="/informatsiya.php">Информация</a>
            <span class="pension-breadcrumbs__separator" aria-hidden="true">›</span>
            <span><?php echo e($pageTitle); ?></span>
        </nav>

        <h1 class="pension-title"><?php echo e($pageTitle); ?></h1>

        <div class="pension-layout">
            <section class="pension-hero" aria-labelledby="pension-hero-title">
                <div>
                    <span class="pension-hero__eyebrow">Программа «3+3»</span>
                    <h2 class="pension-panel__title" id="pension-hero-title">Финансовая поддержка государства для дополнительной пенсии</h2>
                    <p class="pension-hero__lead"><?php echo e($pageLead); ?></p>
                    <p class="pension-panel__text"><?php echo e($pageDescription); ?></p>
                    <div class="pension-hero__actions">
                        <a class="pension-button" href="<?php echo e($pageOfficialHref); ?>" target="_blank" rel="noopener noreferrer">Подробная информация</a>
                        <a class="pension-button pension-button--secondary" href="<?php echo e($pageDocumentHref); ?>" download>Скачать документ</a>
                    </div>
                </div>
                <div class="pension-hero__visual">
                    <img src="<?php echo e($pageBannerHref); ?>" alt="Баннер программы дополнительного накопительного пенсионного страхования.">
                </div>
            </section>

            <div class="pension-grid">
                <section class="pension-panel" aria-labelledby="pension-about-title">
                    <h2 class="pension-panel__title" id="pension-about-title">Что важно знать</h2>
                    <ul class="pension-facts">
<?php foreach ($pageFacts as $fact): ?>
                        <li><?php echo e($fact); ?></li>
<?php endforeach; ?>
                    </ul>
                </section>

                <aside class="pension-panel" aria-labelledby="pension-doc-title">
                    <h2 class="pension-panel__title" id="pension-doc-title">Где получить подробную информацию</h2>
                    <p class="pension-panel__text">Для уточнения условий страхования и порядка участия используйте официальный материал ФСЗН и документ, переданный для размещения на сайте.</p>
                    <div class="pension-document__actions">
                        <a class="pension-button" href="<?php echo e($pageOfficialHref); ?>" target="_blank" rel="noopener noreferrer">Перейти на сайт ФСЗН</a>
                    </div>
                </aside>
            </div>

            <section class="pension-panel pension-layout__footer" aria-labelledby="pension-download-title">
                <h2 class="pension-panel__title" id="pension-download-title">Документ для скачивания</h2>
                <p class="pension-panel__text">При необходимости скачайте исходный документ с информацией для размещения.</p>

                <article class="pension-document">
                    <span class="pension-document__icon" aria-hidden="true">DOCX</span>
                    <div>
                        <a class="pension-document__title" href="<?php echo e($pageDocumentHref); ?>" download>Стравита для сайта</a>
                        <span class="pension-document__meta">DOCX<?php echo $pageDocumentSize !== '' ? ', ' . e($pageDocumentSize) : ''; ?></span>
                        <div class="pension-document__actions">
                            <a class="pension-button" href="<?php echo e($pageDocumentHref); ?>" download>Скачать документ</a>
                        </div>
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

        function syncPensionHeaderOffset() {
            root.style.setProperty('--pension-header-offset', header.offsetHeight + 'px');
        }

        syncPensionHeaderOffset();
        window.addEventListener('load', syncPensionHeaderOffset);
        window.addEventListener('resize', syncPensionHeaderOffset);
    })();
</script>
</body>

</html>
