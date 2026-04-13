<?php

require_once __DIR__ . '/_bootstrap.php';

try {
    messengerApiRequireMethod('GET');
    $actor = messengerApiResolveActor(true, true);
    $attachmentUuid = trim((string) ($_GET['attachment_uuid'] ?? ''));
    $inlineMode = isset($_GET['inline']) && (string) $_GET['inline'] === '1';
    if ($attachmentUuid === '') {
        messengerApiRespondError('validation_error', 'attachment_uuid обязателен.', 422);
    }

    $download = messengerDownloadAttachment($conn, $actor, $attachmentUuid);

    header_remove('Content-Type');
    header_remove('Content-Length');
    header('Content-Type: ' . $download['mime_type']);
    header('Content-Length: ' . (string) $download['size_bytes']);
    header('Content-Disposition: ' . ($inlineMode ? 'inline' : 'attachment') . '; filename="' . rawurlencode($download['download_name']) . '"');
    readfile($download['path']);
    exit();
} catch (Throwable $throwable) {
    messengerApiHandleException($throwable);
}
