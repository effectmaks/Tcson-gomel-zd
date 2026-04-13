<?php
session_start();

require_once __DIR__ . '/lib/security.php';
require_once __DIR__ . '/lib/auth.php';
require_once __DIR__ . '/lib/news_routing.php';
require_once __DIR__ . '/lib/photo_ordering.php';
require_once __DIR__ . '/lib/news_management.php';
include __DIR__ . '/db_connection.php';
require_once __DIR__ . '/Parsedown.php';

ensureNewsSlugInfrastructure($conn);
ensurePhotoSortInfrastructure($conn);
ensureNewsVideoInfrastructure($conn);

$rawRequestedSlug = trim((string) ($_GET['slug'] ?? ''));
$requestedSlug = normalizeNewsSlug($rawRequestedSlug);
$newsId = getIntFromGet('id');

if ($requestedSlug === '' && $newsId === null) {
    redirectTo('/listevents.php');
}

if ($requestedSlug !== '') {
    $stmt = $conn->prepare('SELECT * FROM news WHERE slug = ? LIMIT 1');
    if (!$stmt) {
        http_response_code(500);
        exit('Ошибка загрузки события.');
    }

    $stmt->bind_param('s', $requestedSlug);
} else {
    $stmt = $conn->prepare('SELECT * FROM news WHERE id = ? LIMIT 1');
    if (!$stmt) {
        http_response_code(500);
        exit('Ошибка загрузки события.');
    }

    $stmt->bind_param('i', $newsId);
}

$stmt->execute();
$result = $stmt->get_result();
$news = $result ? $result->fetch_assoc() : null;
$stmt->close();

if (!$news) {
    http_response_code(404);
    exit('Событие не найдено.');
}

$newsId = (int) ($news['id'] ?? 0);
$canonicalNewsPath = buildNewsUrl($news);
$storedSlug = trim((string) ($news['slug'] ?? ''));

if ($storedSlug !== '') {
    if ($requestedSlug === '' && isset($_GET['id'])) {
        redirectTo($canonicalNewsPath, 301);
    }

    if ($requestedSlug !== '' && $rawRequestedSlug !== $storedSlug) {
        redirectTo($canonicalNewsPath, 301);
    }
}

$stmtPhotos = $conn->prepare('SELECT id, filename FROM photos WHERE news_id = ? ORDER BY sort_order ASC, id ASC');
$photos = array();
if ($stmtPhotos) {
    $stmtPhotos->bind_param('i', $newsId);
    $stmtPhotos->execute();
    $photosResult = $stmtPhotos->get_result();
    $photos = $photosResult ? $photosResult->fetch_all(MYSQLI_ASSOC) : array();
    $stmtPhotos->close();
}

function formatNewsDetailNumericDate($dateValue)
{
    $timestamp = strtotime((string) $dateValue);

    return $timestamp ? date('d.m.Y', $timestamp) : '';
}

function buildNewsDetailReadingTimeLabel($text)
{
    $cleanText = trim((string) preg_replace('/\s+/u', ' ', strip_tags((string) $text)));
    if ($cleanText === '') {
        return '1 мин';
    }

    preg_match_all('/[\p{L}\p{N}]+(?:[-\'’][\p{L}\p{N}]+)*/u', $cleanText, $matches);
    $wordCount = count($matches[0] ?? array());
    $minutes = max(1, (int) ceil($wordCount / 180));

    return $minutes . ' мин';
}

