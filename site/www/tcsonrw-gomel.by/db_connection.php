<?php

$connectionConfig = array(
    'host' => getenv('DB_HOST') ?: null,
    'user' => getenv('DB_USER') ?: null,
    'password' => getenv('DB_PASSWORD') !== false ? getenv('DB_PASSWORD') : null,
    'name' => getenv('DB_NAME') ?: null,
    'port' => getenv('DB_PORT') ?: null,
);

$localConfigPath = __DIR__ . '/db_connection.local.php';
if (file_exists($localConfigPath)) {
    $localConfig = require $localConfigPath;
    if (is_array($localConfig)) {
        $connectionConfig = array_merge($connectionConfig, $localConfig);
    }
}

$requiredKeys = array('host', 'user', 'name');
foreach ($requiredKeys as $key) {
    if (!array_key_exists($key, $connectionConfig) || $connectionConfig[$key] === null || $connectionConfig[$key] === '') {
        error_log('Database configuration is incomplete. Missing key: ' . $key);
        http_response_code(500);
        exit('Ошибка конфигурации подключения к базе данных.');
    }
}

if (!array_key_exists('password', $connectionConfig) || $connectionConfig['password'] === null) {
    error_log('Database configuration is incomplete. Missing key: password');
    http_response_code(500);
    exit('Ошибка конфигурации подключения к базе данных.');
}

if (!isset($conn) || !($conn instanceof mysqli)) {
    if ($connectionConfig['port'] !== null && $connectionConfig['port'] !== '') {
        $conn = new mysqli(
            $connectionConfig['host'],
            $connectionConfig['user'],
            $connectionConfig['password'],
            $connectionConfig['name'],
            (int) $connectionConfig['port']
        );
    } else {
        $conn = new mysqli(
            $connectionConfig['host'],
            $connectionConfig['user'],
            $connectionConfig['password'],
            $connectionConfig['name']
        );
    }
}

if ($conn->connect_error) {
    error_log('Database connection error: ' . $conn->connect_error);
    http_response_code(500);
    exit('Ошибка подключения к базе данных.');
}

$conn->set_charset('utf8mb4');
