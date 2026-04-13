<?php
session_start();

require_once __DIR__ . '/lib/security.php';
require_once __DIR__ . '/lib/auth.php';
require_once __DIR__ . '/lib/messenger.php';

requirePermission('messenger');
include __DIR__ . '/db_connection.php';

messengerEnsureReady($conn);

$currentUser = getCurrentUserByLogin($conn);
$currentUserName = $currentUser ? messengerGetUserDisplayName($currentUser) : (string) ($_SESSION['login'] ?? 'Пользователь');
$canManageTokens = hasPermission('tokens');
$csrfToken = getCsrfToken();
$seoTitleMeta = 'Задания — ТЦСОН Железнодорожного района г. Гомеля';
$seoDescriptionMeta = 'Служебные задания административной части ТЦСОН Железнодорожного района г. Гомеля.';
$seoRobotsMeta = 'noindex,nofollow';
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="/img/favicon.png" type="image/x-icon" id="messengerFavicon">
    <link rel="stylesheet" href="/css/style.css?v=<?= filemtime($_SERVER['DOCUMENT_ROOT'] . '/css/style.css') ?>">
    <link rel="stylesheet" href="/css/normalize.css?v=<?= filemtime($_SERVER['DOCUMENT_ROOT'] . '/css/normalize.css') ?>">
    <link rel="stylesheet" href="/css/media.css?v=<?= filemtime($_SERVER['DOCUMENT_ROOT'] . '/css/media.css') ?>">
    <link rel="stylesheet" href="/css/media_mobile.css?v=<?= filemtime($_SERVER['DOCUMENT_ROOT'] . '/css/media_mobile.css') ?>">
    <title>Задания — ТЦСОН</title>
</head>
<body>
<?php include __DIR__ . '/header.php'; ?>
<main class="main messenger-page">
    <section class="container">
        <div class="messenger-card messenger-card__section messenger-hero">
            <div class="messenger-row">
                <div>
                    <h1 class="messenger-title">Задания</h1>
                    <p class="messenger-subtitle">Сотрудник: <?php echo e($currentUserName); ?>. Доступно создание заданий, переписка по ним, поиск по сообщениям, добавление сотрудников и выход из задания.</p>
                </div>
                <?php if ($canManageTokens): ?>
                <div class="messenger-controls">
                    <a href="/auth.php" class="messenger-link-button secondary">Личный кабинет</a>
                    <a href="/messenger_tokens.php" class="messenger-link-button">Bearer-токены</a>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <div id="messengerFlash"></div>

        <div class="messenger-layout">
            <aside class="messenger-card">
                <div class="messenger-card__section">
                    <h2 class="messenger-section-title">Действия</h2>
                    <p class="messenger-meta">Создайте новое задание или откройте поиск по всем сообщениям.</p>
                    <div class="messenger-action-grid messenger-stack-md">
                        <button type="button" id="openCreateChatButton">Создать</button>
                        <button type="button" class="secondary" id="openSearchModalButton">Поиск</button>
                    </div>
                </div>
                <div class="messenger-card__section">
                    <div class="messenger-row">
                        <h3 class="messenger-section-subtitle">Список заданий</h3>
                        <button type="button" class="secondary messenger-icon-button" id="refreshChatsButton" aria-label="Обновить список заданий" title="Обновить список заданий"><svg viewBox="0 0 24 24" aria-hidden="true" focusable="false" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 11a8 8 0 0 0-14.9-3"/><path d="M4 4v4h4"/><path d="M4 13a8 8 0 0 0 14.9 3"/><path d="M20 20v-4h-4"/></svg></button>
                    </div>
                    <div class="messenger-list messenger-stack-md" id="chatList"></div>
                </div>
            </aside>

            <section class="messenger-card">
                <div class="messenger-card__section" id="chatHeader">
                    <div class="messenger-empty">Выберите задание слева.</div>
                </div>
                <div class="messenger-card__section" id="chatParticipantsSection">
                    <div class="messenger-participants" id="chatParticipants"></div>
                </div>
                <div class="messenger-card__section" id="chatMessagesSection">
                    <div class="messenger-messages" id="chatMessages"></div>
                </div>
                <div class="messenger-card__section" id="chatReadStatusSection" hidden>
                    <div id="chatReadStatus"></div>
                </div>
                <div class="messenger-card__section">
                    <form id="messageForm">
                        <textarea name="body_text" id="messageBody" placeholder="Сообщение по выбранному заданию" disabled></textarea>
                        <div class="messenger-file-picker messenger-file-picker--dropzone messenger-stack-sm" id="messageFilesDropzone">
                            <input type="file" id="messageFiles" name="files[]" class="messenger-file-input" accept=".jpg,.jpeg,.png,.gif,.webp,.mp4,.mov,.webm,.pdf,.txt,.rtf,.doc,.docx,.xls,.xlsx,.csv,.odt,.ods,.zip,.rar" multiple disabled>
                            <label for="messageFiles" id="messageFilesButton" class="messenger-link-button secondary messenger-file-button is-disabled" aria-disabled="true">
                                <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H7a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7Z"/><path d="M14 2v5h5"/><path d="M9 13h6"/><path d="M9 17h6"/><path d="M9 9h1"/></svg>
                                <span>Файлы</span>
                            </label>
                            <span class="messenger-file-summary" id="messageFilesSummary">Файлы не выбраны</span>
                            <span class="messenger-file-picker__hint">Можно перетащить вложения в поле сообщения или сюда.</span>
                        </div>
                        <div class="messenger-controls messenger-controls--end messenger-stack-md">
                            <button type="button" id="leaveChatButton" class="secondary" disabled>Выйти из задания</button>
                            <button type="submit" id="sendMessageButton" disabled>Отправить</button>
                        </div>
                    </form>
                </div>
            </section>
        </div>
    </section>
</main>

<div class="messenger-modal" id="searchModal" hidden>
    <div class="messenger-modal__backdrop" data-close-search-modal></div>
    <div class="messenger-modal__dialog" role="dialog" aria-modal="true" aria-labelledby="searchModalTitle">
        <div class="messenger-modal__header">
            <div>
                <h2 class="messenger-section-title" id="searchModalTitle">Поиск по сообщениям</h2>
                <p class="messenger-meta">Введите фразу, имя сотрудника или часть текста сообщения.</p>
            </div>
            <button type="button" class="secondary" id="closeSearchModalButton">Закрыть</button>
        </div>
        <div class="messenger-inline-form messenger-stack-md">
            <input type="search" id="searchInput" placeholder="Например, баннер">
            <button type="button" id="searchButton">Искать</button>
        </div>
        <div class="messenger-search-results" id="searchResults"></div>
    </div>
</div>

