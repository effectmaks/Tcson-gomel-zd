<?php

require_once __DIR__ . '/lib/security.php';
require_once __DIR__ . '/lib/auth.php';
require_once __DIR__ . '/lib/news_routing.php';
require_once __DIR__ . '/lib/photo_ordering.php';
require_once __DIR__ . '/lib/news_management.php';
requirePermission('news');
include __DIR__ . '/db_connection.php';

ensureNewsSlugInfrastructure($conn);
ensurePhotoSortInfrastructure($conn);
ensureNewsVideoInfrastructure($conn);

$newsId = getIntFromGet('id');
if ($newsId === null && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $newsId = getIntFromPost('news_id');
}

$isEditMode = $newsId !== null;
$news = null;

if ($isEditMode) {
    $news = fetchNewsById($conn, $newsId);
    if (!$news) {
        http_response_code(404);
        exit('Событие не найдено.');
    }
}

$error = '';
$pendingDeletedPhotoIds = array();
$photoSequence = array();
$newPhotoClientIds = array();
$deleteVideo = false;
$type = $isEditMode ? (normalizeNewsType($news['type'] ?? '') ?: 'мероприятие') : 'мероприятие';
$title = (string) ($news['title'] ?? '');
$description = (string) ($news['description'] ?? '');
$freim = (string) ($news['freim'] ?? '');
$date = (string) ($news['date'] ?? date('Y-m-d'));

$pageTitle = $isEditMode ? 'Изменить событие' : 'Создать событие';
$pageDescription = $isEditMode
    ? 'Измените новость или мероприятие, обновите описание, дату, фотографии и сохраните изменения.'
    : 'Добавьте новость или мероприятие, заполните описание, дату и при необходимости прикрепите фотографии.';
$submitLabel = $isEditMode ? 'Сохранить изменения' : 'Сохранить';
$seoTitleMeta = ($isEditMode ? 'Изменить событие' : 'Создать событие') . ' — ТЦСОН Железнодорожного района г. Гомеля';
$seoDescriptionMeta = $isEditMode
    ? 'Редактирование новости или мероприятия в ТЦСОН Железнодорожного района г. Гомеля.'
    : 'Добавление новости или мероприятия в ТЦСОН Железнодорожного района г. Гомеля.';
$seoRobotsMeta = 'noindex,nofollow';
$currentUser = getCurrentUserByLogin($conn);
$currentUserId = isset($currentUser['id']) ? (int) $currentUser['id'] : null;
$currentUserLogin = (string) ($currentUser['login'] ?? ($_SESSION['login'] ?? ''));
$currentUserName = (string) ($currentUser['full_name'] ?? $currentUser['fio'] ?? $currentUserLogin);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isRequestBodyExceedingPostLimit()) {
        $error = 'Размер загружаемых файлов превышает лимит сервера. Уменьшите видео или увеличьте post_max_size/upload_max_filesize.';
    } else {
        requireCsrfToken();

        $pendingDeletedPhotoIds = normalizePhotoIdsToDelete($_POST['photos_to_delete'] ?? array());
        $photoSequence = normalizePhotoSequenceTokens($_POST['photo_sequence'] ?? array());
        $newPhotoClientIds = normalizeNewPhotoClientIds($_POST['new_photo_client_ids'] ?? array());
        $deleteVideo = normalizeDeleteVideoFlag($_POST['delete_video'] ?? null);
        try {
            $normalizedPayload = validateAndNormalizeNewsPayload($_POST);
            $type = $normalizedPayload['type'];
            $title = $normalizedPayload['title'];
            $description = $normalizedPayload['description'];
            $freim = $normalizedPayload['freim'];
            $date = $normalizedPayload['date'];
        } catch (InvalidArgumentException $e) {
            $error = $e->getMessage();
        }

        if ($error === '') {
            try {
                ensureNewsAuthorColumns($conn);
            } catch (Throwable $e) {
                $error = 'Ошибка подготовки таблицы новостей.';
                error_log('addnews ensure columns error: ' . $e->getMessage());
            }
        }

        if ($error === '') {
            try {
                if ($isEditMode) {
                    $updated = updateNewsEntry($conn, $newsId, $_POST, $_FILES['photos'] ?? null, $_FILES['video'] ?? null);
                    redirectTo('/news/' . rawurlencode($updated['slug'] ?? ''));
                }

                $created = createNewsEntry(
                    $conn,
                    $_POST,
                    array(
                        'user_id' => $currentUserId,
                        'login' => $currentUserLogin,
                        'name' => $currentUserName,
                    ),
                    $_FILES['photos'] ?? null,
                    $_FILES['video'] ?? null
                );
                redirectTo('/news/' . rawurlencode($created['slug'] ?? ''));
            } catch (Throwable $e) {
                $error = $isEditMode ? 'Ошибка при обновлении события.' : 'Ошибка при добавлении события.';
                error_log('addnews save error: ' . $e->getMessage());
            }
        }
    }
}

