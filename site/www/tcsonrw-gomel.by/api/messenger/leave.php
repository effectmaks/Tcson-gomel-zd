<?php

require_once __DIR__ . '/_bootstrap.php';

try {
    messengerApiRequireMethod('POST');
    $actor = messengerApiResolveActor(true, false);
    messengerApiGuardSessionPost($actor);
    $payload = messengerApiGetJsonInput();
    $data = messengerLeaveChat($conn, $actor, (string) ($payload['chat_uuid'] ?? ''));
    messengerApiRespondSuccess($data);
} catch (Throwable $throwable) {
    messengerApiHandleException($throwable);
}
