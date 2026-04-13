<?php

require_once __DIR__ . '/_bootstrap.php';

try {
    messengerApiRequireMethod('POST');
    $actor = messengerApiResolveActor(true, true);
    messengerApiGuardSessionPost($actor);
    $payload = messengerApiGetJsonInput();
    $requestId = messengerApiGetRequestIdFromInput($payload, $actor);
    $storedResponse = messengerApiHandleIdempotentCentralRequest($actor, 'edit', $requestId);
    if (is_array($storedResponse)) {
        messengerApiRespondSuccess($storedResponse);
    }

    $data = messengerEditMessage($conn, $actor, (string) ($payload['message_uuid'] ?? ''), (string) ($payload['body_text'] ?? ''));
    messengerApiStoreIdempotentCentralResponse($actor, 'edit', $requestId, $data);
    messengerApiRespondSuccess($data);
} catch (Throwable $throwable) {
    messengerApiHandleException($throwable);
}
