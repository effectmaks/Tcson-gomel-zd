<?php

require_once __DIR__ . '/_bootstrap.php';

try {
    messengerApiRequireMethod('GET');
    $actor = messengerApiResolveActor(false, true);
    $messageUuid = trim((string) ($_GET['message_uuid'] ?? ''));
    if ($messageUuid === '') {
        messengerApiRespondError('validation_error', 'message_uuid обязателен.', 422);
    }

    $data = messengerGetHistory($conn, $actor, $messageUuid);
    messengerApiRespondSuccess($data);
} catch (Throwable $throwable) {
    messengerApiHandleException($throwable);
}
