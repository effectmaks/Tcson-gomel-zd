<?php

require_once __DIR__ . '/_bootstrap.php';

try {
    messengerApiRequireMethod('POST');
    $actor = messengerApiResolveActor(true, true);
    messengerApiGuardSessionPost($actor);
    $requestId = messengerApiGetRequestIdFromInput(array(), $actor);
    $storedResponse = messengerApiHandleIdempotentCentralRequest($actor, 'send', $requestId);
    if (is_array($storedResponse)) {
        messengerApiRespondSuccess($storedResponse);
    }

    $files = messengerNormalizeFilesArray($_FILES['files'] ?? array());
    $data = messengerSendMessage($conn, $actor, (string) ($_POST['chat_uuid'] ?? ''), (string) ($_POST['body_text'] ?? ''), $files);
    messengerApiStoreIdempotentCentralResponse($actor, 'send', $requestId, $data);
    messengerApiRespondSuccess($data, 201);
} catch (Throwable $throwable) {
    messengerApiHandleException($throwable);
}
