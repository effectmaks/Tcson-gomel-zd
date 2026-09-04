<?php
require_once __DIR__ . '/lib/security.php';
require_once __DIR__ . '/lib/auth.php';
require_once __DIR__ . '/lib/public_page_visibility.php';
include __DIR__ . '/db_connection.php';

function formatCrisisHelpFileSize($path)
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

$crisisHelpPageTitle = 'Профилактика домашнего насилия';
$crisisHelpSectionTitle = 'Помощь в кризисной ситуации';
$crisisHelpSiteName = 'ТЦСОН Железнодорожного района г. Гомеля';
$crisisHelpDocumentHref = '/documents/gomel-stop-violence-brochure.pdf';
$crisisHelpDocumentPath = __DIR__ . $crisisHelpDocumentHref;
$crisisHelpRegionDocumentHref = '/documents/gomel-region-territorial-centers-list.pdf';
$crisisHelpRegionDocumentPath = __DIR__ . $crisisHelpRegionDocumentHref;
$crisisHelpSlogan = 'ВМЕСТЕ ПРОТИВ ДОМАШНЕГО НАСИЛИЯ';
$crisisHelpIntro = 'Домашнее насилие - это умышленные противоправные либо аморальные действия физического, психологического или сексуального характера близких родственников, бывших супругов, граждан, имеющих общего ребенка (детей), либо иных граждан, которые проживают (проживали) совместно и ведут (вели) общее хозяйство, по отношению друг к другу, причиняющие физические и (или) психические страдания.';

$crisisHelpDefinitionParagraphs = array(
    'Пострадавший от домашнего насилия - гражданин, которому в результате совершения домашнего насилия причинены физические и (или) психические страдания.',
);

$crisisHelpDefinitionItems = array(
    'Действия психологического характера - воздействие на психику гражданина Республики Беларусь, иностранного гражданина и лица без гражданства (далее, если не определено иное, - гражданин) посредством угрозы, унижения чести и достоинства, совершения иных аморальных действий, которые объективно дают основания гражданину опасаться за свою безопасность или безопасность близких ему лиц.',
    'Действия сексуального характера - посягательство на половую свободу или половую неприкосновенность.',
    'Действия физического характера - причинение телесного повреждения, боли, мучений, нанесение побоев.',
    'Экономическое насилие. Материальное давление, запрет работать или обучаться, лишение финансовой поддержки, полный контроль за расходами.',
    'Сталкинг - преследование бывшего партнера дома, по месту работы, по телефону.',
    'Газлайтинг - вызов сомнения у пострадавшего в его адекватности.',
);

$crisisHelpContactsCards = array(
    array(
        'title' => 'ОВД администрации Железнодорожного района г. Гомеля',
        'lines' => array(
            'ул. Кирова, 122',
            '<a href="tel:102">102</a>',
        ),
    ),
    array(
        'title' => 'Территориальный центр социального обслуживания населения Железнодорожного района г. Гомеля',
        'lines' => array(
            'ул. 50 лет БССР, 19',
            'психолог - <a href="tel:+375232349792">8 (0232) 34-97-92</a>, <a href="tel:+375232288600">8 (0232) 28-86-00</a>',
            'юрисконсульт - <a href="tel:+375232256994">8 (0232) 25-69-94</a>',
        ),
    ),
    array(
        'title' => 'Телефоны экстренной анонимной психологической помощи',
        'subtitle' => 'работают круглосуточно',
        'lines' => array(
            '<a href="tel:133">133</a>',
            '<a href="tel:+375232315161">8 (0232) 31-51-61</a>',
        ),
    ),
    array(
        'title' => 'Консультативная помощь',
        'subtitle' => 'круглосуточно и анонимно по телефону',
        'lines' => array(
            '<a href="tel:170">170</a>',
            'вызов со стационарного и мобильного телефона',
        ),
    ),
);

$crisisHelpServiceItems = array(
    'консультационно-информационные услуги;',
    'социально-психологические услуги;',
    'посреднические услуги;',
    'услуги социального патроната;',
    'и другие.',
);

