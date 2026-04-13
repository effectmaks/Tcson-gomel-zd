<?php

header('Content-Type: application/json; charset=UTF-8');
header('X-Robots-Tag: noindex, nofollow', true);
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Accept, X-Requested-With, X-Service-Token, Authorization');
header('Access-Control-Max-Age: 600');

if (strtoupper((string) ($_SERVER['REQUEST_METHOD'] ?? 'GET')) === 'OPTIONS') {
    http_response_code(204);
    exit();
}

require_once dirname(__DIR__, 2) . '/lib/security.php';
include dirname(__DIR__, 2) . '/db_connection.php';

function serviceRespond($data, $httpStatus)
{
    http_response_code((int) $httpStatus);
    echo json_encode(
        array(
            'ok' => $httpStatus < 400,
            'data' => $httpStatus < 400 ? $data : null,
            'error' => $httpStatus < 400 ? null : $data,
        ),
        JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
    );
    exit();
}

function serviceRespondError($code, $message, $httpStatus, array $details = array())
{
    serviceRespond(
        array(
            'code' => $code,
            'message' => $message,
            'details' => (object) $details,
        ),
        $httpStatus
    );
}

function serviceRespondSqlDump($sql, $filename)
{
    header_remove('Content-Type');
    header('Content-Type: application/sql; charset=UTF-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    echo $sql;
    exit();
}

function serviceLoadConfig()
{
    $configPath = __DIR__ . '/config.local.php';
    if (!is_file($configPath)) {
        serviceRespondError('config_missing', 'Отсутствует локальная конфигурация service API.', 500);
    }

    $config = require $configPath;
    if (!is_array($config)) {
        serviceRespondError('config_invalid', 'Некорректная конфигурация service API.', 500);
    }

    return $config;
}

function serviceGetRequestToken()
{
    $headerCandidates = array(
        $_SERVER['HTTP_X_SERVICE_TOKEN'] ?? null,
        $_SERVER['REDIRECT_HTTP_X_SERVICE_TOKEN'] ?? null,
        $_SERVER['HTTP_AUTHORIZATION'] ?? null,
        $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ?? null,
    );

    if (function_exists('getallheaders')) {
        $headers = getallheaders();
        if (is_array($headers)) {
            foreach ($headers as $name => $value) {
                $lower = strtolower((string) $name);
                if ($lower === 'x-service-token' || $lower === 'authorization') {
                    $headerCandidates[] = $value;
                }
            }
        }
    }

    foreach ($headerCandidates as $candidate) {
        if (!is_string($candidate) || trim($candidate) === '') {
            continue;
        }

        $candidate = trim($candidate);
        if (preg_match('/^\s*Bearer\s+(.+)\s*$/i', $candidate, $matches)) {
            return trim((string) $matches[1]);
        }

        return $candidate;
    }

    return '';
}

function serviceReadJsonBody()
{
    $raw = file_get_contents('php://input');
    if (!is_string($raw) || trim($raw) === '') {
        serviceRespondError('validation_error', 'Ожидается JSON-тело запроса.', 422);
    }

    $payload = json_decode($raw, true);
    if (!is_array($payload)) {
        serviceRespondError('validation_error', 'Некорректный JSON.', 422);
    }

    return $payload;
}

function serviceValidateIp(array $config)
{
    $allowedIps = array_values(array_filter(
        array_map('trim', (array) ($config['allowed_ips'] ?? array())),
        static function ($value) {
            return $value !== '';
        }
    ));

    if ($allowedIps === array()) {
        serviceRespondError('config_invalid', 'Список разрешенных IP пуст.', 500);
    }

    $remoteIp = trim((string) ($_SERVER['REMOTE_ADDR'] ?? ''));
    if ($remoteIp === '' || !in_array($remoteIp, $allowedIps, true)) {
        serviceRespondError('access_denied', 'IP-адрес не разрешен.', 403, array('remote_ip' => $remoteIp));
    }
}

function serviceValidateToken(array $config)
{
    $expectedToken = trim((string) ($config['service_token'] ?? ''));
    if ($expectedToken === '') {
        serviceRespondError('config_invalid', 'Не задан service_token.', 500);
    }

    $requestToken = serviceGetRequestToken();
    if ($requestToken === '' || !hash_equals($expectedToken, $requestToken)) {
        serviceRespondError('access_denied', 'Недействительный service token.', 403);
    }
}

function serviceGetBoolean($value, $default = false)
{
    if ($value === null) {
        return (bool) $default;
    }

    if (is_bool($value)) {
        return $value;
    }

    if (is_int($value) || is_float($value)) {
        return (bool) $value;
    }

    $value = strtolower(trim((string) $value));
    if ($value === '') {
        return (bool) $default;
    }

    return in_array($value, array('1', 'true', 'yes', 'on'), true);
}

function serviceGetIntConfig(array $config, $key, $default)
{
    $value = isset($config[$key]) ? (int) $config[$key] : (int) $default;
    return $value > 0 ? $value : (int) $default;
}

function serviceNormalizeAction(array $payload)
{
    $action = strtolower(trim((string) ($payload['action'] ?? 'sql')));
    if (!in_array($action, array('sql', 'dump', 'import'), true)) {
        serviceRespondError('validation_error', 'Поддерживаются только действия sql, dump и import.', 422);
    }

    return $action;
}

function serviceValidateSql($sql, array $config)
{
    $sql = trim((string) $sql);
    if ($sql === '') {
        serviceRespondError('validation_error', 'SQL-запрос не передан.', 422);
    }

    $maxLength = serviceGetIntConfig($config, 'max_sql_length', 50000);
    if (strlen($sql) > $maxLength) {
        serviceRespondError('validation_error', 'SQL-запрос слишком длинный.', 422);
    }

    $trimmed = rtrim($sql);
    if (substr($trimmed, -1) === ';') {
        $trimmed = rtrim(substr($trimmed, 0, -1));
    }

    if ($trimmed === '') {
        serviceRespondError('validation_error', 'SQL-запрос пуст.', 422);
    }

    if (strpos($trimmed, "\0") !== false) {
        serviceRespondError('validation_error', 'SQL содержит недопустимые символы.', 422);
    }

    if (preg_match('/;\s*\S/', $trimmed)) {
        serviceRespondError('validation_error', 'Разрешен только один SQL-запрос за вызов.', 422);
    }

    return $trimmed;
}

function serviceValidateDumpSql($sql, array $config)
{
    $sql = trim((string) $sql);
    if ($sql === '') {
        serviceRespondError('validation_error', 'dump_sql не передан.', 422);
    }

    $maxLength = serviceGetIntConfig($config, 'max_import_length', 20000000);
    if (strlen($sql) > $maxLength) {
        serviceRespondError('validation_error', 'SQL-дамп слишком большой.', 422);
    }

    if (strpos($sql, "\0") !== false) {
        serviceRespondError('validation_error', 'SQL-дамп содержит недопустимые символы.', 422);
    }

    return $sql;
}

function serviceQuoteIdentifier($identifier)
{
    $identifier = (string) $identifier;
    return '`' . str_replace('`', '``', $identifier) . '`';
}

function serviceGetDatabaseName($conn)
{
    $result = $conn->query('SELECT DATABASE() AS db_name');
    if (!($result instanceof mysqli_result)) {
        serviceRespondError('sql_execute_error', 'Не удалось определить текущую базу данных.', 500, array('mysql_error' => $conn->error));
    }

    $row = $result->fetch_assoc();
    $result->free();
    $databaseName = trim((string) ($row['db_name'] ?? ''));
    if ($databaseName === '') {
        serviceRespondError('sql_execute_error', 'Текущая база данных не определена.', 500);
    }

    return $databaseName;
}

function serviceNormalizeTableList($value)
{
    if ($value === null) {
        return array();
    }

    if (!is_array($value)) {
        serviceRespondError('validation_error', 'tables должен быть массивом.', 422);
    }

    $tables = array();
    foreach ($value as $tableName) {
        $tableName = trim((string) $tableName);
        if ($tableName === '') {
            continue;
        }

        if (!preg_match('/^[A-Za-z0-9_]+$/', $tableName)) {
            serviceRespondError('validation_error', 'Недопустимое имя таблицы в tables.', 422, array('table' => $tableName));
        }

        $tables[] = $tableName;
    }

    return array_values(array_unique($tables));
}

function serviceGetDumpTables($conn, array $requestedTables)
{
    $result = $conn->query('SHOW FULL TABLES WHERE Table_type = "BASE TABLE"');
    if (!($result instanceof mysqli_result)) {
        serviceRespondError('sql_execute_error', 'Не удалось получить список таблиц.', 500, array('mysql_error' => $conn->error));
    }

    $availableTables = array();
    while ($row = $result->fetch_row()) {
        if (isset($row[0]) && is_string($row[0]) && $row[0] !== '') {
            $availableTables[] = $row[0];
        }
    }
    $result->free();

    if ($requestedTables === array()) {
        return $availableTables;
    }

    $availableMap = array_fill_keys($availableTables, true);
    foreach ($requestedTables as $tableName) {
        if (!isset($availableMap[$tableName])) {
            serviceRespondError('validation_error', 'Запрошенная таблица не найдена.', 422, array('table' => $tableName));
        }
    }

    return $requestedTables;
}

function serviceDumpValue($conn, $value)
{
    if ($value === null) {
        return 'NULL';
    }

    if (is_int($value) || is_float($value)) {
        return (string) $value;
    }

    if (is_string($value) && preg_match('/^-?\d+(?:\.\d+)?$/', $value)) {
        return $value;
    }

    return "'" . $conn->real_escape_string((string) $value) . "'";
}

function serviceBuildInsertStatements($conn, $tableName, mysqli_result $result, array $config)
{
    $fields = $result->fetch_fields();
    $columnNames = array();
    foreach ($fields as $field) {
        $columnNames[] = serviceQuoteIdentifier($field->name);
    }

    $rowsPerInsert = serviceGetIntConfig($config, 'dump_rows_per_insert', 100);
    $insertStatements = array();
    $chunk = array();

    while ($row = $result->fetch_assoc()) {
        $values = array();
        foreach ($fields as $field) {
            $values[] = serviceDumpValue($conn, $row[$field->name] ?? null);
        }

        $chunk[] = '(' . implode(', ', $values) . ')';

        if (count($chunk) >= $rowsPerInsert) {
            $insertStatements[] = 'INSERT INTO ' . serviceQuoteIdentifier($tableName)
                . ' (' . implode(', ', $columnNames) . ') VALUES' . "\n"
                . implode(",\n", $chunk) . ';';
            $chunk = array();
        }
    }

    if ($chunk !== array()) {
        $insertStatements[] = 'INSERT INTO ' . serviceQuoteIdentifier($tableName)
            . ' (' . implode(', ', $columnNames) . ') VALUES' . "\n"
            . implode(",\n", $chunk) . ';';
    }

    return $insertStatements;
}

function serviceBuildTableDump($conn, $tableName, array $config)
{
    $createResult = $conn->query('SHOW CREATE TABLE ' . serviceQuoteIdentifier($tableName));
    if (!($createResult instanceof mysqli_result)) {
        serviceRespondError('sql_execute_error', 'Не удалось получить структуру таблицы.', 500, array('table' => $tableName, 'mysql_error' => $conn->error));
    }

    $createRow = $createResult->fetch_assoc();
    $createResult->free();
    $createSql = '';
    if (is_array($createRow)) {
        foreach ($createRow as $key => $value) {
            if (stripos((string) $key, 'Create Table') !== false) {
                $createSql = (string) $value;
                break;
            }
        }
    }

    if ($createSql === '') {
        serviceRespondError('sql_execute_error', 'Не удалось прочитать DDL таблицы.', 500, array('table' => $tableName));
    }

    $rowsResult = $conn->query('SELECT * FROM ' . serviceQuoteIdentifier($tableName));
    if (!($rowsResult instanceof mysqli_result)) {
        serviceRespondError('sql_execute_error', 'Не удалось выгрузить данные таблицы.', 500, array('table' => $tableName, 'mysql_error' => $conn->error));
    }

    $rowCount = $rowsResult->num_rows;
    $parts = array(
        '--',
        '-- Table structure for table ' . serviceQuoteIdentifier($tableName),
        '--',
        'DROP TABLE IF EXISTS ' . serviceQuoteIdentifier($tableName) . ';',
        $createSql . ';',
        '',
    );

    if ($rowCount > 0) {
        $parts[] = '--';
        $parts[] = '-- Dumping data for table ' . serviceQuoteIdentifier($tableName);
        $parts[] = '--';

        $insertStatements = serviceBuildInsertStatements($conn, $tableName, $rowsResult, $config);
        foreach ($insertStatements as $statement) {
            $parts[] = $statement;
        }

        $parts[] = '';
    }

    $rowsResult->free();

    return array(
        'sql' => implode("\n", $parts),
        'row_count' => $rowCount,
    );
}

function serviceBuildDump($conn, array $config, array $payload)
{
    $databaseName = serviceGetDatabaseName($conn);
    $tables = serviceGetDumpTables($conn, serviceNormalizeTableList($payload['tables'] ?? null));

    $parts = array(
        '-- SQL dump generated by api/service',
        '-- Host: ' . trim((string) ($_SERVER['HTTP_HOST'] ?? '')),
        '-- Database: ' . serviceQuoteIdentifier($databaseName),
        '-- Generated at: ' . gmdate('Y-m-d H:i:s') . ' UTC',
        '',
        'SET NAMES utf8mb4;',
        'SET FOREIGN_KEY_CHECKS=0;',
        '',
    );

    $tableMeta = array();
    foreach ($tables as $tableName) {
        $tableDump = serviceBuildTableDump($conn, $tableName, $config);
        $parts[] = $tableDump['sql'];
        $tableMeta[] = array(
            'table' => $tableName,
            'rows' => $tableDump['row_count'],
        );
    }

    $parts[] = 'SET FOREIGN_KEY_CHECKS=1;';
    $parts[] = '';

    $dumpSql = implode("\n", $parts);
    $filename = $databaseName . '-' . gmdate('Ymd-His') . '.sql';

    if (serviceGetBoolean($payload['download'] ?? false)) {
        serviceRespondSqlDump($dumpSql, $filename);
    }

    return array(
        'meta' => array(
            'action' => 'dump',
            'database' => $databaseName,
            'table_count' => count($tables),
            'tables' => $tableMeta,
            'bytes' => strlen($dumpSql),
            'filename' => $filename,
        ),
        'dump_sql' => $dumpSql,
    );
}

function serviceSplitSqlStatements($sql)
{
    $statements = array();
    $buffer = '';
    $length = strlen($sql);
    $state = 'normal';

    for ($index = 0; $index < $length; $index++) {
        $char = $sql[$index];
        $next = $index + 1 < $length ? $sql[$index + 1] : '';
        $afterNext = $index + 2 < $length ? $sql[$index + 2] : '';

        if ($state === 'normal') {
            if ($char === "'" || $char === '"' || $char === '`') {
                $buffer .= $char;
                $state = $char;
                continue;
            }

            if ($char === '#' ) {
                $state = 'line_comment';
                continue;
            }

            if ($char === '-' && $next === '-' && ($afterNext === '' || ctype_space($afterNext))) {
                $index++;
                $state = 'line_comment';
                continue;
            }

            if ($char === '/' && $next === '*') {
                $index++;
                $state = 'block_comment';
                continue;
            }

            if ($char === ';') {
                $statement = trim($buffer);
                if ($statement !== '') {
                    $statements[] = $statement;
                }
                $buffer = '';
                continue;
            }

            $buffer .= $char;
            continue;
        }

        if ($state === 'line_comment') {
            if ($char === "\n") {
                $state = 'normal';
                $buffer .= "\n";
            }
            continue;
        }

        if ($state === 'block_comment') {
            if ($char === '*' && $next === '/') {
                $index++;
                $state = 'normal';
            }
            continue;
        }

        $buffer .= $char;
        if ($char === '\\' && $index + 1 < $length) {
            $index++;
            $buffer .= $sql[$index];
            continue;
        }

        if ($char === $state) {
            $state = 'normal';
        }
    }

    $statement = trim($buffer);
    if ($statement !== '') {
        $statements[] = $statement;
    }

    return $statements;
}

function serviceRunSqlStatement($conn, $sql)
{
    $result = $conn->query($sql);
    if ($result === false) {
        serviceRespondError('sql_execute_error', 'Ошибка выполнения SQL-запроса.', 400, array('mysql_error' => $conn->error, 'mysql_errno' => $conn->errno));
    }

    if ($result instanceof mysqli_result) {
        $rows = $result->fetch_all(MYSQLI_ASSOC);
        $meta = array(
            'query_type' => 'result_set',
            'row_count' => count($rows),
            'field_count' => $result->field_count,
        );
        $result->free();
        return array('meta' => $meta, 'rows' => $rows);
    }

    return array(
        'meta' => array(
            'query_type' => 'statement',
            'affected_rows' => $conn->affected_rows,
            'insert_id' => $conn->insert_id,
            'warning_count' => $conn->warning_count,
        ),
    );
}

function serviceExecutePrepared($conn, $sql, array $params, $types)
{
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        serviceRespondError('sql_prepare_error', 'Не удалось подготовить SQL-запрос.', 400, array('mysql_error' => $conn->error));
    }

    $types = (string) $types;
    if ($types === '' || strlen($types) !== count($params)) {
        $stmt->close();
        serviceRespondError('validation_error', 'Количество типов должно совпадать с количеством параметров.', 422);
    }

    bindDynamicParams($stmt, $types, $params);

    if (!$stmt->execute()) {
        $error = $stmt->error;
        $errno = $stmt->errno;
        $stmt->close();
        serviceRespondError('sql_execute_error', 'Ошибка выполнения SQL-запроса.', 400, array('mysql_error' => $error, 'mysql_errno' => $errno));
    }

    $result = $stmt->get_result();
    if ($result instanceof mysqli_result) {
        $rows = $result->fetch_all(MYSQLI_ASSOC);
        $meta = array(
            'query_type' => 'result_set',
            'row_count' => count($rows),
            'field_count' => $result->field_count,
        );
        $result->free();
        $stmt->close();
        return array('meta' => $meta, 'rows' => $rows);
    }

    $response = array(
        'meta' => array(
            'query_type' => 'statement',
            'affected_rows' => $stmt->affected_rows,
            'insert_id' => $stmt->insert_id,
            'warning_count' => $stmt->warning_count,
        ),
    );
    $stmt->close();

    return $response;
}

