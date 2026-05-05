<?php
require_once __DIR__ . '/lib/security.php';

$appealsPageTitle = 'Электронные обращения граждан и юрлиц';
$appealsSiteName = 'ТЦСОН Железнодорожного района г. Гомеля';
$appealsPortalUrl = 'https://xn--80abnmycp7evc.xn--90ais/';
$appealsPortalHelpUrl = 'https://xn--80abnmycp7evc.xn--90ais/help';
$appealsLawUrl = 'https://pravo.by/document/?guid=3871&p0=H11100300';
$appealsWorkQuestionsUrl = 'https://pravo.by/document/?guid=12551&p0=C21200667';

$citizenRequirements = array(
    'наименование и (или) адрес организации либо должность лица, которым направляется обращение;',
    'фамилию, собственное имя, отчество (если таковое имеется) либо инициалы гражданина, адрес его места жительства (места пребывания);',
    'изложение сути обращения;',
    'адрес электронной почты заявителя.',
);

$legalEntityRequirements = array(
    'наименование и (или) адрес организации либо должность лица, которым направляется обращение;',
    'полное наименование юридического лица и его место нахождения;',
    'изложение сути обращения;',
    'фамилию, собственное имя, отчество (если таковое имеется) либо инициалы руководителя или лица, уполномоченного в установленном порядке подписывать обращения;',
    'адрес электронной почты заявителя.',
);

$appealsAttachmentFormats = array(
    array('short' => 'PDF/A', 'full' => 'Portable Document Format/A'),
    array('short' => 'DOCX', 'full' => 'Office Open XML'),
    array('short' => 'DOC', 'full' => 'двойной формат с разметкой'),
    array('short' => 'RTF', 'full' => 'Rich Text Format'),
    array('short' => 'TXT', 'full' => 'текстовый файл'),
    array('short' => 'ODT', 'full' => 'Open Document Format'),
    array('short' => 'ZIP, RAR', 'full' => 'формат архивации и сжатия данных'),
    array('short' => 'PNG', 'full' => 'Portable Network Graphics'),
    array('short' => 'TIFF', 'full' => 'Tagged Image File Format'),
    array('short' => 'JPEG', 'full' => 'Joint Photograph Experts Group'),
    array('short' => 'JPG', 'full' => 'Joint Photograph Group'),
);

$appealsNoReviewReasons = array(
    'обращения не соответствуют вышеуказанным требованиям;',
    'обращения подлежат рассмотрению в соответствии с законодательством о конституционном судопроизводстве, гражданским, гражданским процессуальным, хозяйственным процессуальным, уголовно-процессуальным законодательством, законодательством, определяющим порядок административного процесса, законодательством об административных процедурах, обращения являются обращениями работника к нанимателю либо в соответствии с законодательными актами установлен иной порядок подачи и рассмотрения таких обращений;',
    'обращения содержат вопросы, решение которых не относится к компетенции организации, в которую они поступили, в том числе если замечания и (или) предложения, внесенные в книгу замечаний и предложений, не относятся к деятельности этой организации, индивидуального предпринимателя, не касаются качества производимых (реализуемых) ими товаров, выполняемых работ, оказываемых услуг;',
    'пропущен без уважительной причины срок подачи жалобы;',
    'заявителем подано повторное обращение, в том числе внесенное в книгу замечаний и предложений, и в нем не содержатся новые обстоятельства, имеющие значение для рассмотрения обращения по существу;',
    'с заявителем прекращена переписка по изложенным вопросам.',
);

$appealsComplaintParagraphs = array(
    'Ответ организации на обращение или решение об оставлении обращения без рассмотрения по существу могут быть обжалованы в вышестоящую организацию.',
    'Ответ организации на обращение или решение об оставлении обращения без рассмотрения по существу после обжалования в вышестоящую организацию могут быть обжалованы в суд в порядке, установленном законодательством.',
    'Ответ на жалобу в вышестоящую организацию может быть обжалован в суд, если при рассмотрении этой жалобы принято новое решение, относящееся к компетенции соответствующей вышестоящей организации.',
    'Ответ на обращение или решение об оставлении обращения без рассмотрения по существу организации, не имеющей вышестоящей организации, а также индивидуального предпринимателя могут быть обжалованы в суд в порядке, установленном законодательством.',
);