$photos = $isEditMode ? fetchNewsPhotos($conn, $newsId) : array();
$pendingDeletedPhotoIdsLookup = array_fill_keys($pendingDeletedPhotoIds, true);
$currentVideoFilename = $isEditMode ? sanitizeStoredFilename($news['video_filename'] ?? '') : '';
$isVideoPendingDelete = $currentVideoFilename !== '' && $deleteVideo;
$csrfToken = getCsrfToken();
$publishedByName = '';
if ($isEditMode) {
    $news = fetchNewsById($conn, $newsId);
    $currentVideoFilename = sanitizeStoredFilename($news['video_filename'] ?? '');
    $isVideoPendingDelete = $currentVideoFilename !== '' && $deleteVideo;
    $publishedByName = trim((string) ($news['created_by_name'] ?? $news['created_by_login'] ?? ''));
    if ($publishedByName === '') {
        $publishedByName = 'Не указан';
    }
}
?>

<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="img/favicon.png" type="image/x-icon">
    <link rel="stylesheet" href="/css/normalize.css?v=<?= filemtime($_SERVER['DOCUMENT_ROOT'] . '/css/normalize.css') ?>">
    <link rel="stylesheet" href="/css/cssbootstrap.min.css?v=<?= filemtime($_SERVER['DOCUMENT_ROOT'] . '/css/cssbootstrap.min.css') ?>">
    <link rel="stylesheet" type="text/css" href="/css/slick.css?v=<?= filemtime($_SERVER['DOCUMENT_ROOT'] . '/css/slick.css') ?>" />
    <link rel="stylesheet" href="/css/smartphoto.min.css?v=<?= filemtime($_SERVER['DOCUMENT_ROOT'] . '/css/smartphoto.min.css') ?>">
    <link rel="stylesheet" href="/css/style.css?v=<?= filemtime($_SERVER['DOCUMENT_ROOT'] . '/css/style.css') ?>">
    <script src="https://lidrekon.ru/slep/js/jquery.js"></script>
    <script src="https://lidrekon.ru/slep/js/uhpv-full.min.js"></script>
    <link rel="stylesheet" href="/css/media.css?v=<?= filemtime($_SERVER['DOCUMENT_ROOT'] . '/css/media.css') ?>">
    <link rel="stylesheet" href="/css/media_mobile.css?v=<?= filemtime($_SERVER['DOCUMENT_ROOT'] . '/css/media_mobile.css') ?>">
    <title><?php echo e($pageTitle); ?> — ТЦСОН</title>
    <?php
    $seoScheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $seoHost = $_SERVER['HTTP_HOST'] ?? 'tcsonrw-gomel.by';
    $seoRequestUri = $_SERVER['REQUEST_URI'] ?? '/';
    $seoPath = strtok($seoRequestUri, '?');
    $seoCanonical = $seoScheme . '://' . $seoHost . $seoPath;
    $seoOgImage = $seoOgImage ?? '/img/logo-main.png';
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
</head>