function serviceHandleSqlAction($conn, array $config, array $payload)
{
    $sql = serviceValidateSql($payload['sql'] ?? '', $config);
    $params = $payload['params'] ?? array();
    $types = $payload['types'] ?? '';

    if (!is_array($params)) {
        serviceRespondError('validation_error', 'params должен быть массивом.', 422);
    }

    return $params !== array()
        ? serviceExecutePrepared($conn, $sql, array_values($params), $types)
        : serviceRunSqlStatement($conn, $sql);
}

function serviceHandleImportAction($conn, array $config, array $payload)
{
    $dumpSql = serviceValidateDumpSql($payload['dump_sql'] ?? '', $config);
    $statements = serviceSplitSqlStatements($dumpSql);
    if ($statements === array()) {
        serviceRespondError('validation_error', 'В SQL-дампе не найдено ни одного запроса.', 422);
    }

    $executed = array();
    $statementCount = 0;

    if ($conn->query('SET FOREIGN_KEY_CHECKS=0') === false) {
        serviceRespondError('sql_execute_error', 'Не удалось отключить FOREIGN_KEY_CHECKS.', 500, array('mysql_error' => $conn->error));
    }

    try {
        foreach ($statements as $index => $statement) {
            if ($conn->query($statement) === false) {
                serviceRespondError(
                    'sql_execute_error',
                    'Ошибка импорта SQL-дампа.',
                    400,
                    array(
                        'statement_index' => $index + 1,
                        'mysql_error' => $conn->error,
                        'mysql_errno' => $conn->errno,
                        'statement_preview' => mb_substr($statement, 0, 500),
                    )
                );
            }

            $statementCount++;
            if (count($executed) < 10) {
                $executed[] = mb_substr($statement, 0, 160);
            }
        }
    } finally {
        $conn->query('SET FOREIGN_KEY_CHECKS=1');
    }

    return array(
        'meta' => array(
            'action' => 'import',
            'statement_count' => $statementCount,
            'bytes' => strlen($dumpSql),
        ),
        'executed_preview' => $executed,
    );
}

try {
    if (strtoupper((string) ($_SERVER['REQUEST_METHOD'] ?? 'GET')) !== 'POST') {
        serviceRespondError('method_not_allowed', 'Поддерживается только POST.', 405);
    }

    $config = serviceLoadConfig();
    serviceValidateIp($config);
    serviceValidateToken($config);

    $payload = serviceReadJsonBody();
    $action = serviceNormalizeAction($payload);

    if ($action === 'dump') {
        $response = serviceBuildDump($conn, $config, $payload);
    } elseif ($action === 'import') {
        $response = serviceHandleImportAction($conn, $config, $payload);
    } else {
        $response = serviceHandleSqlAction($conn, $config, $payload);
    }

    serviceRespond(
        array(
            'meta' => $response['meta'],
            'rows' => $response['rows'] ?? array(),
            'dump_sql' => $response['dump_sql'] ?? null,
            'executed_preview' => $response['executed_preview'] ?? array(),
            'remote_ip' => trim((string) ($_SERVER['REMOTE_ADDR'] ?? '')),
        ),
        200
    );
} catch (Throwable $throwable) {
    error_log('Service API fatal: ' . $throwable->getMessage());
    serviceRespondError('internal_error', 'Внутренняя ошибка сервера.', 500);
}
