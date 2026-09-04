<?php
require_once __DIR__ . '/lib/security.php';
require_once __DIR__ . '/lib/auth.php';
require_once __DIR__ . '/lib/public_page_visibility.php';
include __DIR__ . '/db_connection.php';

function formatEasyLanguageFileSize($path)
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

$easyLanguagePageTitle = 'Ясный язык - ясно и просто';
$easyLanguageSiteName = 'ТЦСОН Железнодорожного района г. Гомеля';
$easyLanguageDocumentHref = '/documents/yasnyy-yazyk-yasno-i-prosto.pdf';
$easyLanguageDocumentPath = __DIR__ . $easyLanguageDocumentHref;
$easyLanguageDocumentSize = formatEasyLanguageFileSize($easyLanguageDocumentPath);
$easyLanguageIntro = 'Буклет «Ясный язык - ясно и просто» в доступной форме рассказывает о работе отделения социальной реабилитации, абилитации инвалидов: кому предоставляется помощь, как получить консультацию и поддержку в отделении, на дому или дистанционно, какие занятия и услуги доступны, а также где находится отделение, как с ним связаться и в какое время оно работает.';

$seoTitleMeta = $easyLanguagePageTitle . ' - ' . $easyLanguageSiteName;
$seoDescriptionMeta = 'Буклет ясным языком о помощи и услугах отделения социальной реабилитации, абилитации инвалидов.';

bootstrapPublicPageVisibility($conn, '/easy-language.php', $easyLanguagePageTitle);
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
    $seoRequestUri = $_SERVER['REQUEST_URI'] ?? '/easy-language.php';
    $seoPath = strtok($seoRequestUri, '?');
    $seoCanonical = $seoScheme . '://' . $seoHost . $seoPath;
    $seoOgImageUrl = $seoScheme . '://' . $seoHost . '/img/logo-old-mini.webp';
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
        .easy-language-page {
            scroll-padding-top: calc(var(--easy-language-header-offset, var(--header-height)) + 42px);
        }

        .easy-language-main {
            padding-top: calc(var(--easy-language-header-offset, var(--header-height)) + 42px);
            padding-bottom: 72px;
        }

        .easy-language-breadcrumbs {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 22px;
            color: #6a766f;
            font-size: 14px;
            line-height: 1.35;
        }

        .easy-language-breadcrumbs__home {
            width: 16px;
            height: 16px;
            flex: 0 0 16px;
            background-color: #20604a;
            -webkit-mask: url("/img/dom.svg") no-repeat center / contain;
            mask: url("/img/dom.svg") no-repeat center / contain;
        }

        .easy-language-breadcrumbs a {
            color: #20604a;
            text-decoration: none;
        }

        .easy-language-breadcrumbs a:hover,
        .easy-language-breadcrumbs a:focus-visible {
            color: #c62b30;
        }

        .easy-language-breadcrumbs__separator {
            color: #aab0ac;
            font-weight: 700;
        }

        .easy-language-title {
            position: relative;
            margin: 0 0 28px;
            padding-left: 20px;
            color: #15553d;
            font-size: 28px;
            font-weight: 700;
            line-height: 1.2;
        }

        .easy-language-title::before {
            content: "";
            position: absolute;
            top: 3px;
            bottom: 3px;
            left: 0;
            width: 5px;
            border-radius: 999px;
            background: #d53331;
        }

        .easy-language-panel {
            padding: 28px;
            border: 1px solid rgba(32, 96, 74, 0.12);
            border-radius: 24px;
            background: rgba(255, 255, 255, 0.97);
            box-shadow: 0 14px 32px rgba(48, 56, 52, 0.08);
        }

        .easy-language-panel__title {
            margin: 0 0 14px;
            color: #15553d;
            font-size: 18px;
            font-weight: 700;
            line-height: 1.35;
        }

        .easy-language-panel__text {
            margin: 0;
            color: #355448;
            font-size: 16px;
            line-height: 1.7;
        }

        .easy-language-document {
            display: grid;
            grid-template-columns: 52px minmax(0, 1fr) auto;
            gap: 18px;
            align-items: center;
            margin-top: 24px;
            padding: 20px 22px;
            border: 1px solid rgba(32, 96, 74, 0.12);
            border-radius: 18px;
            background: #f7faf7;
        }

        .easy-language-document__icon {
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

        .easy-language-document__title {
            display: block;
            color: #15553d;
            font-size: 18px;
            font-weight: 700;
            line-height: 1.35;
            text-decoration: none;
        }

        .easy-language-document__title:hover,
        .easy-language-document__title:focus-visible {
            color: #c62b30;
        }

        .easy-language-document__meta {
            display: block;
            margin-top: 5px;
            color: #5f6b63;
            font-size: 14px;
            line-height: 1.35;
        }

        .easy-language-document__button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 44px;
            padding: 0 22px;
            border: 1px solid #15553d;
            border-radius: var(--button-radius);
            background: #15553d;
            color: #fff;
            font-size: 16px;
            font-weight: 700;
            text-decoration: none;
            transition: border-color .2s ease, background-color .2s ease, transform .2s ease;
        }

        .easy-language-document__button:hover,
        .easy-language-document__button:focus-visible {
            border-color: #c62b30;
            background: #c62b30;
            color: #fff;
            transform: translateY(-1px);
        }

        @media (max-width: 767px) {
            .easy-language-main {
                padding-top: calc(var(--easy-language-header-offset, var(--header-height)) + 34px);
                padding-bottom: 56px;
            }

            .easy-language-title {
                font-size: 24px;
            }

            .easy-language-panel {
                padding: 20px;
                border-radius: 20px;
            }

            .easy-language-panel__title,
            .easy-language-document__title {
                font-size: 17px;
            }

            .easy-language-document {
                grid-template-columns: minmax(0, 1fr);
                padding: 18px;
            }

            .easy-language-document__button {
                width: 100%;
            }
        }
    </style>
