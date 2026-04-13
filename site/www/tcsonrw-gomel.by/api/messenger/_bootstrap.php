<?php

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Authorization, Content-Type, Accept, X-Requested-With');
header('Access-Control-Max-Age: 600');
header('Vary: Origin, Access-Control-Request-Method, Access-Control-Request-Headers', false);

if (strtoupper((string) ($_SERVER['REQUEST_METHOD'] ?? 'GET')) === 'OPTIONS') {
    http_response_code(204);
    exit();
}

session_start();

require_once dirname(__DIR__, 2) . '/lib/security.php';
require_once dirname(__DIR__, 2) . '/lib/auth.php';
require_once dirname(__DIR__, 2) . '/lib/messenger.php';
include dirname(__DIR__, 2) . '/db_connection.php';

header('Content-Type: application/json; charset=UTF-8');
header('X-Robots-Tag: noindex, nofollow', true);

messengerEnsureReady($conn);

function messengerApiRespondSuccess(array $data, $httpStatus = 200)
{
    http_response_code((int) $httpStatus);
    echo json_encode(
        array(
            'ok' => true,
            'data' => $data,
            'error' => null,
        ),
        JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
    );
    exit();
}

function messengerApiRespondError($errorCode, $message, $httpStatus = 400, array $details = array())
{
    http_response_code((int) $httpStatus);
    echo json_encode(
        array(
            'ok' => false,
            'data' => null,
            'error' => array(
                'code' => $errorCode,
                'message' => $message,
                'details' => (object) $details,
            ),
        ),
        JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
    );
    exit();
}

function messengerApiHandleException(Throwable $throwable)
{
    if ($throwable instanceof MessengerApiException) {
        messengerApiRespondError(
            $throwable->getErrorCodeName(),
            $throwable->getMessage(),
            $throwable->getHttpStatusCode(),
            $throwable->getDetailsPayload()
        );
    }

    error_log('Messenger API fatal: ' . $throwable->getMessage());
    messengerApiRespondError('validation_error', 'Внутренняя ошибка сервера.', 500);
}

function messengerApiRequireMethod($method)
{
    if (strtoupper((string) $_SERVER['REQUEST_METHOD']) !== strtoupper((string) $method)) {
        messengerApiRespondError('validation_error', 'Метод не поддерживается.', 405);
    }
}

function messengerApiGetJsonInput()
{
    $raw = file_get_contents('php://input');
    if (!is_string($raw) || trim($raw) === '') {
        return array();
    }

    $payload = json_decode($raw, true);
    if (!is_array($payload)) {
        messengerApiRespondError('validation_error', 'Ожидается JSON-тело запроса.', 422);
    }

    return $payload;
}

function messengerApiGetRequestIdFromInput(array $payload, array $actor)
{
    if ($actor['side'] !== 'central') {
        return null;
    }

    $requestId = trim((string) ($payload['request_id'] ?? $_POST['request_id'] ?? ''));
    if ($requestId === '') {
        messengerApiRespondError('validation_error', 'Для Bearer-запроса обязателен request_id.', 422);
    }

    return $requestId;
}

function messengerApiResolveActor($allowSession, $allowBearer)
{
    global $conn;
    return messengerResolveActor($conn, $allowSession, $allowBearer);
}

function messengerApiGuardSessionPost(array $actor)
{
    if ($actor['side'] === 'site' && strtoupper((string) $_SERVER['REQUEST_METHOD']) === 'POST') {
        requireCsrfToken();
    }
}

function messengerApiHandleIdempotentCentralRequest(array $actor, $endpointName, $requestId)
{
    global $conn;
    if ($actor['side'] !== 'central' || $requestId === null) {
        return null;
    }

    return messengerGetStoredExternalResponse($conn, $requestId, $endpointName);
}

function messengerApiStoreIdempotentCentralResponse(array $actor, $endpointName, $requestId, array $response)
{
    global $conn;
    if ($actor['side'] !== 'central' || $requestId === null) {
        return;
    }

    messengerStoreExternalResponse($conn, $requestId, $endpointName, $response);
}
