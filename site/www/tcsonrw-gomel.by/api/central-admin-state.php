<?php

header('Content-Type: application/json; charset=UTF-8');
header('X-Robots-Tag: noindex, nofollow', true);
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Accept, X-Requested-With');
header('Access-Control-Max-Age: 600');

if (strtoupper((string) ($_SERVER['REQUEST_METHOD'] ?? 'GET')) === 'OPTIONS') {
    http_response_code(204);
    exit();
}

function centralAdminStateRespond($data, $httpStatus = 200)
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

function centralAdminStateRespondError($message, $httpStatus = 400, $code = 'validation_error')
{
    http_response_code((int) $httpStatus);
    echo json_encode(
        array(
            'ok' => false,
            'data' => null,
            'error' => array(
                'code' => $code,
                'message' => $message,
                'details' => (object) array(),
            ),
        ),
        JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
    );
    exit();
}

function centralAdminStateResolveKey()
{
    $stateKey = trim((string) ($_GET['state_key'] ?? 'messenger-docker-client'));
    if ($stateKey === '' || !preg_match('/^[A-Za-z0-9._:-]{1,128}$/', $stateKey)) {
        centralAdminStateRespondError('Недопустимый state_key.', 422);
    }

    return $stateKey;
}

function centralAdminStateStorageDir()
{
    $storageDir = dirname(__DIR__) . '/messenger_storage/central-admin-state';
    if (!is_dir($storageDir) && !mkdir($storageDir, 0775, true) && !is_dir($storageDir)) {
        centralAdminStateRespondError('Не удалось создать каталог локального хранилища.', 500);
    }

    return $storageDir;
}

function centralAdminStateFilePath($stateKey)
{
    return centralAdminStateStorageDir() . '/' . $stateKey . '.json';
}

function centralAdminStateReadJsonBody()
{
    $raw = file_get_contents('php://input');
    if (!is_string($raw) || trim($raw) === '') {
        centralAdminStateRespondError('Ожидается JSON-тело запроса.', 422);
    }

    $payload = json_decode($raw, true);
    if (!is_array($payload)) {
        centralAdminStateRespondError('Некорректный JSON.', 422);
    }

    return $payload;
}

function centralAdminStateWriteFile($path, array $payload)
{
    $encoded = json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
    if (!is_string($encoded) || $encoded === '') {
        centralAdminStateRespondError('Не удалось сериализовать состояние.', 500);
    }

    $tempPath = $path . '.tmp';
    if (file_put_contents($tempPath, $encoded . PHP_EOL, LOCK_EX) === false) {
        centralAdminStateRespondError('Не удалось записать состояние на диск.', 500);
    }

    if (!rename($tempPath, $path)) {
        @unlink($tempPath);
        centralAdminStateRespondError('Не удалось сохранить состояние.', 500);
    }
}

try {
    $method = strtoupper((string) ($_SERVER['REQUEST_METHOD'] ?? 'GET'));
    $stateKey = centralAdminStateResolveKey();
    $filePath = centralAdminStateFilePath($stateKey);

    if ($method === 'GET') {
        if (!is_file($filePath)) {
            centralAdminStateRespond(null);
        }

        $raw = file_get_contents($filePath);
        if (!is_string($raw) || trim($raw) === '') {
            centralAdminStateRespond(null);
        }

        $payload = json_decode($raw, true);
        if (!is_array($payload)) {
            centralAdminStateRespondError('Сохраненное состояние повреждено.', 500);
        }

        centralAdminStateRespond(
            array(
                'state' => $payload,
                'updated_at' => gmdate('Y-m-d H:i:s', (int) filemtime($filePath)),
                'storage_path' => $filePath,
            )
        );
    }

    if ($method === 'POST') {
        $payload = centralAdminStateReadJsonBody();
        if (!array_key_exists('version', $payload) || !array_key_exists('servers', $payload)) {
            centralAdminStateRespondError('Состояние должно содержать version и servers.', 422);
        }

        centralAdminStateWriteFile($filePath, $payload);

        centralAdminStateRespond(
            array(
                'state_key' => $stateKey,
                'saved' => true,
                'storage_path' => $filePath,
            )
        );
    }

    if ($method === 'DELETE') {
        if (is_file($filePath) && !unlink($filePath)) {
            centralAdminStateRespondError('Не удалось удалить состояние.', 500);
        }

        centralAdminStateRespond(
            array(
                'state_key' => $stateKey,
                'deleted' => true,
                'storage_path' => $filePath,
            )
        );
    }

    centralAdminStateRespondError('Метод не поддерживается.', 405);
} catch (Throwable $throwable) {
    error_log('Central admin state API fatal: ' . $throwable->getMessage());
    centralAdminStateRespondError('Внутренняя ошибка сервера.', 500);
}
