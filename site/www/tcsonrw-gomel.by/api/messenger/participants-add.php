<?php

require_once __DIR__ . '/_bootstrap.php';

try {
    messengerApiRequireMethod('POST');
    $actor = messengerApiResolveActor(true, false);
    messengerApiGuardSessionPost($actor);
    $payload = messengerApiGetJsonInput();
    $data = messengerAddParticipant($conn, $actor, (string) ($payload['chat_uuid'] ?? ''), (int) ($payload['user_id'] ?? 0));
    messengerApiRespondSuccess($data);
} catch (Throwable $throwable) {
    messengerApiHandleException($throwable);
}
