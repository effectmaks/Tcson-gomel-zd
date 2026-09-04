<?php
require_once __DIR__ . '/lib/security.php';
require_once __DIR__ . '/lib/auth.php';
require_once __DIR__ . '/lib/public_page_visibility.php';
require_once __DIR__ . '/lib/faq-render.php';
include __DIR__ . '/db_connection.php';

$faqPageTitle = 'Вопрос-ответ';
$faqSiteName = 'ТЦСОН Железнодорожного района г. Гомеля';
$faqItems = require __DIR__ . '/lib/faq-data.php';
$seoTitleMeta = $faqPageTitle . ' - ' . $faqSiteName;
$seoDescriptionMeta = 'Ответы на часто задаваемые вопросы о социальной поддержке, трудовых отношениях, занятости, уходе и социальных услугах.';

bootstrapPublicPageVisibility($conn, '/questions-answers.php', $faqPageTitle);
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
    $seoCanonical = $seoScheme . '://' . $seoHost . '/questions-answers.php';
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
        .faq-page {
            scroll-padding-top: calc(var(--faq-page-header-offset, var(--header-height)) + 54px);
        }

        .faq-page-main {
            position: relative;
            isolation: isolate;
            padding-top: calc(var(--faq-page-header-offset, var(--header-height)) + 54px);
            padding-bottom: 78px;
        }

        .faq-page-main::before,
        .faq-page-main::after {
            content: "";
            position: absolute;
            top: 0;
            bottom: -86px;
            width: clamp(0px, calc((100vw - var(--width-container)) / 2), 118px);
            background: url("/img/loop-vert.png") repeat-y center top / 100% auto;
            pointer-events: none;
            z-index: 0;
        }

        .faq-page-main::before {
            left: 0;
            -webkit-mask-image: linear-gradient(270deg, transparent 0%, rgba(0, 0, 0, .3) 42%, rgba(0, 0, 0, .76) 100%);
            mask-image: linear-gradient(270deg, transparent 0%, rgba(0, 0, 0, .3) 42%, rgba(0, 0, 0, .76) 100%);
        }

        .faq-page-main::after {
            right: 0;
            -webkit-mask-image: linear-gradient(90deg, transparent 0%, rgba(0, 0, 0, .3) 42%, rgba(0, 0, 0, .76) 100%);
            mask-image: linear-gradient(90deg, transparent 0%, rgba(0, 0, 0, .3) 42%, rgba(0, 0, 0, .76) 100%);
        }

        .faq-page-layout {
            position: relative;
            z-index: 1;
        }

        .faq-page-breadcrumbs {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 18px;
            color: #6a766f;
            font-size: 14px;
            line-height: 1.35;
        }

        .faq-page-breadcrumbs__home {
            width: 16px;
            height: 16px;
            flex: 0 0 16px;
            background-color: #20604a;
            -webkit-mask: url("/img/dom.svg") no-repeat center / contain;
            mask: url("/img/dom.svg") no-repeat center / contain;
        }

        .faq-page-breadcrumbs__separator {
            color: #aab0ac;
            font-weight: 700;
        }

        .faq-page-breadcrumbs a {
            color: #20604a;
            text-decoration: none;
        }

        .faq-page-breadcrumbs a:hover,
        .faq-page-breadcrumbs a:focus-visible,
        .faq-page-answer a:hover,
        .faq-page-answer a:focus-visible {
            color: #c62b30;
        }

        .faq-page-head {
            max-width: 880px;
            margin-bottom: 34px;
        }

        .faq-page-title {
            position: relative;
            margin: 0 0 14px;
            padding-left: 24px;
            color: #123f31;
            font-size: clamp(34px, 4vw, 54px);
            line-height: 1.08;
        }

        .faq-page-title::before {
            content: "";
            position: absolute;
            top: .12em;
            bottom: .08em;
            left: 0;
            width: 7px;
            border-radius: 999px;
            background: linear-gradient(180deg, #e73535, #ff7658);
        }

        .faq-page-lead {
            margin: 0;
            color: #50655d;
            font-size: 18px;
            line-height: 1.65;
        }

        .faq-page-list {
            display: grid;
            gap: 14px;
            max-width: 1080px;
        }

        .faq-page-item {
            overflow: clip;
            border-bottom: 1px solid #d4dfda;
            background: rgba(255, 255, 255, .72);
            transition: background-color .2s ease, border-color .2s ease;
        }

        .faq-page-item[open] {
            border-color: #9db9ad;
            background: #fff;
        }

        .faq-page-question {
            display: grid;
            grid-template-columns: minmax(0, 1fr) 44px;
            align-items: center;
            gap: 22px;
            padding: 22px 0;
            color: #123f31;
            font-size: 19px;
            font-weight: 700;
            line-height: 1.42;
            cursor: pointer;
            list-style: none;
        }

        .faq-page-question::-webkit-details-marker {
            display: none;
        }

        .faq-page-question::after {
            content: "+";
            display: grid;
            place-items: center;
            width: 42px;
            height: 42px;
            border-radius: 50%;
            background: #f6e8e8;
            color: #c62b30;
            font-size: 28px;
            font-weight: 400;
            line-height: 1;
            transition: transform .2s ease, background-color .2s ease, color .2s ease;
        }

        .faq-page-item[open] .faq-page-question::after {
            transform: rotate(45deg);
            background: #20604a;
            color: #fff;
        }

        .faq-page-answer {
            max-width: 940px;
            padding: 0 64px 25px 0;
            color: #2f493f;
            font-size: 16px;
            line-height: 1.75;
        }

        .faq-page-answer p,
        .faq-page-answer ul,
        .faq-page-answer ol,
        .faq-page-answer blockquote {
            margin: 0 0 14px;
        }

        .faq-page-answer ul,
        .faq-page-answer ol {
            padding-left: 24px;
        }

        .faq-page-answer li + li {
            margin-top: 7px;
        }

        .faq-page-answer a {
            color: #20604a;
            font-weight: 600;
            text-decoration: underline;
            text-underline-offset: 3px;
            transition: color .2s ease;
        }

        .faq-page-answer blockquote {
            padding: 14px 18px;
            border-left: 4px solid #d53331;
            background: #f8f2f1;
        }

        @media (max-width: 760px) {
            .faq-page {
                scroll-padding-top: calc(var(--faq-page-header-offset, var(--header-height)) + 38px);
            }

            .faq-page-main {
                padding-top: calc(var(--faq-page-header-offset, var(--header-height)) + 38px);
                padding-bottom: 58px;
            }

            .faq-page-main::before,
            .faq-page-main::after {
                display: none;
            }

            .faq-page-title {
                font-size: 32px;
            }

            .faq-page-lead {
                font-size: 16px;
            }

            .faq-page-question {
                grid-template-columns: minmax(0, 1fr) 38px;
                gap: 14px;
                padding: 19px 0;
                font-size: 16px;
            }

            .faq-page-question::after {
                width: 36px;
                height: 36px;
                font-size: 24px;
            }

            .faq-page-answer {
                padding: 0 0 22px;
                font-size: 15px;
            }
        }
    </style>
</head>

<body class="faq-page">
    <?php include __DIR__ . '/header.php'; ?>
    <main class="faq-page-main">
        <div class="faq-page-layout container">
            <nav class="faq-page-breadcrumbs" aria-label="Хлебные крошки">
                <a href="/" class="faq-page-breadcrumbs__home" aria-label="Главная"></a>
                <span class="faq-page-breadcrumbs__separator" aria-hidden="true">›</span>
                <a href="/informatsiya.php">Информация</a>
                <span class="faq-page-breadcrumbs__separator" aria-hidden="true">›</span>
                <span>Вопрос-ответ</span>
            </nav>

            <section class="faq-page-head" aria-labelledby="faq-page-title">
                <h1 class="faq-page-title" id="faq-page-title">Вопрос-ответ</h1>
                <p class="faq-page-lead">Ответы на часто задаваемые вопросы о социальной поддержке, трудовых отношениях, занятости и социальных услугах.</p>
            </section>

            <section class="faq-page-list" aria-label="Вопросы и ответы">
                <?php foreach ($faqItems as $faqItem): ?>
                <details class="faq-page-item">
                    <summary class="faq-page-question"><?php echo e($faqItem['question'] ?? ''); ?></summary>
                    <div class="faq-page-answer">
                        <?php echo renderFaqAnswerWithoutLinks($faqItem['answer_html'] ?? ''); ?>
                    </div>
                </details>
                <?php endforeach; ?>
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

            function syncFaqPageOffset() {
                root.style.setProperty('--faq-page-header-offset', header.offsetHeight + 'px');
            }

            syncFaqPageOffset();
            window.addEventListener('load', syncFaqPageOffset);
            window.addEventListener('resize', syncFaqPageOffset);
        })();
    </script>
</body>

</html>
