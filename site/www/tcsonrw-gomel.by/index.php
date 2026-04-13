<?php
session_start();

require_once __DIR__ . '/lib/security.php';
require_once __DIR__ . '/lib/auth.php';
require_once __DIR__ . '/lib/news_routing.php';
require_once __DIR__ . '/lib/photo_ordering.php';
include __DIR__ . '/db_connection.php';

ensureNewsSlugInfrastructure($conn);
ensurePhotoSortInfrastructure($conn);

function fetchHomepageFeed($conn, $limit)
{
    $limit = max(1, (int) $limit);
    $sql = "SELECT news.*, (
        SELECT filename
        FROM photos
        WHERE news_id = news.id
        ORDER BY sort_order ASC, id ASC
        LIMIT 1
    ) AS filename
    FROM news
    ORDER BY news.date DESC, news.id DESC
    LIMIT " . $limit;

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        return array();
    }

    $stmt->execute();
    $result = $stmt->get_result();
    $items = $result ? $result->fetch_all(MYSQLI_ASSOC) : array();
    $stmt->close();

    return $items;
}

function formatHomepageCardDate($value)
{
    $timestamp = strtotime((string) $value);

    return $timestamp ? date('d.m.Y', $timestamp) : '';
}

function buildHomepageExcerpt($value)
{
    $text = trim((string) preg_replace('/\s+/u', ' ', strip_tags((string) $value)));

    if ($text === '') {
        return 'Подробности доступны на странице материала.';
    }

    return $text;
}

$homepageBannerSlides = array(
    array(
        'eyebrow' => 'Указ Президента',
        'title' => '2026 год объявлен Годом белорусской женщины',
        'source' => 'president.gov.by',
        'image' => '/img/bel-woman-2026.webp',
        'image_alt' => 'Баннер 2026 года как Года белорусской женщины.',
        'href' => 'https://president.gov.by/ru/documents/ukaz-no-1-ot-1-anvara-2026-g',
        'position' => 'center center',
    ),
    array(
        'eyebrow' => 'Министерство антимонопольного регулирования и торговли',
        'title' => 'Проект "Кожная пятнiца - роднае, сваё"',
        'source' => 'belta.by',
        'image' => '/img/cozhaya-piatnica.webp',
        'image_alt' => 'Баннер проекта "Кожная пятнiца - роднае, сваё".',
        'href' => 'https://belta.by/society/view/proekt-po-prodvizheniju-otechestvennoj-produktsii-kozhnaja-pjatnitsa-rodnae-svae-zapustili-v-belarusi-753673-2025/',
        'position' => 'center center',
    ),
);

$homepageOfficialSiteCards = array(
    array(
        'title' => 'Президент Республики Беларусь',
        'href' => 'https://president.gov.by/ru',
        'image' => '/img/official-president-belarus.png',
        'image_alt' => 'Президент Республики Беларусь.',
    ),
    array(
        'title' => 'Министерство труда и социальной защиты Республики Беларусь',
        'href' => 'https://mintrud.gov.by/ru',
        'image' => '/img/official-mintrud-belarus-new.png',
        'image_alt' => 'Министерство труда и социальной защиты Республики Беларусь.',
    ),
    array(
        'title' => 'Комитет по труду, занятости и социальной защите Гомельского облисполкома',
        'href' => 'https://ktzsz-gomel.gov.by',
        'image' => '/img/official-gomel-labor-committee.png',
        'image_alt' => 'Комитет по труду, занятости и социальной защите Гомельского облисполкома.',
    ),
    array(
        'title' => 'Гомельский областной исполнительный комитет',
        'href' => 'https://gomel-region.gov.by/',
        'image' => '/img/official-gomel-city-executive-committee.svg',
        'image_alt' => 'Гомельский областной исполнительный комитет.',
    ),
    array(
        'title' => 'Пятилетка качества 2025–2029',
        'href' => 'https://president.gov.by/ru/documents/ukaz-no-31-ot-17-anvara-2025-g',
        'image' => '/img/official-quality-2025-2029.png',
        'image_alt' => 'Пятилетка качества 2025–2029.',
    ),
    array(
        'title' => 'Служба занятости',
        'href' => 'https://gsz.gov.by/directory/business-entity/187494/detail/public/',
        'image' => '/img/sluzba-zanat.svg',
        'image_alt' => 'Служба занятости.',
    ),
    array(
        'title' => 'Портал «Помогут.бай»',
        'href' => 'https://pomogut.by/',
        'image' => '/img/official-pomogut-by-new.jpg',
        'image_alt' => 'Портал «Помогут.бай».',
    ),
    array(
        'title' => 'Национальный правовой Интернет-портал',
        'href' => 'https://pravo.by/',
        'image' => '/img/official-pravo-by.jpg',
        'image_alt' => 'Национальный правовой Интернет-портал.',
    ),
);

$homepageFeedPages = array_chunk(fetchHomepageFeed($conn, 9), 3);