function fetchNewsDetailAdjacentItem($conn, $type, $date, $id, $direction)
{
    $normalizedType = normalizeNewsType($type);
    if ($normalizedType === null || $date === '') {
        return null;
    }

    if ($direction === 'previous') {
        $query = "SELECT id, title, slug, date
            FROM news
            WHERE LOWER(type) = ?
              AND (
                date < ?
                OR (date = ? AND id < ?)
              )
            ORDER BY date DESC, id DESC
            LIMIT 1";
    } else {
        $query = "SELECT id, title, slug, date
            FROM news
            WHERE LOWER(type) = ?
              AND (
                date > ?
                OR (date = ? AND id > ?)
              )
            ORDER BY date ASC, id ASC
            LIMIT 1";
    }

    $stmt = $conn->prepare($query);
    if (!$stmt) {
        return null;
    }

    $stmt->bind_param('sssi', $normalizedType, $date, $date, $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $item = $result ? $result->fetch_assoc() : null;
    $stmt->close();

    return $item ?: null;
}

$type = normalizeNewsType($news['type'] ?? '') ?: 'мероприятие';
$title = (string) ($news['title'] ?? '');
$description = (string) ($news['description'] ?? '');
$freim = (string) ($news['freim'] ?? '');
$date = (string) ($news['date'] ?? '');
$formattedDate = formatRuDate($date);
$formattedNumericDate = formatNewsDetailNumericDate($date);
$readingTimeLabel = buildNewsDetailReadingTimeLabel($description);
$staticViewsLabel = '345 просмотров';
$galleryTitle = ($type === 'новость') ? 'Фотогалерея новости' : 'Фотогалерея с мероприятия';
$previousLabel = ($type === 'новость') ? 'Предыдущая новость' : 'Предыдущее мероприятие';
$nextLabel = ($type === 'новость') ? 'Следующая новость' : 'Следующее мероприятие';
$fallbackListUrl = ($type === 'новость') ? '/listnews.php' : '/listevents.php';
$archiveLabel = ($type === 'новость') ? 'Новости' : 'Мероприятия';

$parsedown = new Parsedown();
if (method_exists($parsedown, 'setSafeMode')) {
    $parsedown->setSafeMode(true);
}
$descriptionHtml = $parsedown->text($description);
$safeFreim = getSafeIframeHtml($freim);
$seoTitleMeta = $title . ' — ТЦСОН Железнодорожного района г. Гомеля';
$seoDescriptionMeta = trim(preg_replace('/\s+/u', ' ', strip_tags($description)));
if ($seoDescriptionMeta === '') {
    $seoDescriptionMeta = 'Новость ТЦСОН Железнодорожного района г. Гомеля.';
} elseif (mb_strlen($seoDescriptionMeta, 'UTF-8') > 160) {
    $seoDescriptionMeta = mb_substr($seoDescriptionMeta, 0, 157, 'UTF-8') . '...';
}
$seoOgImage = '/img/logo-main.png';
if (!empty($photos)) {
    $firstPhotoFilename = sanitizeStoredFilename($photos[0]['filename'] ?? '');
    if ($firstPhotoFilename !== '') {
        $seoOgImage = '/photos/' . $firstPhotoFilename;
    }
}

$photoItems = array();
foreach ($photos as $photo) {
    $safeFilename = sanitizeStoredFilename($photo['filename'] ?? '');
    if ($safeFilename === '') {
        continue;
    }

    $photoItems[] = array(
        'full' => '/photos/' . $safeFilename,
        'thumb' => '/photos/' . $safeFilename,
    );
}

$videoFilename = getNewsVideoFilename($news);
$videoUrl = $videoFilename !== null ? '/videos/' . $videoFilename : '';
$heroPhoto = $photoItems[0] ?? null;
$galleryPhotos = $photoItems;
$previousItem = fetchNewsDetailAdjacentItem($conn, $type, $date, $newsId, 'previous');
$nextItem = fetchNewsDetailAdjacentItem($conn, $type, $date, $newsId, 'next');
?>

<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
    <title><?php echo e($title); ?> ТЦСОН Железнодорожного района г. Гомеля</title>
    <?php
    $seoScheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $seoHost = $_SERVER['HTTP_HOST'] ?? 'tcsonrw-gomel.by';
    $seoCanonical = $seoScheme . '://' . $seoHost . $canonicalNewsPath;
    $seoTitleMeta = $seoTitleMeta ?? 'ТЦСОН Железнодорожного района г. Гомеля';
    $seoDescriptionMeta = $seoDescriptionMeta ?? 'Официальный сайт ТЦСОН Железнодорожного района г. Гомеля. Новости, мероприятия, услуги и контакты.';
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
        .news-detail-page {
            --news-detail-header-offset: var(--header-height);
            --news-detail-footer-gap: 88px;
            --news-detail-ornament-width: clamp(0px, calc((100vw - var(--width-container)) / 2), 118px);
            --news-detail-accent-green: #20604a;
            --news-detail-title-green: #196847;
            --news-detail-accent-red: #d53331;
            --news-detail-muted: #6a766f;
            --news-detail-border: rgba(32, 96, 74, 0.16);
            --news-detail-shadow: 0 18px 36px rgba(23, 53, 42, 0.1);
        }

        .news-detail-main {
            position: relative;
            padding-top: calc(var(--news-detail-header-offset) + 26px);
            padding-bottom: 72px;
            isolation: isolate;
        }

        .news-detail-main .container {
            position: relative;
            z-index: 1;
        }

        .news-detail-main__ornament {
            position: absolute;
            top: 0;
            bottom: calc(var(--news-detail-footer-gap) * -1);
            width: var(--news-detail-ornament-width);
            overflow: hidden;
            pointer-events: none;
            z-index: 0;
        }

        .news-detail-main__ornament::before {
            content: "";
            position: absolute;
            inset: 0;
        }

        .news-detail-main__ornament--left {
            left: 0;
        }

        .news-detail-main__ornament--left::before {
            background: url("/img/loop-vert.png") repeat-y left top / 100% auto;
            -webkit-mask-image: linear-gradient(270deg, transparent 0%, rgba(0, 0, 0, 0.28) 40%, rgba(0, 0, 0, 0.58) 70%, rgba(0, 0, 0, 0.82) 95%, rgba(0, 0, 0, 0.96) 100%);
            mask-image: linear-gradient(270deg, transparent 0%, rgba(0, 0, 0, 0.28) 40%, rgba(0, 0, 0, 0.58) 70%, rgba(0, 0, 0, 0.82) 95%, rgba(0, 0, 0, 0.96) 100%);
        }

        .news-detail-main__ornament--right {
            right: 0;
        }

        .news-detail-main__ornament--right::before {
            background: url("/img/loop-vert.png") repeat-y right top / 100% auto;
            -webkit-mask-image: linear-gradient(90deg, transparent 0%, rgba(0, 0, 0, 0.28) 40%, rgba(0, 0, 0, 0.58) 70%, rgba(0, 0, 0, 0.82) 95%, rgba(0, 0, 0, 0.96) 100%);
            mask-image: linear-gradient(90deg, transparent 0%, rgba(0, 0, 0, 0.28) 40%, rgba(0, 0, 0, 0.58) 70%, rgba(0, 0, 0, 0.82) 95%, rgba(0, 0, 0, 0.96) 100%);
        }

        .news-detail-intro {
            margin-bottom: 18px;
        }

        .news-detail-intro__shell {
            position: relative;
            padding: 22px 0 0;
        }

        .news-detail-intro__content {
            position: relative;
            z-index: 1;
        }

        .news-detail-breadcrumbs {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
            margin: 0;
            color: var(--news-detail-muted);
            font-size: 14px;
            line-height: 1.35;
        }

        .news-detail-breadcrumbs__home {
            width: 16px;
            height: 16px;
            flex: 0 0 16px;
            background-color: var(--news-detail-accent-green);
            -webkit-mask: url("/img/dom.svg") no-repeat center / contain;
            mask: url("/img/dom.svg") no-repeat center / contain;
        }

        .news-detail-breadcrumbs__separator {
            color: #aab0ac;
            font-weight: 700;
        }

        .news-detail-breadcrumbs a {
            color: var(--news-detail-accent-green);
            text-decoration: none;
        }

        .news-detail-breadcrumbs a:hover,
        .news-detail-breadcrumbs a:focus-visible {
            color: var(--news-detail-accent-red);
        }

        .news-detail-shell {
            max-width: 980px;
            margin: 0 auto;
        }

        .news-detail-content {
            display: flex;
            flex-direction: column;
            gap: 28px;
        }

        .news-detail-toolbar {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 16px;
            margin-bottom: -8px;
        }

        .news-detail-back {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            min-height: 44px;
            padding: 0 18px;
            border: 1px solid var(--news-detail-border);
            border-radius: var(--button-radius);
            background: rgba(255, 255, 255, 0.88);
            color: var(--news-detail-accent-green);
            font-size: 15px;
            font-weight: 600;
            text-decoration: none;
            transition: border-color .2s ease, color .2s ease, transform .2s ease;
        }

        .news-detail-back::before {
            content: "←";
            font-size: 16px;
            line-height: 1;
            font-weight: 700;
        }

        .news-detail-back:hover,
        .news-detail-back:focus-visible,
        .news-detail-toolbar__action:hover,
        .news-detail-toolbar__action:focus-visible {
            border-color: rgba(213, 51, 49, 0.28);
            color: var(--news-detail-accent-red);
            transform: translateY(-1px);
        }

        .news-detail-toolbar__action {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 44px;
            padding: 0 18px;
            border: 1px solid var(--news-detail-border);
            border-radius: var(--button-radius);
            background: rgba(255, 255, 255, 0.88);
            color: var(--news-detail-accent-green);
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            white-space: nowrap;
            transition: border-color .2s ease, color .2s ease, transform .2s ease;
        }

        .news-detail-title {
            margin: 0;
            color: var(--news-detail-title-green);
            font-size: 32px;
            font-weight: 700;
            line-height: 1.18;
            max-width: 860px;
        }

        .news-detail-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 14px 28px;
            margin-top: -10px;
        }

        .news-detail-meta__item {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            color: #4b665b;
            font-size: 14px;
            font-weight: 600;
            line-height: 1.4;
        }

        .news-detail-meta__icon {
            width: 18px;
            height: 18px;
            flex: 0 0 18px;
            background-color: var(--news-detail-accent-green);
        }

        .news-detail-meta__icon--date {
            -webkit-mask: url("/img/meropriatie.svg") no-repeat center / contain;
            mask: url("/img/meropriatie.svg") no-repeat center / contain;
        }

        .news-detail-meta__icon--views {
            -webkit-mask: url("/img/glaz.svg") no-repeat center / contain;
            mask: url("/img/glaz.svg") no-repeat center / contain;
        }

        .news-detail-meta__icon--time {
            -webkit-mask: url("/img/time.svg") no-repeat center / contain;
            mask: url("/img/time.svg") no-repeat center / contain;
        }

        .news-detail-hero {
            margin-top: -4px;
            display: flex;
            justify-content: flex-start;
        }

        .news-detail-hero__link,
        .news-detail-hero__placeholder {
            display: block;
            border-radius: 28px;
            overflow: hidden;
            background: linear-gradient(145deg, rgba(240, 245, 242, 0.96), rgba(224, 233, 228, 0.92));
            box-shadow: var(--news-detail-shadow);
        }

        .news-detail-hero__link {
            display: inline-flex;
            align-items: flex-start;
            justify-content: center;
            max-width: 100%;
        }

        .news-detail-hero__image {
            display: block;
            width: auto;
            max-width: 100%;
            height: auto;
            max-height: min(72vh, 760px);
        }

        .news-detail-hero__placeholder {
            display: grid;
            place-items: center;
            min-height: 360px;
            padding: 36px;
            text-align: center;
        }

        .news-detail-hero__placeholder-icon {
            width: 62px;
            height: 62px;
            margin: 0 auto 16px;
            background-color: rgba(32, 96, 74, 0.18);
            -webkit-mask: url("/img/sobytiya.svg") no-repeat center / contain;
            mask: url("/img/sobytiya.svg") no-repeat center / contain;
        }

        .news-detail-hero__placeholder-title {
            margin: 0 0 8px;
            color: var(--news-detail-title-green);
            font-size: 22px;
            font-weight: 700;
            line-height: 1.3;
        }

        .news-detail-hero__placeholder-text {
            margin: 0;
            color: var(--news-detail-muted);
            font-size: 16px;
            line-height: 1.65;
        }

        .news-detail-article {
            color: #24312d;
            font-size: 17px;
            line-height: 1.75;
        }

        .news-detail-article > *:first-child {
            margin-top: 0;
        }

        .news-detail-article > *:last-child {
            margin-bottom: 0;
        }

        .news-detail-article p,
        .news-detail-article ul,
        .news-detail-article ol,
        .news-detail-article blockquote {
            margin: 0 0 18px;
        }

        .news-detail-article h2,
        .news-detail-article h3,
        .news-detail-article h4 {
            margin: 28px 0 14px;
            color: var(--news-detail-title-green);
            line-height: 1.25;
        }

        .news-detail-article strong {
            color: #17392f;
            font-weight: 700;
        }

        .news-detail-article a {
            color: var(--news-detail-accent-red);
        }

        .news-detail-frame iframe {
            display: block;
            width: 100%;
            min-height: 360px;
            border: 0;
            border-radius: 24px;
            overflow: hidden;
            box-shadow: var(--news-detail-shadow);
        }

        .news-detail-divider {
            position: relative;
            height: 24px;
        }

        .news-detail-divider::before,
        .news-detail-divider::after {
            content: "";
            position: absolute;
            top: 50%;
            width: calc(50% - 20px);
            border-top: 2px solid var(--news-detail-accent-green);
            transform: translateY(-50%);
        }

        .news-detail-divider::before {
            left: 0;
        }

        .news-detail-divider::after {
            right: 0;
        }

        .news-detail-divider span {
            position: absolute;
            left: 50%;
            top: 50%;
            width: 24px;
            height: 24px;
            background-color: var(--news-detail-accent-red);
            -webkit-mask: url("/img/icon-arnament.svg") no-repeat center / contain;
            mask: url("/img/icon-arnament.svg") no-repeat center / contain;
            transform: translate(-50%, -50%);
        }

        .news-detail-gallery {
            display: flex;
            flex-direction: column;
            gap: 22px;
        }

        .news-detail-gallery__head {
            display: flex;
            align-items: center;
            gap: 12px;
            color: var(--news-detail-title-green);
            font-size: 20px;
            font-weight: 700;
            line-height: 1.3;
        }

        .news-detail-gallery__icon {
            width: 22px;
            height: 22px;
            flex: 0 0 22px;
            background-color: var(--news-detail-accent-green);
            -webkit-mask: url("/img/camera.svg") no-repeat center / contain;
            mask: url("/img/camera.svg") no-repeat center / contain;
        }

        .news-detail-gallery__grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 18px;
        }

        .news-detail-gallery__link {
            display: block;
            border-radius: 18px;
            overflow: hidden;
            box-shadow: 0 12px 28px rgba(23, 53, 42, 0.12);
        }

        .news-detail-gallery__image {
            display: block;
            width: 100%;
            aspect-ratio: 1.28 / 1;
            object-fit: cover;
        }

        .news-detail-gallery__empty {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 160px;
            padding: 24px;
            border: 1px dashed rgba(32, 96, 74, 0.24);
            border-radius: 22px;
            background: rgba(246, 249, 247, 0.82);
            color: var(--news-detail-muted);
            font-size: 15px;
            line-height: 1.6;
            text-align: center;
        }

        .news-detail-video {
            display: flex;
            flex-direction: column;
            gap: 22px;
        }

        .news-detail-video__head {
            display: flex;
            align-items: center;
            gap: 12px;
            color: var(--news-detail-title-green);
            font-size: 20px;
            font-weight: 700;
            line-height: 1.3;
        }

        .news-detail-video__icon {
            width: 22px;
            height: 22px;
            flex: 0 0 22px;
            background-color: var(--news-detail-accent-green);
            -webkit-mask: url("/img/meropriatie.svg") no-repeat center / contain;
            mask: url("/img/meropriatie.svg") no-repeat center / contain;
        }

        .news-detail-video__media {
            overflow: hidden;
            border-radius: 24px;
            background: #0f1513;
            box-shadow: var(--news-detail-shadow);
            isolation: isolate;
        }

        .news-detail-video__player {
            display: block;
            width: 100%;
            min-height: 360px;
            background: #0f1513;
        }

        .news-detail-video__caption {
            margin: 0;
            color: var(--news-detail-muted);
            font-size: 14px;
            line-height: 1.6;
        }

        .news-detail-neighbors {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 18px;
        }

        .news-detail-neighbors__item {
            display: flex;
            flex-direction: column;
            gap: 10px;
            min-height: 126px;
            padding: 22px 24px;
            border: 1px solid var(--news-detail-border);
            border-radius: 24px;
            background: rgba(255, 255, 255, 0.94);
            box-shadow: 0 12px 28px rgba(23, 53, 42, 0.08);
            text-decoration: none;
            transition: border-color .2s ease, transform .2s ease, box-shadow .2s ease;
        }

        .news-detail-neighbors__item:hover,
        .news-detail-neighbors__item:focus-visible {
            border-color: rgba(213, 51, 49, 0.22);
            transform: translateY(-2px);
            box-shadow: 0 16px 32px rgba(23, 53, 42, 0.12);
        }

        .news-detail-neighbors__item--empty {
            justify-content: center;
            color: var(--news-detail-muted);
            box-shadow: none;
        }

        .news-detail-neighbors__label {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: var(--news-detail-accent-green);
            font-size: 13px;
            font-weight: 700;
            line-height: 1.35;
        }

        .news-detail-neighbors__label::before {
            font-size: 16px;
            line-height: 1;
        }

        .news-detail-neighbors__label--previous::before {
            content: "←";
        }

        .news-detail-neighbors__label--next::before {
            content: "→";
        }

        .news-detail-neighbors__title {
            margin: 0;
            color: #21302c;
            font-size: 17px;
            font-weight: 700;
            line-height: 1.45;
        }

        .news-detail-neighbors__date {
            color: var(--news-detail-muted);
            font-size: 14px;
            line-height: 1.45;
        }

        .news-detail-published {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            min-height: 0;
            padding: 0;
            border: 0;
            border-radius: 0;
            background: transparent;
            color: #4b665b;
            font-size: 14px;
            font-weight: 600;
            line-height: 1.4;
        }

        .news-detail-published__icon {
            width: 18px;
            height: 18px;
            flex: 0 0 18px;
            background-color: var(--news-detail-accent-green);
            -webkit-mask: url("/img/meropriatie.svg") no-repeat center / contain;
            mask: url("/img/meropriatie.svg") no-repeat center / contain;
        }

        @media (max-width: 820px) {
            .news-detail-page {
                --news-detail-footer-gap: 64px;
            }

            .news-detail-intro__shell {
                padding: 18px 0 0;
            }

            .news-detail-main__ornament {
                display: none;
            }

            .news-detail-toolbar {
                flex-direction: column;
                align-items: flex-start;
            }

            .news-detail-title {
                font-size: 28px;
            }

            .news-detail-gallery__grid,
            .news-detail-neighbors {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .news-detail-hero__placeholder,
            .news-detail-frame iframe,
            .news-detail-video__player {
                min-height: 280px;
            }
        }

        @media (max-width: 640px) {
            .news-detail-breadcrumbs {
                gap: 8px;
            }

            .news-detail-main {
                padding-bottom: 56px;
            }

            .news-detail-content {
                gap: 24px;
            }

            .news-detail-title {
                font-size: 24px;
            }

            .news-detail-meta {
                gap: 12px 18px;
            }

            .news-detail-article {
                font-size: 16px;
            }

            .news-detail-gallery__grid,
            .news-detail-neighbors {
                grid-template-columns: 1fr;
            }

            .news-detail-gallery__head {
                font-size: 18px;
            }

            .news-detail-hero__image {
                max-height: min(58vh, 520px);
            }

            .news-detail-hero__placeholder {
                min-height: 220px;
                padding: 28px 22px;
            }

            .news-detail-published {
                width: 100%;
                justify-content: flex-start;
            }
        }
    </style>


</head>

<body class="news-detail-page">
    <?php
include 'header.php';
?>
    <main class="main news-detail-main">
        <span class="news-detail-main__ornament news-detail-main__ornament--left" aria-hidden="true"></span>
        <span class="news-detail-main__ornament news-detail-main__ornament--right" aria-hidden="true"></span>

        <section class="news-detail-intro container">
            <div class="news-detail-shell">
                <div class="news-detail-intro__shell">
                    <div class="news-detail-intro__content">
                        <nav class="news-detail-breadcrumbs" aria-label="Хлебные крошки">
                            <span class="news-detail-breadcrumbs__home" aria-hidden="true"></span>
                            <a href="/">Главная</a>
                            <span class="news-detail-breadcrumbs__separator" aria-hidden="true">›</span>
                            <a href="/listevents.php">События</a>
                            <span class="news-detail-breadcrumbs__separator" aria-hidden="true">›</span>
                            <a href="<?php echo e($fallbackListUrl); ?>"><?php echo e($archiveLabel); ?></a>
                            <span class="news-detail-breadcrumbs__separator" aria-hidden="true">›</span>
                            <span aria-current="page"><?php echo e($title); ?></span>
                        </nav>
                    </div>
                </div>
            </div>
        </section>

        <section class="container">
            <div class="news-detail-shell">
                <article class="news-detail-content">
                    <div class="news-detail-toolbar">
                        <a class="news-detail-back" href="<?php echo e($fallbackListUrl); ?>" data-previous-page data-fallback-url="<?php echo e($fallbackListUrl); ?>">К списку событий</a>
                        <?php if (hasPermission('news')): ?>
                        <a class="news-detail-toolbar__action" href="/addnews.php?id=<?php echo $newsId; ?>">Редактировать событие</a>
                        <?php endif; ?>
                    </div>

                    <h1 class="news-detail-title"><?php echo e($title); ?></h1>

                    <div class="news-detail-meta">
                        <div class="news-detail-meta__item">
                            <span class="news-detail-meta__icon news-detail-meta__icon--date" aria-hidden="true"></span>
                            <span><?php echo e($formattedNumericDate); ?></span>
                        </div>
                        <div class="news-detail-meta__item">
                            <span class="news-detail-meta__icon news-detail-meta__icon--views" aria-hidden="true"></span>
                            <span><?php echo e($staticViewsLabel); ?></span>
                        </div>
                        <div class="news-detail-meta__item">
                            <span class="news-detail-meta__icon news-detail-meta__icon--time" aria-hidden="true"></span>
                            <span>Время чтения: <?php echo e($readingTimeLabel); ?></span>
                        </div>
                    </div>

                    <div class="news-detail-hero">
                        <?php if ($heroPhoto !== null): ?>
                        <a class="js-smartPhoto news-detail-hero__link" href="<?php echo e($heroPhoto['full']); ?>">
                            <img class="news-detail-hero__image" src="<?php echo e($heroPhoto['thumb']); ?>" alt="<?php echo e($title); ?>">
                        </a>
                        <?php else: ?>
                        <div class="news-detail-hero__placeholder">
                            <div>
                                <div class="news-detail-hero__placeholder-icon" aria-hidden="true"></div>
                                <p class="news-detail-hero__placeholder-title"><?php echo e(getNewsTypeLabel($type)); ?></p>
                                <p class="news-detail-hero__placeholder-text">Фотография для этого материала пока не добавлена.</p>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>

                    <div class="news-detail-article">
                        <?php echo $descriptionHtml; ?>
                    </div>

                    <?php if ($safeFreim !== ''): ?>
                    <div class="news-detail-frame">
                        <?php echo $safeFreim; ?>
                    </div>
                    <?php endif; ?>

                    <div class="news-detail-divider" aria-hidden="true"><span></span></div>
                    <section class="news-detail-gallery" aria-label="<?php echo e($galleryTitle); ?>">
                        <div class="news-detail-gallery__head">
                            <span class="news-detail-gallery__icon" aria-hidden="true"></span>
                            <span><?php echo e($galleryTitle); ?></span>
                        </div>
                        <?php if (!empty($galleryPhotos)): ?>
                        <div class="news-detail-gallery__grid">
                            <?php foreach ($galleryPhotos as $galleryPhoto): ?>
                            <a class="js-smartPhoto news-detail-gallery__link" href="<?php echo e($galleryPhoto['full']); ?>">
                                <img class="news-detail-gallery__image" src="<?php echo e($galleryPhoto['thumb']); ?>" alt="<?php echo e($galleryTitle); ?>">
                            </a>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="news-detail-gallery__empty">Фотографии для этого материала будут добавлены позже.</div>
                        <?php endif; ?>
                    </section>
                    <?php if ($videoUrl !== ''): ?>
                    <div class="news-detail-divider" aria-hidden="true"><span></span></div>
                    <section class="news-detail-video" aria-label="Видео события">
                        <div class="news-detail-video__head">
                            <span class="news-detail-video__icon" aria-hidden="true"></span>
                            <span>Видео события</span>
                        </div>
                        <div class="news-detail-video__media">
                            <video class="news-detail-video__player" src="<?php echo e($videoUrl); ?>" controls preload="metadata" playsinline></video>
                        </div>
                        <p class="news-detail-video__caption">Если видео не запускается в браузере, откройте его напрямую: <a href="<?php echo e($videoUrl); ?>" target="_blank" rel="noopener noreferrer">скачать видео</a>.</p>
                    </section>
                    <?php endif; ?>
                    <div class="news-detail-divider" aria-hidden="true"><span></span></div>

                    <div class="news-detail-neighbors">
                        <?php if ($previousItem !== null): ?>
                        <a class="news-detail-neighbors__item" href="<?php echo e(buildNewsUrl($previousItem)); ?>">
                            <span class="news-detail-neighbors__label news-detail-neighbors__label--previous"><?php echo e($previousLabel); ?></span>
                            <h2 class="news-detail-neighbors__title"><?php echo e($previousItem['title'] ?? ''); ?></h2>
                            <span class="news-detail-neighbors__date"><?php echo e(formatNewsDetailNumericDate($previousItem['date'] ?? '')); ?></span>
                        </a>
                        <?php else: ?>
                        <div class="news-detail-neighbors__item news-detail-neighbors__item--empty">
                            <span class="news-detail-neighbors__label news-detail-neighbors__label--previous"><?php echo e($previousLabel); ?></span>
                            <p class="news-detail-neighbors__title">Более ранних материалов пока нет.</p>
                        </div>
                        <?php endif; ?>

                        <?php if ($nextItem !== null): ?>
                        <a class="news-detail-neighbors__item" href="<?php echo e(buildNewsUrl($nextItem)); ?>">
                            <span class="news-detail-neighbors__label news-detail-neighbors__label--next"><?php echo e($nextLabel); ?></span>
                            <h2 class="news-detail-neighbors__title"><?php echo e($nextItem['title'] ?? ''); ?></h2>
                            <span class="news-detail-neighbors__date"><?php echo e(formatNewsDetailNumericDate($nextItem['date'] ?? '')); ?></span>
                        </a>
                        <?php else: ?>
                        <div class="news-detail-neighbors__item news-detail-neighbors__item--empty">
                            <span class="news-detail-neighbors__label news-detail-neighbors__label--next"><?php echo e($nextLabel); ?></span>
                            <p class="news-detail-neighbors__title">Более новых материалов пока нет.</p>
                        </div>
                        <?php endif; ?>
                    </div>

                    <div class="news-detail-published">
                        <span class="news-detail-published__icon" aria-hidden="true"></span>
                        <span>Дата публикации: <?php echo e($formattedNumericDate); ?></span>
                    </div>
                </article>
            </div>
        </section>
    </main>
    <?php
include 'footer.php';
?>
    <script src="/script/smartphoto.min.js"></script>
    <script>
    (function () {
        var root = document.documentElement;
        var header = document.querySelector('.header-down');

        if (!root || !header) {
            return;
        }

        function syncNewsDetailOffset() {
            root.style.setProperty('--news-detail-header-offset', header.offsetHeight + 'px');
        }

        syncNewsDetailOffset();
        window.addEventListener('load', syncNewsDetailOffset);
        window.addEventListener('resize', syncNewsDetailOffset);
    })();

    document.addEventListener('DOMContentLoaded', function () {
        var previousPageLink = document.querySelector('[data-previous-page]');

        if (previousPageLink) {
            var fallbackUrl = previousPageLink.getAttribute('data-fallback-url') || previousPageLink.getAttribute('href') || '/listevents.php';
            previousPageLink.setAttribute('href', fallbackUrl);
        }

        new SmartPhoto('.js-smartPhoto', {
            nav: false
        });
    });
    </script>
</body>

</html>
