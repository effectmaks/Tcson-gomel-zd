<?php

require_once __DIR__ . '/_bootstrap.php';

try {
    messengerApiRequireMethod('GET');
    $actor = messengerApiResolveActor(true, true);
    $data = messengerListUsers($conn, $actor, $_GET);
    messengerApiRespondSuccess($data);
} catch (Throwable $throwable) {
    messengerApiHandleException($throwable);
}
