<?php

require_once __DIR__ . '/_bootstrap.php';

try {
    messengerApiRequireMethod('GET');
    messengerApiResolveActor(false, true);
    messengerApiRespondSuccess(messengerGetSiteInfo($conn));
} catch (Throwable $throwable) {
    messengerApiHandleException($throwable);
}
