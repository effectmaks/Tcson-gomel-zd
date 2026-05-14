<?php
require_once __DIR__ . '/lib/security.php';

$contactsPageTitle = 'Контактная информация';
$contactsSiteName = 'ТЦСОН Железнодорожного района г. Гомеля';
$contactsAddress = '246032, Республика Беларусь, г. Гомель, ул. 50 лет БССР, д. 19';
$contactsPhoneMain = '8 (0232) 21-09-46';
$contactsPhoneSecond = '8 (0232) 34-98-99';
$contactsFax = '8 (0232) 21-09-46';
$contactsEmail = 'officer@tcsonrw-gomel.by';
$contactsWebsite = 'tcsonrw-gomel.by';
$contactsMapQuery = rawurlencode('ТЦСОН Железнодорожного района г. Гомеля, Гомель, улица 50 лет БССР, 19');
$contactsMapFrameUrl = 'https://yandex.by/map-widget/v1/?mode=search&text=' . $contactsMapQuery . '&z=16';
$contactsMapRouteUrl = 'https://yandex.by/maps/?mode=search&text=' . $contactsMapQuery;
$contactsLeadershipRows = array(
    array(
        'role' => 'Директор',
        'name' => 'Забавчик Наталья Александровна',
        'phone' => '8 (0232) 27-72-53',
        'phone_href' => '+375232277253',
        'room' => 'каб. № 11',
    ),
    array(
        'role' => 'Заместитель директора',
        'name' => 'Снежкова Екатерина Петровна',
        'phone' => '8 (0232) 29-45-68',
        'phone_href' => '+375232294568',
        'room' => '1 этаж',
    ),
    array(
        'role' => 'Главный бухгалтер',
        'name' => 'Смагур Ольга Анатольевна',
        'phone' => '8 (0232) 23-06-73',
        'phone_href' => '+375232230673',
        'room' => 'каб. № 1',
    ),
    array(
        'role' => 'Заведующий отделением первичного приема, информации, анализа и прогнозирования',
        'name' => 'Волчкова Виктория Станиславовна',
        'phone' => '8 (0232) 34-98-99',
        'phone_href' => '+375232349899',
        'room' => 'каб. № 3',
    ),
    array(
        'role' => 'Заведующий отделением социальной поддержки населения',
        'name' => 'Коржова Елена Викторовна',
        'phone' => '8 (0232) 34-97-95',
        'phone_href' => '+375232349795',
        'room' => 'каб. № 4',
    ),
    array(
        'role' => 'Заведующий отделением опеки и попечительства',
        'name' => 'Коржова Карина Валерьевна',
        'phone' => '8 (0232) 55-00-36',
        'phone_href' => '+375232550036',
        'room' => '',
    ),
    array(
        'role' => 'Заведующий отделением социальной помощи на дому',
        'name' => 'Светюха Наталья Михайловна',
        'phone' => '8 (0232) 34-98-97',
        'phone_href' => '+375232349897',
        'room' => 'каб. № 6',
    ),
    array(
        'role' => 'Заведующий отделением комплексной поддержки в кризисной ситуации',
        'name' => 'Дайнеко Ирина Сергеевна',
        'phone' => '8 (0232) 34-97-92',
        'phone_href' => '+375232349792',
        'room' => 'каб. № 8',
    ),
    array(
        'role' => 'Заведующий отделением дневного пребывания для граждан пожилого возраста',
        'name' => 'Усова Лилия Евгеньевна',
        'phone' => '8 (0232) 35-75-63',
        'phone_href' => '+375232357563',
        'room' => 'ул. Юбилейная, д. 8/2',
    ),
    array(
        'role' => 'Заведующий отделением социальной реабилитации, абилитации инвалидов',
        'name' => 'Кулаковская Алина Егоровна',
        'phone' => '8 (0232) 34-99-76',
        'phone_href' => '+375232349976',
        'room' => 'каб. № 9',
    ),
    array(
        'role' => 'Инженер по охране труда',
        'name' => 'Солдатенко Людмила Григорьевна',
        'phone' => '8 (0232) 22-32-28',
        'phone_href' => '+375232223228',
        'room' => 'каб. № 9',
    ),
    array(
        'role' => 'Специалист по кадрам',
        'name' => 'Лысюк Илона Григорьевна',
        'phone' => '8 (0232) 20-97-75',
        'phone_href' => '+375232209775',
        'room' => 'каб. № 10',
    ),
    array(
        'role' => 'Юрисконсульт',
        'name' => 'Анашкина Татьяна Сергеевна',
        'phone' => '8 (0232) 25-69-94',
        'phone_href' => '+375232256994',
        'room' => 'каб. № 7',
    ),
);