</head>

<body class="easy-language-page">
<?php include __DIR__ . '/header.php'; ?>
<main class="main easy-language-main">
    <div class="container">
        <nav class="easy-language-breadcrumbs" aria-label="Хлебные крошки">
            <span class="easy-language-breadcrumbs__home" aria-hidden="true"></span>
            <a href="/">Главная</a>
            <span class="easy-language-breadcrumbs__separator" aria-hidden="true">›</span>
            <a href="/informatsiya.php">Информация</a>
            <span class="easy-language-breadcrumbs__separator" aria-hidden="true">›</span>
            <span><?php echo e($easyLanguagePageTitle); ?></span>
        </nav>

        <h1 class="easy-language-title"><?php echo e($easyLanguagePageTitle); ?></h1>

        <section class="easy-language-panel" aria-labelledby="easy-language-information-title">
            <h2 class="easy-language-panel__title" id="easy-language-information-title">Информация</h2>
            <p class="easy-language-panel__text"><?php echo e($easyLanguageIntro); ?></p>

            <article class="easy-language-document">
                <span class="easy-language-document__icon" aria-hidden="true">PDF</span>
                <div>
                    <a class="easy-language-document__title" href="<?php echo e($easyLanguageDocumentHref); ?>" download>Буклет «Ясный язык - ясно и просто»</a>
                    <span class="easy-language-document__meta">PDF<?php echo $easyLanguageDocumentSize !== '' ? ', ' . e($easyLanguageDocumentSize) : ''; ?></span>
                </div>
                <a class="easy-language-document__button" href="<?php echo e($easyLanguageDocumentHref); ?>" download>Скачать</a>
            </article>
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

        function syncEasyLanguageHeaderOffset() {
            root.style.setProperty('--easy-language-header-offset', header.offsetHeight + 'px');
        }

        syncEasyLanguageHeaderOffset();
        window.addEventListener('load', syncEasyLanguageHeaderOffset);
        window.addEventListener('resize', syncEasyLanguageHeaderOffset);
    })();
</script>
</body>

</html>