$crisisHelpCrisisRoomParagraphs = array(
    'Если Вы оказались в сложной жизненной ситуации, Вам некуда идти, обратитесь в «кризисную» комнату, где Вам предоставят БЕСПЛАТНУЮ возможность проживания на необходимый для решения жизненных вопросов срок.',
    '«Кризисная комната» - специально оборудованное помещение, в котором созданы необходимые условия для временного пребывания лиц, оказавшихся в кризисной ситуации, в том числе, пострадавших от домашнего насилия.',
    'Получить направление в «кризисную» комнату МОЖНО в отделах внутренних дел администраций районов г. Гомеля. Специалисты, выдавшие Вам направление, адресуют Вас по месту расположения «кризисной» комнаты, работающей круглосуточно, без выходных и праздничных дней.',
    'В «кризисной» комнате имеется все необходимое для проживания Вас и Вашего ребенка.',
);

$crisisHelpDocuments = array(
    array(
        'title' => 'Брошюра «Останови насилие»',
        'href' => $crisisHelpDocumentHref,
        'meta' => 'PDF' . (formatCrisisHelpFileSize($crisisHelpDocumentPath) !== '' ? ', ' . formatCrisisHelpFileSize($crisisHelpDocumentPath) : ''),
        'description' => 'Информационный материал о признаках домашнего насилия, видах насилия и вариантах получения помощи.',
    ),
    array(
        'title' => 'Список территориальных центров социального обслуживания населения Гомельской области',
        'href' => $crisisHelpRegionDocumentHref,
        'meta' => 'PDF' . (formatCrisisHelpFileSize($crisisHelpRegionDocumentPath) !== '' ? ', ' . formatCrisisHelpFileSize($crisisHelpRegionDocumentPath) : ''),
        'description' => 'Контакты территориальных центров Гомельской области, куда можно обратиться за консультацией и поддержкой.',
    ),
);

$crisisHelpFinalNote = 'Помните - безвыходных ситуаций не бывает! Вы можешь все изменить и остановить насилие над собой! Главное - не молчите!';

$seoTitleMeta = $crisisHelpPageTitle . ' - ' . $crisisHelpSiteName;
$seoDescriptionMeta = 'Информация о профилактике домашнего насилия, видах помощи и контактах для обращения.';

