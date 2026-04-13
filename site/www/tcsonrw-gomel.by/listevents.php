<?php
session_start();

require_once __DIR__ . '/lib/security.php';
require_once __DIR__ . '/lib/auth.php';
require_once __DIR__ . '/lib/news_routing.php';
require_once __DIR__ . '/lib/photo_ordering.php';
include __DIR__ . '/db_connection.php';

ensureNewsSlugInfrastructure($conn);
ensurePhotoSortInfrastructure($conn);

function buildEventsArchiveExcerpt($value)
{
    $text = trim((string) preg_replace('/\s+/u', ' ', strip_tags((string) $value)));

    if ($text === '') {
        return 'Подробности доступны на странице события.';
    }

    if (mb_strlen($text, 'UTF-8') <= 170) {
        return $text;
    }

    return rtrim(mb_substr($text, 0, 167, 'UTF-8')) . '...';
}

function formatEventsArchiveDate($value)
{
    $timestamp = strtotime((string) $value);

    return $timestamp ? date('d.m.Y', $timestamp) : '';
}

function fetchEventsArchiveTotal($conn, $type)
{
    $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM news WHERE LOWER(type) = ?");
    if (!$stmt) {
        return 0;
    }

    $stmt->bind_param('s', $type);
    $stmt->execute();
    $result = $stmt->get_result();
    $total = $result ? (int) ($result->fetch_assoc()['total'] ?? 0) : 0;
    $stmt->close();

    return $total;
}

function fetchEventsArchiveItems($conn, $type, $limit, $offset)
{
    $stmt = $conn->prepare(
        "SELECT news.*, (
            SELECT filename
            FROM photos
            WHERE news_id = news.id
            ORDER BY sort_order ASC, id ASC
            LIMIT 1
        ) AS filename
        FROM news
        WHERE LOWER(type) = ?
        ORDER BY news.date DESC, news.id DESC
        LIMIT ? OFFSET ?"
    );

    if (!$stmt) {
        return array();
    }

    $stmt->bind_param('sii', $type, $limit, $offset);
    $stmt->execute();
    $result = $stmt->get_result();
    $items = $result ? $result->fetch_all(MYSQLI_ASSOC) : array();
    $stmt->close();

    return $items;
}

$archiveType = 'мероприятие';
$itemsPerPage = 12;
$page = filter_var($_GET['page'] ?? 1, FILTER_VALIDATE_INT, array('options' => array('min_range' => 1)));
$page = $page === false ? 1 : $page;

$totalItems = fetchEventsArchiveTotal($conn, $archiveType);
$totalPages = max(1, (int) ceil($totalItems / $itemsPerPage));
$page = min($page, $totalPages);
$offset = ($page - 1) * $itemsPerPage;
$rows = fetchEventsArchiveItems($conn, $archiveType, $itemsPerPage, $offset);

$archivePageTitle = 'Мероприятия';
$archiveSectionTitle = 'Актуальные мероприятия';
$archiveCurrentPath = '/listevents.php';
$archiveNewsUrl = '/listnews.php';
$archiveEventsUrl = '/listevents.php';
$seoTitleMeta = 'События — ТЦСОН Железнодорожного района г. Гомеля';
$seoDescriptionMeta = 'Новости и мероприятия ТЦСОН Железнодорожного района г. Гомеля.';