<div class="messenger-modal" id="participantModal" hidden>
    <div class="messenger-modal__backdrop" data-close-participant-modal></div>
    <div class="messenger-modal__dialog" role="dialog" aria-modal="true" aria-labelledby="participantModalTitle">
        <div class="messenger-modal__header">
            <div>
                <h2 class="messenger-section-title" id="participantModalTitle">Добавить сотрудника</h2>
                <p class="messenger-meta">Отметьте сотрудников, которых нужно добавить в текущее задание.</p>
            </div>
            <button type="button" class="secondary" id="closeParticipantModalButton">Отмена</button>
        </div>
        <div class="messenger-stack-md">
            <div class="messenger-meta" id="participantModalStatus">Загрузка списка сотрудников...</div>
            <div class="messenger-participant-options" id="participantOptions"></div>
            <div class="messenger-controls messenger-controls--end messenger-stack-md">
                <button type="button" class="secondary" id="cancelParticipantModalButton">Отмена</button>
                <button type="button" id="confirmParticipantModalButton" disabled>Добавить</button>
            </div>
        </div>
    </div>
</div>

<div class="messenger-modal messenger-modal--gallery" id="imageViewerModal" hidden>
    <div class="messenger-modal__backdrop" data-close-image-viewer></div>
    <div class="messenger-gallery" role="dialog" aria-modal="true" aria-labelledby="imageViewerTitle">
        <button type="button" class="messenger-gallery__close secondary" id="closeImageViewerButton" aria-label="Закрыть просмотр">Закрыть</button>
        <button type="button" class="messenger-gallery__nav secondary" id="prevImageButton" aria-label="Предыдущее изображение">&#8592;</button>
        <div class="messenger-gallery__stage">
            <p class="messenger-gallery__title" id="imageViewerTitle">Просмотр вложения</p>
            <img src="" alt="" id="imageViewerImage" class="messenger-gallery__image" hidden>
            <div id="imageViewerFileCard" class="messenger-file-card messenger-file-card--viewer" hidden></div>
            <div class="messenger-gallery__caption" id="imageViewerCaption"></div>
            <div class="messenger-gallery__actions">
                <a href="#" class="messenger-link-button secondary" id="downloadImageButton" download>Скачать</a>
                <button type="button" class="secondary" id="deleteImageButton" hidden>Удалить</button>
            </div>
        </div>
        <button type="button" class="messenger-gallery__nav secondary" id="nextImageButton" aria-label="Следующее изображение">&#8594;</button>
    </div>
</div>