bootstrapPublicPageVisibility($conn, '/domestic-violence-prevention.php', $crisisHelpPageTitle);
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
    <title><?php echo e($crisisHelpPageTitle); ?> - <?php echo e($crisisHelpSiteName); ?></title>
    <?php
    $seoScheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $seoHost = $_SERVER['HTTP_HOST'] ?? 'tcsonrw-gomel.by';
    $seoRequestUri = $_SERVER['REQUEST_URI'] ?? '/domestic-violence-prevention.php';
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
        .crisis-help-page {
            scroll-padding-top: calc(var(--crisis-help-page-header-offset, var(--header-height)) + 54px);
        }

        .crisis-help-main {
            position: relative;
            isolation: isolate;
            padding-top: calc(var(--crisis-help-page-header-offset, var(--header-height)) + 54px);
            padding-bottom: 74px;
        }

        .crisis-help-main::before,
        .crisis-help-main::after {
            content: "";
            position: absolute;
            top: 0;
            bottom: -86px;
            width: clamp(0px, calc((100vw - var(--width-container)) / 2), 118px);
            background: url("/img/loop-vert.png") repeat-y center top / 100% auto;
            pointer-events: none;
            z-index: 0;
        }

        .crisis-help-main::before {
            left: 0;
            -webkit-mask-image: linear-gradient(270deg, transparent 0%, rgba(0, 0, 0, 0.3) 42%, rgba(0, 0, 0, 0.76) 100%);
            mask-image: linear-gradient(270deg, transparent 0%, rgba(0, 0, 0, 0.3) 42%, rgba(0, 0, 0, 0.76) 100%);
        }

        .crisis-help-main::after {
            right: 0;
            -webkit-mask-image: linear-gradient(90deg, transparent 0%, rgba(0, 0, 0, 0.3) 42%, rgba(0, 0, 0, 0.76) 100%);
            mask-image: linear-gradient(90deg, transparent 0%, rgba(0, 0, 0, 0.3) 42%, rgba(0, 0, 0, 0.76) 100%);
        }

        .crisis-help-layout {
            position: relative;
            z-index: 1;
            display: grid;
            grid-template-columns: 315px minmax(0, 1fr);
            gap: 36px;
            align-items: start;
        }

        .crisis-help-content {
            min-width: 0;
        }

        .crisis-help-breadcrumbs {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 18px;
            color: #6a766f;
            font-size: 14px;
            line-height: 1.35;
        }

        .crisis-help-breadcrumbs__home {
            width: 16px;
            height: 16px;
            flex: 0 0 16px;
            background-color: #20604a;
            -webkit-mask: url("/img/dom.svg") no-repeat center / contain;
            mask: url("/img/dom.svg") no-repeat center / contain;
        }

        .crisis-help-breadcrumbs__separator {
            color: #aab0ac;
            font-weight: 700;
        }

        .crisis-help-breadcrumbs a {
            color: #20604a;
            text-decoration: none;
        }

        .crisis-help-breadcrumbs a:hover,
        .crisis-help-breadcrumbs a:focus-visible {
            color: #c62b30;
        }

        .crisis-help-hero {
            margin-bottom: 28px;
            padding: 28px;
            border-radius: 30px;
            background: linear-gradient(135deg, rgba(26, 84, 64, 0.08), rgba(214, 181, 107, 0.2));
            border: 1px solid rgba(32, 96, 74, 0.14);
            box-shadow: 0 22px 44px rgba(17, 52, 40, 0.08);
        }

        .crisis-help-title-row {
            margin-bottom: 16px;
        }

        .crisis-help-title {
            margin: 0;
            color: #14352b;
            font-size: clamp(30px, 4vw, 42px);
            line-height: 1.1;
        }

        .crisis-help-slogan {
            display: block;
            margin: 0 0 14px;
            color: #15553d;
            font-size: 13px;
            font-weight: 800;
            letter-spacing: 0.12em;
            line-height: 1.45;
            text-transform: uppercase;
        }

        .crisis-help-lead {
            max-width: 860px;
            margin: 0;
            color: #355448;
            font-size: 17px;
            line-height: 1.65;
        }

        .crisis-help-section {
            margin-bottom: 28px;
        }

        .crisis-help-section__title {
            margin: 0 0 14px;
            color: #14352b;
            font-size: 26px;
            line-height: 1.2;
        }

        .crisis-help-card {
            padding: 24px;
            border-radius: 24px;
            background: rgba(255, 255, 255, 0.96);
            border: 1px solid rgba(32, 96, 74, 0.12);
            box-shadow: 0 16px 34px rgba(17, 52, 40, 0.07);
        }

        .crisis-help-text {
            margin: 0 0 14px;
            color: #355448;
            font-size: 16px;
            line-height: 1.7;
        }

        .crisis-help-text:last-child {
            margin-bottom: 0;
        }

        .crisis-help-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 18px;
        }

        .crisis-help-topic-card {
            height: 100%;
            padding: 22px;
            border-radius: 24px;
            background: rgba(255, 255, 255, 0.96);
            border: 1px solid rgba(32, 96, 74, 0.12);
            box-shadow: 0 16px 34px rgba(17, 52, 40, 0.07);
        }

        .crisis-help-topic-card__title {
            margin: 0 0 14px;
            color: #15553d;
            font-size: 21px;
            line-height: 1.3;
        }

        .crisis-help-topic-card__subtitle {
            display: block;
            margin: -2px 0 12px;
            color: #6e7f76;
            font-size: 13px;
            font-weight: 700;
            line-height: 1.4;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .crisis-help-list {
            margin: 0;
            padding-left: 20px;
            color: #355448;
            font-size: 15px;
            line-height: 1.65;
        }

        .crisis-help-list li + li {
            margin-top: 10px;
        }

        .crisis-help-docs {
            display: grid;
            gap: 14px;
        }

        .crisis-help-doc-card {
            display: grid;
            grid-template-columns: 52px minmax(0, 1fr) auto;
            gap: 18px;
            align-items: center;
            padding: 20px 22px;
            border-radius: 18px;
            background: rgba(255, 255, 255, 0.96);
            border: 1px solid rgba(32, 96, 74, 0.12);
            box-shadow: 0 14px 30px rgba(48, 56, 52, 0.08);
        }

        .crisis-help-doc-card__icon {
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

        .crisis-help-doc-card__title {
            display: block;
            color: #15553d;
            font-size: 17px;
            font-weight: 700;
            line-height: 1.35;
            text-decoration: none;
        }

        .crisis-help-doc-card__title:hover,
        .crisis-help-doc-card__title:focus-visible {
            color: #c62b30;
        }

        .crisis-help-doc-card__meta {
            display: block;
            margin-top: 4px;
            color: #5f6b63;
            font-size: 14px;
            line-height: 1.35;
        }

        .crisis-help-doc-card__description {
            display: block;
            margin-top: 8px;
            color: #355448;
            font-size: 15px;
            line-height: 1.5;
        }

        .crisis-help-doc-card__button {
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

        .crisis-help-doc-card__button:hover,
        .crisis-help-doc-card__button:focus-visible {
            border-color: rgba(213, 51, 49, 0.3);
            color: #c62b30;
            transform: translateY(-1px);
        }

        .crisis-help-rich-text {
            color: #355448;
            font-size: 16px;
            line-height: 1.7;
        }

        .crisis-help-rich-text p {
            margin: 0 0 14px;
        }

        .crisis-help-rich-text p:last-child {
            margin-bottom: 0;
        }

        .crisis-help-rich-text a {
            color: #20604a;
            text-decoration: none;
        }

        .crisis-help-rich-text a:hover,
        .crisis-help-rich-text a:focus-visible {
            color: #c62b30;
        }

        .crisis-help-note {
            padding: 18px 20px;
            border-radius: 18px;
            background: rgba(242, 247, 244, 0.96);
            border: 1px solid rgba(32, 96, 74, 0.12);
            color: #14352b;
            font-size: 17px;
            font-weight: 600;
            line-height: 1.6;
        }

        @media (max-width: 1080px) {
            .crisis-help-layout,
            .crisis-help-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 767px) {
            .crisis-help-main {
                padding-top: calc(var(--crisis-help-page-header-offset, var(--header-height)) + 34px);
                padding-bottom: 58px;
            }

            .crisis-help-hero {
                padding: 20px;
                border-radius: 22px;
            }

            .crisis-help-title {
                font-size: 28px;
            }

            .crisis-help-section__title {
                font-size: 22px;
            }

            .crisis-help-card,
            .crisis-help-topic-card,
            .crisis-help-doc-card {
                padding: 18px;
            }

            .crisis-help-doc-card {
                grid-template-columns: minmax(0, 1fr);
            }

            .crisis-help-doc-card__button {
                width: 100%;
            }
        }
    </style>
</head>

<body class="crisis-help-page">
<?php include __DIR__ . '/header.php'; ?>
<main class="main crisis-help-main">
    <div class="crisis-help-layout container">
        <?php
        $crisisHelpMenuActive = 'domestic-violence-prevention';
        include __DIR__ . '/crisis-help-side-menu.php';
        ?>

        <div class="crisis-help-content">
            <nav class="crisis-help-breadcrumbs" aria-label="Хлебные крошки">
                <span class="crisis-help-breadcrumbs__home" aria-hidden="true"></span>
                <a href="/">Главная</a>
                <span class="crisis-help-breadcrumbs__separator" aria-hidden="true">›</span>
                <a href="/informatsiya.php">Информация</a>
                <span class="crisis-help-breadcrumbs__separator" aria-hidden="true">›</span>
                <span><?php echo e($crisisHelpSectionTitle); ?></span>
                <span class="crisis-help-breadcrumbs__separator" aria-hidden="true">›</span>
                <span><?php echo e($crisisHelpPageTitle); ?></span>
            </nav>

            <section class="crisis-help-hero" aria-labelledby="crisis-help-page-title">
                <div class="crisis-help-title-row">
                    <h1 class="crisis-help-title" id="crisis-help-page-title"><?php echo e($crisisHelpPageTitle); ?></h1>
                </div>
                <span class="crisis-help-slogan"><?php echo e($crisisHelpSlogan); ?></span>
                <p class="crisis-help-lead"><?php echo e($crisisHelpIntro); ?></p>
            </section>

            <section class="crisis-help-section" aria-labelledby="crisis-help-definition-title">
                <h2 class="crisis-help-section__title" id="crisis-help-definition-title">Что такое домашнее насилие?</h2>
                <article class="crisis-help-card">
                    <?php foreach ($crisisHelpDefinitionParagraphs as $crisisHelpDefinitionParagraph): ?>
                        <p class="crisis-help-text"><?php echo e($crisisHelpDefinitionParagraph); ?></p>
                    <?php endforeach; ?>
                    <ul class="crisis-help-list">
                        <?php foreach ($crisisHelpDefinitionItems as $crisisHelpDefinitionItem): ?>
                            <li><?php echo e($crisisHelpDefinitionItem); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </article>
            </section>

            <section class="crisis-help-section" aria-labelledby="crisis-help-contacts-title">
                <h2 class="crisis-help-section__title" id="crisis-help-contacts-title">Куда можно обратиться за помощью?</h2>
                <div class="crisis-help-grid">
                    <?php foreach ($crisisHelpContactsCards as $crisisHelpContactsCard): ?>
                        <article class="crisis-help-topic-card">
                            <h3 class="crisis-help-topic-card__title"><?php echo e($crisisHelpContactsCard['title']); ?></h3>
                            <?php if (!empty($crisisHelpContactsCard['subtitle'])): ?>
                                <span class="crisis-help-topic-card__subtitle"><?php echo e($crisisHelpContactsCard['subtitle']); ?></span>
                            <?php endif; ?>
                            <div class="crisis-help-rich-text">
                                <?php foreach ($crisisHelpContactsCard['lines'] as $crisisHelpContactsLine): ?>
                                    <p><?php echo $crisisHelpContactsLine; ?></p>
                                <?php endforeach; ?>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            </section>

            <section class="crisis-help-section" aria-labelledby="crisis-help-services-title">
                <h2 class="crisis-help-section__title" id="crisis-help-services-title">Какие виды помощи оказывает центр?</h2>
                <article class="crisis-help-card">
                    <p class="crisis-help-text">В учреждении «Территориальный центр социального обслуживания населения Железнодорожного района г. Гомеля» гражданам (семьям), пострадавшим от домашнего насилия оказываются следующие виды помощи:</p>
                    <ul class="crisis-help-list">
                        <?php foreach ($crisisHelpServiceItems as $crisisHelpServiceItem): ?>
                            <li><?php echo e($crisisHelpServiceItem); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </article>
            </section>

            <section class="crisis-help-section" aria-labelledby="crisis-help-room-title">
                <h2 class="crisis-help-section__title" id="crisis-help-room-title">Кризисная комната</h2>
                <article class="crisis-help-card">
                    <?php foreach ($crisisHelpCrisisRoomParagraphs as $crisisHelpCrisisRoomParagraph): ?>
                        <p class="crisis-help-text"><?php echo e($crisisHelpCrisisRoomParagraph); ?></p>
                    <?php endforeach; ?>
                </article>
            </section>

            <section class="crisis-help-section" aria-label="Важное сообщение">
                <div class="crisis-help-note"><?php echo e($crisisHelpFinalNote); ?></div>
            </section>

            <section class="crisis-help-section" aria-labelledby="crisis-help-documents-title">
                <h2 class="crisis-help-section__title" id="crisis-help-documents-title">Материалы для скачивания</h2>
                <div class="crisis-help-docs">
                    <?php foreach ($crisisHelpDocuments as $crisisHelpDocument): ?>
                        <article class="crisis-help-doc-card">
                            <span class="crisis-help-doc-card__icon" aria-hidden="true"><?php echo e((string) ($crisisHelpDocument['meta'] !== '' ? strtok((string) $crisisHelpDocument['meta'], ',') : 'FILE')); ?></span>
                            <div>
                                <a class="crisis-help-doc-card__title" href="<?php echo e($crisisHelpDocument['href']); ?>"><?php echo e($crisisHelpDocument['title']); ?></a>
                                <?php if (!empty($crisisHelpDocument['meta'])): ?>
                                    <span class="crisis-help-doc-card__meta"><?php echo e($crisisHelpDocument['meta']); ?></span>
                                <?php endif; ?>
                                <?php if (!empty($crisisHelpDocument['description'])): ?>
                                    <span class="crisis-help-doc-card__description"><?php echo e($crisisHelpDocument['description']); ?></span>
                                <?php endif; ?>
                            </div>
                            <a class="crisis-help-doc-card__button" href="<?php echo e($crisisHelpDocument['href']); ?>" download>Скачать</a>
                        </article>
                    <?php endforeach; ?>
                </div>
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

        function syncCrisisHelpPageOffset() {
            root.style.setProperty('--crisis-help-page-header-offset', header.offsetHeight + 'px');
        }

        syncCrisisHelpPageOffset();
        window.addEventListener('load', syncCrisisHelpPageOffset);
        window.addEventListener('resize', syncCrisisHelpPageOffset);
    })();
</script>
</body>

</html>