$appealsReferenceLinks = array(
    array(
        'href' => $appealsPortalUrl,
        'title' => 'Государственная единая республиканская информационная система учета и обработки обращений граждан и юридических лиц',
        'label' => 'обращения.бел',
    ),
    array(
        'href' => $appealsPortalHelpUrl,
        'title' => 'Правила подачи электронных обращений посредством системы учета и обработки обращений граждан и юридических лиц',
        'label' => 'справочный раздел портала обращения.бел',
    ),
    array(
        'href' => $appealsLawUrl,
        'title' => 'Закон Республики Беларусь от 18 июля 2011 г. № 300-З «Об обращениях граждан и юридических лиц»',
        'label' => 'Национальный правовой Интернет-портал Республики Беларусь',
    ),
    array(
        'href' => $appealsWorkQuestionsUrl,
        'title' => 'Постановление Совета Министров Республики Беларусь от 23 июля 2012 г. № 667 «О некоторых вопросах работы с обращениями граждан и юридических лиц»',
        'label' => 'Национальный правовой Интернет-портал Республики Беларусь',
    ),
);

$seoTitleMeta = $appealsPageTitle . ' - ' . $appealsSiteName;
$seoDescriptionMeta = 'Порядок подачи электронных обращений граждан и юридических лиц через государственную систему обращения.бел.';
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
    <title><?php echo e($appealsPageTitle); ?> - <?php echo e($appealsSiteName); ?></title>
    <?php
    $seoScheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $seoHost = $_SERVER['HTTP_HOST'] ?? 'tcsonrw-gomel.by';
    $seoRequestUri = $_SERVER['REQUEST_URI'] ?? '/electronic-appeals.php';
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
        .appeals-page {
            scroll-padding-top: calc(var(--appeals-page-header-offset, var(--header-height)) + 54px);
        }

        .appeals-page-main {
            position: relative;
            isolation: isolate;
            padding-top: calc(var(--appeals-page-header-offset, var(--header-height)) + 54px);
            padding-bottom: 74px;
        }

        .appeals-page-main::before,
        .appeals-page-main::after {
            content: "";
            position: absolute;
            top: 0;
            bottom: -86px;
            width: clamp(0px, calc((100vw - var(--width-container)) / 2), 118px);
            background: url("/img/loop-vert.png") repeat-y center top / 100% auto;
            pointer-events: none;
            z-index: 0;
        }

        .appeals-page-main::before {
            left: 0;
            -webkit-mask-image: linear-gradient(270deg, transparent 0%, rgba(0, 0, 0, 0.3) 42%, rgba(0, 0, 0, 0.76) 100%);
            mask-image: linear-gradient(270deg, transparent 0%, rgba(0, 0, 0, 0.3) 42%, rgba(0, 0, 0, 0.76) 100%);
        }

        .appeals-page-main::after {
            right: 0;
            -webkit-mask-image: linear-gradient(90deg, transparent 0%, rgba(0, 0, 0, 0.3) 42%, rgba(0, 0, 0, 0.76) 100%);
            mask-image: linear-gradient(90deg, transparent 0%, rgba(0, 0, 0, 0.3) 42%, rgba(0, 0, 0, 0.76) 100%);
        }

        .appeals-page-layout {
            position: relative;
            z-index: 1;
            display: grid;
            grid-template-columns: 315px minmax(0, 1fr);
            gap: 36px;
            align-items: start;
        }

        .appeals-page-content {
            min-width: 0;
        }

        .appeals-breadcrumbs {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 18px;
            color: #6a766f;
            font-size: 14px;
            line-height: 1.35;
        }

        .appeals-breadcrumbs__home {
            width: 16px;
            height: 16px;
            flex: 0 0 16px;
            background-color: #20604a;
            -webkit-mask: url("/img/dom.svg") no-repeat center / contain;
            mask: url("/img/dom.svg") no-repeat center / contain;
        }

        .appeals-breadcrumbs__separator {
            color: #aab0ac;
            font-weight: 700;
        }

        .appeals-breadcrumbs a {
            color: #20604a;
            text-decoration: none;
        }

        .appeals-breadcrumbs a:hover,
        .appeals-breadcrumbs a:focus-visible {
            color: #c62b30;
        }

        .appeals-title {
            position: relative;
            margin: 0 0 18px;
            padding-left: 20px;
            color: #15553d;
            font-size: clamp(30px, 3vw, 40px);
            font-weight: 700;
            line-height: 1.12;
        }

        .appeals-title::before {
            content: "";
            position: absolute;
            left: 0;
            top: 3px;
            bottom: 5px;
            width: 5px;
            border-radius: 999px;
            background: #d53331;
        }

        .appeals-lead {
            max-width: 920px;
            margin: 0 0 22px;
            color: #2f3a35;
            font-size: 18px;
            line-height: 1.55;
        }

        .appeals-alert-card {
            margin-bottom: 24px;
            padding: 24px;
            border: 1px solid rgba(213, 51, 49, 0.16);
            border-radius: 18px;
            background:
                linear-gradient(135deg, rgba(255, 255, 255, 0.96), rgba(255, 247, 243, 0.94)),
                radial-gradient(circle at 0 0, rgba(213, 51, 49, 0.1), transparent 48%);
            box-shadow: 0 16px 34px rgba(48, 56, 52, 0.08);
        }

        .appeals-alert-card__title {
            margin: 0 0 8px;
            color: #15553d;
            font-size: 22px;
            font-weight: 800;
            line-height: 1.2;
        }

        .appeals-alert-card__subtitle {
            margin: 0 0 14px;
            color: #c62b30;
            font-size: 18px;
            font-weight: 800;
            line-height: 1.3;
            text-transform: uppercase;
        }

        .appeals-alert-card__text {
            margin: 0;
            color: #2f3a35;
            font-size: 17px;
            line-height: 1.55;
        }

        .appeals-alert-card__text + .appeals-alert-card__text {
            margin-top: 10px;
        }

        .appeals-portal-card {
            display: grid;
            grid-template-columns: 58px minmax(0, 1fr) auto;
            gap: 20px;
            align-items: center;
            margin-bottom: 24px;
            padding: 24px;
            border: 1px solid rgba(32, 96, 74, 0.12);
            border-radius: 18px;
            background:
                linear-gradient(135deg, rgba(255, 255, 255, 0.96), rgba(246, 252, 248, 0.94)),
                radial-gradient(circle at 0 0, rgba(213, 51, 49, 0.08), transparent 45%);
            box-shadow: 0 16px 34px rgba(48, 56, 52, 0.08);
        }

        .appeals-portal-card__icon {
            width: 52px;
            height: 52px;
            border-radius: 14px;
            background-color: #15553d;
            -webkit-mask: url("/img/email.svg") no-repeat center / 36px 36px;
            mask: url("/img/email.svg") no-repeat center / 36px 36px;
        }

        .appeals-portal-card__title {
            margin: 0 0 5px;
            color: #15553d;
            font-size: 22px;
            font-weight: 800;
            line-height: 1.2;
        }

        .appeals-portal-card__text {
            margin: 0;
            color: #3f4d47;
            font-size: 16px;
            line-height: 1.45;
        }

        .appeals-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 46px;
            padding: 0 20px;
            border: 1px solid rgba(198, 43, 48, 0.28);
            border-radius: var(--button-radius);
            background: linear-gradient(180deg, #df3d38, #cb2d30);
            color: #fff;
            font-size: 16px;
            font-weight: 700;
            line-height: 1.2;
            text-decoration: none;
            box-shadow: 0 10px 22px rgba(198, 43, 48, 0.2);
            transition: transform .2s ease, box-shadow .2s ease;
        }

        .appeals-button:hover,
        .appeals-button:focus-visible {
            color: #fff;
            transform: translateY(-1px);
            box-shadow: 0 14px 26px rgba(198, 43, 48, 0.24);
        }

        .appeals-section {
            margin-top: 30px;
        }

        .appeals-section__title {
            margin: 0 0 16px;
            color: #196847;
            font-size: 26px;
            font-weight: 700;
            line-height: 1.16;
        }

        .appeals-info-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 14px;
        }

        .appeals-info-card {
            min-width: 0;
            padding: 22px;
            border: 1px solid rgba(32, 96, 74, 0.1);
            border-radius: 16px;
            background: rgba(255, 255, 255, 0.94);
            box-shadow: 0 14px 30px rgba(48, 56, 52, 0.08);
        }

        .appeals-info-card--wide {
            grid-column: 1 / -1;
        }

        .appeals-info-card__title {
            margin: 0 0 12px;
            color: #15553d;
            font-size: 18px;
            font-weight: 800;
            line-height: 1.28;
        }

        .appeals-info-card__text {
            margin: 0;
            color: #2f3a35;
            font-size: 16px;
            line-height: 1.5;
        }

        .appeals-info-card__text + .appeals-info-card__text {
            margin-top: 10px;
        }

        .appeals-alert-card a,
        .appeals-info-card a {
            color: #15553d;
            font-weight: 700;
            text-decoration: none;
        }

        .appeals-alert-card a:hover,
        .appeals-alert-card a:focus-visible,
        .appeals-info-card a:hover,
        .appeals-info-card a:focus-visible {
            color: #c62b30;
        }

        .appeals-list {
            display: grid;
            gap: 9px;
            margin: 0;
            padding: 0;
            list-style: none;
        }

        .appeals-list li {
            position: relative;
            padding-left: 20px;
            color: #2f3a35;
            font-size: 16px;
            line-height: 1.45;
        }

        .appeals-list li::before {
            content: "";
            position: absolute;
            left: 0;
            top: 0.63em;
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: #d53331;
        }

        .appeals-format-list {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 8px;
            margin: 0;
            padding: 0;
            list-style: none;
        }

        .appeals-format-list li {
            padding: 10px 12px;
            border: 1px solid rgba(32, 96, 74, 0.14);
            border-radius: 10px;
            background: #eef7f1;
            color: #15553d;
            font-size: 14px;
            font-weight: 800;
            line-height: 1.25;
        }

        .appeals-format-list strong {
            display: block;
            margin-bottom: 3px;
            color: #0f4f3a;
            text-transform: uppercase;
        }

        .appeals-format-list span {
            display: block;
            color: #4f655d;
            font-size: 13px;
            font-weight: 600;
            text-transform: none;
        }

        .appeals-reference-list {
            display: grid;
            gap: 12px;
            margin: 0;
            padding: 0;
            list-style: none;
        }

        .appeals-reference-list__link {
            display: block;
            padding: 16px 18px;
            border: 1px solid rgba(32, 96, 74, 0.12);
            border-radius: 12px;
            background: #f8fbf8;
            color: #15553d;
            text-decoration: none;
            transition: border-color .2s ease, color .2s ease, transform .2s ease;
        }

        .appeals-reference-list__link:hover,
        .appeals-reference-list__link:focus-visible {
            border-color: rgba(213, 51, 49, 0.28);
            color: #c62b30;
            transform: translateY(-1px);
        }

        .appeals-reference-list__title {
            display: block;
            color: inherit;
            font-size: 16px;
            font-weight: 800;
            line-height: 1.35;
        }

        .appeals-reference-list__label {
            display: block;
            margin-top: 5px;
            color: #5f6b63;
            font-size: 14px;
            font-weight: 600;
            line-height: 1.3;
        }

        @media (max-width: 1100px) {
            .appeals-page-layout {
                grid-template-columns: 260px minmax(0, 1fr);
                gap: 24px;
            }

            .appeals-portal-card {
                grid-template-columns: 52px minmax(0, 1fr);
            }

            .appeals-portal-card .appeals-button {
                grid-column: 1 / -1;
                width: fit-content;
            }
        }

        @media (max-width: 860px) {
            .appeals-page {
                scroll-padding-top: calc(var(--appeals-page-header-offset, var(--header-height)) + 38px);
            }

            .appeals-page-main {
                padding-top: calc(var(--appeals-page-header-offset, var(--header-height)) + 38px);
                padding-bottom: 58px;
            }

            .appeals-page-main::before,
            .appeals-page-main::after {
                display: none;
            }

            .appeals-page-layout,
            .appeals-info-grid {
                grid-template-columns: 1fr;
            }

            .appeals-format-list {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 620px) {
            .appeals-title {
                font-size: 30px;
            }

            .appeals-lead {
                font-size: 16px;
            }

            .appeals-portal-card {
                grid-template-columns: 1fr;
                padding: 20px 16px;
            }

            .appeals-info-card {
                padding: 18px 16px;
            }

            .appeals-portal-card .appeals-button {
                width: 100%;
            }
        }
    </style>
</head>

<body class="appeals-page">
<?php include __DIR__ . '/header.php'; ?>
<main class="main appeals-page-main">
    <div class="appeals-page-layout container">
        <?php
        $contactsMenuActive = 'electronic-appeals';
        include __DIR__ . '/contacts-side-menu.php';
        ?>

        <div class="appeals-page-content">
            <nav class="appeals-breadcrumbs" aria-label="Хлебные крошки">
                <span class="appeals-breadcrumbs__home" aria-hidden="true"></span>
                <a href="/">Главная</a>
                <span class="appeals-breadcrumbs__separator" aria-hidden="true">›</span>
                <a href="/contacts.php">Контакты</a>
                <span class="appeals-breadcrumbs__separator" aria-hidden="true">›</span>
                <span><?php echo e($appealsPageTitle); ?></span>
            </nav>

            <section aria-labelledby="appeals-page-title">
                <h1 class="appeals-title" id="appeals-page-title"><?php echo e($appealsPageTitle); ?></h1>
                <p class="appeals-lead">Порядок подачи электронных обращений граждан и юридических лиц через государственную единую республиканскую информационную систему учета и обработки обращений.</p>

                <article class="appeals-alert-card">
                    <h2 class="appeals-alert-card__title">Уважаемые заявители!</h2>
                    <p class="appeals-alert-card__subtitle">Обращаем ВАШЕ ВНИМАНИЕ!</p>
                    <p class="appeals-alert-card__text">С <strong>02.01.2023</strong> на основании статьи 25 Закона Республики Беларусь от 18 июля 2011 г. № 300-З «Об обращениях граждан и юридических лиц» (ред. от 17 июля 2023 г. № 284-З) <strong>электронные обращения подаются посредством государственной единой республиканской информационной системы учета и обработки обращений граждан и юридических лиц</strong> (<a href="<?php echo e($appealsPortalUrl); ?>" target="_blank" rel="noopener noreferrer">обращения.бел</a>).</p>
                    <p class="appeals-alert-card__text">Доступ к данной системе осуществляется бесплатно.</p>
                    <p class="appeals-alert-card__text">С правилами подачи электронных обращений посредством системы можно ознакомиться <a href="<?php echo e($appealsPortalHelpUrl); ?>" target="_blank" rel="noopener noreferrer">здесь</a>.</p>
                </article>

                <article class="appeals-portal-card">
                    <span class="appeals-portal-card__icon" aria-hidden="true"></span>
                    <div>
                        <h2 class="appeals-portal-card__title">Портал обращения.бел</h2>
                        <p class="appeals-portal-card__text">Официальная государственная система для подачи и обработки электронных обращений граждан и юридических лиц.</p>
                    </div>
                    <a class="appeals-button" href="<?php echo e($appealsPortalUrl); ?>" target="_blank" rel="noopener noreferrer">Перейти на портал</a>
                </article>
            </section>

            <section class="appeals-section" aria-labelledby="appeals-requirements-title">
                <h2 class="appeals-section__title" id="appeals-requirements-title">Требования к электронным обращениям</h2>
                <div class="appeals-info-grid">
                    <article class="appeals-info-card appeals-info-card--wide">
                        <h3 class="appeals-info-card__title">Язык обращения</h3>
                        <p class="appeals-info-card__text"><strong>Электронные обращения должны излагаться на белорусском или русском языке.</strong></p>
                    </article>

                    <article class="appeals-info-card">
                        <h3 class="appeals-info-card__title">Для граждан</h3>
                        <ul class="appeals-list">
                            <?php foreach ($citizenRequirements as $requirement): ?>
                                <li><?php echo e($requirement); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </article>

                    <article class="appeals-info-card">
                        <h3 class="appeals-info-card__title">Для юридических лиц</h3>
                        <ul class="appeals-list">
                            <?php foreach ($legalEntityRequirements as $requirement): ?>
                                <li><?php echo e($requirement); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </article>
                </div>
            </section>

            <section class="appeals-section" aria-labelledby="appeals-rules-title">
                <h2 class="appeals-section__title" id="appeals-rules-title">Текст обращения и документы</h2>
                <div class="appeals-info-grid">
                    <article class="appeals-info-card">
                        <h3 class="appeals-info-card__title">Текст обращения</h3>
                        <p class="appeals-info-card__text">Текст обращения должен поддаваться прочтению. Не допускается употребление в обращениях нецензурных либо оскорбительных слов или выражений.</p>
                    </article>

                    <article class="appeals-info-card">
                        <h3 class="appeals-info-card__title">Предыдущее рассмотрение</h3>
                        <p class="appeals-info-card__text">В обращениях должна содержаться информация о результатах их предыдущего рассмотрения с приложением (при наличии) подтверждающих эту информацию документов.</p>
                    </article>

                    <article class="appeals-info-card">
                        <h3 class="appeals-info-card__title">Представители заявителя</h3>
                        <p class="appeals-info-card__text">К электронным обращениям, подаваемым представителями заявителей, должны прилагаться электронные копии документов, подтверждающих их полномочия.</p>
                    </article>

                    <article class="appeals-info-card">
                        <h3 class="appeals-info-card__title">Дополнительные материалы</h3>
                        <p class="appeals-info-card__text">К электронному обращению могут быть прикреплены дополнительные документы и (или) сведения: документы, подтверждающие полномочия представителей заявителей, документы о результатах предыдущего рассмотрения обращений и другие документы и (или) сведения, необходимые для решения вопросов, изложенных в обращении.</p>
                    </article>
                </div>
            </section>

            <section class="appeals-section" aria-labelledby="appeals-files-title">
                <h2 class="appeals-section__title" id="appeals-files-title">Файлы к обращению</h2>
                <article class="appeals-info-card appeals-info-card--wide">
                    <p class="appeals-info-card__text">Допустимыми форматами прикрепляемых документов и (или) сведений, в электронном виде и их графических образов на бумажных носителях (сканов) являются:</p>
                    <ul class="appeals-format-list" aria-label="Поддерживаемые форматы файлов">
                        <?php foreach ($appealsAttachmentFormats as $format): ?>
                            <li>
                                <strong><?php echo e($format['short']); ?></strong>
                                <span><?php echo e($format['full']); ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </article>
            </section>

            <section class="appeals-section" aria-labelledby="appeals-review-title">
                <h2 class="appeals-section__title" id="appeals-review-title">Когда обращение могут оставить без рассмотрения</h2>
                <article class="appeals-info-card appeals-info-card--wide">
                    <ul class="appeals-list">
                        <?php foreach ($appealsNoReviewReasons as $reason): ?>
                            <li><?php echo e($reason); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </article>
            </section>

            <section class="appeals-section" aria-labelledby="appeals-withdraw-title">
                <h2 class="appeals-section__title" id="appeals-withdraw-title">Отзыв электронного обращения</h2>
                <article class="appeals-info-card appeals-info-card--wide">
                    <p class="appeals-info-card__text">Заявитель имеет право отозвать свое обращение до рассмотрения его по существу.</p>
                    <p class="appeals-info-card__text">Отзыв электронного обращения осуществляется путем подачи письменного заявления либо направления заявления в электронной форме тем же способом, которым было направлено электронное обращение.</p>
                </article>
            </section>

            <section class="appeals-section" aria-labelledby="appeals-complaint-title">
                <h2 class="appeals-section__title" id="appeals-complaint-title">Обжалование ответов на обращения</h2>
                <article class="appeals-info-card appeals-info-card--wide">
                    <?php foreach ($appealsComplaintParagraphs as $appealsComplaintParagraph): ?>
                        <p class="appeals-info-card__text"><?php echo e($appealsComplaintParagraph); ?></p>
                    <?php endforeach; ?>
                </article>
            </section>

            <section class="appeals-section" aria-labelledby="appeals-mass-title">
                <h2 class="appeals-section__title" id="appeals-mass-title">Массовые электронные обращения</h2>
                <article class="appeals-info-card appeals-info-card--wide">
                    <p class="appeals-info-card__text">В случае, если поступающие электронные обращения аналогичного содержания от разных заявителей носят массовый характер (более десяти обращений), ответы на такие обращения по решению руководителя государственного органа или иной государственной организации либо лица, уполномоченного им подписывать в установленном порядке ответы на обращения, могут размещаться на официальном сайте государственного органа или иной государственной организации в глобальной компьютерной сети Интернет без направления ответов (уведомлений) заявителям.</p>
                </article>
            </section>

            <section class="appeals-section" aria-labelledby="appeals-reference-title">
                <h2 class="appeals-section__title" id="appeals-reference-title">Ссылки на документы и сервисы</h2>
                <article class="appeals-info-card appeals-info-card--wide">
                    <ul class="appeals-reference-list">
                        <?php foreach ($appealsReferenceLinks as $appealsReferenceLink): ?>
                            <li>
                                <a class="appeals-reference-list__link" href="<?php echo e($appealsReferenceLink['href']); ?>" target="_blank" rel="noopener noreferrer">
                                    <span class="appeals-reference-list__title"><?php echo e($appealsReferenceLink['title']); ?></span>
                                    <span class="appeals-reference-list__label"><?php echo e($appealsReferenceLink['label']); ?></span>
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

        function syncAppealsPageOffset() {
            root.style.setProperty('--appeals-page-header-offset', header.offsetHeight + 'px');
        }

        syncAppealsPageOffset();
        window.addEventListener('load', syncAppealsPageOffset);
        window.addEventListener('resize', syncAppealsPageOffset);
    })();
</script>
</body>

</html>
