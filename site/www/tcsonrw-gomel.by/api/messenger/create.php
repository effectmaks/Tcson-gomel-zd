<?php

require_once __DIR__ . '/_bootstrap.php';

try {
    messengerApiRequireMethod('POST');
    $actor = messengerApiResolveActor(false, true);
    $requestId = messengerApiGetRequestIdFromInput(array(), $actor);
    $storedResponse = messengerApiHandleIdempotentCentralRequest($actor, 'create', $requestId);
    if (is_array($storedResponse)) {
        messengerApiRespondSuccess($storedResponse);
    }

    $siteUserIds = isset($_POST['site_user_ids']) ? (array) $_POST['site_user_ids'] : array();
    $files = messengerNormalizeFilesArray($_FILES['files'] ?? array());
    $data = messengerCreateChat($conn, $actor, (string) ($_POST['body_text'] ?? ''), $siteUserIds, $files);
    messengerApiStoreIdempotentCentralResponse($actor, 'create', $requestId, $data);
    messengerApiRespondSuccess($data, 201);
} catch (Throwable $throwable) {
    messengerApiHandleException($throwable);
}
