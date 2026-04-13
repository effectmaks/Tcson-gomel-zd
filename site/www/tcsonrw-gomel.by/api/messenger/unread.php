<?php

require_once __DIR__ . '/_bootstrap.php';

try {
    messengerApiRequireMethod('POST');
    $actor = messengerApiResolveActor(true, true);
    messengerApiGuardSessionPost($actor);
    $payload = messengerApiGetJsonInput();
    $requestId = messengerApiGetRequestIdFromInput($payload, $actor);
    $storedResponse = messengerApiHandleIdempotentCentralRequest($actor, 'unread', $requestId);
    if (is_array($storedResponse)) {
        messengerApiRespondSuccess($storedResponse);
    }

    $data = messengerMarkUnread($conn, $actor, (string) ($payload['chat_uuid'] ?? ''));
    messengerApiStoreIdempotentCentralResponse($actor, 'unread', $requestId, $data);
    messengerApiRespondSuccess($data);
} catch (Throwable $throwable) {
    messengerApiHandleException($throwable);
}