<body>
    <!-- Yandex.Metrika counter -->
    <script type="text/javascript">
        (function(m,e,t,r,i,k,a){
            m[i]=m[i]||function(){(m[i].a=m[i].a||[]).push(arguments)};
            m[i].l=1*new Date();
            for (var j = 0; j < document.scripts.length; j++) {if (document.scripts[j].src === r) { return; }}
            k=e.createElement(t),a=e.getElementsByTagName(t)[0],k.async=1,k.src=r,a.parentNode.insertBefore(k,a)
        })(window, document,'script','https://mc.yandex.ru/metrika/tag.js?id=106899778', 'ym');

        ym(106899778, 'init', {ssr:true, webvisor:true, clickmap:true, ecommerce:"dataLayer", referrer: document.referrer, url: location.href, accurateTrackBounce:true, trackLinks:true});
    </script>
    <noscript><div><img src="https://mc.yandex.ru/watch/106899778" style="position:absolute; left:-9999px;" alt=""></div></noscript>
    <!-- /Yandex.Metrika counter -->
    <?php include 'header.php'; ?>
    <main class="admin-page">
        <div class="container">
            <div class="admin-page__shell">
                <section class="admin-card admin-card--action">
                    <div class="admin-card__top admin-card__top--compact">
                        <div class="admin-card__intro admin-card__intro--compact">
                            <p class="admin-card__eyebrow">События</p>
                            <h1 class="admin-card__title"><?php echo e($pageTitle); ?></h1>
                            <p class="admin-card__text"><?php echo e($pageDescription); ?></p>
                        </div>
                        <div class="admin-card__actions">
                            <?php if ($isEditMode): ?>
                            <a href="<?php echo e(buildNewsUrl(array('id' => $newsId, 'slug' => $news['slug'] ?? ''))); ?>" class="admin-link-chip">Карточка события</a>
                            <?php endif; ?>
                            <a href="/auth.php" class="admin-secondary-button">Личный кабинет</a>
                            <a href="/listevents.php" class="admin-primary-button">Список событий</a>
                        </div>
                    </div>
                </section>

                <section class="admin-card">
                    <?php if ($error !== ''): ?>
                    <div class="admin-alert"><?php echo e($error); ?></div>
                    <?php endif; ?>

                    <form method="POST" action="" enctype="multipart/form-data" class="admin-editor-form" id="news-editor-form">
                        <input type="hidden" name="csrf_token" value="<?php echo e($csrfToken); ?>">
                        <?php if ($isEditMode): ?>
                        <input type="hidden" name="news_id" value="<?php echo (int) $newsId; ?>">
                        <input type="hidden" name="id" value="<?php echo (int) $newsId; ?>">
                        <?php endif; ?>

                        <div class="admin-form-field">
                            <label class="admin-form-label" for="type">Тип</label>
                            <div class="admin-type-toggle" id="type">
                                <div>
                                    <input class="admin-type-toggle__input" type="radio" id="type-event" name="type" value="мероприятие" <?php echo $type === 'мероприятие' ? 'checked' : ''; ?>>
                                    <label class="admin-type-toggle__button" for="type-event">Мероприятие</label>
                                </div>
                                <div>
                                    <input class="admin-type-toggle__input" type="radio" id="type-news" name="type" value="новость" <?php echo $type === 'новость' ? 'checked' : ''; ?>>
                                    <label class="admin-type-toggle__button" for="type-news">Новость</label>
                                </div>
                            </div>
                        </div>

                        <div class="admin-form-field">
                            <label class="admin-form-label" for="title">Название</label>
                            <p class="admin-field-error" id="titleValidationMessage"></p>
                            <textarea class="admin-form-input admin-form-textarea admin-form-textarea--compact" id="title" name="title" maxlength="60" required><?php echo e($title); ?></textarea>
                        </div>

                        <div class="admin-form-field">
                            <label class="admin-form-label" for="description">Описание</label>
                            <textarea class="admin-form-input admin-form-textarea" id="description" name="description" required><?php echo e($description); ?></textarea>
                        </div>

                        <div class="admin-form-field">
                            <label class="admin-form-label" for="freim">Фрейм</label>
                            <textarea class="admin-form-input admin-form-textarea admin-form-textarea--compact" id="freim" name="freim"><?php echo e($freim); ?></textarea>
                        </div>

                        <div class="admin-form-field">
                            <label class="admin-form-label" for="date">Дата</label>
                            <input class="admin-form-input" type="date" id="date" name="date" value="<?php echo e($date); ?>" required>
                        </div>

                        <div class="admin-form-field">
                            <label class="admin-form-label" for="photos">Фотографии</label>
                            <div class="admin-file-picker admin-file-picker--dropzone" id="newsPhotoDropzone">
                                <input class="admin-file-picker__input" type="file" id="photos" name="photos[]" accept=".jpg,.jpeg,.png,.gif" multiple>
                                <label for="photos" class="admin-file-picker__button">
                                    <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H7a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7Z"/><path d="M14 2v5h5"/><path d="M9 13h6"/><path d="M9 17h6"/><path d="M9 9h1"/></svg>
                                    <span>Добавить файл</span>
                                </label>
                                <span class="admin-file-picker__summary" id="photosSummary">Файлы не выбраны</span>
                                <span class="admin-file-picker__hint">Или перетащите изображения сюда.</span>
                            </div>
                            <p class="admin-form-note">Можно выбрать несколько изображений в формате JPG, PNG или GIF.</p>
                        </div>

                        <div class="admin-form-field">
                            <label class="admin-form-label">Фотогалерея события</label>
                            <p class="admin-form-note">Перетащите карточки, чтобы изменить порядок. Первое фото станет главным на странице новости. Новые фото появятся здесь сразу после добавления, но прикрепятся к новости только после сохранения.</p>
                            <div class="admin-photo-grid" id="editnews-photos">
                                <?php if (!empty($photos)): ?>
                                <?php foreach ($photos as $photo): ?>
                                <?php
                                    $safeFilename = sanitizeStoredFilename($photo['filename']);
                                    $photoId = (int) $photo['id'];
                                    $isPendingDelete = isset($pendingDeletedPhotoIdsLookup[$photoId]);
                                ?>
                                <article class="admin-photo-card editnews__photo" data-photo-id="<?php echo $photoId; ?>" data-photo-token="existing:<?php echo $photoId; ?>" draggable="true" <?php echo $isPendingDelete ? 'hidden' : ''; ?>>
                                    <span class="admin-photo-card__badge" data-photo-badge>Фото</span>
                                    <a class="admin-photo-card__media" href="/photos/<?php echo e($safeFilename); ?>" target="_blank" rel="noopener noreferrer" aria-label="Открыть фотографию в новой вкладке">
                                        <img class="admin-photo-card__image" src="/photos/<?php echo e($safeFilename); ?>" alt="Фото">
                                    </a>
                                    <button class="btn-reset admin-photo-card__remove" type="button" data-delete-photo="<?php echo $photoId; ?>" aria-label="Удалить фото">
                                        <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                                            <path d="M7 7L17 17"></path>
                                            <path d="M17 7L7 17"></path>
                                        </svg>
                                    </button>
                                </article>
                                <?php endforeach; ?>
                                <?php endif; ?>
                                <p class="admin-photo-grid__empty" id="editnews-photos-empty" <?php echo !empty($photos) ? 'hidden' : ''; ?>>Фотографии пока не добавлены.</p>
                            </div>
                            <div id="photo-order-inputs"></div>
                            <div id="new-photo-client-id-inputs"></div>
                            <div id="photos-to-delete">
                                <?php foreach ($pendingDeletedPhotoIds as $photoId): ?>
                                <input type="hidden" name="photos_to_delete[]" value="<?php echo (int) $photoId; ?>" data-photo-delete-input="<?php echo (int) $photoId; ?>">
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <div class="admin-form-field">
                            <label class="admin-form-label" for="video">Видео</label>
                            <div class="admin-file-picker admin-file-picker--dropzone" id="newsVideoDropzone">
                                <input class="admin-file-picker__input" type="file" id="video" name="video" accept=".mp4,.webm,.ogv,video/mp4,video/webm,video/ogg">
                                <label for="video" class="admin-file-picker__button">
                                    <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="9 7 19 12 9 17 9 7"></polygon><rect x="3" y="5" width="18" height="14" rx="2" ry="2"></rect></svg>
                                    <span>Прикрепить видео</span>
                                </label>
                                <span class="admin-file-picker__summary" id="videoSummary">Файл не выбран</span>
                                <span class="admin-file-picker__hint">Или перетащите видео сюда.</span>
                            </div>
                            <p class="admin-form-note">Поддерживаются MP4, WebM и OGV. Новый файл заменит текущее видео.</p>
                        </div>

                        <div class="admin-form-field">
                            <label class="admin-form-label">Видео события</label>
                            <p class="admin-form-note">Можно удалить текущее видео или заменить его новым файлом до сохранения формы.</p>
                            <div class="admin-photo-grid" id="editnews-video">
                                <?php if ($currentVideoFilename !== ''): ?>
                                <article class="admin-photo-card editnews__video" id="existingNewsVideoCard" <?php echo $isVideoPendingDelete ? 'hidden' : ''; ?>>
                                    <span class="admin-photo-card__badge">Видео</span>
                                    <div class="admin-photo-card__media">
                                        <video class="admin-photo-card__image" src="/videos/<?php echo e($currentVideoFilename); ?>" controls preload="metadata"></video>
                                    </div>
                                    <button class="btn-reset admin-photo-card__remove" type="button" data-delete-video="existing" aria-label="Удалить видео">
                                        <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                                            <path d="M7 7L17 17"></path>
                                            <path d="M17 7L7 17"></path>
                                        </svg>
                                    </button>
                                </article>
                                <?php endif; ?>
                                <p class="admin-photo-grid__empty" id="editnews-video-empty" <?php echo ($currentVideoFilename !== '' && !$isVideoPendingDelete) ? 'hidden' : ''; ?>>Видео пока не добавлено.</p>
                            </div>
                            <div id="video-delete-inputs">
                                <?php if ($isVideoPendingDelete): ?>
                                <input type="hidden" name="delete_video" value="1" data-video-delete-input="1">
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="admin-form-actions">
                            <?php if ($isEditMode): ?>
                            <p class="admin-form-note" style="margin-right:auto;">Опубликовал: <?php echo e($publishedByName); ?></p>
                            <?php endif; ?>
                            <?php if ($isEditMode): ?>
                            <button class="admin-secondary-button" type="submit" formaction="/deletenews.php" formmethod="POST" formnovalidate onclick="return confirm('Вы уверены, что хотите удалить событие?');">Удалить событие</button>
                            <?php endif; ?>
                            <button class="admin-primary-button" type="submit" id="submitNewsButton"><?php echo e($submitLabel); ?></button>
                        </div>
                    </form>
                </section>
            </div>
        </div>
    </main>

    <?php include 'footer.php'; ?>

    <script>
    const form = document.getElementById('news-editor-form');
    const title = document.getElementById('title');
    const errorMessage = document.getElementById('titleValidationMessage');
    const submitButton = document.getElementById('submitNewsButton');
    const textareaTitle = document.getElementById('title');
    const textareaDescription = document.getElementById('description');
    const textareaFreim = document.getElementById('freim');
    const photosInput = document.getElementById('photos');
    const photosSummary = document.getElementById('photosSummary');
    const photoDropzone = document.getElementById('newsPhotoDropzone');
    const videoInput = document.getElementById('video');
    const videoSummary = document.getElementById('videoSummary');
    const videoDropzone = document.getElementById('newsVideoDropzone');
    const deleteInputsContainer = document.getElementById('photos-to-delete');
    const photosContainer = document.getElementById('editnews-photos');
    const emptyPhotosMessage = document.getElementById('editnews-photos-empty');
    const photoOrderInputsContainer = document.getElementById('photo-order-inputs');
    const newPhotoClientIdsContainer = document.getElementById('new-photo-client-id-inputs');
    const videoContainer = document.getElementById('editnews-video');
    const emptyVideoMessage = document.getElementById('editnews-video-empty');
    const videoDeleteInputsContainer = document.getElementById('video-delete-inputs');
    const existingVideoCard = document.getElementById('existingNewsVideoCard');
    const pendingNewPhotos = new Map();
    let activeDraggedPhotoCard = null;
    let pendingPhotoCounter = 0;
    let pendingVideo = null;
    let existingVideoHiddenForReplacement = false;

    function autoResizeTextarea(textarea) {
        if (!textarea) {
            return;
        }

        textarea.style.height = 'auto';
        textarea.style.height = `${textarea.scrollHeight}px`;
    }

    function validateTitleLength() {
        if (!title || !errorMessage || !submitButton) {
            return true;
        }

        const value = title.value.trim();

        if (value.length > 60) {
            errorMessage.textContent = 'Название должно содержать не более 60 символов';
            submitButton.disabled = true;
            return false;
        }

        if (value.length > 0 && value.length < 3) {
            errorMessage.textContent = 'Название должно содержать минимум 3 символа';
            submitButton.disabled = true;
            return false;
        }

        errorMessage.textContent = '';
        submitButton.disabled = false;
        return true;
    }

    function isAcceptedPhotoFile(file) {
        if (!file) {
            return false;
        }

        const fileName = String(file.name || '').toLowerCase();
        const mimeType = String(file.type || '').toLowerCase();

        return ['image/jpeg', 'image/png', 'image/gif'].includes(mimeType)
            || /\.(jpe?g|png|gif)$/i.test(fileName);
    }

    function isAcceptedVideoFile(file) {
        if (!file) {
            return false;
        }

        const fileName = String(file.name || '').toLowerCase();
        const mimeType = String(file.type || '').toLowerCase();

        return ['video/mp4', 'video/webm', 'video/ogg', 'application/ogg'].includes(mimeType)
            || /\.(mp4|m4v|webm|ogv|ogg)$/i.test(fileName);
    }

    function createPendingPhotoClientId() {
        pendingPhotoCounter += 1;
        return `new_photo_${Date.now()}_${pendingPhotoCounter}`;
    }

    function getVisiblePhotoCards() {
        if (!photosContainer) {
            return [];
        }

        return Array.from(photosContainer.querySelectorAll('.editnews__photo:not([hidden])'));
    }

    function updatePhotosSummary() {
        if (!photosSummary || !photosInput) {
            return;
        }

        const files = Array.from(photosInput.files || []);
        if (files.length === 0) {
            photosSummary.textContent = 'Файлы не выбраны';
        } else if (files.length === 1) {
            photosSummary.textContent = files[0].name;
        } else {
            photosSummary.textContent = `Новых файлов выбрано: ${files.length}`;
        }
    }

    function getVisibleVideoCards() {
        if (!videoContainer) {
            return [];
        }

        return Array.from(videoContainer.querySelectorAll('.editnews__video:not([hidden])'));
    }

    function updateVideoSummary() {
        if (!videoSummary) {
            return;
        }

        if (pendingVideo && pendingVideo.file) {
            videoSummary.textContent = pendingVideo.file.name;
            return;
        }

        if (videoInput && videoInput.files && videoInput.files[0]) {
            videoSummary.textContent = videoInput.files[0].name;
            return;
        }

        videoSummary.textContent = 'Файл не выбран';
    }

    function updateEmptyVideoState() {
        if (!emptyVideoMessage) {
            return;
        }

        emptyVideoMessage.hidden = getVisibleVideoCards().length > 0;
    }

    function ensureDeleteVideoInput() {
        if (!videoDeleteInputsContainer) {
            return;
        }

        let input = videoDeleteInputsContainer.querySelector('[data-video-delete-input="1"]');
        if (!input) {
            input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'delete_video';
            input.value = '1';
            input.setAttribute('data-video-delete-input', '1');
            videoDeleteInputsContainer.appendChild(input);
        }
    }

    function clearDeleteVideoInput() {
        if (!videoDeleteInputsContainer) {
            return;
        }

        const input = videoDeleteInputsContainer.querySelector('[data-video-delete-input="1"]');
        if (input) {
            input.remove();
        }
    }

    function updateVideoState() {
        updateVideoSummary();
        updateEmptyVideoState();
    }

    function updateEmptyPhotosState() {
        if (!emptyPhotosMessage) {
            return;
        }

        emptyPhotosMessage.hidden = getVisiblePhotoCards().length > 0;
    }

    function rebuildPhotoOrderInputs() {
        if (!photoOrderInputsContainer) {
            return;
        }

        photoOrderInputsContainer.innerHTML = '';
        getVisiblePhotoCards().forEach((card) => {
            const token = card.getAttribute('data-photo-token');
            if (!token) {
                return;
            }

            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'photo_sequence[]';
            input.value = token;
            photoOrderInputsContainer.appendChild(input);
        });
    }

    function syncPendingPhotosToInput() {
        if (!photosInput || typeof DataTransfer === 'undefined') {
            updatePhotosSummary();
            return;
        }

        const orderedPendingIds = getVisiblePhotoCards()
            .map((card) => card.getAttribute('data-new-photo-id'))
            .filter(Boolean);

        const transfer = new DataTransfer();
        orderedPendingIds.forEach((pendingId) => {
            const item = pendingNewPhotos.get(pendingId);
            if (item && item.file) {
                transfer.items.add(item.file);
            }
        });
        photosInput.files = transfer.files;

        if (newPhotoClientIdsContainer) {
            newPhotoClientIdsContainer.innerHTML = '';
            orderedPendingIds.forEach((pendingId) => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'new_photo_client_ids[]';
                input.value = pendingId;
                newPhotoClientIdsContainer.appendChild(input);
            });
        }

        updatePhotosSummary();
    }

    function updatePhotoBadges() {
        getVisiblePhotoCards().forEach((card, index) => {
            const badge = card.querySelector('[data-photo-badge]');
            if (!badge) {
                return;
            }

            const isPending = card.classList.contains('admin-photo-card--pending');
            card.classList.toggle('is-primary', index === 0);
            if (index === 0) {
                badge.textContent = isPending ? 'Главное · новое' : 'Главное фото';
            } else {
                badge.textContent = isPending ? `Новое фото ${index + 1}` : `Фото ${index + 1}`;
            }
        });
    }

    function updatePhotoGridState() {
        updateEmptyPhotosState();
        updatePhotoBadges();
        rebuildPhotoOrderInputs();
        syncPendingPhotosToInput();
    }

    function makePhotoCardDraggable(card) {
        if (!card) {
            return;
        }

        card.draggable = true;
        card.addEventListener('dragstart', (event) => {
            if (card.hidden) {
                event.preventDefault();
                return;
            }

            activeDraggedPhotoCard = card;
            card.classList.add('is-dragging');
            if (event.dataTransfer) {
                event.dataTransfer.effectAllowed = 'move';
                event.dataTransfer.setData('text/plain', card.getAttribute('data-photo-token') || '');
            }
        });

        card.addEventListener('dragend', () => {
            card.classList.remove('is-dragging');
            activeDraggedPhotoCard = null;
            updatePhotoGridState();
        });
    }

    function buildPendingPhotoCard(pendingId, objectUrl) {
        const card = document.createElement('article');
        card.className = 'admin-photo-card admin-photo-card--pending editnews__photo';
        card.setAttribute('data-new-photo-id', pendingId);
        card.setAttribute('data-photo-token', `new:${pendingId}`);

        const badge = document.createElement('span');
        badge.className = 'admin-photo-card__badge';
        badge.setAttribute('data-photo-badge', '');
        badge.textContent = 'Новое фото';
        card.appendChild(badge);

        const link = document.createElement('a');
        link.className = 'admin-photo-card__media';
        link.href = objectUrl;
        link.target = '_blank';
        link.rel = 'noopener noreferrer';
        link.setAttribute('aria-label', 'Открыть фотографию в новой вкладке');

        const image = document.createElement('img');
        image.className = 'admin-photo-card__image';
        image.src = objectUrl;
        image.alt = 'Новое фото';
        link.appendChild(image);
        card.appendChild(link);

        const removeButton = document.createElement('button');
        removeButton.className = 'btn-reset admin-photo-card__remove';
        removeButton.type = 'button';
        removeButton.setAttribute('data-remove-new-photo', pendingId);
        removeButton.setAttribute('aria-label', 'Убрать новое фото');
        removeButton.innerHTML = '<svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M7 7L17 17"></path><path d="M17 7L7 17"></path></svg>';
        card.appendChild(removeButton);

        makePhotoCardDraggable(card);
        return card;
    }

    function removePendingVideoPreview() {
        if (pendingVideo && pendingVideo.objectUrl) {
            URL.revokeObjectURL(pendingVideo.objectUrl);
        }

        pendingVideo = null;
        if (videoInput) {
            videoInput.value = '';
        }

        if (!videoContainer) {
            updateVideoState();
            return;
        }

        const card = videoContainer.querySelector('[data-pending-video="1"]');
        if (card) {
            card.remove();
        }

        if (existingVideoCard && existingVideoHiddenForReplacement) {
            existingVideoCard.hidden = false;
            clearDeleteVideoInput();
            existingVideoHiddenForReplacement = false;
        }

        updateVideoState();
    }

    function buildPendingVideoCard(file, objectUrl) {
        const card = document.createElement('article');
        card.className = 'admin-photo-card admin-photo-card--pending editnews__video';
        card.setAttribute('data-pending-video', '1');

        const badge = document.createElement('span');
        badge.className = 'admin-photo-card__badge';
        badge.textContent = 'Новое видео';
        card.appendChild(badge);

        const media = document.createElement('div');
        media.className = 'admin-photo-card__media';

        const video = document.createElement('video');
        video.className = 'admin-photo-card__image';
        video.src = objectUrl;
        video.controls = true;
        video.preload = 'metadata';
        media.appendChild(video);
        card.appendChild(media);

        const removeButton = document.createElement('button');
        removeButton.className = 'btn-reset admin-photo-card__remove';
        removeButton.type = 'button';
        removeButton.setAttribute('data-remove-new-video', '1');
        removeButton.setAttribute('aria-label', 'Убрать новое видео');
        removeButton.innerHTML = '<svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M7 7L17 17"></path><path d="M17 7L7 17"></path></svg>';
        card.appendChild(removeButton);

        return card;
    }

    function setPendingVideo(file) {
        if (!videoContainer || !isAcceptedVideoFile(file)) {
            removePendingVideoPreview();
            return;
        }

        removePendingVideoPreview();

        const objectUrl = URL.createObjectURL(file);
        pendingVideo = {
            file,
            objectUrl,
        };

        if (existingVideoCard && !existingVideoCard.hidden) {
            existingVideoCard.hidden = true;
            ensureDeleteVideoInput();
            existingVideoHiddenForReplacement = true;
        }

        videoContainer.appendChild(buildPendingVideoCard(file, objectUrl));
        updateVideoState();
    }

    function appendPendingPhotoFiles(files) {
        if (!photosContainer) {
            return;
        }

        const acceptedFiles = Array.from(files || []).filter(isAcceptedPhotoFile);
        acceptedFiles.forEach((file) => {
            const pendingId = createPendingPhotoClientId();
            const objectUrl = URL.createObjectURL(file);
            pendingNewPhotos.set(pendingId, {
                id: pendingId,
                file,
                objectUrl,
            });
            photosContainer.appendChild(buildPendingPhotoCard(pendingId, objectUrl));
        });

        updatePhotoGridState();
    }

    function removePendingPhoto(pendingId) {
        if (!pendingId || !photosContainer) {
            return;
        }

        const pendingPhoto = pendingNewPhotos.get(pendingId);
        if (pendingPhoto && pendingPhoto.objectUrl) {
            URL.revokeObjectURL(pendingPhoto.objectUrl);
        }
        pendingNewPhotos.delete(pendingId);

        const photoCard = photosContainer.querySelector(`[data-new-photo-id="${pendingId}"]`);
        if (photoCard) {
            photoCard.remove();
        }

        updatePhotoGridState();
    }

    function markPhotoForDelete(photoId) {
        if (!deleteInputsContainer || !photoId) {
            return;
        }

        const existingInput = deleteInputsContainer.querySelector(`[data-photo-delete-input="${photoId}"]`);
        if (!existingInput) {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'photos_to_delete[]';
            input.value = photoId;
            input.setAttribute('data-photo-delete-input', photoId);
            deleteInputsContainer.appendChild(input);
        }

        const photoCard = photosContainer.querySelector(`[data-photo-id="${photoId}"]`);
        if (photoCard) {
            photoCard.hidden = true;
        }

        updatePhotoGridState();
    }

    function markExistingVideoForDelete() {
        if (!existingVideoCard) {
            return;
        }

        existingVideoCard.hidden = true;
        ensureDeleteVideoInput();
        existingVideoHiddenForReplacement = false;
        updateVideoState();
    }

    if (title && errorMessage && submitButton) {
        title.addEventListener('input', validateTitleLength);
        title.addEventListener('blur', validateTitleLength);
        validateTitleLength();
    }

    [textareaTitle, textareaDescription, textareaFreim].forEach((textarea) => {
        if (!textarea) {
            return;
        }

        textarea.addEventListener('input', () => autoResizeTextarea(textarea));
        autoResizeTextarea(textarea);
    });

    if (photosInput) {
        photosInput.addEventListener('change', () => {
            const incomingFiles = Array.from(photosInput.files || []);
            if (!incomingFiles.length) {
                updatePhotosSummary();
                return;
            }

            appendPendingPhotoFiles(incomingFiles);
        });
    }

    if (videoInput) {
        videoInput.addEventListener('change', () => {
            const file = videoInput.files && videoInput.files[0] ? videoInput.files[0] : null;
            if (!file) {
                removePendingVideoPreview();
                return;
            }

            if (!isAcceptedVideoFile(file)) {
                removePendingVideoPreview();
                updateVideoSummary();
                return;
            }

            setPendingVideo(file);
        });
    }

    if (photoDropzone && photosInput) {
        ['dragenter', 'dragover'].forEach((eventName) => {
            photoDropzone.addEventListener(eventName, (event) => {
                event.preventDefault();
                photoDropzone.classList.add('is-dragover');
            });
        });

        photoDropzone.addEventListener('dragleave', (event) => {
            if (!photoDropzone.contains(event.relatedTarget)) {
                photoDropzone.classList.remove('is-dragover');
            }
        });

        photoDropzone.addEventListener('drop', (event) => {
            event.preventDefault();
            photoDropzone.classList.remove('is-dragover');
            appendPendingPhotoFiles(event.dataTransfer ? event.dataTransfer.files : []);
        });
    }

    if (videoDropzone && videoInput) {
        ['dragenter', 'dragover'].forEach((eventName) => {
            videoDropzone.addEventListener(eventName, (event) => {
                event.preventDefault();
                videoDropzone.classList.add('is-dragover');
            });
        });

        videoDropzone.addEventListener('dragleave', (event) => {
            if (!videoDropzone.contains(event.relatedTarget)) {
                videoDropzone.classList.remove('is-dragover');
            }
        });

        videoDropzone.addEventListener('drop', (event) => {
            event.preventDefault();
            videoDropzone.classList.remove('is-dragover');
            const file = event.dataTransfer && event.dataTransfer.files ? event.dataTransfer.files[0] : null;
            if (!file) {
                return;
            }

            if (typeof DataTransfer !== 'undefined') {
                const transfer = new DataTransfer();
                transfer.items.add(file);
                videoInput.files = transfer.files;
            }

            setPendingVideo(file);
        });
    }

    if (photosContainer) {
        Array.from(photosContainer.querySelectorAll('.editnews__photo')).forEach((card) => {
            makePhotoCardDraggable(card);
        });

        photosContainer.addEventListener('click', (event) => {
            const deleteExistingButton = event.target.closest('[data-delete-photo]');
            if (deleteExistingButton) {
                markPhotoForDelete(deleteExistingButton.getAttribute('data-delete-photo'));
                return;
            }

            const deleteNewButton = event.target.closest('[data-remove-new-photo]');
            if (deleteNewButton) {
                removePendingPhoto(deleteNewButton.getAttribute('data-remove-new-photo'));
            }
        });

        photosContainer.addEventListener('dragover', (event) => {
            if (!activeDraggedPhotoCard) {
                return;
            }

            event.preventDefault();

            const targetCard = event.target.closest('.editnews__photo:not([hidden])');
            if (!targetCard || targetCard === activeDraggedPhotoCard) {
                if (emptyPhotosMessage && photosContainer.contains(emptyPhotosMessage)) {
                    photosContainer.insertBefore(activeDraggedPhotoCard, emptyPhotosMessage);
                } else {
                    photosContainer.appendChild(activeDraggedPhotoCard);
                }
                return;
            }

            const rect = targetCard.getBoundingClientRect();
            const useHorizontalAxis = Math.abs(event.clientY - (rect.top + rect.height / 2)) < rect.height * 0.35;
            const shouldInsertBefore = useHorizontalAxis
                ? event.clientX < rect.left + rect.width / 2
                : event.clientY < rect.top + rect.height / 2;

            if (shouldInsertBefore) {
                photosContainer.insertBefore(activeDraggedPhotoCard, targetCard);
            } else {
                photosContainer.insertBefore(activeDraggedPhotoCard, targetCard.nextElementSibling);
            }
        });

        photosContainer.addEventListener('drop', (event) => {
            if (!activeDraggedPhotoCard) {
                return;
            }

            event.preventDefault();
            updatePhotoGridState();
        });
    }

    if (videoContainer) {
        videoContainer.addEventListener('click', (event) => {
            const deleteExistingVideoButton = event.target.closest('[data-delete-video]');
            if (deleteExistingVideoButton) {
                markExistingVideoForDelete();
                return;
            }

            const removePendingVideoButton = event.target.closest('[data-remove-new-video]');
            if (removePendingVideoButton) {
                removePendingVideoPreview();
            }
        });
    }

    if (form) {
        form.addEventListener('submit', () => {
            updatePhotoGridState();
        });
    }

    updatePhotoGridState();
    updateVideoState();
    </script>
</body>

</html>
