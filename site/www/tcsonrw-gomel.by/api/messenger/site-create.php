<?php

require_once __DIR__ . '/_bootstrap.php';

try {
    messengerApiRequireMethod('POST');
    $actor = messengerApiResolveActor(true, false);
    messengerApiGuardSessionPost($actor);
    $files = messengerNormalizeFilesArray($_FILES['files'] ?? array());
    $data = messengerCreateChat($conn, $actor, (string) ($_POST['body_text'] ?? ''), array(), $files);
    messengerApiRespondSuccess($data, 201);
} catch (Throwable $throwable) {
    messengerApiHandleException($throwable);
}
