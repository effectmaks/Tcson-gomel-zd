<?php
require_once __DIR__ . '/lib/security.php';
require_once __DIR__ . '/lib/auth.php';
require_once __DIR__ . '/lib/public_page_visibility.php';
include __DIR__ . '/db_connection.php';

function formatDisabilitySupportFileSize($path)
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

$disabilitySupportPageTitle = 'Льготы, права и гарантии инвалидов';
$disabilitySupportSiteName = 'ТЦСОН Железнодорожного района г. Гомеля';
$disabilitySupportDocumentHref = '/documents/gomel-benefits-rights-guarantees-disabled.docx';
$disabilitySupportDocumentPath = __DIR__ . $disabilitySupportDocumentHref;

$disabilitySupportIntro = array(
    'Государственные социальные льготы, права и гарантии для людей с инвалидностью предоставляются в соответствии с законодательством Республики Беларусь и подтверждаются удостоверением инвалида.',
    'На этой странице размещена краткая информация по основным мерам поддержки. Полный перечень условий и деталей доступен в прикрепленной памятке для скачивания.',
);

$disabilitySupportBenefitGroups = array(
    array(
        'title' => 'Инвалиды I и II группы',
        'items' => array(
            '90-процентная скидка на лекарственные средства по рецептам врачей в пределах установленного перечня.',
            'Бесплатное изготовление и ремонт зубных протезов в государственных организациях здравоохранения по месту жительства.',
            'Бесплатное либо льготное обеспечение техническими средствами социальной реабилитации.',
            'Первоочередное бесплатное санаторно-курортное лечение или оздоровление для неработающих инвалидов при наличии показаний.',
            'Бесплатный проезд на отдельных видах городского и пригородного транспорта, а для инвалида I группы также для сопровождающего лица.',
            'Льготы по оплате жилья и коммунальных услуг при соблюдении условий, установленных законодательством.',
        ),
    ),
    array(
        'title' => 'Инвалиды III группы',
        'items' => array(
            '50-процентная скидка на лекарственные средства по рецептам врачей для лечения заболевания, приведшего к инвалидности.',
        ),
    ),
    array(
        'title' => 'Дети-инвалиды до 18 лет',
        'items' => array(
            'Бесплатное обеспечение лекарственными средствами по рецептам врачей.',
            'Бесплатное изготовление и ремонт зубных протезов в государственных организациях здравоохранения.',
            'Бесплатное обеспечение техническими средствами социальной реабилитации.',
            'Первоочередное бесплатное санаторно-курортное лечение или оздоровление.',
            'Бесплатный проезд на отдельных видах транспорта для ребенка-инвалида и сопровождающего лица.',
        ),
    ),
);

$disabilitySupportSections = array(
    array(
        'title' => 'Материальная поддержка',
        'paragraphs' => array(
            'Нуждающимся гражданам может предоставляться государственная адресная социальная помощь в виде ежемесячного или единовременного социального пособия.',
            'Неработающим инвалидам также может оказываться материальная помощь из средств Фонда социальной защиты населения. Размер помощи определяется по заявлению, акту обследования материально-бытового положения и подтверждающим документам.',
        ),
    ),
    array(
        'title' => 'Социальное обслуживание',
        'paragraphs' => array(
            'Территориальные центры социального обслуживания населения оказывают социально-бытовые, социально-реабилитационные, социально-посреднические и иные услуги в стационарной, полустационарной, срочной и надомной формах.',
            'Для получения социальных услуг, как правило, требуется письменное заявление, медицинская справка о состоянии здоровья и заключение договора на оказание социальных услуг.',
        ),
    ),
);

$disabilitySupportDocuments = array(
    array(
        'title' => 'Памятка «Льготы, права и гарантии для людей с инвалидностью»',
        'href' => $disabilitySupportDocumentHref,
        'meta' => 'DOCX' . (formatDisabilitySupportFileSize($disabilitySupportDocumentPath) !== '' ? ', ' . formatDisabilitySupportFileSize($disabilitySupportDocumentPath) : ''),
    ),
);