function buildEventsArchivePageUrl($basePath, $pageNumber)
{
    return $basePath . ($pageNumber > 1 ? '?page=' . $pageNumber : '');
}
?>
<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="format-detection" content="telephone=no">
    <link rel="icon" href="img/favicon.png" type="image/x-icon">
    <link rel="stylesheet" href="/css/cssbootstrap.min.css?v=<?= filemtime($_SERVER['DOCUMENT_ROOT'] . '/css/cssbootstrap.min.css') ?>">
    <link rel="stylesheet" href="/css/smartphoto.min.css?v=<?= filemtime($_SERVER['DOCUMENT_ROOT'] . '/css/smartphoto.min.css') ?>">
    <link rel="stylesheet" href="/css/style.css?v=<?= filemtime($_SERVER['DOCUMENT_ROOT'] . '/css/style.css') ?>">
    <script src="https://lidrekon.ru/slep/js/jquery.js"></script>
    <script src="https://lidrekon.ru/slep/js/uhpv-full.min.js"></script>
    <link rel="stylesheet" href="/css/normalize.css?v=<?= filemtime($_SERVER['DOCUMENT_ROOT'] . '/css/normalize.css') ?>">
    <link rel="stylesheet" href="/css/media.css?v=<?= filemtime($_SERVER['DOCUMENT_ROOT'] . '/css/media.css') ?>">
    <link rel="stylesheet" href="/css/media_mobile.css?v=<?= filemtime($_SERVER['DOCUMENT_ROOT'] . '/css/media_mobile.css') ?>">
    <title>События — ТЦСОН</title>
    <?php
    $seoScheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $seoHost = $_SERVER['HTTP_HOST'] ?? 'tcsonrw-gomel.by';
    $seoRequestUri = $_SERVER['REQUEST_URI'] ?? $archiveCurrentPath;
    $seoPath = strtok($seoRequestUri, '?');
    $seoCanonical = $seoScheme . '://' . $seoHost . $seoPath;
    $seoOgImage = '/img/logo-main.png';
    $seoRobotsMeta = 'index,follow';
    $seoOgImageUrl = $seoScheme . '://' . $seoHost . $seoOgImage;
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
        .events-archive-page {
            --events-archive-header-offset: var(--header-height);
            --events-archive-footer-gap: 88px;
            --events-archive-ornament-width: clamp(0px, calc((100vw - var(--width-container)) / 2), 118px);
        }

        .events-archive-main {
            position: relative;
            padding-top: calc(var(--events-archive-header-offset) + 26px);
            padding-bottom: 72px;
            isolation: isolate;
        }

        .events-archive-main::before {
            content: "";
            position: absolute;
            left: 0;
            top: 0;
            bottom: calc(var(--events-archive-footer-gap) * -1);
            width: var(--events-archive-ornament-width);
            background: url("/img/loop-vert.png") repeat-y left top / 100% auto;
            -webkit-mask-image: linear-gradient(270deg, transparent 0%, rgba(0, 0, 0, 0.28) 40%, rgba(0, 0, 0, 0.58) 70%, rgba(0, 0, 0, 0.82) 95%, rgba(0, 0, 0, 0.96) 100%);
            mask-image: linear-gradient(270deg, transparent 0%, rgba(0, 0, 0, 0.28) 40%, rgba(0, 0, 0, 0.58) 70%, rgba(0, 0, 0, 0.82) 95%, rgba(0, 0, 0, 0.96) 100%);
            pointer-events: none;
            z-index: 0;
        }

        .events-archive-main::after {
            content: "";
            position: absolute;
            right: 0;
            top: 0;
            bottom: calc(var(--events-archive-footer-gap) * -1);
            width: var(--events-archive-ornament-width);
            background: url("/img/loop-vert.png") repeat-y right top / 100% auto;
            -webkit-mask-image: linear-gradient(90deg, transparent 0%, rgba(0, 0, 0, 0.28) 40%, rgba(0, 0, 0, 0.58) 70%, rgba(0, 0, 0, 0.82) 95%, rgba(0, 0, 0, 0.96) 100%);
            mask-image: linear-gradient(90deg, transparent 0%, rgba(0, 0, 0, 0.28) 40%, rgba(0, 0, 0, 0.58) 70%, rgba(0, 0, 0, 0.82) 95%, rgba(0, 0, 0, 0.96) 100%);
            pointer-events: none;
            z-index: 0;
        }

        .events-archive-main .container {
            position: relative;
            z-index: 1;
        }

        .events-archive-intro {
            margin-bottom: 26px;
        }

        .events-archive-intro__shell {
            position: relative;
            padding: 22px 0 8px;
        }

        .events-archive-intro__content {
            position: relative;
            z-index: 1;
        }

        .events-archive-breadcrumbs {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 20px;
            color: #6a766f;
            font-size: 14px;
            line-height: 1.35;
        }

        .events-archive-breadcrumbs__home {
            width: 16px;
            height: 16px;
            flex: 0 0 16px;
            background-color: #20604a;
            -webkit-mask: url("/img/dom.svg") no-repeat center / contain;
            mask: url("/img/dom.svg") no-repeat center / contain;
        }

        .events-archive-breadcrumbs__separator {
            color: #aab0ac;
            font-weight: 700;
        }

        .events-archive-breadcrumbs a {
            color: #20604a;
            text-decoration: none;
        }

        .events-archive-breadcrumbs a:hover,
        .events-archive-breadcrumbs a:focus-visible {
            color: #c62b30;
        }

        .events-archive-head {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 20px;
            margin-bottom: 24px;
        }

        .events-archive-head__title {
            position: relative;
            margin: 0;
            padding-left: 18px;
            color: #196847;
            font-size: 32px;
            font-weight: 700;
            line-height: 1.1;
        }

        .events-archive-head__title::before {
            content: "";
            position: absolute;
            left: 0;
            top: 3px;
            bottom: 3px;
            width: 5px;
            border-radius: 999px;
            background: #d53331;
        }

        .events-archive-head__action {
            display: inline-flex;
            align-items: center;
            min-height: 42px;
            padding: 0 18px;
            border: 1px solid rgba(32, 96, 74, 0.22);
            border-radius: var(--button-radius);
            color: #20604a;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            white-space: nowrap;
            transition: border-color .2s ease, color .2s ease, transform .2s ease;
        }

        .events-archive-head__action:hover,
        .events-archive-head__action:focus-visible {
            border-color: rgba(198, 43, 48, 0.3);
            color: #c62b30;
            transform: translateY(-1px);
        }

        .events-archive-toggle {
            display: flex;
            justify-content: center;
        }

        .events-archive-toggle__shell {
            display: inline-grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 4px;
            padding: 4px;
            border: 1px solid rgba(35, 72, 58, 0.28);
            border-radius: var(--button-radius);
            background: #fff;
            box-shadow: 0 10px 24px rgba(35, 72, 58, 0.08);
        }

        .events-archive-toggle__button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            min-height: 46px;
            padding: 0 28px;
            border-radius: var(--button-radius);
            color: #295744;
            font-size: 16px;
            font-weight: 700;
            text-decoration: none;
            white-space: nowrap;
            transition: background-color .2s ease, color .2s ease, transform .2s ease;
        }

        .events-archive-toggle__button:hover,
        .events-archive-toggle__button:focus-visible {
            transform: translateY(-1px);
        }

        .events-archive-toggle__button.is-active {
            background: linear-gradient(180deg, #df3d38, #cb2d30);
            color: #fff;
            box-shadow: inset 0 0 0 1px rgba(255, 255, 255, 0.16);
        }

        .events-archive-toggle__icon {
            width: 17px;
            height: 17px;
            flex: 0 0 17px;
            background-color: currentColor;
        }

        .events-archive-toggle__icon--news {
            -webkit-mask: url("/img/novosti.svg") no-repeat center / contain;
            mask: url("/img/novosti.svg") no-repeat center / contain;
        }

        .events-archive-toggle__icon--events {
            -webkit-mask: url("/img/meropriatie.svg") no-repeat center / contain;
            mask: url("/img/meropriatie.svg") no-repeat center / contain;
        }

        .events-archive-list__heading {
            margin: 0 0 22px;
            padding-bottom: 10px;
            border-bottom: 2px solid rgba(25, 104, 71, 0.9);
            color: #196847;
            font-size: 28px;
            font-weight: 700;
            line-height: 1.15;
        }

        .events-archive-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 28px;
        }

        .events-archive-card {
            display: flex;
            flex-direction: column;
            min-width: 0;
            border-radius: 24px;
            background: #fff;
            box-shadow: 0 14px 30px rgba(34, 34, 34, 0.08);
            overflow: hidden;
        }

        .events-archive-card__media {
            position: relative;
            display: block;
            aspect-ratio: 1.52 / 1;
            background-color: #e5ddd7;
            background-repeat: no-repeat;
            background-position: center;
            background-size: cover;
        }

        .events-archive-card__media::after {
            content: "";
            position: absolute;
            inset: auto 18px 18px auto;
            width: 56px;
            height: 56px;
            border-radius: 18px;
            background-color: rgba(255, 255, 255, 0.18);
            opacity: 0;
        }

        .events-archive-card__media--placeholder {
            overflow: hidden;
        }

        .events-archive-card__media--placeholder::before {
            content: "";
            position: absolute;
            inset: 0;
            background:
                linear-gradient(135deg, rgba(255, 255, 255, 0.18), rgba(255, 255, 255, 0)),
                radial-gradient(circle at top left, rgba(255, 255, 255, 0.26), rgba(255, 255, 255, 0) 44%);
        }

        .events-archive-card__media--placeholder::after {
            opacity: 1;
            -webkit-mask-repeat: no-repeat;
            mask-repeat: no-repeat;
            -webkit-mask-position: center;
            mask-position: center;
            -webkit-mask-size: 28px 28px;
            mask-size: 28px 28px;
        }

        .events-archive-card__media--news {
            background-image: linear-gradient(135deg, #f0e0da 0%, #f7f2ef 48%, #d9ebe2 100%);
        }

        .events-archive-card__media--news::after {
            background-color: rgba(205, 43, 48, 0.92);
            -webkit-mask-image: url("/img/novosti.svg");
            mask-image: url("/img/novosti.svg");
        }

        .events-archive-card__media--events {
            background-image: linear-gradient(135deg, #efe3d4 0%, #faf7f2 42%, #d8ede6 100%);
        }

        .events-archive-card__media--events::after {
            background-color: rgba(25, 104, 71, 0.96);
            -webkit-mask-image: url("/img/meropriatie.svg");
            mask-image: url("/img/meropriatie.svg");
        }

        .events-archive-card__body {
            display: flex;
            flex: 1 1 auto;
            flex-direction: column;
            padding: 18px 20px 20px;
        }

        .events-archive-card__date {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 12px;
            color: #537264;
            font-size: 14px;
            font-weight: 600;
            line-height: 1.2;
        }

        .events-archive-card__date-icon {
            width: 16px;
            height: 16px;
            flex: 0 0 16px;
            background-color: currentColor;
            -webkit-mask: url("/img/time.svg") no-repeat center / contain;
            mask: url("/img/time.svg") no-repeat center / contain;
        }

        .events-archive-card__title {
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

        .events-archive-card__title:hover,
        .events-archive-card__title:focus-visible {
            color: #c62b30;
        }

        .events-archive-card__excerpt {
            margin: 0 0 18px;
            color: #4b5b54;
            font-size: 15px;
            line-height: 1.5;
            overflow: hidden;
            display: -webkit-box;
            -webkit-box-orient: vertical;
            -webkit-line-clamp: 3;
        }

        .events-archive-card__more {
            margin-top: auto;
            color: #d53331;
            font-size: 15px;
            font-weight: 700;
            line-height: 1.2;
            text-decoration: none;
        }

        .events-archive-card__more:hover,
        .events-archive-card__more:focus-visible {
            color: #ab2026;
        }

        .events-archive-empty {
            padding: 24px 0;
            color: #4f655d;
            font-size: 16px;
        }

        .events-archive-pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
            margin-top: 34px;
        }

        .events-archive-pagination__item {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            border: 1px solid rgba(35, 72, 58, 0.22);
            border-radius: 50%;
            background: #fff;
            color: #6b736f;
            font-size: 16px;
            font-weight: 700;
            text-decoration: none;
            transition: border-color .2s ease, color .2s ease, background-color .2s ease, transform .2s ease;
        }

        .events-archive-pagination__item:hover,
        .events-archive-pagination__item:focus-visible {
            border-color: rgba(213, 51, 49, 0.35);
            color: #d53331;
            transform: translateY(-1px);
        }

        .events-archive-pagination__item.is-active {
            border-color: #d53331;
            background: #d53331;
            color: #fff;
        }

        .events-archive-pagination__item--arrow {
            font-size: 20px;
            line-height: 1;
        }

        @media (max-width: 1100px) {
            .events-archive-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 820px) {
            .events-archive-page {
                --events-archive-footer-gap: 64px;
            }

            .events-archive-main {
                padding-bottom: 56px;
            }

            .events-archive-main::before,
            .events-archive-main::after {
                display: none;
            }

            .events-archive-intro__shell {
                padding: 18px 26px 8px;
            }

            .events-archive-head {
                flex-direction: column;
                align-items: flex-start;
            }

            .events-archive-head__title {
                font-size: 28px;
            }

            .events-archive-toggle__shell {
                width: 100%;
            }

            .events-archive-toggle__button {
                min-width: 0;
                padding-left: 18px;
                padding-right: 18px;
            }

            .events-archive-list__heading {
                font-size: 24px;
            }

            .events-archive-grid {
                grid-template-columns: 1fr;
                gap: 22px;
            }
        }

        @media (max-width: 560px) {
            .events-archive-breadcrumbs {
                gap: 8px;
            }

            .events-archive-toggle__button {
                gap: 8px;
                min-height: 44px;
                font-size: 15px;
            }

            .events-archive-card__body {
                padding: 16px 16px 18px;
            }

            .events-archive-card__title {
                font-size: 17px;
            }

            .events-archive-card__excerpt {
                font-size: 14px;
            }

            .events-archive-pagination {
                gap: 8px;
                flex-wrap: wrap;
            }
        }
    </style>
</head>

<body class="events-archive-page">
<?php include __DIR__ . '/header.php'; ?>
<main class="main events-archive-main">
    <section class="events-archive-intro container">
        <div class="events-archive-intro__shell">
            <div class="events-archive-intro__content">
                <nav class="events-archive-breadcrumbs" aria-label="Хлебные крошки">
                    <span class="events-archive-breadcrumbs__home" aria-hidden="true"></span>
                    <a href="/">Главная</a>
                    <span class="events-archive-breadcrumbs__separator" aria-hidden="true">›</span>
                    <span>События</span>
                </nav>

                <div class="events-archive-head">
                    <h1 class="events-archive-head__title">События</h1>
                    <?php if (hasPermission('news')): ?>
                    <a class="events-archive-head__action" href="/addnews.php">Создать событие</a>
                    <?php endif; ?>
                </div>

                <div class="events-archive-toggle" aria-label="Переключение типа событий">
                    <div class="events-archive-toggle__shell">
                        <a class="events-archive-toggle__button" href="<?php echo e($archiveNewsUrl); ?>">
                            <span class="events-archive-toggle__icon events-archive-toggle__icon--news" aria-hidden="true"></span>
                            <span>Новости</span>
                        </a>
                        <a class="events-archive-toggle__button is-active" href="<?php echo e($archiveEventsUrl); ?>">
                            <span class="events-archive-toggle__icon events-archive-toggle__icon--events" aria-hidden="true"></span>
                            <span>Мероприятия</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="events-archive-list container">
        <h2 class="events-archive-list__heading"><?php echo e($archiveSectionTitle); ?></h2>

        <?php if ($rows === array()): ?>
        <div class="events-archive-empty"><?php echo e($archivePageTitle); ?> пока не опубликованы.</div>
        <?php else: ?>
        <div class="events-archive-grid">
            <?php foreach ($rows as $row): ?>
            <?php
                $itemId = (int) ($row['id'] ?? 0);
                $title = (string) ($row['title'] ?? '');
                $excerpt = buildEventsArchiveExcerpt($row['description'] ?? '');
                $safeFilename = sanitizeStoredFilename($row['filename'] ?? '');
                $itemType = normalizeNewsType($row['type'] ?? '') ?: $archiveType;
                $mediaClass = $itemType === 'новость'
                    ? 'events-archive-card__media--news'
                    : 'events-archive-card__media--events';
                $mediaStyle = $safeFilename !== ''
                    ? "background-image: url('/photos/" . e($safeFilename) . "');"
                    : '';
            ?>
            <article class="events-archive-card">
                <a
                    class="events-archive-card__media <?php echo e($mediaClass); ?><?php echo $safeFilename === '' ? ' events-archive-card__media--placeholder' : ''; ?>"
                    href="<?php echo e(buildNewsUrl($row)); ?>"
                    aria-label="<?php echo e($title); ?>"
                    <?php echo $mediaStyle !== '' ? 'style="' . $mediaStyle . '"' : ''; ?>
                ></a>
                <div class="events-archive-card__body">
                    <div class="events-archive-card__date">
                        <span class="events-archive-card__date-icon" aria-hidden="true"></span>
                        <span><?php echo e(formatEventsArchiveDate($row['date'] ?? '')); ?></span>
                    </div>
                    <a class="events-archive-card__title" href="<?php echo e(buildNewsUrl($row)); ?>">
                        <?php echo e($title); ?>
                    </a>
                    <p class="events-archive-card__excerpt"><?php echo e($excerpt); ?></p>
                    <a class="events-archive-card__more" href="<?php echo e(buildNewsUrl($row)); ?>">Подробнее →</a>
                </div>
            </article>
            <?php endforeach; ?>
        </div>

        <?php if ($totalPages > 1): ?>
        <nav class="events-archive-pagination" aria-label="Пагинация событий">
            <?php if ($page > 1): ?>
            <a class="events-archive-pagination__item events-archive-pagination__item--arrow" href="<?php echo e(buildEventsArchivePageUrl($archiveCurrentPath, $page - 1)); ?>" aria-label="Предыдущая страница">‹</a>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <a class="events-archive-pagination__item<?php echo $i === $page ? ' is-active' : ''; ?>" href="<?php echo e(buildEventsArchivePageUrl($archiveCurrentPath, $i)); ?>"<?php echo $i === $page ? ' aria-current="page"' : ''; ?>>
                <?php echo $i; ?>
            </a>
            <?php endfor; ?>

            <?php if ($page < $totalPages): ?>
            <a class="events-archive-pagination__item events-archive-pagination__item--arrow" href="<?php echo e(buildEventsArchivePageUrl($archiveCurrentPath, $page + 1)); ?>" aria-label="Следующая страница">›</a>
            <?php endif; ?>
        </nav>
        <?php endif; ?>
        <?php endif; ?>
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

        function syncEventsArchiveOffset() {
            root.style.setProperty('--events-archive-header-offset', header.offsetHeight + 'px');
        }

        syncEventsArchiveOffset();
        window.addEventListener('load', syncEventsArchiveOffset);
        window.addEventListener('resize', syncEventsArchiveOffset);
    })();
</script>
</body>

</html>