$seoTitleMeta = 'Контактная информация - ТЦСОН Железнодорожного района г. Гомеля';
$seoDescriptionMeta = 'Адрес, телефоны, руководство учреждения и карта проезда ТЦСОН Железнодорожного района г. Гомеля.';
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
    <title><?php echo e($contactsPageTitle); ?> - <?php echo e($contactsSiteName); ?></title>
    <?php
    $seoScheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $seoHost = $_SERVER['HTTP_HOST'] ?? 'tcsonrw-gomel.by';
    $seoRequestUri = $_SERVER['REQUEST_URI'] ?? '/contacts.php';
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
        .contacts-page-main {
            position: relative;
            isolation: isolate;
            padding-top: calc(var(--contacts-page-header-offset, var(--header-height)) + 54px);
            padding-bottom: 74px;
        }

        .contacts-page {
            scroll-padding-top: calc(var(--contacts-page-header-offset, var(--header-height)) + 54px);
        }

        #contact-info,
        #fax-card,
        #contacts-leadership {
            scroll-margin-top: calc(var(--contacts-page-header-offset, var(--header-height)) + 54px);
        }

        .contacts-page-main::before,
        .contacts-page-main::after {
            content: "";
            position: absolute;
            top: 0;
            bottom: -86px;
            width: clamp(0px, calc((100vw - var(--width-container)) / 2), 118px);
            background: url("/img/loop-vert.png") repeat-y center top / 100% auto;
            pointer-events: none;
            z-index: 0;
        }

        .contacts-page-main::before {
            left: 0;
            -webkit-mask-image: linear-gradient(270deg, transparent 0%, rgba(0, 0, 0, 0.3) 42%, rgba(0, 0, 0, 0.76) 100%);
            mask-image: linear-gradient(270deg, transparent 0%, rgba(0, 0, 0, 0.3) 42%, rgba(0, 0, 0, 0.76) 100%);
        }

        .contacts-page-main::after {
            right: 0;
            -webkit-mask-image: linear-gradient(90deg, transparent 0%, rgba(0, 0, 0, 0.3) 42%, rgba(0, 0, 0, 0.76) 100%);
            mask-image: linear-gradient(90deg, transparent 0%, rgba(0, 0, 0, 0.3) 42%, rgba(0, 0, 0, 0.76) 100%);
        }

        .contacts-page-layout {
            position: relative;
            z-index: 1;
            display: grid;
            grid-template-columns: 315px minmax(0, 1fr);
            gap: 36px;
            align-items: start;
        }

        .contacts-page-content {
            min-width: 0;
        }

        .contacts-page-breadcrumbs {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 18px;
            color: #6a766f;
            font-size: 14px;
            line-height: 1.35;
        }

        .contacts-page-breadcrumbs__home {
            width: 16px;
            height: 16px;
            flex: 0 0 16px;
            background-color: #20604a;
            -webkit-mask: url("/img/dom.svg") no-repeat center / contain;
            mask: url("/img/dom.svg") no-repeat center / contain;
        }

        .contacts-page-breadcrumbs__separator {
            color: #aab0ac;
            font-weight: 700;
        }

        .contacts-page-breadcrumbs a {
            color: #20604a;
            text-decoration: none;
        }

        .contacts-page-breadcrumbs a:hover,
        .contacts-page-breadcrumbs a:focus-visible {
            color: #c62b30;
        }

        .contacts-page-title {
            position: relative;
            margin: 0 0 24px;
            padding-left: 20px;
            color: #15553d;
            font-size: clamp(30px, 3vw, 40px);
            font-weight: 700;
            line-height: 1.12;
        }

        .contacts-page-title::before {
            content: "";
            position: absolute;
            left: 0;
            top: 3px;
            bottom: 5px;
            width: 5px;
            border-radius: 999px;
            background: #d53331;
        }

        .contacts-info-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 14px;
        }

        .contacts-info-card {
            display: flex;
            gap: 22px;
            min-width: 0;
            min-height: 106px;
            padding: 22px 26px;
            border: 1px solid rgba(32, 96, 74, 0.08);
            border-radius: 16px;
            background: rgba(255, 255, 255, 0.94);
            box-shadow: 0 14px 30px rgba(48, 56, 52, 0.08);
        }

        .contacts-info-card--wide {
            grid-column: 1 / -1;
        }

        .contacts-info-card__icon {
            width: 42px;
            height: 42px;
            flex: 0 0 42px;
            margin-top: 2px;
            background-color: #15553d;
        }

        .contacts-info-card__icon--location {
            -webkit-mask: url("/img/location.svg") no-repeat center / contain;
            mask: url("/img/location.svg") no-repeat center / contain;
        }

        .contacts-info-card__icon--phone {
            -webkit-mask: url("/img/zvonok.svg") no-repeat center / contain;
            mask: url("/img/zvonok.svg") no-repeat center / contain;
        }

        .contacts-info-card__icon--email {
            -webkit-mask: url("/img/email.svg") no-repeat center / contain;
            mask: url("/img/email.svg") no-repeat center / contain;
        }

        .contacts-info-card__icon--site {
            -webkit-mask: url("/img/www.svg") no-repeat center / contain;
            mask: url("/img/www.svg") no-repeat center / contain;
        }

        .contacts-info-card__icon--time {
            -webkit-mask: url("/img/time.svg") no-repeat center / contain;
            mask: url("/img/time.svg") no-repeat center / contain;
        }

        .contacts-info-card__icon--info {
            -webkit-mask: url("/img/info.svg") no-repeat center / contain;
            mask: url("/img/info.svg") no-repeat center / contain;
        }

        .contacts-info-card__icon--fax {
            -webkit-mask: url("/img/fax.svg") no-repeat center / contain;
            mask: url("/img/fax.svg") no-repeat center / contain;
        }

        .contacts-info-card__icon--bus {
            -webkit-mask: url("/img/bus.svg") no-repeat center / contain;
            mask: url("/img/bus.svg") no-repeat center / contain;
        }

        .contacts-info-card__icon--law {
            -webkit-mask: url("/img/sud.svg") no-repeat center / contain;
            mask: url("/img/sud.svg") no-repeat center / contain;
        }

        .contacts-info-card__body {
            min-width: 0;
            color: #252c29;
            font-size: 17px;
            line-height: 1.45;
        }

        .contacts-info-card__label {
            display: block;
            margin-bottom: 4px;
            color: #202522;
            font-size: 17px;
            font-weight: 700;
            line-height: 1.2;
        }

        .contacts-info-card__line {
            display: block;
            overflow-wrap: anywhere;
        }

        .contacts-info-card__accent {
            color: #d53331;
            font-weight: 700;
        }

        .contacts-info-card a {
            color: #15553d;
            text-decoration: none;
        }

        .contacts-info-card a:hover,
        .contacts-info-card a:focus-visible {
            color: #c62b30;
        }

        .contacts-map {
            margin-top: 14px;
            border: 1px solid rgba(32, 96, 74, 0.1);
            border-radius: 16px;
            background: #fff;
            box-shadow: 0 16px 34px rgba(48, 56, 52, 0.08);
            overflow: hidden;
        }

        .contacts-map iframe {
            display: block;
            width: 100%;
            height: 360px;
            border: 0;
            background: #f3eee8;
        }

        .contacts-section {
            margin-top: 34px;
        }

        .contacts-section__title {
            margin: 0 0 16px;
            color: #196847;
            font-size: 26px;
            font-weight: 700;
            line-height: 1.16;
        }

        .contacts-leadership-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 14px;
        }

        .contacts-leadership-card {
            min-width: 0;
            padding: 18px 20px;
            border: 1px solid rgba(32, 96, 74, 0.1);
            border-radius: 16px;
            background: rgba(255, 255, 255, 0.94);
            box-shadow: 0 14px 30px rgba(48, 56, 52, 0.08);
        }

        .contacts-leadership-card__title {
            margin: 0 0 9px;
            color: #15553d;
            font-size: 16px;
            font-weight: 800;
            line-height: 1.28;
        }

        .contacts-leadership-card__name {
            margin: 0 0 8px;
            color: #252c29;
            font-size: 17px;
            font-weight: 700;
            line-height: 1.35;
        }

        .contacts-leadership-card__meta {
            display: flex;
            flex-wrap: wrap;
            gap: 6px 14px;
            color: #4f655d;
            font-size: 15px;
            line-height: 1.35;
        }

        .contacts-leadership-card__meta a {
            color: #15553d;
            font-weight: 700;
            text-decoration: none;
        }

        .contacts-leadership-card__meta a:hover,
        .contacts-leadership-card__meta a:focus-visible {
            color: #c62b30;
        }

        @media (max-width: 1100px) {
            .contacts-page-layout {
                grid-template-columns: 260px minmax(0, 1fr);
                gap: 24px;
            }

            .contacts-info-card {
                padding: 20px;
            }

            .contacts-leadership-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 860px) {
            .contacts-page-main {
                padding-top: calc(var(--contacts-page-header-offset, var(--header-height)) + 38px);
                padding-bottom: 58px;
            }

            .contacts-page {
                scroll-padding-top: calc(var(--contacts-page-header-offset, var(--header-height)) + 38px);
            }

            #contact-info,
            #fax-card,
            #contacts-leadership {
                scroll-margin-top: calc(var(--contacts-page-header-offset, var(--header-height)) + 38px);
            }

            .contacts-page-main::before,
            .contacts-page-main::after {
                display: none;
            }

            .contacts-page-layout {
                grid-template-columns: 1fr;
            }

            .contacts-info-grid {
                grid-template-columns: 1fr;
            }

            .contacts-map iframe {
                height: 320px;
            }
        }

        @media (max-width: 620px) {
            .contacts-page-title {
                font-size: 30px;
            }

            .contacts-info-card {
                gap: 16px;
                padding: 18px 16px;
            }

            .contacts-info-card__icon {
                width: 34px;
                height: 34px;
                flex-basis: 34px;
            }

            .contacts-info-card__body {
                font-size: 16px;
            }

            .contacts-map iframe {
                height: 280px;
            }
        }
    </style>
