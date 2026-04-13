<?php

require_once __DIR__ . '/_bootstrap.php';

try {
    messengerApiRequireMethod('POST');
    $actor = messengerApiResolveActor(true, true);
    messengerApiGuardSessionPost($actor);
    $payload = messengerApiGetJsonInput();
    $requestId = messengerApiGetRequestIdFromInput($payload, $actor);
    $storedResponse = messengerApiHandleIdempotentCentralRequest($actor, 'delete', $requestId);
    if (is_array($storedResponse)) {
        messengerApiRespondSuccess($storedResponse);
    }

    $data = messengerDeleteEntity($conn, $actor, (string) ($payload['entity_type'] ?? ''), (string) ($payload['entity_uuid'] ?? ''));
    messengerApiStoreIdempotentCentralResponse($actor, 'delete', $requestId, $data);
    messengerApiRespondSuccess($data);
} catch (Throwable $throwable) {
    messengerApiHandleException($throwable);
}
