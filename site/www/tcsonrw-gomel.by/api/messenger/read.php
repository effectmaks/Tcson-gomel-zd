<?php

require_once __DIR__ . '/_bootstrap.php';

try {
    messengerApiRequireMethod('POST');
    $actor = messengerApiResolveActor(true, true);
    messengerApiGuardSessionPost($actor);
    $payload = messengerApiGetJsonInput();
    $requestId = messengerApiGetRequestIdFromInput($payload, $actor);
    $storedResponse = messengerApiHandleIdempotentCentralRequest($actor, 'read', $requestId);
    if (is_array($storedResponse)) {
        messengerApiRespondSuccess($storedResponse);
    }

    $data = messengerMarkRead($conn, $actor, (string) ($payload['chat_uuid'] ?? ''), (int) ($payload['last_read_event_id'] ?? 0));
    messengerApiStoreIdempotentCentralResponse($actor, 'read', $requestId, $data);
    messengerApiRespondSuccess($data);
} catch (Throwable $throwable) {
    messengerApiHandleException($throwable);
}