<script>
    const messengerState = {
        csrfToken: <?php echo json_encode($csrfToken, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?>,
        currentUserName: <?php echo json_encode($currentUserName, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?>,
        currentUserId: <?php echo json_encode((int) ($currentUser['id'] ?? 0), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?>,
        chats: [],
        selectedChatUuid: null,
        selectedChat: null,
        mode: 'chat',
        imageGalleryItems: [],
        activeImageIndex: null,
        manualUnreadChatUuids: new Set()
    };

    const CHAT_AUTO_REFRESH_INTERVAL_MS = 60000;
    let autoRefreshTimerId = null;
    let autoRefreshInFlight = false;

    const statusLabels = {
        new: 'Новый',
        in_progress: 'В работе',
        done: 'Выполнен',
        closed: 'Закрыт'
    };

    const refs = {
        flash: document.getElementById('messengerFlash'),
        favicon: document.getElementById('messengerFavicon'),
        chatList: document.getElementById('chatList'),
        chatHeader: document.getElementById('chatHeader'),
        chatParticipantsSection: document.getElementById('chatParticipantsSection'),
        chatParticipants: document.getElementById('chatParticipants'),
        chatMessages: document.getElementById('chatMessages'),
        chatReadStatusSection: document.getElementById('chatReadStatusSection'),
        chatReadStatus: document.getElementById('chatReadStatus'),
        openCreateChatButton: document.getElementById('openCreateChatButton'),
        openSearchModalButton: document.getElementById('openSearchModalButton'),
        searchModal: document.getElementById('searchModal'),
        closeSearchModalButton: document.getElementById('closeSearchModalButton'),
        searchInput: document.getElementById('searchInput'),
        searchButton: document.getElementById('searchButton'),
        searchResults: document.getElementById('searchResults'),
        participantModal: document.getElementById('participantModal'),
        closeParticipantModalButton: document.getElementById('closeParticipantModalButton'),
        cancelParticipantModalButton: document.getElementById('cancelParticipantModalButton'),
        participantModalStatus: document.getElementById('participantModalStatus'),
        participantOptions: document.getElementById('participantOptions'),
        confirmParticipantModalButton: document.getElementById('confirmParticipantModalButton'),
        refreshChatsButton: document.getElementById('refreshChatsButton'),
        imageViewerModal: document.getElementById('imageViewerModal'),
        closeImageViewerButton: document.getElementById('closeImageViewerButton'),
        prevImageButton: document.getElementById('prevImageButton'),
        nextImageButton: document.getElementById('nextImageButton'),
        imageViewerTitle: document.getElementById('imageViewerTitle'),
        imageViewerImage: document.getElementById('imageViewerImage'),
        imageViewerFileCard: document.getElementById('imageViewerFileCard'),
        imageViewerCaption: document.getElementById('imageViewerCaption'),
        downloadImageButton: document.getElementById('downloadImageButton'),
        deleteImageButton: document.getElementById('deleteImageButton'),
        messageForm: document.getElementById('messageForm'),
        messageBody: document.getElementById('messageBody'),
        messageFiles: document.getElementById('messageFiles'),
        messageFilesDropzone: document.getElementById('messageFilesDropzone'),
        messageFilesButton: document.getElementById('messageFilesButton'),
        messageFilesSummary: document.getElementById('messageFilesSummary'),
        sendMessageButton: document.getElementById('sendMessageButton'),
        leaveChatButton: document.getElementById('leaveChatButton')
    };

    const baseDocumentTitle = document.title;
    const baseFaviconHref = refs.favicon ? refs.favicon.href : '/img/favicon.png';
    function showFlash(type, message) {
        refs.flash.innerHTML = `<div class="messenger-${type}">${message}</div>`;
    }

    function clearFlash() {
        refs.flash.innerHTML = '';
    }

    function getTotalUnreadCount() {
        return messengerState.chats.reduce((sum, chat) => sum + Math.max(0, Number(chat.unread_count || 0)), 0);
    }

    function syncManualUnreadChatUuids() {
        messengerState.manualUnreadChatUuids = new Set(
            messengerState.chats
                .filter((chat) => Boolean(chat.manual_unread))
                .map((chat) => chat.chat_uuid)
        );
    }

    function updateTabNotification() {
        const totalUnread = getTotalUnreadCount();
        document.title = totalUnread > 0 ? `(${totalUnread}) ${baseDocumentTitle}` : baseDocumentTitle;

        if (!refs.favicon) {
            return;
        }

        refs.favicon.href = baseFaviconHref;
    }

    async function fetchJson(url, options = {}) {
        const response = await fetch(url, options);
        const data = await response.json();
        if (!response.ok || !data.ok) {
            const error = data && data.error ? data.error : { code: 'validation_error', message: 'Ошибка запроса' };
            throw new Error(`${error.code}: ${error.message}`);
        }
        return data.data;
    }

    function statusLabel(status) {
        return statusLabels[status] || status;
    }

    function escapeHtml(value) {
        return String(value ?? '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function formatDateTime(value) {
        if (!value) {
            return '—';
        }

        const date = new Date(value);
        if (Number.isNaN(date.getTime())) {
            return '—';
        }

        return date.toLocaleString('ru-RU');
    }

    function trashIconSvg() {
        return '<svg viewBox="0 0 24 24" aria-hidden="true" focusable="false" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M8 6V4h8v2"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/></svg>';
    }

    function downloadIconSvg() {
        return '<svg viewBox="0 0 24 24" aria-hidden="true" focusable="false" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3v12"/><path d="m7 10 5 5 5-5"/><path d="M5 21h14"/></svg>';
    }

    function userAddIconSvg() {
        return '<svg viewBox="0 0 24 24" aria-hidden="true" focusable="false" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 21v-2a4 4 0 0 0-4-4H7a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M19 8v6"/><path d="M16 11h6"/></svg>';
    }

    function unreadDotIconSvg() {
        return '<svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><circle cx="12" cy="12" r="5" fill="currentColor"></circle></svg>';
    }

    function isImageAttachment(attachment) {
        return !attachment.is_deleted && String(attachment.mime_type || '').toLowerCase().startsWith('image/');
    }

    function buildAttachmentUrl(attachmentUuid, inline = false) {
        const inlineQuery = inline ? '&inline=1' : '';
        return `/api/messenger/download?attachment_uuid=${encodeURIComponent(attachmentUuid)}${inlineQuery}`;
    }

    function formatFileSize(sizeBytes) {
        const size = Number(sizeBytes || 0);
        if (!Number.isFinite(size) || size <= 0) {
            return '';
        }

        if (size >= 1024 * 1024) {
            return `${(size / (1024 * 1024)).toFixed(1)} МБ`;
        }

        if (size >= 1024) {
            return `${Math.round(size / 1024)} КБ`;
        }

        return `${size} Б`;
    }

    function getAttachmentExtension(fileName) {
        const normalized = String(fileName || '').trim();
        if (!normalized) {
            return 'FILE';
        }

        const parts = normalized.split('.');
        if (parts.length < 2) {
            return 'FILE';
        }

        const extension = parts.pop().replace(/[^a-zA-Z0-9а-яА-Я]/g, '');
        return extension ? extension.slice(0, 5).toUpperCase() : 'FILE';
    }

    function getAttachmentCode(fileName) {
        const normalized = String(fileName || '').trim();
        if (!normalized) {
            return 'FI';
        }

        const baseName = normalized.replace(/\.[^.]+$/, '');
        const prepared = baseName
            .replace(/[_-]+/g, ' ')
            .replace(/[^\p{L}\p{N}\s]/gu, ' ')
            .trim();

        if (!prepared) {
            return 'FI';
        }

        const words = prepared.split(/\s+/).filter(Boolean);
        if (words.length >= 2) {
            return `${words[0][0] || ''}${words[1][0] || ''}`.toUpperCase();
        }

        return prepared.slice(0, 2).toUpperCase();
    }

    function buildFileCardMarkup(fileName, extraClass = '', showFullName = false) {
        const extension = getAttachmentExtension(fileName);
        const code = getAttachmentCode(fileName);
        const safeExtraClass = extraClass ? ` ${extraClass}` : '';
        const primaryLabel = showFullName ? escapeHtml(fileName) : escapeHtml(code);
        const primaryClass = showFullName ? 'messenger-file-card__name' : 'messenger-file-card__code';
        return `
            <div class="messenger-file-card__body${safeExtraClass}">
                <span class="${primaryClass}">${primaryLabel}</span>
                <span class="messenger-file-card__ext">${escapeHtml(extension)}</span>
            </div>
        `;
    }

    function triggerAttachmentDownload(url, fileName) {
        const link = document.createElement('a');
        link.href = url;
        link.setAttribute('download', fileName || '');
        link.style.display = 'none';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }

    function downloadMessageAttachments(messageUuid) {
        const chat = messengerState.selectedChat;
        if (!chat) {
            return;
        }

        const message = (chat.messages || []).find((item) => item.message_uuid === messageUuid);
        if (!message) {
            return;
        }

        const attachments = (message.attachments || []).filter((attachment) => !attachment.is_deleted);
        attachments.forEach((attachment, index) => {
            window.setTimeout(() => {
                triggerAttachmentDownload(
                    buildAttachmentUrl(attachment.attachment_uuid),
                    attachment.original_name
                );
            }, index * 120);
        });
    }

    function closeImageViewer() {
        messengerState.activeImageIndex = null;
        refs.imageViewerModal.hidden = true;
        refs.imageViewerImage.removeAttribute('src');
        refs.imageViewerImage.alt = '';
        refs.imageViewerImage.hidden = true;
        refs.imageViewerFileCard.innerHTML = '';
        refs.imageViewerFileCard.hidden = true;
        refs.imageViewerCaption.innerHTML = '';
        refs.imageViewerTitle.textContent = 'Просмотр вложения';
        refs.downloadImageButton.href = '#';
        refs.downloadImageButton.removeAttribute('download');
        refs.deleteImageButton.hidden = true;
        refs.deleteImageButton.removeAttribute('data-delete-attachment');
        syncModalScrollState();
    }

    function updateImageViewer() {
        const activeIndex = Number(messengerState.activeImageIndex);
        const galleryItems = messengerState.imageGalleryItems || [];
        if (!Number.isInteger(activeIndex) || activeIndex < 0 || activeIndex >= galleryItems.length) {
            closeImageViewer();
            return;
        }

        const item = galleryItems[activeIndex];
        refs.imageViewerTitle.textContent = item.isImage ? 'Просмотр изображения' : 'Просмотр файла';
        refs.imageViewerImage.hidden = !item.isImage;
        refs.imageViewerFileCard.hidden = item.isImage;
        if (item.isImage) {
            refs.imageViewerImage.src = item.url;
            refs.imageViewerImage.alt = item.name;
            refs.imageViewerFileCard.innerHTML = '';
        } else {
            refs.imageViewerImage.removeAttribute('src');
            refs.imageViewerImage.alt = '';
            refs.imageViewerFileCard.innerHTML = buildFileCardMarkup(item.name, 'messenger-file-card__body--viewer', true);
        }
        refs.imageViewerCaption.innerHTML = `
            <strong>${escapeHtml(item.name)}</strong>
            <span>${escapeHtml(item.messageAuthor)} · ${escapeHtml(item.createdAtLabel)}${item.sizeLabel ? ` · ${escapeHtml(item.sizeLabel)}` : ''}</span>
        `;
        refs.downloadImageButton.href = item.downloadUrl;
        refs.downloadImageButton.setAttribute('download', item.name);
        refs.deleteImageButton.hidden = !item.canDelete;
        if (item.canDelete) {
            refs.deleteImageButton.setAttribute('data-delete-attachment', item.attachmentUuid);
        } else {
            refs.deleteImageButton.removeAttribute('data-delete-attachment');
        }
        refs.prevImageButton.disabled = activeIndex === 0;
        refs.nextImageButton.disabled = activeIndex >= galleryItems.length - 1;
    }

    function openImageViewer(index) {
        if (!Number.isInteger(index) || index < 0 || index >= messengerState.imageGalleryItems.length) {
            return;
        }

        messengerState.activeImageIndex = index;
        refs.imageViewerModal.hidden = false;
        updateImageViewer();
        syncModalScrollState();
    }

    function showPreviousImage() {
        if (!Number.isInteger(messengerState.activeImageIndex) || messengerState.activeImageIndex <= 0) {
            return;
        }

        messengerState.activeImageIndex -= 1;
        updateImageViewer();
    }

    function showNextImage() {
        if (!Number.isInteger(messengerState.activeImageIndex) || messengerState.activeImageIndex >= messengerState.imageGalleryItems.length - 1) {
            return;
        }

        messengerState.activeImageIndex += 1;
        updateImageViewer();
    }

    function renderAttachmentItem(attachment, options = {}) {
        const { viewerIndex = null } = options;
        const previewIndexAttribute = Number.isInteger(viewerIndex) ? ` data-attachment-index="${viewerIndex}"` : '';
        const safeName = escapeHtml(attachment.original_name);

        if (attachment.is_deleted) {
            return `
                <div class="messenger-file-card messenger-file-card--deleted" title="${safeName}">
                    <div class="messenger-file-card__body messenger-file-card__body--deleted">
                        <span class="messenger-file-card__deleted-label">Удалено</span>
                        <span class="messenger-file-card__deleted-text">Файл</span>
                    </div>
                </div>
            `;
        }

        if (isImageAttachment(attachment) && Number.isInteger(viewerIndex)) {
            return `
                <button type="button" class="messenger-image-card" data-image-index="${viewerIndex}" aria-label="Открыть вложение ${safeName}" title="${safeName}">
                    <img src="${buildAttachmentUrl(attachment.attachment_uuid, true)}" alt="${safeName}" class="messenger-image-card__preview" loading="lazy">
                </button>
            `;
        }

        return `
            <button type="button" class="messenger-file-card" aria-label="Открыть файл ${safeName}" title="${safeName}"${previewIndexAttribute}>
                ${buildFileCardMarkup(attachment.original_name)}
            </button>
        `;
    }

    function renderAttachmentGroup(message, attachments, chat, imageGalleryItemsRef, imageGalleryIndexRef) {
        if (!attachments.length) {
            return '';
        }

        const messageCreatedAtLabel = new Date(message.created_at).toLocaleString('ru-RU');
        const tiles = attachments.map((attachment) => {
            const canDeleteAttachment = !chat.read_only && message.author_side === 'site' && Number(message.author_user_id) === Number(messengerState.currentUserId) && !attachment.is_deleted;

            let currentGalleryIndex = null;
            if (!attachment.is_deleted) {
                currentGalleryIndex = imageGalleryIndexRef.value;
                imageGalleryItemsRef.push({
                    url: buildAttachmentUrl(attachment.attachment_uuid, true),
                    downloadUrl: buildAttachmentUrl(attachment.attachment_uuid),
                    attachmentUuid: attachment.attachment_uuid,
                    name: attachment.original_name,
                    messageAuthor: message.author_user_name,
                    createdAtLabel: messageCreatedAtLabel,
                    sizeLabel: formatFileSize(attachment.size_bytes),
                    canDelete: canDeleteAttachment,
                    isImage: isImageAttachment(attachment)
                });
                imageGalleryIndexRef.value += 1;
            }

            return renderAttachmentItem(attachment, {
                viewerIndex: currentGalleryIndex
            });
        }).join('');

        return `
            <div class="messenger-attachment">
                <div class="messenger-image-grid">${tiles}</div>
            </div>
        `;
    }

    function formatReaderName(name) {
        const normalized = String(name || '').trim().replace(/\s+/g, ' ');
        if (!normalized) {
            return '';
        }

        const parts = normalized.split(' ');
        if (parts.length < 2) {
            return normalized;
        }

        const surname = parts[0];
        const initials = parts
            .slice(1)
            .filter(Boolean)
            .map((part) => part.charAt(0).toUpperCase() + '.')
            .join('');

        return initials ? `${surname} ${initials}` : surname;
    }

    function renderChatReadStatus(chat) {
        if (!chat || !Array.isArray(chat.readers) || !chat.readers.length) {
            return '';
        }

        const latestEventId = Number(chat.latest_event_id || 0);
        if (latestEventId <= 0) {
            return '';
        }

        const labels = chat.readers
            .filter((reader) => !(reader.side === 'site' && Number(reader.user_id) === Number(messengerState.currentUserId)))
            .filter((reader) => Number(reader.last_read_event_id || 0) >= latestEventId)
            .map((reader) => formatReaderName(reader.user_name))
            .filter(Boolean);

        if (!labels.length) {
            return '';
        }

        return `<div class="messenger-readers">Прочитано: ${escapeHtml(labels.join(', '))}</div>`;
    }

    function updateFilePickerSummary() {
        const files = Array.from(refs.messageFiles.files || []);
        if (files.length === 0) {
            refs.messageFilesSummary.textContent = 'Файлы не выбраны';
            return;
        }

        if (files.length === 1) {
            refs.messageFilesSummary.textContent = files[0].name;
            return;
        }

        refs.messageFilesSummary.textContent = `Выбрано файлов: ${files.length}`;
    }

    function isAcceptedMessengerFile(file, input) {
        if (!file || !input) {
            return false;
        }

        const acceptValue = String(input.getAttribute('accept') || '');
        const tokens = acceptValue
            .split(',')
            .map((token) => token.trim().toLowerCase())
            .filter(Boolean);

        if (!tokens.length) {
            return true;
        }

        const fileName = String(file.name || '').toLowerCase();
        const mimeType = String(file.type || '').toLowerCase();

        return tokens.some((token) => {
            if (token.startsWith('.')) {
                return fileName.endsWith(token);
            }

            if (token.endsWith('/*')) {
                return mimeType.startsWith(token.slice(0, -1));
            }

            return mimeType === token;
        });
    }

    function appendFilesToInput(input, files) {
        if (!input || input.disabled || typeof DataTransfer === 'undefined') {
            return { accepted: 0, rejected: 0 };
        }

        const incomingFiles = Array.from(files || []);
        const acceptedFiles = incomingFiles.filter((file) => isAcceptedMessengerFile(file, input));
        const combinedFiles = Array.from(input.files || []).concat(acceptedFiles);
        const transfer = new DataTransfer();

        combinedFiles.forEach((file) => transfer.items.add(file));
        input.files = transfer.files;
        input.dispatchEvent(new Event('change', { bubbles: true }));

        return {
            accepted: acceptedFiles.length,
            rejected: incomingFiles.length - acceptedFiles.length
        };
    }

    function updateFilePickerState() {
        const disabled = Boolean(refs.messageFiles.disabled);
        refs.messageFilesButton.classList.toggle('is-disabled', disabled);
        refs.messageFilesButton.setAttribute('aria-disabled', disabled ? 'true' : 'false');
        if (refs.messageFilesDropzone) {
            refs.messageFilesDropzone.classList.toggle('is-disabled', disabled);
        }
        updateFilePickerSummary();
    }

    function bindFileDropTarget(node) {
        if (!node || !refs.messageFiles) {
            return;
        }

        ['dragenter', 'dragover'].forEach((eventName) => {
            node.addEventListener(eventName, (event) => {
                if (refs.messageFiles.disabled) {
                    return;
                }

                event.preventDefault();
                node.classList.add('is-dragover');
            });
        });

        node.addEventListener('dragleave', (event) => {
            if (!node.contains(event.relatedTarget)) {
                node.classList.remove('is-dragover');
            }
        });

        node.addEventListener('drop', (event) => {
            event.preventDefault();
            node.classList.remove('is-dragover');

            const result = appendFilesToInput(refs.messageFiles, event.dataTransfer ? event.dataTransfer.files : []);
            if (result.rejected > 0) {
                showFlash('error', 'Часть файлов пропущена. Разрешены только поддерживаемые вложения.');
            }
        });
    }

    function openCreateMode() {
        messengerState.mode = 'create';
        messengerState.selectedChatUuid = null;
        messengerState.selectedChat = null;
        messengerState.imageGalleryItems = [];
        closeImageViewer();
        refs.messageForm.reset();
        renderChats();
        renderChatDetails();
    }

    function openSearchModal() {
        refs.searchModal.hidden = false;
        syncModalScrollState();
        refs.searchInput.focus();
    }

    function closeSearchModal() {
        refs.searchModal.hidden = true;
        syncModalScrollState();
    }

    function syncModalScrollState() {
        const hasOpenModal = !refs.searchModal.hidden || !refs.participantModal.hidden || !refs.imageViewerModal.hidden;
        document.body.classList.toggle('stop-scroll', hasOpenModal);
    }

    function closeParticipantModal() {
        refs.participantModal.hidden = true;
        refs.participantOptions.innerHTML = '';
        refs.participantModalStatus.textContent = '';
        refs.confirmParticipantModalButton.disabled = true;
        syncModalScrollState();
    }

    function updateParticipantSelectionState() {
        const selectedCount = refs.participantOptions.querySelectorAll('input[type="checkbox"]:checked').length;
        refs.confirmParticipantModalButton.disabled = selectedCount === 0;
    }

    async function openParticipantModal() {
        if (!messengerState.selectedChatUuid) {
            return;
        }

        refs.participantModal.hidden = false;
        refs.participantModalStatus.textContent = 'Загрузка списка сотрудников...';
        refs.participantOptions.innerHTML = '<div class="messenger-empty">Загрузка...</div>';
        refs.confirmParticipantModalButton.disabled = true;
        syncModalScrollState();

        const data = await fetchJson(`/api/messenger/users?chat_uuid=${encodeURIComponent(messengerState.selectedChatUuid)}&limit=100`);
        const items = data.items || [];
        if (items.length === 0) {
            refs.participantModalStatus.textContent = 'Нет доступных сотрудников для добавления.';
            refs.participantOptions.innerHTML = '<div class="messenger-empty">Все доступные сотрудники уже участвуют в задании.</div>';
            return;
        }

        refs.participantModalStatus.textContent = 'Выберите одного или нескольких сотрудников.';
        refs.participantOptions.innerHTML = items.map((item) => `
            <label class="messenger-participant-option">
                <input type="checkbox" value="${item.user_id}">
                <span>${escapeHtml(item.user_name)}</span>
            </label>
        `).join('');

        refs.participantOptions.querySelectorAll('input[type="checkbox"]').forEach((checkbox) => {
            checkbox.addEventListener('change', updateParticipantSelectionState);
        });

        updateParticipantSelectionState();
    }

    function renderChats() {
        if (messengerState.chats.length === 0) {
            refs.chatList.innerHTML = '<div class="messenger-empty">У вас пока нет активных заданий.</div>';
            return;
        }

        refs.chatList.innerHTML = messengerState.chats.map((chat) => `
            <article class="messenger-chat-item ${messengerState.mode === 'chat' && chat.chat_uuid === messengerState.selectedChatUuid ? 'is-active' : ''}" data-chat-uuid="${chat.chat_uuid}">
                <div class="messenger-row">
                    <strong class="messenger-chat-item__title" title="${escapeHtml(chat.display_name)}">${chat.display_name}</strong>
                </div>
                <div class="messenger-row messenger-stack-sm">
                    <span class="messenger-meta">${new Date(chat.last_activity_at).toLocaleString('ru-RU')}</span>
                    <div class="messenger-controls">
                        ${chat.unread_count > 0
                            ? `<span class="messenger-unread">1</span>`
                            : messengerState.manualUnreadChatUuids.has(chat.chat_uuid)
                                ? `<button type="button" class="messenger-unread-toggle is-active" aria-label="Чат помечен как непрочитанный" title="Помечено как непрочитанное">${unreadDotIconSvg()}</button>`
                                : `<button type="button" class="messenger-unread-toggle" data-mark-chat-unread="${chat.chat_uuid}" aria-label="Пометить чат как непрочитанный" title="Прочитать позже">${unreadDotIconSvg()}</button>`}
                        <span class="messenger-status status-${chat.status}">${statusLabel(chat.status)}</span>
                    </div>
                </div>
            </article>
        `).join('');

        refs.chatList.querySelectorAll('[data-chat-uuid]').forEach((node) => {
            node.addEventListener('click', () => loadChat(node.getAttribute('data-chat-uuid')));
        });

        refs.chatList.querySelectorAll('[data-mark-chat-unread]').forEach((button) => {
            button.addEventListener('click', (event) => {
                event.stopPropagation();
                markChatUnread(button.getAttribute('data-mark-chat-unread')).catch((error) => showFlash('error', error.message));
            });
        });
    }

    function updateChatListUnreadIndicator(chatUuid) {
        const chat = messengerState.chats.find((item) => item.chat_uuid === chatUuid);
        if (!chat) {
            return;
        }

        const chatNode = refs.chatList.querySelector(`[data-chat-uuid="${chatUuid}"]`);
        if (!chatNode) {
            return;
        }

        const controlsNode = chatNode.querySelector('.messenger-controls');
        const statusNode = controlsNode ? controlsNode.querySelector('.messenger-status') : null;
        if (!controlsNode || !statusNode) {
            return;
        }

        const indicatorMarkup = chat.unread_count > 0
            ? `<span class="messenger-unread">1</span>`
            : messengerState.manualUnreadChatUuids.has(chat.chat_uuid)
                ? `<button type="button" class="messenger-unread-toggle is-active" aria-label="Чат помечен как непрочитанный" title="Помечено как непрочитанное">${unreadDotIconSvg()}</button>`
                : `<button type="button" class="messenger-unread-toggle" data-mark-chat-unread="${chat.chat_uuid}" aria-label="Пометить чат как непрочитанный" title="Прочитать позже">${unreadDotIconSvg()}</button>`;

        controlsNode.innerHTML = `${indicatorMarkup}${statusNode.outerHTML}`;
        const unreadButton = controlsNode.querySelector('[data-mark-chat-unread]');
        if (unreadButton) {
            unreadButton.addEventListener('click', (event) => {
                event.stopPropagation();
                markChatUnread(unreadButton.getAttribute('data-mark-chat-unread')).catch((error) => showFlash('error', error.message));
            });
        }
    }

    function renderChatDetails() {
        if (messengerState.mode === 'create') {
            messengerState.imageGalleryItems = [];
            closeImageViewer();
            refs.chatHeader.innerHTML = `
                <div class="messenger-row">
                    <div>
                        <h2 class="messenger-section-title messenger-section-title--spaced">Новое задание</h2>
                        <div class="messenger-meta">Напишите первое сообщение и при необходимости добавьте вложения.</div>
                    </div>
                    <span class="messenger-status status-new">Создание</span>
                </div>
            `;
            refs.chatParticipants.innerHTML = `
                <div class="messenger-row">
                    <div class="messenger-meta"><strong>Сотрудники:</strong></div>
                </div>
            `;
            refs.chatParticipantsSection.hidden = true;
            refs.chatMessages.innerHTML = '';
            refs.chatReadStatusSection.hidden = true;
            refs.chatReadStatus.innerHTML = '';
            refs.messageBody.disabled = false;
            refs.messageFiles.disabled = false;
            refs.sendMessageButton.disabled = false;
            refs.sendMessageButton.textContent = 'Создать задание';
            refs.leaveChatButton.hidden = true;
            refs.leaveChatButton.disabled = true;
            refs.messageBody.placeholder = 'Первое сообщение нового задания';
            updateFilePickerState();
            return;
        }

        const chat = messengerState.selectedChat;
        if (!chat) {
            messengerState.imageGalleryItems = [];
            closeImageViewer();
            refs.chatHeader.innerHTML = '<div class="messenger-empty">Выберите задание слева.</div>';
            refs.chatParticipants.innerHTML = '';
            refs.chatParticipantsSection.hidden = true;
            refs.chatMessages.innerHTML = '';
            refs.chatReadStatusSection.hidden = true;
            refs.chatReadStatus.innerHTML = '';
            refs.messageBody.disabled = true;
            refs.messageFiles.disabled = true;
            refs.sendMessageButton.disabled = true;
            refs.sendMessageButton.textContent = 'Отправить';
            refs.leaveChatButton.hidden = false;
            refs.leaveChatButton.disabled = true;
            refs.messageBody.placeholder = 'Сообщение по выбранному заданию';
            updateFilePickerState();
            return;
        }

        refs.chatHeader.innerHTML = `
            <div class="messenger-row">
                <div>
                    <h2 class="messenger-section-title messenger-section-title--spaced">${chat.display_name}</h2>
                </div>
                <span class="messenger-status status-${chat.status}">${statusLabel(chat.status)}</span>
            </div>
            ${chat.read_only ? '<div class="messenger-meta messenger-stack-md">Задание открыто в режиме только чтение: отправка сообщений и добавление сотрудников недоступны.</div>' : ''}
        `;

        refs.chatParticipants.innerHTML = `
            <div class="messenger-row">
                <div class="messenger-meta">Сотрудники: ${chat.participants.length
                    ? chat.participants.map((participant) => participant.user_name).join(', ')
                    : 'нет активных сотрудников'}</div>
                ${chat.read_only ? '' : `<button type="button" class="secondary messenger-icon-button" data-add-participant aria-label="Добавить сотрудника" title="Добавить сотрудника">${userAddIconSvg()}</button>`}
            </div>
        `;
        refs.chatParticipantsSection.hidden = Boolean(chat.read_only);

        const imageGalleryItems = [];
        const imageGalleryIndexRef = { value: 0 };
        refs.chatMessages.innerHTML = chat.messages.length
            ? chat.messages.map((message) => `
                <article class="messenger-message" data-side="${message.author_side}">
                    <div class="messenger-row">
                        <strong class="messenger-author">
                            <span>${message.author_user_name}</span>
                            ${message.is_edited ? '<span class="messenger-edited-badge" aria-label="Сообщение отредактировано" title="Сообщение отредактировано">&#9998;</span>' : ''}
                            ${message.is_deleted ? `<span class="messenger-deleted-badge" aria-label="Сообщение удалено" title="Сообщение удалено">${trashIconSvg()}</span>` : ''}
                        </strong>
                        <span class="messenger-meta">${new Date(message.created_at).toLocaleString('ru-RU')}</span>
                    </div>
                    ${message.is_deleted ? '' : `<div class="messenger-stack-sm">${message.body_text}</div>`}
                    <div class="messenger-attachments">
                        ${(() => {
                            return renderAttachmentGroup(message, message.attachments, chat, imageGalleryItems, imageGalleryIndexRef);
                        })()}
                    </div>
                    ${(!chat.read_only && message.author_side === 'site' && Number(message.author_user_id) === Number(messengerState.currentUserId) && !message.is_deleted) ? `
                        <div class="messenger-controls messenger-controls--end messenger-stack-sm">
                            ${message.attachments.some((attachment) => !attachment.is_deleted) ? `<button type="button" class="secondary messenger-icon-button" data-download-message-attachments="${message.message_uuid}" aria-label="Скачать все вложения сообщения" title="Скачать все вложения сообщения">${downloadIconSvg()}</button>` : ''}
                            <button type="button" class="secondary messenger-icon-button" data-edit-message="${message.message_uuid}" aria-label="Редактировать сообщение" title="Редактировать сообщение">&#9998;</button>
                            <button type="button" class="secondary messenger-icon-button" data-delete-message="${message.message_uuid}" aria-label="Удалить сообщение" title="Удалить сообщение">${trashIconSvg()}</button>
                        </div>
                    ` : ''}
                </article>
            `).join('')
            : '<div class="messenger-empty">Сообщений пока нет.</div>';

        messengerState.imageGalleryItems = imageGalleryItems;
        if (!messengerState.imageGalleryItems.length) {
            closeImageViewer();
        } else if (!refs.imageViewerModal.hidden && Number.isInteger(messengerState.activeImageIndex)) {
            if (messengerState.activeImageIndex >= messengerState.imageGalleryItems.length) {
                messengerState.activeImageIndex = messengerState.imageGalleryItems.length - 1;
            }
            updateImageViewer();
        }

        const readOnly = Boolean(chat.read_only);
        const chatReadStatusMarkup = renderChatReadStatus(chat);
        refs.chatReadStatus.innerHTML = chatReadStatusMarkup;
        refs.chatReadStatusSection.hidden = chatReadStatusMarkup === '';
        refs.messageBody.disabled = readOnly;
        refs.messageFiles.disabled = readOnly;
        refs.sendMessageButton.disabled = readOnly;
        refs.sendMessageButton.textContent = 'Отправить';
        refs.leaveChatButton.hidden = false;
        refs.leaveChatButton.disabled = false;
        refs.messageBody.placeholder = 'Сообщение по выбранному заданию';
        updateFilePickerState();

        refs.chatParticipants.querySelectorAll('[data-add-participant]').forEach((button) => {
            button.addEventListener('click', () => openParticipantModal().catch((error) => showFlash('error', error.message)));
        });

        refs.chatMessages.querySelectorAll('[data-edit-message]').forEach((button) => {
            button.addEventListener('click', async () => {
                const newText = window.prompt('Новый текст сообщения');
                if (!newText) {
                    return;
                }

                await postJson('/api/messenger/edit.php', {
                    message_uuid: button.getAttribute('data-edit-message'),
                    body_text: newText
                });
                await loadChat(messengerState.selectedChatUuid, true);
            });
        });

        refs.chatMessages.querySelectorAll('[data-delete-message]').forEach((button) => {
            button.addEventListener('click', async () => {
                if (!window.confirm('Удалить сообщение?')) {
                    return;
                }

                await postJson('/api/messenger/delete.php', {
                    entity_type: 'message',
                    entity_uuid: button.getAttribute('data-delete-message')
                });
                await loadChat(messengerState.selectedChatUuid, true);
            });
        });

        refs.chatMessages.querySelectorAll('[data-download-message-attachments]').forEach((button) => {
            button.addEventListener('click', async () => {
                downloadMessageAttachments(button.getAttribute('data-download-message-attachments'));
            });
        });

        refs.chatMessages.querySelectorAll('[data-image-index], [data-attachment-index]').forEach((button) => {
            button.addEventListener('click', () => {
                const imageIndex = Number(button.getAttribute('data-image-index') || button.getAttribute('data-attachment-index'));
                openImageViewer(imageIndex);
            });
        });
    }

    async function loadChats(options = {}) {
        const { preserveFlash = false, skipSelectedChatLoad = false } = options;
        if (!preserveFlash) {
            clearFlash();
        }

        const data = await fetchJson('/api/messenger/chats.php');
        messengerState.chats = data.items || [];
        syncManualUnreadChatUuids();
        updateTabNotification();
        if (messengerState.mode === 'create') {
            renderChats();
            renderChatDetails();
            return;
        }
        if (messengerState.selectedChatUuid) {
            const selectedChatExists = messengerState.chats.some((chat) => chat.chat_uuid === messengerState.selectedChatUuid);
            if (!selectedChatExists) {
                messengerState.selectedChatUuid = null;
                messengerState.selectedChat = null;
            }
        }
        renderChats();

        if (messengerState.selectedChatUuid && !skipSelectedChatLoad) {
            await loadChat(messengerState.selectedChatUuid, true, '', {
                markRead: !messengerState.manualUnreadChatUuids.has(messengerState.selectedChatUuid),
                clearManualUnread: false
            });
        } else if (!messengerState.selectedChatUuid) {
            messengerState.selectedChat = null;
            renderChatDetails();
        }
    }

    async function autoRefreshChats() {
        if (autoRefreshInFlight) {
            return;
        }

        autoRefreshInFlight = true;
        try {
            await loadChats({
                preserveFlash: true,
                skipSelectedChatLoad: document.hidden
            });
        } catch (error) {
            console.error(error);
        } finally {
            autoRefreshInFlight = false;
        }
    }

    function startAutoRefresh() {
        if (autoRefreshTimerId !== null) {
            return;
        }

        autoRefreshTimerId = window.setInterval(() => {
            autoRefreshChats();
        }, CHAT_AUTO_REFRESH_INTERVAL_MS);
    }

    function stopAutoRefresh() {
        if (autoRefreshTimerId === null) {
            return;
        }

        window.clearInterval(autoRefreshTimerId);
        autoRefreshTimerId = null;
    }

    function clearChatUnreadBadge(chatUuid) {
        messengerState.chats = messengerState.chats.map((chat) => {
            if (chat.chat_uuid !== chatUuid) {
                return chat;
            }

            return {
                ...chat,
                unread_count: 0,
                manual_unread: false
            };
        });
        messengerState.manualUnreadChatUuids.delete(chatUuid);
        updateTabNotification();
        updateChatListUnreadIndicator(chatUuid);
    }

    async function markChatUnread(chatUuid) {
        if (!chatUuid) {
            return;
        }

        clearFlash();
        const data = await postJson('/api/messenger/unread.php', {
            chat_uuid: chatUuid
        });
        messengerState.manualUnreadChatUuids.add(chatUuid);
        messengerState.chats = messengerState.chats.map((chat) => {
            if (chat.chat_uuid !== chatUuid) {
                return chat;
            }

            return {
                ...chat,
                unread_count: Math.max(0, Number(data.unread_count || 0)),
                manual_unread: true
            };
        });
        updateTabNotification();
        updateChatListUnreadIndicator(chatUuid);
    }

    async function loadChat(chatUuid, preserveSelection = false, focusMessageUuid = '', options = {}) {
        const { markRead = true, clearManualUnread = true } = options;
        messengerState.mode = 'chat';
        messengerState.selectedChatUuid = chatUuid;
        const focusQuery = focusMessageUuid ? `&focus_message_uuid=${encodeURIComponent(focusMessageUuid)}` : '';
        const data = await fetchJson(`/api/messenger/chat?chat_uuid=${encodeURIComponent(chatUuid)}${focusQuery}`);
        messengerState.selectedChat = data;
        renderChats();
        renderChatDetails();

        if (markRead) {
            try {
                const readData = await postJson('/api/messenger/read.php', {
                    chat_uuid: chatUuid,
                    last_read_event_id: data.latest_event_id || 0
                });
                if (
                    messengerState.selectedChat
                    && messengerState.selectedChat.chat_uuid === chatUuid
                    && Array.isArray(messengerState.selectedChat.participants)
                ) {
                    messengerState.selectedChat.participants = messengerState.selectedChat.participants.map((participant) => {
                        if (Number(participant.user_id) !== Number(messengerState.currentUserId)) {
                            return participant;
                        }

                        return {
                            ...participant,
                            last_read_event_id: Number(readData.last_read_event_id || data.latest_event_id || 0),
                            last_read_at: readData.last_read_at || participant.last_read_at || null
                        };
                    });
                    messengerState.selectedChat.readers = Array.isArray(messengerState.selectedChat.readers)
                        ? messengerState.selectedChat.readers.map((reader) => {
                            if (!(reader.side === 'site' && Number(reader.user_id) === Number(messengerState.currentUserId))) {
                                return reader;
                            }

                            return {
                                ...reader,
                                last_read_event_id: Number(readData.last_read_event_id || data.latest_event_id || 0),
                                last_read_at: readData.last_read_at || reader.last_read_at || null
                            };
                        })
                        : [];
                    renderChatDetails();
                }
                clearChatUnreadBadge(chatUuid);
                if (clearManualUnread) {
                    messengerState.manualUnreadChatUuids.delete(chatUuid);
                }
                renderChats();
            } catch (error) {
                console.error(error);
            }
        }

    }

    async function postJson(url, payload) {
        return fetchJson(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-Token': messengerState.csrfToken
            },
            body: JSON.stringify(payload)
        });
    }

    async function submitCreateChat(event) {
        event.preventDefault();
        clearFlash();

        const formData = new FormData();
        formData.append('csrf_token', messengerState.csrfToken);
        formData.append('body_text', refs.messageBody.value);
        Array.from(refs.messageFiles.files).forEach((file) => formData.append('files[]', file));

        const response = await fetch('/api/messenger/site-create.php', {
            method: 'POST',
            body: formData
        });
        const data = await response.json();
        if (!response.ok || !data.ok) {
            throw new Error(data.error ? `${data.error.code}: ${data.error.message}` : 'Ошибка создания задания');
        }

        refs.messageForm.reset();
        updateFilePickerSummary();
        messengerState.mode = 'chat';
        showFlash('success', 'Задание создано.');
        await loadChats();
        if (data.data.chat_uuid) {
            await loadChat(data.data.chat_uuid, true);
        }
    }

    async function submitMessage(event) {
        event.preventDefault();
        if (messengerState.mode === 'create') {
            await submitCreateChat(event);
            return;
        }
        if (!messengerState.selectedChatUuid) {
            return;
        }

        clearFlash();
        const formData = new FormData();
        formData.append('csrf_token', messengerState.csrfToken);
        formData.append('chat_uuid', messengerState.selectedChatUuid);
        formData.append('body_text', refs.messageBody.value);
        Array.from(refs.messageFiles.files).forEach((file) => formData.append('files[]', file));

        const response = await fetch('/api/messenger/send.php', {
            method: 'POST',
            body: formData
        });
        const data = await response.json();
        if (!response.ok || !data.ok) {
            throw new Error(data.error ? `${data.error.code}: ${data.error.message}` : 'Ошибка отправки сообщения');
        }

        refs.messageForm.reset();
        updateFilePickerSummary();
        await loadChats();
        await loadChat(messengerState.selectedChatUuid, true);
    }

    async function runSearch() {
        const query = refs.searchInput.value.trim();
        if (!query) {
            refs.searchResults.innerHTML = '<div class="messenger-empty">Введите запрос.</div>';
            return;
        }

        const data = await fetchJson(`/api/messenger/search?q=${encodeURIComponent(query)}`);
        const items = data.items || [];
        refs.searchResults.innerHTML = items.length
            ? items.map((item) => `
                <article class="messenger-search-item" data-search-chat="${item.chat_uuid}" data-search-message="${item.focus_message_uuid}">
                    <strong>${item.display_name}</strong>
                    <div class="messenger-meta messenger-stack-sm">${item.author_user_name} · ${new Date(item.created_at).toLocaleString('ru-RU')}</div>
                    <div class="messenger-stack-sm">${item.snippet}</div>
                </article>
            `).join('')
            : '<div class="messenger-empty">Ничего не найдено.</div>';

        refs.searchResults.querySelectorAll('[data-search-chat]').forEach((node) => {
            node.addEventListener('click', async () => {
                const chatUuid = node.getAttribute('data-search-chat');
                const focusMessageUuid = node.getAttribute('data-search-message');
                await loadChat(chatUuid, true, focusMessageUuid);
                closeSearchModal();
            });
        });
    }

    async function addSelectedParticipants() {
        if (!messengerState.selectedChatUuid) {
            return;
        }

        const selectedIds = Array.from(refs.participantOptions.querySelectorAll('input[type="checkbox"]:checked'))
            .map((checkbox) => Number(checkbox.value))
            .filter((value) => Number.isFinite(value) && value > 0);

        if (selectedIds.length === 0) {
            return;
        }

        refs.confirmParticipantModalButton.disabled = true;
        for (const userId of selectedIds) {
            await postJson('/api/messenger/participants-add.php', {
                chat_uuid: messengerState.selectedChatUuid,
                user_id: userId
            });
        }

        closeParticipantModal();
        showFlash('success', selectedIds.length === 1 ? 'Сотрудник добавлен.' : 'Сотрудники добавлены.');
        await loadChat(messengerState.selectedChatUuid, true);
    }

    async function leaveChat() {
        if (!messengerState.selectedChatUuid || !window.confirm('Выйти из задания?')) {
            return;
        }

        await postJson('/api/messenger/leave.php', {
            chat_uuid: messengerState.selectedChatUuid
        });
        showFlash('success', 'Вы вышли из задания.');
        messengerState.selectedChatUuid = null;
        messengerState.selectedChat = null;
        await loadChats();
    }

    refs.refreshChatsButton.addEventListener('click', () => loadChats().catch((error) => showFlash('error', error.message)));
    refs.messageForm.addEventListener('submit', (event) => submitMessage(event).catch((error) => showFlash('error', error.message)));
    refs.messageBody.addEventListener('keydown', (event) => {
        if (event.key !== 'Enter') {
            return;
        }

        if (event.ctrlKey || event.shiftKey || event.altKey || event.metaKey) {
            return;
        }

        event.preventDefault();
        if (!refs.messageBody.disabled) {
            refs.messageForm.requestSubmit();
        }
    });
    refs.messageFiles.addEventListener('change', updateFilePickerSummary);
    bindFileDropTarget(refs.messageBody);
    bindFileDropTarget(refs.messageFilesDropzone);
    refs.openCreateChatButton.addEventListener('click', openCreateMode);
    refs.openSearchModalButton.addEventListener('click', openSearchModal);
    refs.closeSearchModalButton.addEventListener('click', closeSearchModal);
    refs.closeParticipantModalButton.addEventListener('click', closeParticipantModal);
    refs.cancelParticipantModalButton.addEventListener('click', closeParticipantModal);
    refs.confirmParticipantModalButton.addEventListener('click', () => addSelectedParticipants().catch((error) => showFlash('error', error.message)));
    refs.searchButton.addEventListener('click', () => runSearch().catch((error) => showFlash('error', error.message)));
    refs.leaveChatButton.addEventListener('click', () => leaveChat().catch((error) => showFlash('error', error.message)));
    refs.closeImageViewerButton.addEventListener('click', closeImageViewer);
    refs.prevImageButton.addEventListener('click', showPreviousImage);
    refs.nextImageButton.addEventListener('click', showNextImage);
    refs.deleteImageButton.addEventListener('click', async () => {
        const attachmentUuid = refs.deleteImageButton.getAttribute('data-delete-attachment');
        if (!attachmentUuid || !window.confirm('Удалить вложение?')) {
            return;
        }

        await postJson('/api/messenger/delete.php', {
            entity_type: 'attachment',
            entity_uuid: attachmentUuid
        });
        closeImageViewer();
        await loadChat(messengerState.selectedChatUuid, true);
    });
    refs.searchModal.querySelectorAll('[data-close-search-modal]').forEach((node) => {
        node.addEventListener('click', closeSearchModal);
    });
    refs.participantModal.querySelectorAll('[data-close-participant-modal]').forEach((node) => {
        node.addEventListener('click', closeParticipantModal);
    });
    refs.imageViewerModal.querySelectorAll('[data-close-image-viewer]').forEach((node) => {
        node.addEventListener('click', closeImageViewer);
    });
    refs.searchInput.addEventListener('keydown', (event) => {
        if (event.key === 'Enter') {
            event.preventDefault();
            runSearch().catch((error) => showFlash('error', error.message));
        }
    });
    document.addEventListener('keydown', (event) => {
        if (!refs.imageViewerModal.hidden) {
            if (event.key === 'Escape') {
                closeImageViewer();
                return;
            }

            if (event.key === 'ArrowLeft') {
                showPreviousImage();
                return;
            }

            if (event.key === 'ArrowRight') {
                showNextImage();
                return;
            }
        }

        if (event.key !== 'Escape') {
            return;
        }

        if (!refs.participantModal.hidden) {
            closeParticipantModal();
            return;
        }

        if (!refs.searchModal.hidden) {
            closeSearchModal();
        }
    });
    document.addEventListener('visibilitychange', () => {
        if (!document.hidden) {
            autoRefreshChats();
        }
    });
    window.addEventListener('beforeunload', stopAutoRefresh);

    updateFilePickerState();
    startAutoRefresh();
    loadChats().catch((error) => showFlash('error', error.message));
</script>
<?php include __DIR__ . '/footer.php'; ?>
</body>
</html>