$seoTitleMeta = 'ТЦСОН Железнодорожного района г. Гомеля — официальный сайт';
$seoDescriptionMeta = 'Главная страница ТЦСОН Железнодорожного района г. Гомеля: новости, мероприятия и доступ в административную часть сайта.';
?>
<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="format-detection" content="telephone=no">
    <link rel="icon" href="img/favicon.png" type="image/x-icon">
    <link rel="stylesheet" href="/css/cssbootstrap.min.css?v=<?= filemtime($_SERVER['DOCUMENT_ROOT'] . '/css/cssbootstrap.min.css') ?>">
    <link rel="stylesheet" type="text/css" href="/css/slick.css?v=<?= filemtime($_SERVER['DOCUMENT_ROOT'] . '/css/slick.css') ?>">
    <link rel="stylesheet" href="/css/smartphoto.min.css?v=<?= filemtime($_SERVER['DOCUMENT_ROOT'] . '/css/smartphoto.min.css') ?>">
    <link rel="stylesheet" href="/css/style.css?v=<?= filemtime($_SERVER['DOCUMENT_ROOT'] . '/css/style.css') ?>">
    <script src="https://lidrekon.ru/slep/js/jquery.js"></script>
    <script src="https://lidrekon.ru/slep/js/uhpv-full.min.js"></script>
    <link rel="stylesheet" href="/css/normalize.css?v=<?= filemtime($_SERVER['DOCUMENT_ROOT'] . '/css/normalize.css') ?>">
    <link rel="stylesheet" href="/css/media.css?v=<?= filemtime($_SERVER['DOCUMENT_ROOT'] . '/css/media.css') ?>">
    <link rel="stylesheet" href="/css/media_mobile.css?v=<?= filemtime($_SERVER['DOCUMENT_ROOT'] . '/css/media_mobile.css') ?>">
    <title>ТЦСОН Железнодорожного района г. Гомеля</title>
    <?php
    $seoScheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $seoHost = $_SERVER['HTTP_HOST'] ?? 'tcsonrw-gomel.by';
    $seoRequestUri = $_SERVER['REQUEST_URI'] ?? '/';
    $seoPath = strtok($seoRequestUri, '?');
    $seoCanonical = $seoScheme . '://' . $seoHost . $seoPath;
    $seoTitleMeta = $seoTitleMeta ?? 'ТЦСОН Железнодорожного района г. Гомеля';
    $seoDescriptionMeta = $seoDescriptionMeta ?? 'Официальный сайт ТЦСОН Железнодорожного района г. Гомеля. Новости, мероприятия и административная часть.';
    $seoOgImage = $seoOgImage ?? '/img/logo-main.png';
    $seoRobotsMeta = $seoRobotsMeta ?? 'index,follow';
    $seoOgImageUrl = preg_match('#^https?://#i', $seoOgImage)
        ? $seoOgImage
        : ($seoScheme . '://' . $seoHost . $seoOgImage);
    ?>
    <meta name="description" content="<?php echo htmlspecialchars($seoDescriptionMeta, ENT_QUOTES, 'UTF-8'); ?>">
    <meta name="robots" content="<?php echo htmlspecialchars($seoRobotsMeta, ENT_QUOTES, 'UTF-8'); ?>">
    <link rel="canonical" href="<?php echo htmlspecialchars($seoCanonical, ENT_QUOTES, 'UTF-8'); ?>">
    <meta property="og:locale" content="ru_RU">
    <meta property="og:type" content="website">
    <meta property="og:title" content="<?php echo htmlspecialchars($seoTitleMeta, ENT_QUOTES, 'UTF-8'); ?>">
    <meta property="og:description" content="<?php echo htmlspecialchars($seoDescriptionMeta, ENT_QUOTES, 'UTF-8'); ?>">
    <meta property="og:url" content="<?php echo htmlspecialchars($seoCanonical, ENT_QUOTES, 'UTF-8'); ?>">
    <meta property="og:image" content="<?php echo htmlspecialchars($seoOgImageUrl, ENT_QUOTES, 'UTF-8'); ?>">
    <style>
        .homepage-page {
            --homepage-header-offset: var(--header-height);
            --homepage-carousel-page-gap: 30px;
        }

        .homepage-section {
            margin-bottom: 48px;
        }

        .homepage-section--first {
            padding-top: calc(var(--homepage-header-offset) + 24px);
        }

        .homepage-banner {
            position: relative;
        }

        .homepage-official-sites {
            position: relative;
        }

        .homepage-official-sites__shell {
            position: relative;
            padding: 28px 0 18px;
            border-radius: 28px;
            background: transparent;
            border: none;
            box-shadow: none;
        }

        .homepage-official-sites__top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
            margin-bottom: 24px;
        }

        .homepage-official-sites__heading {
            display: inline-flex;
            align-items: center;
            gap: 12px;
            min-width: 0;
        }

        .homepage-official-sites__title-icon {
            width: 36px;
            height: 36px;
            flex: 0 0 36px;
            object-fit: contain;
        }

        .homepage-official-sites__title {
            margin: 0;
            color: #15553d;
            font-size: 28px;
            font-weight: 700;
            line-height: 1.14;
        }

        .homepage-official-sites__controls {
            display: inline-flex;
            align-items: center;
            gap: 14px;
        }

        .homepage-official-sites__arrow {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 48px;
            height: 48px;
            border: 1px solid rgba(24, 61, 49, 0.2);
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.98);
            color: #1c4032;
            box-shadow: 0 6px 16px rgba(29, 59, 45, 0.08);
            cursor: pointer;
            transition: transform .18s ease, color .18s ease, border-color .18s ease, box-shadow .18s ease;
        }

        .homepage-official-sites__arrow:hover,
        .homepage-official-sites__arrow:focus-visible {
            color: #c62b30;
            border-color: rgba(198, 43, 48, 0.28);
            transform: translateY(-1px);
            box-shadow: 0 8px 18px rgba(29, 59, 45, 0.12);
        }

        .homepage-official-sites__arrow[disabled] {
            opacity: 0.45;
            cursor: default;
            transform: none;
            box-shadow: 0 4px 10px rgba(29, 59, 45, 0.06);
        }

        .homepage-official-sites__arrow-icon {
            font-size: 34px;
            line-height: 1;
        }

        .homepage-official-sites__viewport {
            overflow: hidden;
            padding: 8px 2px 18px;
            margin: -8px -2px -18px;
        }

        .homepage-official-sites__track {
            --homepage-official-sites-gap: 20px;
            display: flex;
            gap: var(--homepage-official-sites-gap);
            transition: transform .38s ease;
            will-change: transform;
        }

        .homepage-official-sites__slide {
            min-width: calc((100% - (3 * var(--homepage-official-sites-gap))) / 4);
            max-width: calc((100% - (3 * var(--homepage-official-sites-gap))) / 4);
        }

        .homepage-official-sites__card {
            display: flex;
            flex-direction: column;
            min-height: 100%;
            border-radius: 26px;
            background: #fff;
            border: 1px solid rgba(26, 63, 48, 0.08);
            box-shadow:
                0 4px 10px rgba(19, 56, 42, 0.08),
                0 1px 3px rgba(19, 56, 42, 0.05);
            overflow: hidden;
            text-decoration: none;
            transition: transform .2s ease, box-shadow .2s ease, border-color .2s ease;
        }

        .homepage-official-sites__card:hover,
        .homepage-official-sites__card:focus-visible {
            transform: translateY(-2px);
            border-color: rgba(198, 43, 48, 0.16);
            box-shadow:
                0 6px 14px rgba(19, 56, 42, 0.1),
                0 2px 4px rgba(19, 56, 42, 0.06);
        }

        .homepage-official-sites__media {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            aspect-ratio: 2.96 / 1;
            padding: 18px 20px;
            box-sizing: border-box;
            overflow: hidden;
            background: linear-gradient(180deg, #ffffff 0%, #fdfbf7 100%);
        }

        .homepage-official-sites__image {
            display: block;
            width: 100%;
            height: 78%;
            object-fit: contain;
            object-position: center center;
        }

        .homepage-official-sites__body {
            position: relative;
            padding: 18px 52px 16px 18px;
            min-height: 108px;
        }

        .homepage-official-sites__label {
            display: block;
            color: #20342b;
            font-size: 16px;
            font-weight: 700;
            line-height: 1.35;
        }

        .homepage-official-sites__link-icon {
            position: absolute;
            right: 18px;
            bottom: 16px;
            color: #d53331;
            font-size: 34px;
            line-height: 1;
        }

        .homepage-official-sites__dots {
            display: flex;
            justify-content: center;
            gap: 14px;
            margin-top: 18px;
        }

        .homepage-official-sites__dot {
            width: 12px;
            height: 12px;
            padding: 0;
            border: none;
            border-radius: 50%;
            background: rgba(27, 89, 63, 0.18);
            cursor: pointer;
            transition: transform .18s ease, background-color .18s ease;
        }

        .homepage-official-sites__dot.is-active {
            background: #15553d;
            transform: scale(1.08);
        }

        .homepage-banner__shell {
            position: relative;
            overflow: visible;
        }

        .homepage-banner__viewport-shell {
            position: relative;
            padding: 0 68px;
        }

        .homepage-banner__viewport {
            overflow: hidden;
            padding: 18px 22px 42px;
            margin: -18px -22px -42px;
        }

        .homepage-banner__track {
            display: flex;
            gap: var(--homepage-carousel-page-gap);
            transition: transform .45s ease;
        }

        .homepage-banner__slide {
            box-sizing: border-box;
            min-width: 100%;
            padding: 0;
        }

        .homepage-banner__card {
            display: flex;
            flex-direction: column;
            min-width: 0;
            border-radius: 28px;
            overflow: hidden;
            background: #fff;
            border: none;
            box-shadow:
                0 4px 10px rgba(19, 56, 42, 0.08),
                0 1px 3px rgba(19, 56, 42, 0.05);
            text-decoration: none;
            transition: transform .22s ease, border-color .22s ease, box-shadow .22s ease;
        }

        .homepage-banner__card:hover,
        .homepage-banner__card:focus-within {
            transform: translateY(-2px);
            box-shadow:
                0 6px 10px rgba(19, 56, 42, 0.1),
                0 2px 4px rgba(19, 56, 42, 0.06);
        }

        .homepage-banner__media-wrap {
            position: relative;
        }

        .homepage-banner__media-link {
            display: block;
            text-decoration: none;
        }

        .homepage-banner__media {
            position: relative;
            aspect-ratio: 3 / 1;
            overflow: hidden;
            background: transparent;
        }

        .homepage-banner__media::after {
            content: "";
            position: absolute;
            inset: 0;
            border-radius: 28px 28px 0 0;
            background: linear-gradient(180deg, rgba(0, 0, 0, 0) 58%, rgba(18, 43, 34, 0.12) 100%);
            pointer-events: none;
        }

        .homepage-banner__image {
            display: block;
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center center;
            background: transparent;
        }

        .homepage-banner__content {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 18px;
            padding: 18px 22px 22px;
            background: linear-gradient(135deg, #ffffff 0%, #fbf7f1 100%);
            text-decoration: none;
        }

        .homepage-banner__copy {
            display: flex;
            flex-direction: column;
            gap: 6px;
            min-width: 0;
        }

        .homepage-banner__eyebrow {
            color: #678173;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: .14em;
            line-height: 1.2;
            text-transform: uppercase;
        }

        .homepage-banner__headline {
            color: #143f2f;
            font-size: 24px;
            font-weight: 700;
            line-height: 1.2;
        }

        .homepage-banner__action {
            display: inline-flex;
            align-items: center;
            gap: 16px;
        }

        .homepage-banner__source {
            color: #5c6d66;
            font-size: 14px;
            line-height: 1.2;
            white-space: nowrap;
        }

        .homepage-banner__action-label {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 44px;
            padding: 10px 18px;
            border-radius: 999px;
            background: #f4e6e7;
            color: #b61f2c;
            font-size: 15px;
            font-weight: 700;
            line-height: 1.2;
            white-space: nowrap;
            transition: transform .18s ease, background-color .18s ease, color .18s ease;
        }

        .homepage-banner__card:hover .homepage-banner__action-label,
        .homepage-banner__card:focus-within .homepage-banner__action-label {
            transform: translateX(2px);
            background: #b61f2c;
            color: #fff;
        }

        .homepage-banner__arrow {
            position: absolute;
            top: 50%;
            z-index: 2;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 44px;
            height: 44px;
            margin-top: -22px;
            border: 1px solid rgba(35, 72, 58, 0.18);
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.96);
            color: #2b5744;
            box-shadow:
                0 8px 18px rgba(22, 47, 36, 0.08),
                0 2px 5px rgba(22, 47, 36, 0.04);
            cursor: pointer;
            transition: transform .18s ease, color .18s ease, border-color .18s ease, box-shadow .18s ease;
        }

        .homepage-banner__arrow:hover,
        .homepage-banner__arrow:focus-visible {
            color: #c62b30;
            border-color: rgba(198, 43, 48, 0.22);
            transform: translateY(-1px);
        }

        .homepage-banner__arrow--prev {
            left: 0;
        }

        .homepage-banner__arrow--next {
            right: 0;
        }

        .homepage-banner__arrow-icon {
            font-size: 24px;
            line-height: 1;
        }

        .homepage-banner__dots {
            position: absolute;
            left: 50%;
            bottom: 10px;
            z-index: 3;
            display: inline-flex;
            justify-content: center;
            gap: 12px;
            margin: 0;
            padding: 0;
            transform: translateX(-50%);
        }

        .homepage-banner__dot {
            width: 12px;
            height: 12px;
            padding: 0;
            border: none;
            border-radius: 50%;
            background: rgba(6, 93, 70, 0.22);
            cursor: pointer;
            transition: transform .18s ease, background-color .18s ease;
        }

        .homepage-banner__dot.is-active {
            background: #065d46;
            transform: scale(1.08);
        }

        .homepage-events__shell {
            position: relative;
            padding: 0;
            background: transparent;
            box-shadow: none;
            overflow: visible;
        }

        .homepage-events__top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
            margin-bottom: 28px;
        }

        .homepage-events__heading {
            display: inline-flex;
            align-items: center;
            gap: 14px;
        }

        .homepage-events__icon {
            width: 36px;
            height: 36px;
            flex: 0 0 36px;
            background-color: var(--accent-green-dark);
            -webkit-mask: url("/img/sobytiya.svg") no-repeat center / contain;
            mask: url("/img/sobytiya.svg") no-repeat center / contain;
        }

        .homepage-events__title {
            margin: 0;
            color: #143f2f;
            font-size: 28px;
            font-weight: 700;
            line-height: 1.2;
        }

        .homepage-events__link {
            color: #b61f2c;
            font-size: 16px;
            line-height: 1.2;
            text-decoration: underline;
            text-decoration-thickness: 1px;
            text-underline-offset: 5px;
            white-space: nowrap;
            transition: color .18s ease, transform .18s ease;
        }

        .homepage-events__link:hover,
        .homepage-events__link:focus-visible {
            color: #8f1722;
            transform: translateX(2px);
        }

        .homepage-events__viewport {
            overflow: hidden;
            padding: 18px 18px 28px;
            margin: -18px -18px -28px;
        }

        .homepage-events__viewport-shell {
            position: relative;
            padding: 0 60px;
        }

        .homepage-events__track {
            display: flex;
            gap: var(--homepage-carousel-page-gap);
            transition: transform .35s ease;
        }

        .homepage-events__page {
            box-sizing: border-box;
            min-width: 100%;
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 30px;
            padding: 0 4px;
        }

        .homepage-event-card {
            display: flex;
            flex-direction: column;
            min-width: 0;
            border-radius: 24px;
            background: #fff;
            border: 1px solid rgba(26, 63, 48, 0.08);
            box-shadow:
                0 4px 10px rgba(22, 47, 36, 0.07),
                0 1px 3px rgba(22, 47, 36, 0.04);
            overflow: hidden;
        }

        .homepage-event-card__media {
            position: relative;
            display: block;
            width: 100%;
            aspect-ratio: 1.52 / 1;
            background-color: #e5ddd7;
            background-repeat: no-repeat;
            background-position: center;
            background-size: cover;
        }

        .homepage-event-card__media::after {
            content: "";
            position: absolute;
            inset: auto 18px 18px auto;
            width: 56px;
            height: 56px;
            border-radius: 18px;
            background-color: rgba(255, 255, 255, 0.18);
            opacity: 0;
        }

        .homepage-event-card__media--placeholder {
            overflow: hidden;
        }

        .homepage-event-card__media--placeholder::before {
            content: "";
            position: absolute;
            inset: 0;
            background:
                linear-gradient(135deg, rgba(255, 255, 255, 0.18), rgba(255, 255, 255, 0)),
                radial-gradient(circle at top left, rgba(255, 255, 255, 0.26), rgba(255, 255, 255, 0) 44%);
        }

        .homepage-event-card__media--placeholder::after {
            opacity: 1;
            -webkit-mask-repeat: no-repeat;
            mask-repeat: no-repeat;
            -webkit-mask-position: center;
            mask-position: center;
            -webkit-mask-size: 28px 28px;
            mask-size: 28px 28px;
        }

        .homepage-event-card__media--news {
            background-image: linear-gradient(135deg, #f0e0da 0%, #f7f2ef 48%, #d9ebe2 100%);
        }

        .homepage-event-card__media--news::after {
            background-color: rgba(205, 43, 48, 0.92);
            -webkit-mask-image: url("/img/novosti.svg");
            mask-image: url("/img/novosti.svg");
        }

        .homepage-event-card__media--events {
            background-image: linear-gradient(135deg, #efe3d4 0%, #faf7f2 42%, #d8ede6 100%);
        }

        .homepage-event-card__media--events::after {
            background-color: rgba(25, 104, 71, 0.96);
            -webkit-mask-image: url("/img/meropriatie.svg");
            mask-image: url("/img/meropriatie.svg");
        }

        .homepage-event-card__body {
            display: flex;
            flex: 1 1 auto;
            flex-direction: column;
            padding: 18px 20px 20px;
        }

        .homepage-event-card__date {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 12px;
            color: #537264;
            font-size: 14px;
            font-weight: 600;
            line-height: 1.2;
        }

        .homepage-event-card__date-icon {
            width: 16px;
            height: 16px;
            flex: 0 0 16px;
            background-color: currentColor;
            -webkit-mask: url("/img/time.svg") no-repeat center / contain;
            mask: url("/img/time.svg") no-repeat center / contain;
        }

        .homepage-event-card__title {
            display: block;
            margin: 0 0 12px;
            color: #234839;
            font-size: 18px;
            font-weight: 700;
            line-height: 1.35;
            text-decoration: none;
            overflow: hidden;
            display: -webkit-box;
            -webkit-box-orient: vertical;
            -webkit-line-clamp: 3;
        }

        .homepage-event-card__title:hover,
        .homepage-event-card__title:focus-visible {
            color: #c62b30;
        }

        .homepage-event-card__excerpt {
            margin: 0 0 18px;
            color: #4b5b54;
            font-size: 15px;
            line-height: 1.5;
            overflow: hidden;
            display: -webkit-box;
            -webkit-box-orient: vertical;
            -webkit-line-clamp: 3;
        }

        .homepage-event-card__more {
            margin-top: auto;
            color: #d53331;
            font-size: 15px;
            font-weight: 700;
            line-height: 1.2;
            text-decoration: none;
        }

        .homepage-event-card__more:hover,
        .homepage-event-card__more:focus-visible {
            color: #ab2026;
        }

        .homepage-events__arrow {
            position: absolute;
            top: 50%;
            z-index: 2;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 44px;
            height: 44px;
            margin-top: -22px;
            border: 1px solid rgba(35, 72, 58, 0.18);
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.96);
            color: #2b5744;
            box-shadow:
                0 8px 18px rgba(22, 47, 36, 0.08),
                0 2px 5px rgba(22, 47, 36, 0.04);
            cursor: pointer;
            transition: transform .18s ease, color .18s ease, border-color .18s ease, box-shadow .18s ease;
        }

        .homepage-events__arrow:hover,
        .homepage-events__arrow:focus-visible {
            color: #c62b30;
            border-color: rgba(198, 43, 48, 0.22);
            transform: translateY(-1px);
        }

        .homepage-events__arrow--prev {
            left: 0;
        }

        .homepage-events__arrow--next {
            right: 0;
        }

        .homepage-events__arrow-icon {
            font-size: 24px;
            line-height: 1;
        }

        .homepage-events__dots {
            display: flex;
            justify-content: center;
            gap: 12px;
            margin-top: 28px;
        }

        .homepage-empty {
            padding: 28px;
            border-radius: 18px;
            background: rgba(15, 143, 79, 0.08);
            color: #355243;
        }

        .homepage-events__dot {
            width: 12px;
            height: 12px;
            padding: 0;
            border: none;
            border-radius: 50%;
            background: rgba(182, 31, 44, 0.26);
            cursor: pointer;
            transition: transform .18s ease, background-color .18s ease;
        }

        .homepage-events__dot.is-active {
            background: #b61f2c;
            transform: scale(1.08);
        }

        .homepage-service-panel {
            position: relative;
            overflow: hidden;
        }

        .homepage-service-panel__shell {
            position: relative;
            overflow: hidden;
            padding: 20px 24px 22px 88px;
            border-radius: 28px;
            background:
                radial-gradient(circle at top center, rgba(255, 255, 255, 0.82), rgba(255, 255, 255, 0) 44%),
                linear-gradient(135deg, #f3eadf 0%, #fbf8f2 44%, #eef5ef 100%);
            box-shadow:
                0 4px 10px rgba(22, 47, 36, 0.07),
                0 1px 3px rgba(22, 47, 36, 0.04);
        }

        .homepage-service-panel__ornament {
            position: absolute;
            inset: 0 auto 0 0;
            width: 54px;
            overflow: hidden;
            pointer-events: none;
        }

        .homepage-service-panel__ornament::before {
            content: "";
            position: absolute;
            inset: 0;
            background: url("/img/loop-vert.png") repeat-y left top / 100% auto;
            -webkit-mask-image: linear-gradient(270deg, transparent 0%, rgba(0, 0, 0, 0.28) 40%, rgba(0, 0, 0, 0.58) 70%, rgba(0, 0, 0, 0.82) 95%, rgba(0, 0, 0, 0.96) 100%);
            mask-image: linear-gradient(270deg, transparent 0%, rgba(0, 0, 0, 0.28) 40%, rgba(0, 0, 0, 0.58) 70%, rgba(0, 0, 0, 0.82) 95%, rgba(0, 0, 0, 0.96) 100%);
        }

        .homepage-service-panel__layout {
            position: relative;
            z-index: 1;
            display: grid;
            grid-template-columns: minmax(0, 1.06fr) minmax(0, 0.94fr);
            gap: 24px;
            align-items: start;
        }

        .homepage-service-panel__card {
            position: relative;
        }

        .homepage-service-panel__card--schedule {
            padding-left: 24px;
            border-left: 1px solid rgba(20, 63, 47, 0.16);
        }

        .homepage-service-panel__header {
            display: flex;
            align-items: flex-start;
            gap: 14px;
            margin-bottom: 12px;
        }

        .homepage-service-panel__badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 64px;
            height: 64px;
            flex: 0 0 64px;
            border-radius: 50%;
            background: linear-gradient(180deg, #195d46 0%, #0f4938 100%);
            color: #fff;
            box-shadow:
                0 10px 20px rgba(19, 73, 53, 0.16),
                inset 0 1px 0 rgba(255, 255, 255, 0.2);
        }

        .homepage-service-panel__badge svg {
            width: 30px;
            height: 30px;
        }

        .homepage-service-panel__badge-image {
            display: block;
            width: auto;
            height: 34px;
        }

        .homepage-service-panel__header-copy {
            display: flex;
            flex-direction: column;
            gap: 8px;
            min-width: 0;
        }

        .homepage-service-panel__title {
            margin: 0;
            color: #214634;
            font-size: 22px;
            font-weight: 700;
            line-height: 1.08;
            letter-spacing: 0.02em;
            text-transform: uppercase;
        }

        .homepage-service-panel__lead {
            margin: 0;
            color: #2e4138;
            font-size: 14px;
            line-height: 1.35;
        }

        .homepage-service-panel__divider {
            position: relative;
            height: 18px;
            margin-bottom: 14px;
        }

        .homepage-service-panel__divider::before,
        .homepage-service-panel__divider::after {
            content: "";
            position: absolute;
            top: 50%;
            width: calc(50% - 14px);
            border-top: 1px solid #20604a;
            transform: translateY(-50%);
        }

        .homepage-service-panel__divider::before {
            left: 0;
        }

        .homepage-service-panel__divider::after {
            right: 0;
        }

        .homepage-service-panel__divider span {
            position: absolute;
            inset: 50% auto auto 50%;
            width: 18px;
            height: 18px;
            background-color: #20604a;
            -webkit-mask: url("/img/icon-arnament.svg") no-repeat center / contain;
            mask: url("/img/icon-arnament.svg") no-repeat center / contain;
            transform: translate(-50%, -50%);
        }

        .homepage-service-panel__divider--accent-red span {
            background-color: #d53331;
        }

        .homepage-service-panel__contact-list {
            display: flex;
            flex-direction: column;
            gap: 14px;
        }

        .homepage-service-panel__contact-item {
            display: grid;
            grid-template-columns: 74px minmax(0, 1fr);
            gap: 14px;
            align-items: center;
            padding-bottom: 14px;
            border-bottom: 1px solid rgba(23, 87, 64, 0.15);
        }

        .homepage-service-panel__contact-item:last-child {
            padding-bottom: 0;
            border-bottom: none;
        }

        .homepage-service-panel__contact-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 64px;
            height: 64px;
            border-radius: 50%;
            border: 2px solid rgba(255, 255, 255, 0.78);
            box-shadow:
                0 8px 18px rgba(28, 53, 43, 0.12),
                inset 0 1px 0 rgba(255, 255, 255, 0.38);
        }

        .homepage-service-panel__contact-icon svg {
            width: 28px;
            height: 28px;
        }

        .homepage-service-panel__contact-icon-glyph {
            display: block;
            background-color: currentColor;
            -webkit-mask-repeat: no-repeat;
            mask-repeat: no-repeat;
            -webkit-mask-position: center;
            mask-position: center;
            -webkit-mask-size: contain;
            mask-size: contain;
        }

        .homepage-service-panel__contact-icon--hotline {
            background: linear-gradient(180deg, #f8d8d3 0%, #f3b7ae 100%);
            color: #b63131;
        }

        .homepage-service-panel__contact-icon--hotline .homepage-service-panel__contact-icon-glyph {
            width: 38px;
            height: 38px;
            -webkit-mask-image: url("/img/servis.svg");
            mask-image: url("/img/servis.svg");
        }

        .homepage-service-panel__contact-icon--trust {
            background: linear-gradient(180deg, #e2ecd9 0%, #cfdcbe 100%);
            color: #0f5b43;
        }

        .homepage-service-panel__contact-icon--trust .homepage-service-panel__contact-icon-glyph {
            width: 38px;
            height: 38px;
            -webkit-mask-image: url("/img/socialnaya%20podderzhka.svg");
            mask-image: url("/img/socialnaya%20podderzhka.svg");
        }

        .homepage-service-panel__contact-icon--law {
            background: linear-gradient(180deg, #efe1cc 0%, #e4cfb1 100%);
            color: #7a5538;
        }

        .homepage-service-panel__contact-icon--law .homepage-service-panel__contact-icon-glyph {
            width: 36px;
            height: 36px;
            -webkit-mask-image: url("/img/sud.svg");
            mask-image: url("/img/sud.svg");
        }

        .homepage-service-panel__contact-text {
            margin: 0 0 4px;
            color: #22342d;
            font-size: 14px;
            line-height: 1.35;
        }

        .homepage-service-panel__contact-phone {
            display: inline-block;
            color: #b63131;
            font-size: 26px;
            font-weight: 700;
            line-height: 1;
            letter-spacing: 0.02em;
            text-decoration: none;
            transition: color .18s ease, transform .18s ease;
        }

        .homepage-service-panel__contact-phone:hover,
        .homepage-service-panel__contact-phone:focus-visible {
            color: #922227;
            transform: translateX(2px);
        }

        .homepage-service-panel__schedule-board {
            overflow: hidden;
            border-radius: 22px;
            background: rgba(255, 255, 255, 0.9);
            border: 1px solid rgba(20, 63, 47, 0.08);
            box-shadow:
                0 12px 24px rgba(24, 54, 41, 0.08),
                inset 0 1px 0 rgba(255, 255, 255, 0.86);
        }

        .homepage-service-panel__schedule-row {
            display: grid;
            grid-template-columns: 58px minmax(0, 1fr) auto;
            gap: 14px;
            align-items: center;
            padding: 12px 16px;
            border-bottom: 1px solid rgba(20, 63, 47, 0.11);
        }

        .homepage-service-panel__schedule-row:last-of-type {
            border-bottom: none;
        }

        .homepage-service-panel__schedule-day {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 34px;
            padding: 6px 10px;
            border-radius: 12px;
            background: linear-gradient(180deg, #195d46 0%, #0f4938 100%);
            color: #fff;
            font-size: 13px;
            font-weight: 700;
            line-height: 1;
            letter-spacing: 0.04em;
            text-transform: uppercase;
        }

        .homepage-service-panel__schedule-label {
            color: #233831;
            font-size: 15px;
            font-weight: 600;
            line-height: 1.2;
        }

        .homepage-service-panel__schedule-time {
            color: #1a2d26;
            font-size: 17px;
            font-weight: 700;
            line-height: 1.2;
            white-space: nowrap;
        }

        @media (max-width: 900px) {
            .homepage-page {
                --homepage-carousel-page-gap: 22px;
                --homepage-mobile-inline-padding: 18px;
            }

            .homepage-banner__viewport-shell {
                padding-left: 0;
                padding-right: 0;
            }

            .homepage-official-sites__shell {
                padding: 24px 0 18px;
            }

            .homepage-official-sites__top {
                align-items: flex-start;
            }

            .homepage-official-sites__slide {
                min-width: calc((100% - (2 * var(--homepage-official-sites-gap))) / 3);
                max-width: calc((100% - (2 * var(--homepage-official-sites-gap))) / 3);
            }

            .homepage-official-sites__arrow {
                width: 42px;
                height: 42px;
            }

            .homepage-banner__viewport {
                padding-left: var(--homepage-mobile-inline-padding);
                padding-right: var(--homepage-mobile-inline-padding);
                margin-left: calc(var(--homepage-mobile-inline-padding) * -1);
                margin-right: calc(var(--homepage-mobile-inline-padding) * -1);
            }

            .homepage-banner__media {
                aspect-ratio: 2.7 / 1;
            }

            .homepage-banner__slide {
                padding: 0;
            }

            .homepage-banner__content {
                flex-direction: column;
                align-items: flex-start;
            }

            .homepage-banner__action {
                width: 100%;
                justify-content: space-between;
            }

            .homepage-banner__arrow {
                display: none;
            }

            .homepage-events__top {
                flex-direction: column;
                align-items: flex-start;
            }

            .homepage-events__viewport-shell {
                padding-left: 0;
                padding-right: 0;
            }

            .homepage-events__viewport {
                padding-left: var(--homepage-mobile-inline-padding);
                padding-right: var(--homepage-mobile-inline-padding);
                margin-left: calc(var(--homepage-mobile-inline-padding) * -1);
                margin-right: calc(var(--homepage-mobile-inline-padding) * -1);
            }

            .homepage-events__page {
                grid-template-columns: 1fr;
                gap: 22px;
                padding-left: 0;
                padding-right: 0;
            }

            .homepage-events__arrow {
                display: none;
            }

            .homepage-service-panel__shell {
                padding: 18px 18px 18px 66px;
            }

            .homepage-service-panel__ornament {
                width: 42px;
            }

            .homepage-service-panel__layout {
                grid-template-columns: 1fr;
                gap: 18px;
            }

            .homepage-service-panel__card--schedule {
                padding-left: 0;
                padding-top: 18px;
                border-left: none;
                border-top: 1px solid rgba(20, 63, 47, 0.16);
            }

            .homepage-service-panel__header {
                gap: 12px;
            }

            .homepage-service-panel__badge {
                width: 54px;
                height: 54px;
                flex-basis: 54px;
            }

            .homepage-service-panel__badge svg {
                width: 26px;
                height: 26px;
            }

            .homepage-service-panel__badge-image {
                height: 30px;
            }

            .homepage-service-panel__title {
                font-size: 20px;
            }

            .homepage-service-panel__contact-item {
                grid-template-columns: 74px minmax(0, 1fr);
                gap: 12px;
            }

            .homepage-service-panel__contact-icon {
                width: 64px;
                height: 64px;
            }

            .homepage-service-panel__contact-icon svg {
                width: 24px;
                height: 24px;
            }

            .homepage-service-panel__contact-phone {
                font-size: 24px;
            }
        }

        @media (max-width: 640px) {
            .homepage-banner__content {
                padding: 16px 18px 18px;
            }

            .homepage-official-sites__shell {
                padding: 18px 0 16px;
                border-radius: 22px;
            }

            .homepage-official-sites__top {
                flex-direction: column;
                align-items: flex-start;
                margin-bottom: 18px;
            }

            .homepage-official-sites__heading {
                gap: 10px;
            }

            .homepage-official-sites__title-icon {
                width: 36px;
                height: 36px;
                flex-basis: 36px;
            }

            .homepage-official-sites__title {
                font-size: 24px;
            }

            .homepage-official-sites__controls {
                align-self: flex-end;
            }

            .homepage-official-sites__track {
                --homepage-official-sites-gap: 16px;
            }

            .homepage-official-sites__slide {
                min-width: calc((100% - var(--homepage-official-sites-gap)) / 2);
                max-width: calc((100% - var(--homepage-official-sites-gap)) / 2);
            }

            .homepage-official-sites__card {
                border-radius: 22px;
            }

            .homepage-official-sites__body {
                padding: 16px;
            }

            .homepage-official-sites__label {
                font-size: 15px;
            }

            .homepage-banner__headline {
                font-size: 20px;
            }

            .homepage-banner__media {
                aspect-ratio: 2.45 / 1;
            }

            .homepage-banner__action {
                flex-direction: column;
                align-items: flex-start;
                gap: 12px;
            }

            .homepage-official-sites__slide {
                min-width: 100%;
                max-width: 100%;
            }

            .homepage-official-sites__controls {
                gap: 10px;
            }

            .homepage-official-sites__arrow {
                width: 38px;
                height: 38px;
            }

            .homepage-official-sites__arrow-icon {
                font-size: 24px;
            }

            .homepage-banner__dots {
                bottom: 8px;
                gap: 10px;
            }

            .homepage-service-panel__shell {
                padding: 16px 14px 16px 48px;
                border-radius: 22px;
            }

            .homepage-service-panel__ornament {
                width: 30px;
            }

            .homepage-service-panel__header {
                gap: 10px;
                margin-bottom: 10px;
            }

            .homepage-service-panel__badge {
                width: 46px;
                height: 46px;
                flex-basis: 46px;
            }

            .homepage-service-panel__badge svg {
                width: 22px;
                height: 22px;
            }

            .homepage-service-panel__badge-image {
                height: 24px;
            }

            .homepage-service-panel__title {
                font-size: 17px;
            }

            .homepage-service-panel__lead,
            .homepage-service-panel__contact-text {
                font-size: 13px;
            }

            .homepage-service-panel__divider {
                margin-bottom: 10px;
            }

            .homepage-service-panel__contact-list {
                gap: 12px;
            }

            .homepage-service-panel__contact-item {
                grid-template-columns: 1fr;
                gap: 10px;
                align-items: start;
                padding-bottom: 12px;
            }

            .homepage-service-panel__contact-icon {
                width: 64px;
                height: 64px;
            }

            .homepage-service-panel__contact-phone {
                font-size: 22px;
            }

            .homepage-service-panel__schedule-row {
                grid-template-columns: 54px minmax(0, 1fr);
                gap: 8px 12px;
                padding: 10px 12px;
            }

            .homepage-service-panel__schedule-time {
                grid-column: 2 / 3;
                font-size: 15px;
                white-space: normal;
            }

        }
    </style>
</head>

<body class="homepage-page">
<?php $pageBrandLogoSrc = '/img/logo-old-mini.webp'; ?>
<?php include __DIR__ . '/header.php'; ?>
<main class="main">
    <h1 class="main-title">ТЦСОН Железнодорожного района г. Гомеля</h1>

    <section class="homepage-section homepage-section--first homepage-banner container" aria-label="Актуальные баннеры" data-carousel data-carousel-autoplay="12000">
        <div class="homepage-banner__shell">
            <div class="homepage-banner__viewport-shell">
                <?php if (count($homepageBannerSlides) > 1): ?>
                <button class="homepage-banner__arrow homepage-banner__arrow--prev" type="button" aria-label="Предыдущий баннер" data-carousel-prev>
                    <span class="homepage-banner__arrow-icon" aria-hidden="true">‹</span>
                </button>
                <button class="homepage-banner__arrow homepage-banner__arrow--next" type="button" aria-label="Следующий баннер" data-carousel-next>
                    <span class="homepage-banner__arrow-icon" aria-hidden="true">›</span>
                </button>
                <?php endif; ?>

                <div class="homepage-banner__viewport">
                    <div class="homepage-banner__track" data-carousel-track>
                        <?php foreach ($homepageBannerSlides as $index => $slide): ?>
                        <?php
                            $slideHref = (string) ($slide['href'] ?? '');
                            $slideImage = (string) ($slide['image'] ?? '');
                            $slidePosition = trim((string) ($slide['position'] ?? ''));
                            $slideStyle = $slidePosition !== ''
                                ? 'object-position: ' . e($slidePosition) . ';'
                                : '';
                        ?>
                        <div class="homepage-banner__slide">
                            <div class="homepage-banner__card">
                                <div class="homepage-banner__media-wrap">
                                    <a
                                        class="homepage-banner__media-link"
                                        href="<?php echo e($slideHref); ?>"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        aria-label="<?php echo e($slide['title'] ?? ''); ?>"
                                    >
                                        <div class="homepage-banner__media">
                                            <img
                                                class="homepage-banner__image"
                                                src="<?php echo e($slideImage); ?>"
                                                alt="<?php echo e($slide['image_alt'] ?? ''); ?>"
                                                <?php echo $slideStyle !== '' ? 'style="' . $slideStyle . '"' : ''; ?>
                                                loading="<?php echo $index === 0 ? 'eager' : 'lazy'; ?>"
                                                decoding="async"
                                                <?php echo $index === 0 ? 'fetchpriority="high"' : ''; ?>
                                            >
                                        </div>
                                    </a>
                                    <?php if (count($homepageBannerSlides) > 1): ?>
                                    <div class="homepage-banner__dots" aria-label="Навигация баннеров">
                                        <?php foreach ($homepageBannerSlides as $dotIndex => $dotSlide): ?>
                                        <button
                                            class="homepage-banner__dot<?php echo $dotIndex === 0 ? ' is-active' : ''; ?>"
                                            type="button"
                                            aria-label="Баннер <?php echo $dotIndex + 1; ?>"
                                            data-carousel-dot="<?php echo $dotIndex; ?>"
                                        ></button>
                                        <?php endforeach; ?>
                                    </div>
                                    <?php endif; ?>
                                </div>
                                <a
                                    class="homepage-banner__content"
                                    href="<?php echo e($slideHref); ?>"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    aria-label="Открыть материал: <?php echo e($slide['title'] ?? ''); ?>"
                                >
                                    <div class="homepage-banner__copy">
                                        <span class="homepage-banner__eyebrow"><?php echo e($slide['eyebrow'] ?? ''); ?></span>
                                        <span class="homepage-banner__headline"><?php echo e($slide['title'] ?? ''); ?></span>
                                    </div>
                                    <div class="homepage-banner__action">
                                        <span class="homepage-banner__source"><?php echo e($slide['source'] ?? ''); ?></span>
                                        <span class="homepage-banner__action-label">Открыть →</span>
                                    </div>
                                </a>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="homepage-section homepage-events container" data-carousel data-carousel-autoplay="10000">
        <div class="homepage-events__shell">
            <div class="homepage-events__top">
                <div class="homepage-events__heading">
                    <span class="homepage-events__icon" aria-hidden="true"></span>
                    <h2 class="homepage-events__title">События</h2>
                </div>
                <a class="homepage-events__link" href="/listevents.php">Все события →</a>
            </div>

            <?php if ($homepageFeedPages === array()): ?>
            <div class="homepage-empty">Материалы пока не добавлены.</div>
            <?php else: ?>
            <div class="homepage-events__viewport-shell">
                <?php if (count($homepageFeedPages) > 1): ?>
                <button class="homepage-events__arrow homepage-events__arrow--prev" type="button" aria-label="Предыдущий слайд" data-carousel-prev>
                    <span class="homepage-events__arrow-icon" aria-hidden="true">‹</span>
                </button>
                <button class="homepage-events__arrow homepage-events__arrow--next" type="button" aria-label="Следующий слайд" data-carousel-next>
                    <span class="homepage-events__arrow-icon" aria-hidden="true">›</span>
                </button>
                <?php endif; ?>

                <div class="homepage-events__viewport">
                    <div class="homepage-events__track" data-carousel-track>
                        <?php foreach ($homepageFeedPages as $page): ?>
                        <div class="homepage-events__page">
                            <?php foreach ($page as $row): ?>
                            <?php
                                $itemId = (int) ($row['id'] ?? 0);
                                $title = (string) ($row['title'] ?? '');
                                $safeFilename = sanitizeStoredFilename($row['filename'] ?? '');
                                $itemType = normalizeNewsType($row['type'] ?? '') ?: 'мероприятие';
                                $mediaClass = $itemType === 'новость'
                                    ? 'homepage-event-card__media--news'
                                    : 'homepage-event-card__media--events';
                                $mediaStyle = $safeFilename !== ''
                                    ? "background-image: url('photos/" . e($safeFilename) . "');"
                                    : '';
                                $newsUrl = buildNewsUrl($row);
                            ?>
                            <article class="homepage-event-card">
                                <a
                                    class="homepage-event-card__media <?php echo e($mediaClass); ?><?php echo $safeFilename === '' ? ' homepage-event-card__media--placeholder' : ''; ?>"
                                    href="<?php echo e($newsUrl); ?>"
                                    <?php echo $mediaStyle !== '' ? 'style="' . $mediaStyle . '"' : ''; ?>
                                    aria-label="<?php echo e($title); ?>"
                                ></a>
                                <div class="homepage-event-card__body">
                                    <div class="homepage-event-card__date">
                                        <span class="homepage-event-card__date-icon" aria-hidden="true"></span>
                                        <span><?php echo e(formatHomepageCardDate($row['date'] ?? '')); ?></span>
                                    </div>
                                    <a class="homepage-event-card__title" href="<?php echo e($newsUrl); ?>">
                                        <?php echo e($title); ?>
                                    </a>
                                    <p class="homepage-event-card__excerpt"><?php echo e(buildHomepageExcerpt($row['description'] ?? '')); ?></p>
                                    <a class="homepage-event-card__more" href="<?php echo e($newsUrl); ?>">Подробнее →</a>
                                </div>
                            </article>
                            <?php endforeach; ?>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <?php if (count($homepageFeedPages) > 1): ?>
            <div class="homepage-events__dots" aria-label="Навигация карусели событий">
                <?php foreach ($homepageFeedPages as $index => $page): ?>
                <button
                    class="homepage-events__dot<?php echo $index === 0 ? ' is-active' : ''; ?>"
                    type="button"
                    aria-label="Страница <?php echo $index + 1; ?>"
                    data-carousel-dot="<?php echo $index; ?>"
                ></button>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
            <?php endif; ?>
        </div>
    </section>

    <section class="homepage-section homepage-service-panel container" aria-labelledby="homepage-service-panel-title">
        <div class="homepage-service-panel__shell">
            <div class="homepage-service-panel__ornament" aria-hidden="true"></div>

            <div class="homepage-service-panel__layout">
                <article class="homepage-service-panel__card homepage-service-panel__card--contacts">
                    <div class="homepage-service-panel__header">
                        <div class="homepage-service-panel__badge" aria-hidden="true">
                            <img class="homepage-service-panel__badge-image" src="/img/info.svg" alt="" loading="lazy" decoding="async">
                        </div>
                        <div class="homepage-service-panel__header-copy">
                            <h2 class="homepage-service-panel__title" id="homepage-service-panel-title">Важно знать</h2>
                            <p class="homepage-service-panel__lead">Ежедневно, в рабочие дни к вашим услугам информация и телефоны поддержки.</p>
                        </div>
                    </div>

                    <div class="homepage-service-panel__divider homepage-service-panel__divider--accent-red" aria-hidden="true"><span></span></div>

                        <div class="homepage-service-panel__contact-list">
                            <div class="homepage-service-panel__contact-item">
                                <div class="homepage-service-panel__contact-icon homepage-service-panel__contact-icon--hotline" aria-hidden="true">
                                    <span class="homepage-service-panel__contact-icon-glyph" aria-hidden="true"></span>
                                </div>
                                <div>
                                <p class="homepage-service-panel__contact-text">В целях оказания психологической помощи и поддержки работает телефон «Горячая линия»</p>
                                <a class="homepage-service-panel__contact-phone" href="tel:+375232349956">+375 (232) 34-99-56</a>
                            </div>
                        </div>

                        <div class="homepage-service-panel__contact-item">
                            <div class="homepage-service-panel__contact-icon homepage-service-panel__contact-icon--trust" aria-hidden="true">
                                <span class="homepage-service-panel__contact-icon-glyph" aria-hidden="true"></span>
                            </div>
                            <div>
                                <p class="homepage-service-panel__contact-text">В целях оказания психологической помощи и поддержки работает телефон «Доверие»</p>
                                <a class="homepage-service-panel__contact-phone" href="tel:+375232349792">+375 (232) 34-97-92</a>
                            </div>
                        </div>

                        <div class="homepage-service-panel__contact-item">
                            <div class="homepage-service-panel__contact-icon homepage-service-panel__contact-icon--law" aria-hidden="true">
                                <span class="homepage-service-panel__contact-icon-glyph" aria-hidden="true"></span>
                            </div>
                            <div>
                                <p class="homepage-service-panel__contact-text">Консультации юрисконсульта по правовым вопросам можно получить по телефону</p>
                                <a class="homepage-service-panel__contact-phone" href="tel:+375232256994">+375 (232) 25-69-94</a>
                            </div>
                        </div>
                    </div>
                </article>

                <article class="homepage-service-panel__card homepage-service-panel__card--schedule">
                    <div class="homepage-service-panel__header">
                        <div class="homepage-service-panel__badge" aria-hidden="true">
                            <img class="homepage-service-panel__badge-image" src="/img/time.svg" alt="" loading="lazy" decoding="async">
                        </div>
                        <div class="homepage-service-panel__header-copy">
                            <h3 class="homepage-service-panel__title">Время приема заинтересованных лиц</h3>
                            <p class="homepage-service-panel__lead">по осуществлению административных процедур</p>
                        </div>
                    </div>

                    <div class="homepage-service-panel__divider homepage-service-panel__divider--accent-red" aria-hidden="true"><span></span></div>

                    <div class="homepage-service-panel__schedule-board">
                        <div class="homepage-service-panel__schedule-row">
                            <span class="homepage-service-panel__schedule-day">ПН</span>
                            <span class="homepage-service-panel__schedule-label">Понедельник</span>
                            <span class="homepage-service-panel__schedule-time">08.00 – 13.00</span>
                        </div>
                        <div class="homepage-service-panel__schedule-row">
                            <span class="homepage-service-panel__schedule-day">ВТ</span>
                            <span class="homepage-service-panel__schedule-label">Вторник</span>
                            <span class="homepage-service-panel__schedule-time">14.00 – 20.00</span>
                        </div>
                        <div class="homepage-service-panel__schedule-row">
                            <span class="homepage-service-panel__schedule-day">СР</span>
                            <span class="homepage-service-panel__schedule-label">Среда</span>
                            <span class="homepage-service-panel__schedule-time">08.00 – 13.00</span>
                        </div>
                        <div class="homepage-service-panel__schedule-row">
                            <span class="homepage-service-panel__schedule-day">ЧТ</span>
                            <span class="homepage-service-panel__schedule-label">Четверг</span>
                            <span class="homepage-service-panel__schedule-time">08.00 – 13.00</span>
                        </div>
                        <div class="homepage-service-panel__schedule-row">
                            <span class="homepage-service-panel__schedule-day">ПТ</span>
                            <span class="homepage-service-panel__schedule-label">Пятница</span>
                            <span class="homepage-service-panel__schedule-time">08.00 – 13.00</span>
                        </div>

                    </div>
                </article>
            </div>
        </div>
    </section>

    <section class="homepage-section homepage-official-sites container" aria-labelledby="homepage-official-sites-title" data-card-carousel>
        <div class="homepage-official-sites__shell">
            <div class="homepage-official-sites__top">
                <div class="homepage-official-sites__heading">
                    <img class="homepage-official-sites__title-icon" src="/img/www.svg" alt="" aria-hidden="true" loading="lazy" decoding="async">
                    <h2 class="homepage-official-sites__title" id="homepage-official-sites-title">Официальные сайты</h2>
                </div>
                <div class="homepage-official-sites__controls">
                    <button class="homepage-official-sites__arrow" type="button" aria-label="Предыдущие сайты" data-card-carousel-prev>
                        <span class="homepage-official-sites__arrow-icon" aria-hidden="true">‹</span>
                    </button>
                    <button class="homepage-official-sites__arrow" type="button" aria-label="Следующие сайты" data-card-carousel-next>
                        <span class="homepage-official-sites__arrow-icon" aria-hidden="true">›</span>
                    </button>
                </div>
            </div>

            <div class="homepage-official-sites__viewport">
                <div class="homepage-official-sites__track" data-card-carousel-track>
                    <?php foreach ($homepageOfficialSiteCards as $index => $card): ?>
                    <article class="homepage-official-sites__slide">
                        <a
                            class="homepage-official-sites__card"
                            href="<?php echo e($card['href'] ?? ''); ?>"
                            target="_blank"
                            rel="noopener noreferrer"
                            aria-label="<?php echo e($card['title'] ?? ''); ?>"
                        >
                            <div class="homepage-official-sites__media">
                                <img
                                    class="homepage-official-sites__image"
                                    src="<?php echo e($card['image'] ?? ''); ?>"
                                    alt="<?php echo e($card['image_alt'] ?? ''); ?>"
                                    loading="<?php echo $index < 3 ? 'eager' : 'lazy'; ?>"
                                    decoding="async"
                                >
                            </div>
                            <div class="homepage-official-sites__body">
                                <span class="homepage-official-sites__label"><?php echo e($card['title'] ?? ''); ?></span>
                                <span class="homepage-official-sites__link-icon" aria-hidden="true">→</span>
                            </div>
                        </a>
                    </article>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="homepage-official-sites__dots" aria-label="Навигация по официальным сайтам" data-card-carousel-dots></div>
        </div>
    </section>

</main>
<?php include __DIR__ . '/footer.php'; ?>
<script src="/script/index.js"></script>
<script>
    (function () {
        var root = document.documentElement;
        var header = document.querySelector('.header-down');

        if (!root || !header) {
            return;
        }

        function syncHomepageHeaderOffset() {
            root.style.setProperty('--homepage-header-offset', header.offsetHeight + 'px');
        }

        syncHomepageHeaderOffset();
        window.addEventListener('resize', syncHomepageHeaderOffset);
        window.addEventListener('load', syncHomepageHeaderOffset);

        Array.prototype.forEach.call(document.querySelectorAll('[data-carousel]'), function (carousel) {
            var track = carousel.querySelector('[data-carousel-track]');
            var slides = track ? Array.prototype.slice.call(track.children) : [];
            var dots = Array.prototype.slice.call(carousel.querySelectorAll('[data-carousel-dot]'));
            var prevButton = carousel.querySelector('[data-carousel-prev]');
            var nextButton = carousel.querySelector('[data-carousel-next]');
            var currentSlide = 0;
            var autoRotateTimer = null;
            var autoRotateDelay = parseInt(carousel.getAttribute('data-carousel-autoplay') || '0', 10);

            if (!track || slides.length === 0) {
                return;
            }

            function stopAutoRotate() {
                if (autoRotateTimer) {
                    window.clearInterval(autoRotateTimer);
                    autoRotateTimer = null;
                }
            }

            function scheduleAutoRotate() {
                stopAutoRotate();

                if (slides.length < 2 || autoRotateDelay < 1000) {
                    return;
                }

                autoRotateTimer = window.setInterval(function () {
                    setCarouselSlide(currentSlide + 1);
                }, autoRotateDelay);
            }

            function setCarouselSlide(index) {
                var totalSlides = slides.length;
                var trackStyles = window.getComputedStyle(track);
                var pageGap = parseFloat(trackStyles.columnGap || trackStyles.gap || '0') || 0;

                if (totalSlides === 0) {
                    return;
                }

                currentSlide = (index + totalSlides) % totalSlides;
                track.style.transform = 'translateX(calc(' + (currentSlide * -100) + '% - ' + (currentSlide * pageGap) + 'px))';

                dots.forEach(function (dot, dotIndex) {
                    var targetSlide = Number(dot.getAttribute('data-carousel-dot'));
                    var isActive = targetSlide === currentSlide;

                    dot.classList.toggle('is-active', isActive);
                    dot.setAttribute('aria-current', isActive ? 'true' : 'false');
                });
            }

            dots.forEach(function (dot) {
                dot.addEventListener('click', function () {
                    setCarouselSlide(Number(dot.getAttribute('data-carousel-dot')) || 0);
                    scheduleAutoRotate();
                });
            });

            if (prevButton) {
                prevButton.addEventListener('click', function () {
                    setCarouselSlide(currentSlide - 1);
                    scheduleAutoRotate();
                });
            }

            if (nextButton) {
                nextButton.addEventListener('click', function () {
                    setCarouselSlide(currentSlide + 1);
                    scheduleAutoRotate();
                });
            }

            carousel.addEventListener('mouseenter', stopAutoRotate);
            carousel.addEventListener('mouseleave', scheduleAutoRotate);
            carousel.addEventListener('focusin', stopAutoRotate);
            carousel.addEventListener('focusout', function (event) {
                if (!carousel.contains(event.relatedTarget)) {
                    scheduleAutoRotate();
                }
            });

            document.addEventListener('visibilitychange', function () {
                if (document.hidden) {
                    stopAutoRotate();
                    return;
                }

                scheduleAutoRotate();
            });

            setCarouselSlide(0);
            scheduleAutoRotate();
        });

        Array.prototype.forEach.call(document.querySelectorAll('[data-card-carousel]'), function (carousel) {
            var track = carousel.querySelector('[data-card-carousel-track]');
            var slides = track ? Array.prototype.slice.call(track.children) : [];
            var dotsShell = carousel.querySelector('[data-card-carousel-dots]');
            var prevButton = carousel.querySelector('[data-card-carousel-prev]');
            var nextButton = carousel.querySelector('[data-card-carousel-next]');
            var currentIndex = 0;
            var dots = [];

            if (!track || slides.length === 0 || !dotsShell) {
                return;
            }

            function getVisibleCards() {
                if (window.innerWidth <= 640) {
                    return 1;
                }

                if (window.innerWidth <= 768) {
                    return 2;
                }

                if (window.innerWidth <= 1100) {
                    return 3;
                }

                return 4;
            }

            function getGap() {
                var styles = window.getComputedStyle(track);
                return parseFloat(styles.columnGap || styles.gap || '0') || 0;
            }

            function getMaxIndex() {
                return Math.max(0, slides.length - getVisibleCards());
            }

            function getPageCount() {
                return Math.max(1, Math.ceil(slides.length / getVisibleCards()));
            }

            function getPageIndex(index) {
                return Math.floor(Math.min(Math.max(index, 0), getMaxIndex()) / getVisibleCards());
            }

            function getScrollStep() {
                return getVisibleCards();
            }

            function buildDots() {
                var dotCount = getPageCount();

                dotsShell.innerHTML = '';
                dots = [];

                if (dotCount < 2) {
                    dotsShell.hidden = true;
                    return;
                }

                dotsShell.hidden = false;

                for (var index = 0; index < dotCount; index += 1) {
                    var dot = document.createElement('button');
                    dot.type = 'button';
                    dot.className = 'homepage-official-sites__dot';
                    dot.setAttribute('aria-label', 'Позиция ' + (index + 1));
                    dot.addEventListener('click', (function (pageIndex) {
                        return function () {
                            setCardIndex(pageIndex * getVisibleCards());
                        };
                    })(index));
                    dotsShell.appendChild(dot);
                    dots.push(dot);
                }
            }

            function setCardIndex(index) {
                var maxIndex = getMaxIndex();
                var firstSlide = slides[0];
                var slideWidth = firstSlide ? firstSlide.getBoundingClientRect().width : 0;
                var offset = (slideWidth + getGap()) * Math.min(Math.max(index, 0), maxIndex);

                currentIndex = Math.min(Math.max(index, 0), maxIndex);
                track.style.transform = 'translateX(' + (offset * -1) + 'px)';

                dots.forEach(function (dot, dotIndex) {
                    var isActive = dotIndex === getPageIndex(currentIndex);
                    dot.classList.toggle('is-active', isActive);
                    dot.setAttribute('aria-current', isActive ? 'true' : 'false');
                });

                if (prevButton) {
                    prevButton.disabled = currentIndex === 0;
                }

                if (nextButton) {
                    nextButton.disabled = currentIndex >= maxIndex;
                }
            }

            function syncCardCarousel() {
                buildDots();
                setCardIndex(Math.min(currentIndex, getMaxIndex()));
            }

            if (prevButton) {
                prevButton.addEventListener('click', function () {
                    setCardIndex(currentIndex - getScrollStep());
                });
            }

            if (nextButton) {
                nextButton.addEventListener('click', function () {
                    setCardIndex(currentIndex + getScrollStep());
                });
            }

            window.addEventListener('resize', syncCardCarousel);
            window.addEventListener('load', syncCardCarousel);
            syncCardCarousel();
        });
    })();
</script>
</body>

</html>