</head>

<body class="contacts-page">
<?php include __DIR__ . '/header.php'; ?>
<main class="main contacts-page-main">
    <div class="contacts-page-layout container">
        <?php
        $contactsMenuActive = 'contacts';
        include __DIR__ . '/contacts-side-menu.php';
        ?>

        <div class="contacts-page-content">
            <nav class="contacts-page-breadcrumbs" aria-label="Хлебные крошки">
                <span class="contacts-page-breadcrumbs__home" aria-hidden="true"></span>
                <a href="/">Главная</a>
                <span class="contacts-page-breadcrumbs__separator" aria-hidden="true">›</span>
                <span><?php echo e($contactsPageTitle); ?></span>
            </nav>

            <section id="contact-info" aria-labelledby="contacts-page-title">
                <h1 class="contacts-page-title" id="contacts-page-title"><?php echo e($contactsPageTitle); ?></h1>

                <div class="contacts-info-grid">
                    <article class="contacts-info-card contacts-info-card--wide">
                        <span class="contacts-info-card__icon contacts-info-card__icon--location" aria-hidden="true"></span>
                        <div class="contacts-info-card__body">
                            <strong class="contacts-info-card__label">Адрес</strong>
                            <span class="contacts-info-card__line"><?php echo e($contactsAddress); ?></span>
                        </div>
                    </article>

                    <article class="contacts-info-card">
                        <span class="contacts-info-card__icon contacts-info-card__icon--phone" aria-hidden="true"></span>
                        <div class="contacts-info-card__body">
                            <strong class="contacts-info-card__label">Телефон</strong>
                            <a class="contacts-info-card__line" href="tel:+375232210946"><?php echo e($contactsPhoneMain); ?></a>
                            <a class="contacts-info-card__line" href="tel:+375232349899"><?php echo e($contactsPhoneSecond); ?></a>
                            <span class="contacts-info-card__line contacts-info-card__accent">пн-пт: 8:30 - 17:30</span>
                        </div>
                    </article>

                    <article class="contacts-info-card">
                        <span class="contacts-info-card__icon contacts-info-card__icon--email" aria-hidden="true"></span>
                        <div class="contacts-info-card__body">
                            <strong class="contacts-info-card__label">E-mail</strong>
                            <a class="contacts-info-card__line" href="mailto:<?php echo e($contactsEmail); ?>"><?php echo e($contactsEmail); ?></a>
                            <span class="contacts-info-card__line">для деловой переписки</span>
                        </div>
                    </article>

                    <article class="contacts-info-card" id="fax-card">
                        <span class="contacts-info-card__icon contacts-info-card__icon--fax" aria-hidden="true"></span>
                        <div class="contacts-info-card__body">
                            <strong class="contacts-info-card__label">Факс</strong>
                            <a class="contacts-info-card__line" href="tel:+375232210946"><?php echo e($contactsFax); ?></a>
                        </div>
                    </article>

                    <article class="contacts-info-card">
                        <span class="contacts-info-card__icon contacts-info-card__icon--site" aria-hidden="true"></span>
                        <div class="contacts-info-card__body">
                            <strong class="contacts-info-card__label">Сайт</strong>
                            <a class="contacts-info-card__line" href="https://<?php echo e($contactsWebsite); ?>"><?php echo e($contactsWebsite); ?></a>
                        </div>
                    </article>

                    <article class="contacts-info-card contacts-info-card--wide">
                        <span class="contacts-info-card__icon contacts-info-card__icon--bus" aria-hidden="true"></span>
                        <div class="contacts-info-card__body">
                            <strong class="contacts-info-card__label">Как добраться</strong>
                            <span class="contacts-info-card__line">Постройте маршрут до учреждения на интерактивной карте.</span>
                            <a class="contacts-info-card__line contacts-info-card__accent" href="<?php echo e($contactsMapRouteUrl); ?>" target="_blank" rel="noopener noreferrer">Открыть маршрут в Яндекс.Картах</a>
                        </div>
                    </article>
                </div>

                <div class="contacts-map">
                    <iframe
                        src="<?php echo e($contactsMapFrameUrl); ?>"
                        title="Карта проезда к ТЦСОН Железнодорожного района г. Гомеля"
                        loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade"
                    ></iframe>
                </div>
            </section>

            <section class="contacts-section" id="contacts-leadership" aria-labelledby="contacts-leadership-title">
                <h2 class="contacts-section__title" id="contacts-leadership-title">Руководство учреждения</h2>
                <div class="contacts-leadership-grid">
                    <?php foreach ($contactsLeadershipRows as $row): ?>
                    <article class="contacts-leadership-card">
                        <h3 class="contacts-leadership-card__title"><?php echo e($row['role']); ?></h3>
                        <p class="contacts-leadership-card__name"><?php echo e($row['name']); ?></p>
                        <div class="contacts-leadership-card__meta">
                            <a href="tel:<?php echo e($row['phone_href']); ?>">тел. <?php echo e($row['phone']); ?></a>
                            <?php if (!empty($row['room'])): ?>
                                <span><?php echo e($row['room']); ?></span>
                            <?php endif; ?>
                        </div>
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

        function syncContactsPageOffset() {
            root.style.setProperty('--contacts-page-header-offset', header.offsetHeight + 'px');
        }

        syncContactsPageOffset();
        window.addEventListener('load', syncContactsPageOffset);
        window.addEventListener('resize', syncContactsPageOffset);
    })();
</script>
</body>

</html>
