<?php

require_once __DIR__ . '/_bootstrap.php';

try {
    messengerApiRequireMethod('GET');
    $actor = messengerApiResolveActor(true, false);
    $data = messengerGetChatView($conn, $actor, $_GET);
    messengerApiRespondSuccess($data);
} catch (Throwable $throwable) {
    messengerApiHandleException($throwable);
}
