<?php

require_once __DIR__ . '/_bootstrap.php';

try {
    messengerApiRequireMethod('POST');
    $actor = messengerApiResolveActor(false, true);
    $payload = messengerApiGetJsonInput();
    $requestId = messengerApiGetRequestIdFromInput($payload, $actor);
    $storedResponse = messengerApiHandleIdempotentCentralRequest($actor, 'status', $requestId);
    if (is_array($storedResponse)) {
        messengerApiRespondSuccess($storedResponse);
    }

    $data = messengerChangeStatus($conn, $actor, (string) ($payload['chat_uuid'] ?? ''), (string) ($payload['status'] ?? ''));
    messengerApiStoreIdempotentCentralResponse($actor, 'status', $requestId, $data);
    messengerApiRespondSuccess($data);
} catch (Throwable $throwable) {
    messengerApiHandleException($throwable);
}
