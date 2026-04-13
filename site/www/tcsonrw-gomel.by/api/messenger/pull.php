<?php

require_once __DIR__ . '/_bootstrap.php';

try {
    messengerApiRequireMethod('POST');
    messengerApiResolveActor(false, true);
    $payload = messengerApiGetJsonInput();
    $data = messengerPullEvents($conn, $payload);
    messengerApiRespondSuccess($data);
} catch (Throwable $throwable) {
    messengerApiHandleException($throwable);
}
