<?php

require_once __DIR__ . '/_bootstrap.php';

try {
    $actor = messengerApiResolveActor(true, false);
    messengerRequireTokenManagerAccess($actor);

    if (strtoupper((string) $_SERVER['REQUEST_METHOD']) === 'GET') {
        messengerApiRespondSuccess(array(
            'items' => messengerListApiTokens($conn),
        ));
    }

    messengerApiRequireMethod('POST');
    messengerApiGuardSessionPost($actor);
    $payload = messengerApiGetJsonInput();
    $action = trim((string) ($payload['action'] ?? ''));

    if ($action === 'create') {
        $data = messengerCreateApiToken(
            $conn,
            $actor,
            (string) ($payload['label'] ?? ''),
            $payload['expires_at'] ?? null
        );
        messengerApiRespondSuccess($data, 201);
    }

    if ($action === 'revoke') {
        $data = messengerRevokeApiToken(
            $conn,
            $actor,
            (int) ($payload['token_id'] ?? 0)
        );
        messengerApiRespondSuccess($data);
    }

    messengerApiRespondError('validation_error', 'Неизвестное действие для Bearer-токена.', 422);
} catch (Throwable $throwable) {
    messengerApiHandleException($throwable);
}