$seoTitleMeta = $disabilitySupportPageTitle . ' - ' . $disabilitySupportSiteName;
$seoDescriptionMeta = 'Краткая информация о льготах, правах, гарантиях и мерах социальной поддержки людей с инвалидностью.';

bootstrapPublicPageVisibility($conn, '/gossocpodderzhka-invalidov.php', $disabilitySupportPageTitle);
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
    <title><?php echo e($disabilitySupportPageTitle); ?> - <?php echo e($disabilitySupportSiteName); ?></title>
    <?php
    $seoScheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $seoHost = $_SERVER['HTTP_HOST'] ?? 'tcsonrw-gomel.by';
    $seoRequestUri = $_SERVER['REQUEST_URI'] ?? '/gossocpodderzhka-invalidov.php';
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
        .disability-support-page {
            scroll-padding-top: calc(var(--disability-support-page-header-offset, var(--header-height)) + 54px);
        }

        .disability-support-main {
            position: relative;
            isolation: isolate;
            padding-top: calc(var(--disability-support-page-header-offset, var(--header-height)) + 54px);
            padding-bottom: 74px;
        }

        .disability-support-main::before,
        .disability-support-main::after {
            content: "";
            position: absolute;
            top: 0;
            bottom: -86px;
            width: clamp(0px, calc((100vw - var(--width-container)) / 2), 118px);
            background: url("/img/loop-vert.png") repeat-y center top / 100% auto;
            pointer-events: none;
            z-index: 0;
        }

        .disability-support-main::before {
            left: 0;
            -webkit-mask-image: linear-gradient(270deg, transparent 0%, rgba(0, 0, 0, 0.3) 42%, rgba(0, 0, 0, 0.76) 100%);
            mask-image: linear-gradient(270deg, transparent 0%, rgba(0, 0, 0, 0.3) 42%, rgba(0, 0, 0, 0.76) 100%);
        }

        .disability-support-main::after {
            right: 0;
            -webkit-mask-image: linear-gradient(90deg, transparent 0%, rgba(0, 0, 0, 0.3) 42%, rgba(0, 0, 0, 0.76) 100%);
            mask-image: linear-gradient(90deg, transparent 0%, rgba(0, 0, 0, 0.3) 42%, rgba(0, 0, 0, 0.76) 100%);
        }

        .disability-support-layout {
            position: relative;
            z-index: 1;
            display: grid;
            grid-template-columns: 315px minmax(0, 1fr);
            gap: 36px;
            align-items: start;
        }

        .disability-support-content {
            min-width: 0;
        }

        .disability-support-breadcrumbs {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 18px;
            color: #6a766f;
            font-size: 14px;
            line-height: 1.35;
        }

        .disability-support-breadcrumbs__home {
            width: 16px;
            height: 16px;
            flex: 0 0 16px;
            background-color: #20604a;
            -webkit-mask: url("/img/dom.svg") no-repeat center / contain;
            mask: url("/img/dom.svg") no-repeat center / contain;
        }

        .disability-support-breadcrumbs__separator {
            color: #aab0ac;
            font-weight: 700;
        }

        .disability-support-breadcrumbs a {
            color: #20604a;
            text-decoration: none;
        }

        .disability-support-breadcrumbs a:hover,
        .disability-support-breadcrumbs a:focus-visible {
            color: #c62b30;
        }

        .disability-support-hero {
            margin-bottom: 28px;
            padding: 30px;
            border-radius: 30px;
            background: linear-gradient(135deg, rgba(24, 85, 61, 0.1), rgba(215, 188, 123, 0.22));
            border: 1px solid rgba(32, 96, 74, 0.14);
            box-shadow: 0 22px 44px rgba(17, 52, 40, 0.08);
        }

        .disability-support-eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 16px;
            color: #1a5945;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.1em;
            text-transform: uppercase;
        }

        .disability-support-eyebrow::before {
            content: "";
            width: 22px;
            height: 22px;
            flex: 0 0 22px;
            background-color: #1a5945;
            -webkit-mask: url("/img/gossocpodderzhka invalidov.svg") no-repeat center / contain;
            mask: url("/img/gossocpodderzhka invalidov.svg") no-repeat center / contain;
        }

        .disability-support-title {
            margin: 0 0 16px;
            color: #14352b;
            font-size: clamp(30px, 4vw, 42px);
            line-height: 1.1;
        }

        .disability-support-lead {
            max-width: 860px;
            margin: 0;
            color: #355448;
            font-size: 17px;
            line-height: 1.7;
        }

        .disability-support-section {
            margin-bottom: 28px;
        }

        .disability-support-section__title {
            margin: 0 0 14px;
            color: #14352b;
            font-size: 26px;
            line-height: 1.2;
        }

        .disability-support-card {
            padding: 24px;
            border-radius: 24px;
            background: rgba(255, 255, 255, 0.96);
            border: 1px solid rgba(32, 96, 74, 0.12);
            box-shadow: 0 16px 34px rgba(17, 52, 40, 0.07);
        }

        .disability-support-text {
            margin: 0 0 14px;
            color: #274238;
            font-size: 16px;
            line-height: 1.7;
        }

        .disability-support-text:last-child {
            margin-bottom: 0;
        }

        .disability-support-benefits {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 18px;
        }

        .disability-support-benefit-card {
            height: 100%;
            padding: 22px;
            border-radius: 24px;
            background: rgba(255, 255, 255, 0.96);
            border: 1px solid rgba(32, 96, 74, 0.12);
            box-shadow: 0 16px 34px rgba(17, 52, 40, 0.07);
        }

        .disability-support-benefit-card--wide {
            grid-column: 1 / -1;
        }

        .disability-support-benefit-card__title {
            margin: 0 0 14px;
            color: #15553d;
            font-size: 21px;
            line-height: 1.3;
        }

        .disability-support-benefit-list {
            margin: 0;
            padding-left: 20px;
            color: #2d473d;
            font-size: 15px;
            line-height: 1.65;
        }

        .disability-support-benefit-list li + li {
            margin-top: 10px;
        }

        .disability-support-documents {
            list-style: none;
            margin: 0;
            padding: 0;
        }

        .disability-support-document-link {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 18px;
            padding: 20px 22px;
            border-radius: 22px;
            background: linear-gradient(135deg, rgba(26, 84, 64, 0.08), rgba(214, 181, 107, 0.16));
            color: #14352b;
            text-decoration: none;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .disability-support-document-link:hover,
        .disability-support-document-link:focus-visible {
            transform: translateY(-2px);
            box-shadow: 0 18px 36px rgba(17, 52, 40, 0.1);
        }

        .disability-support-document-link__body {
            min-width: 0;
        }

        .disability-support-document-link__title {
            display: block;
            margin-bottom: 6px;
            font-size: 17px;
            font-weight: 700;
            line-height: 1.45;
        }

        .disability-support-document-link__meta {
            display: block;
            color: #5d7166;
            font-size: 13px;
            line-height: 1.4;
        }

        .disability-support-document-link__badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 112px;
            min-height: 44px;
            padding: 0 18px;
            border-radius: 999px;
            background: #18553d;
            color: #ffffff;
            font-size: 13px;
            font-weight: 700;
            letter-spacing: 0.04em;
            text-transform: uppercase;
        }

        @media (max-width: 1080px) {
            .disability-support-layout,
            .disability-support-benefits {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 767px) {
            .disability-support-main {
                padding-top: calc(var(--disability-support-page-header-offset, var(--header-height)) + 34px);
                padding-bottom: 58px;
            }

            .disability-support-hero,
            .disability-support-card,
            .disability-support-benefit-card {
                padding: 20px 18px;
            }

            .disability-support-document-link {
                flex-direction: column;
                align-items: flex-start;
            }

            .disability-support-document-link__badge {
                min-width: 0;
            }
        }
    </style>
</head>

<body class="disability-support-page">
<?php include __DIR__ . '/header.php'; ?>
<main class="main disability-support-main">
    <div class="disability-support-layout container">
        <?php
        $disabilitySupportMenuActive = 'benefits-rights';
        include __DIR__ . '/disability-support-side-menu.php';
        ?>

        <div class="disability-support-content">
            <nav class="disability-support-breadcrumbs" aria-label="Хлебные крошки">
                <span class="disability-support-breadcrumbs__home" aria-hidden="true"></span>
                <a href="/">Главная</a>
                <span class="disability-support-breadcrumbs__separator" aria-hidden="true">›</span>
                <span>Госсоцподдержка инвалидов</span>
                <span class="disability-support-breadcrumbs__separator" aria-hidden="true">›</span>
                <span><?php echo e($disabilitySupportPageTitle); ?></span>
            </nav>

            <section class="disability-support-hero" aria-labelledby="disability-support-page-title">
                <div class="disability-support-eyebrow">Госсоцподдержка инвалидов</div>
                <h1 class="disability-support-title" id="disability-support-page-title"><?php echo e($disabilitySupportPageTitle); ?></h1>
                <?php foreach ($disabilitySupportIntro as $disabilitySupportIntroIndex => $disabilitySupportIntroParagraph): ?>
                    <p class="disability-support-lead"<?php echo $disabilitySupportIntroIndex > 0 ? ' style="margin-top: 14px;"' : ''; ?>><?php echo e($disabilitySupportIntroParagraph); ?></p>
                <?php endforeach; ?>
            </section>

            <section class="disability-support-section" aria-labelledby="disability-support-benefits-title">
                <h2 class="disability-support-section__title" id="disability-support-benefits-title">Кратко об основных льготах</h2>
                <div class="disability-support-benefits">
                    <?php foreach ($disabilitySupportBenefitGroups as $disabilitySupportBenefitGroupIndex => $disabilitySupportBenefitGroup): ?>
                        <article class="disability-support-benefit-card<?php echo $disabilitySupportBenefitGroupIndex === 2 ? ' disability-support-benefit-card--wide' : ''; ?>">
                            <h3 class="disability-support-benefit-card__title"><?php echo e($disabilitySupportBenefitGroup['title']); ?></h3>
                            <ul class="disability-support-benefit-list">
                                <?php foreach ($disabilitySupportBenefitGroup['items'] as $disabilitySupportBenefitItem): ?>
                                    <li><?php echo e($disabilitySupportBenefitItem); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </article>
                    <?php endforeach; ?>
                </div>
            </section>

            <?php foreach ($disabilitySupportSections as $disabilitySupportSectionIndex => $disabilitySupportSection): ?>
                <section class="disability-support-section" aria-labelledby="disability-support-section-<?php echo (int) $disabilitySupportSectionIndex; ?>">
                    <h2 class="disability-support-section__title" id="disability-support-section-<?php echo (int) $disabilitySupportSectionIndex; ?>"><?php echo e($disabilitySupportSection['title']); ?></h2>
                    <article class="disability-support-card">
                        <?php foreach ($disabilitySupportSection['paragraphs'] as $disabilitySupportParagraph): ?>
                            <p class="disability-support-text"><?php echo e($disabilitySupportParagraph); ?></p>
                        <?php endforeach; ?>
                    </article>
                </section>
            <?php endforeach; ?>

            <section class="disability-support-section" aria-labelledby="disability-support-documents-title">
                <h2 class="disability-support-section__title" id="disability-support-documents-title">Памятка для скачивания</h2>
                <article class="disability-support-card">
                    <ul class="disability-support-documents">
                        <?php foreach ($disabilitySupportDocuments as $disabilitySupportDocument): ?>
                            <li>
                                <a class="disability-support-document-link" href="<?php echo e($disabilitySupportDocument['href']); ?>" download>
                                    <span class="disability-support-document-link__body">
                                        <span class="disability-support-document-link__title"><?php echo e($disabilitySupportDocument['title']); ?></span>
                                        <span class="disability-support-document-link__meta"><?php echo e($disabilitySupportDocument['meta']); ?></span>
                                    </span>
                                    <span class="disability-support-document-link__badge">Скачать</span>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
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

        function syncDisabilitySupportPageOffset() {
            root.style.setProperty('--disability-support-page-header-offset', header.offsetHeight + 'px');
        }

        syncDisabilitySupportPageOffset();
        window.addEventListener('load', syncDisabilitySupportPageOffset);
        window.addEventListener('resize', syncDisabilitySupportPageOffset);
    })();
</script>
</body>

</html>
