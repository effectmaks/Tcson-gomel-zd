<?php

class MessengerApiException extends RuntimeException
{
    protected $errorCodeName;
    protected $httpStatusCode;
    protected $detailsPayload;

    public function __construct($errorCodeName, $message, $httpStatusCode = 400, array $detailsPayload = array())
    {
        parent::__construct((string) $message);
        $this->errorCodeName = (string) $errorCodeName;
        $this->httpStatusCode = (int) $httpStatusCode;
        $this->detailsPayload = $detailsPayload;
    }

    public function getErrorCodeName()
    {
        return $this->errorCodeName;
    }

    public function getHttpStatusCode()
    {
        return $this->httpStatusCode;
    }

    public function getDetailsPayload()
    {
        return $this->detailsPayload;
    }
}

function messengerUtcNow()
{
    return gmdate('Y-m-d H:i:s');
}

function messengerToIso8601($value)
{
    if ($value === null || $value === '') {
        return null;
    }

    $timestamp = strtotime((string) $value);
    if ($timestamp === false) {
        return null;
    }

    return gmdate('c', $timestamp);
}

function messengerGenerateUuid()
{
    $bytes = random_bytes(16);
    $bytes[6] = chr((ord($bytes[6]) & 0x0f) | 0x40);
    $bytes[8] = chr((ord($bytes[8]) & 0x3f) | 0x80);

    $hex = bin2hex($bytes);
    return sprintf(
        '%s-%s-%s-%s-%s',
        substr($hex, 0, 8),
        substr($hex, 8, 4),
        substr($hex, 12, 4),
        substr($hex, 16, 4),
        substr($hex, 20, 12)
    );
}

function messengerEnsureReady($conn)
{
    messengerEnsureUsersMessengerColumn($conn);
    messengerEnsureSchema($conn);
    messengerEnsureSettingsRow($conn);
    messengerEnsureStorageRoot();
}

function messengerEnsureUsersMessengerColumn($conn)
{
    if (!($conn instanceof mysqli)) {
        return;
    }

    $result = $conn->query("SHOW COLUMNS FROM users LIKE 'messenger'");
    if ($result && $result->num_rows > 0) {
        return;
    }

    $conn->query("ALTER TABLE users ADD COLUMN messenger TINYINT(1) NOT NULL DEFAULT 0");
}

function messengerEnsureSchema($conn)
{
    $queries = array(
        "CREATE TABLE IF NOT EXISTS messenger_settings (
            id INT AUTO_INCREMENT PRIMARY KEY,
            site_uuid CHAR(36) NOT NULL,
            site_name VARCHAR(255) NOT NULL,
            site_code VARCHAR(6) NOT NULL,
            created_at DATETIME NOT NULL,
            updated_at DATETIME NOT NULL,
            UNIQUE KEY uniq_site_uuid (site_uuid)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
        "CREATE TABLE IF NOT EXISTS messenger_chats (
            id INT AUTO_INCREMENT PRIMARY KEY,
            chat_uuid CHAR(36) NOT NULL,
            chat_no INT NOT NULL,
            first_message_id INT NULL,
            status ENUM('new','in_progress','done','closed') NOT NULL DEFAULT 'new',
            created_by_side ENUM('site','central') NOT NULL,
            created_by_user_id INT NULL,
            created_by_user_name VARCHAR(255) NOT NULL,
            is_deleted TINYINT(1) NOT NULL DEFAULT 0,
            deleted_at DATETIME NULL,
            deleted_by_side ENUM('site','central') NULL,
            deleted_by_user_id INT NULL,
            deleted_by_user_name VARCHAR(255) NULL,
            created_at DATETIME NOT NULL,
            updated_at DATETIME NOT NULL,
            last_activity_at DATETIME NOT NULL,
            closed_at DATETIME NULL,
            UNIQUE KEY uniq_chat_uuid (chat_uuid),
            UNIQUE KEY uniq_chat_no (chat_no),
            KEY idx_status_last_activity (status, last_activity_at),
            KEY idx_deleted_last_activity (is_deleted, last_activity_at),
            KEY idx_last_activity (last_activity_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
        "CREATE TABLE IF NOT EXISTS messenger_chat_participants (
            id INT AUTO_INCREMENT PRIMARY KEY,
            chat_id INT NOT NULL,
            user_id INT NOT NULL,
            user_name VARCHAR(255) NOT NULL,
            is_active TINYINT(1) NOT NULL DEFAULT 1,
            joined_at DATETIME NOT NULL,
            left_at DATETIME NULL,
            added_by_side ENUM('site','central') NOT NULL,
            added_by_user_id INT NULL,
            added_by_user_name VARCHAR(255) NOT NULL,
            updated_at DATETIME NOT NULL,
            UNIQUE KEY uniq_chat_user (chat_id, user_id),
            KEY idx_chat_active (chat_id, is_active),
            KEY idx_user_active_updated (user_id, is_active, updated_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
        "CREATE TABLE IF NOT EXISTS messenger_messages (
            id INT AUTO_INCREMENT PRIMARY KEY,
            message_uuid CHAR(36) NOT NULL,
            chat_id INT NOT NULL,
            author_side ENUM('site','central') NOT NULL,
            author_user_id INT NULL,
            author_user_name VARCHAR(255) NOT NULL,
            body_text MEDIUMTEXT NOT NULL,
            is_deleted TINYINT(1) NOT NULL DEFAULT 0,
            created_at DATETIME NOT NULL,
            updated_at DATETIME NOT NULL,
            deleted_at DATETIME NULL,
            UNIQUE KEY uniq_message_uuid (message_uuid),
            KEY idx_chat_created_at (chat_id, created_at),
            KEY idx_chat_updated_at (chat_id, updated_at),
            FULLTEXT KEY ft_body_text (body_text)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
        "CREATE TABLE IF NOT EXISTS messenger_message_edits (
            id INT AUTO_INCREMENT PRIMARY KEY,
            message_id INT NOT NULL,
            version_no INT NOT NULL,
            previous_body_text MEDIUMTEXT NOT NULL,
            edited_by_side ENUM('site','central') NOT NULL,
            edited_by_user_id INT NULL,
            edited_by_user_name VARCHAR(255) NOT NULL,
            edited_at DATETIME NOT NULL,
            KEY idx_message_version (message_id, version_no)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
        "CREATE TABLE IF NOT EXISTS messenger_attachments (
            id INT AUTO_INCREMENT PRIMARY KEY,
            attachment_uuid CHAR(36) NOT NULL,
            chat_id INT NOT NULL,
            message_id INT NOT NULL,
            stored_name VARCHAR(255) NOT NULL,
            storage_path VARCHAR(500) NOT NULL,
            original_name VARCHAR(255) NOT NULL,
            mime_type VARCHAR(255) NOT NULL,
            extension VARCHAR(20) NOT NULL,
            size_bytes BIGINT NOT NULL,
            sha256 CHAR(64) NULL,
            uploaded_by_side ENUM('site','central') NOT NULL,
            uploaded_by_user_id INT NULL,
            uploaded_by_user_name VARCHAR(255) NOT NULL,
            is_deleted TINYINT(1) NOT NULL DEFAULT 0,
            created_at DATETIME NOT NULL,
            deleted_at DATETIME NULL,
            UNIQUE KEY uniq_attachment_uuid (attachment_uuid),
            KEY idx_message_created_at (message_id, created_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
        "CREATE TABLE IF NOT EXISTS messenger_reads (
            id INT AUTO_INCREMENT PRIMARY KEY,
            chat_id INT NOT NULL,
            reader_key VARCHAR(64) NOT NULL,
            side ENUM('site','central') NOT NULL,
            user_id INT NULL,
            user_name VARCHAR(255) NULL,
            last_read_event_id INT NULL,
            last_read_at DATETIME NOT NULL,
            manual_unread TINYINT(1) NOT NULL DEFAULT 0,
            UNIQUE KEY uniq_chat_reader (chat_id, reader_key),
            KEY idx_chat_side_read_event (chat_id, side, last_read_event_id),
            KEY idx_user_last_read_at (user_id, last_read_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
        "CREATE TABLE IF NOT EXISTS messenger_api_tokens (
            id INT AUTO_INCREMENT PRIMARY KEY,
            token_prefix VARCHAR(32) NOT NULL,
            token_hash CHAR(64) NOT NULL,
            label VARCHAR(255) NOT NULL,
            is_active TINYINT(1) NOT NULL DEFAULT 1,
            created_at DATETIME NOT NULL,
            expires_at DATETIME NULL,
            revoked_at DATETIME NULL,
            last_used_at DATETIME NULL,
            UNIQUE KEY uniq_token_hash (token_hash)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
        "CREATE TABLE IF NOT EXISTS messenger_events (
            id INT AUTO_INCREMENT PRIMARY KEY,
            event_uuid CHAR(36) NOT NULL,
            entity_type VARCHAR(50) NOT NULL,
            entity_uuid CHAR(36) NOT NULL,
            action VARCHAR(64) NOT NULL,
            payload_json JSON NOT NULL,
            created_at DATETIME NOT NULL,
            UNIQUE KEY uniq_event_uuid (event_uuid),
            KEY idx_created_at (created_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
        "CREATE TABLE IF NOT EXISTS messenger_audit_log (
            id INT AUTO_INCREMENT PRIMARY KEY,
            actor_side ENUM('site','central') NOT NULL,
            actor_user_id INT NULL,
            actor_user_name VARCHAR(255) NULL,
            auth_mode ENUM('session','bearer') NOT NULL,
            entity_type VARCHAR(50) NOT NULL,
            entity_uuid CHAR(36) NOT NULL,
            action VARCHAR(64) NOT NULL,
            request_id CHAR(36) NULL,
            result_code VARCHAR(64) NOT NULL,
            ip_address VARCHAR(64) NULL,
            user_agent VARCHAR(500) NULL,
            created_at DATETIME NOT NULL,
            KEY idx_created_at (created_at),
            KEY idx_entity_created_at (entity_type, entity_uuid, created_at),
            KEY idx_actor_created_at (actor_side, actor_user_id, created_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
        "CREATE TABLE IF NOT EXISTS messenger_external_requests (
            id INT AUTO_INCREMENT PRIMARY KEY,
            request_id CHAR(36) NOT NULL,
            endpoint_name VARCHAR(100) NOT NULL,
            response_json JSON NOT NULL,
            created_at DATETIME NOT NULL,
            UNIQUE KEY uniq_request_endpoint (request_id, endpoint_name)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
    );

    foreach ($queries as $query) {
        if (!$conn->query($query)) {
            throw new MessengerApiException('validation_error', 'Не удалось подготовить схему мессенджера.', 500);
        }
    }

    $manualUnreadColumn = $conn->query("SHOW COLUMNS FROM messenger_reads LIKE 'manual_unread'");
    if (!$manualUnreadColumn) {
        throw new MessengerApiException('validation_error', 'Не удалось проверить схему состояния прочтения.', 500);
    }

    if ($manualUnreadColumn->num_rows === 0) {
        if (!$conn->query("ALTER TABLE messenger_reads ADD COLUMN manual_unread TINYINT(1) NOT NULL DEFAULT 0 AFTER last_read_at")) {
            throw new MessengerApiException('validation_error', 'Не удалось обновить схему состояния прочтения.', 500);
        }
    }
}

function messengerEnsureSettingsRow($conn)
{
    $result = $conn->query('SELECT id FROM messenger_settings LIMIT 1');
    if ($result && $result->num_rows > 0) {
        return;
    }

    $siteName = trim((string) getenv('MESSENGER_SITE_NAME'));
    if ($siteName === '') {
        $siteName = trim((string) ($_SERVER['HTTP_HOST'] ?? 'TCSON Site'));
    }

    $siteCode = messengerNormalizeSiteCode(getenv('MESSENGER_SITE_CODE') ?: $siteName);
    $siteUuid = getenv('MESSENGER_SITE_UUID') ?: messengerGenerateUuid();
    $now = messengerUtcNow();

    $stmt = $conn->prepare('INSERT INTO messenger_settings (site_uuid, site_name, site_code, created_at, updated_at) VALUES (?, ?, ?, ?, ?)');
    if (!$stmt) {
        throw new MessengerApiException('validation_error', 'Не удалось создать настройки сайта для мессенджера.', 500);
    }

    $stmt->bind_param('sssss', $siteUuid, $siteName, $siteCode, $now, $now);
    if (!$stmt->execute()) {
        $stmt->close();
        throw new MessengerApiException('validation_error', 'Не удалось сохранить настройки сайта для мессенджера.', 500);
    }
    $stmt->close();
}

function messengerNormalizeSiteCode($value)
{
    $value = strtoupper(preg_replace('/[^A-Z0-9]/', '', (string) $value));
    if ($value === '') {
        $value = 'SITE';
    }

    return substr($value, 0, 6);
}

function messengerGetSiteSettings($conn)
{
    messengerEnsureSettingsRow($conn);
    $result = $conn->query('SELECT * FROM messenger_settings ORDER BY id ASC LIMIT 1');
    $row = $result ? $result->fetch_assoc() : null;
    if (!$row) {
        throw new MessengerApiException('validation_error', 'Не удалось загрузить настройки сайта.', 500);
    }

    return $row;
}

function messengerEnsureStorageRoot()
{
    $storageRoot = messengerGetStorageRoot();
    if (!is_dir($storageRoot) && !mkdir($storageRoot, 0775, true) && !is_dir($storageRoot)) {
        throw new MessengerApiException('validation_error', 'Не удалось подготовить хранилище вложений мессенджера.', 500);
    }

    return $storageRoot;
}

function messengerGetStorageRoot()
{
    $envPath = trim((string) getenv('MESSENGER_STORAGE_PATH'));
    if ($envPath !== '') {
        return $envPath;
    }

    return dirname(__DIR__) . '/messenger_storage';
}

function messengerGetUserDisplayName(array $user)
{
    foreach (array('full_name', 'fio', 'name', 'login') as $key) {
        if (isset($user[$key]) && trim((string) $user[$key]) !== '') {
            return trim((string) $user[$key]);
        }
    }

    return 'Пользователь';
}

function messengerGetUserDisplayNamesByIds($conn, array $userIds)
{
    $normalizedIds = array_values(array_unique(array_filter(array_map('intval', $userIds), function ($userId) {
        return $userId > 0;
    })));

    if (empty($normalizedIds)) {
        return array();
    }

    $placeholders = implode(', ', array_fill(0, count($normalizedIds), '?'));
    $stmt = $conn->prepare('SELECT * FROM users WHERE id IN (' . $placeholders . ')');
    if (!$stmt) {
        return array();
    }

    bindDynamicParams($stmt, str_repeat('i', count($normalizedIds)), $normalizedIds);
    $stmt->execute();
    $result = $stmt->get_result();
    $rows = $result ? $result->fetch_all(MYSQLI_ASSOC) : array();
    $stmt->close();

    $namesById = array();
    foreach ($rows as $row) {
        $userId = (int) ($row['id'] ?? 0);
        if ($userId > 0) {
            $namesById[$userId] = messengerGetUserDisplayName($row);
        }
    }

    return $namesById;
}

function messengerResolveStoredSiteUserName(array $namesById, $side, $userId, $fallbackName)
{
    if ((string) $side !== 'site') {
        return (string) $fallbackName;
    }

    $normalizedUserId = $userId !== null ? (int) $userId : 0;
    if ($normalizedUserId > 0 && isset($namesById[$normalizedUserId]) && trim((string) $namesById[$normalizedUserId]) !== '') {
        return (string) $namesById[$normalizedUserId];
    }

    return (string) $fallbackName;
}

function messengerFetchReadRowsByChatId($conn, $chatId)
{
    $stmt = $conn->prepare('SELECT * FROM messenger_reads WHERE chat_id = ?');
    if (!$stmt) {
        return array();
    }

    $stmt->bind_param('i', $chatId);
    $stmt->execute();
    $result = $stmt->get_result();
    $rows = $result ? $result->fetch_all(MYSQLI_ASSOC) : array();
    $stmt->close();

    $items = array();
    foreach ($rows as $row) {
        $readerKey = trim((string) ($row['reader_key'] ?? ''));
        if ($readerKey !== '') {
            $items[$readerKey] = $row;
        }
    }

    return $items;
}

function messengerBuildChatReaders(array $participants, array $readRowsByReaderKey, array $userDisplayNamesById)
{
    $items = array();
    $participantUserIds = array();

    foreach ($participants as $participant) {
        $participantUserId = (int) ($participant['user_id'] ?? 0);
        if ($participantUserId > 0) {
            $participantUserIds[$participantUserId] = true;
        }

        $participantLastReadEventId = isset($participant['last_read_event_id']) ? (int) $participant['last_read_event_id'] : 0;
        if ($participantLastReadEventId <= 0) {
            continue;
        }

        $items[] = array(
            'reader_key' => 'site:' . $participantUserId,
            'side' => 'site',
            'user_id' => $participantUserId,
            'user_name' => messengerResolveStoredSiteUserName(
                $userDisplayNamesById,
                'site',
                $participant['user_id'] ?? null,
                (string) ($participant['user_name'] ?? '')
            ),
            'last_read_event_id' => $participantLastReadEventId,
            'last_read_at' => $participant['last_read_at'] ?? null,
        );
    }

    if (isset($readRowsByReaderKey['central'])) {
        $centralRead = $readRowsByReaderKey['central'];
        $centralLastReadEventId = isset($centralRead['last_read_event_id']) ? (int) $centralRead['last_read_event_id'] : 0;
        if ($centralLastReadEventId > 0) {
            $items[] = array(
                'reader_key' => 'central',
                'side' => 'central',
                'user_id' => null,
                'user_name' => trim((string) ($centralRead['user_name'] ?? 'central')) !== '' ? (string) $centralRead['user_name'] : 'central',
                'last_read_event_id' => $centralLastReadEventId,
                'last_read_at' => isset($centralRead['last_read_at']) ? messengerToIso8601($centralRead['last_read_at']) : null,
            );
        }
    }

    return $items;
}

function messengerFetchLatestMessageEventIds($conn, $chatUuid, array $messageUuids)
{
    $normalizedUuids = array_values(array_unique(array_filter(array_map(function ($messageUuid) {
        return trim((string) $messageUuid);
    }, $messageUuids), function ($messageUuid) {
        return $messageUuid !== '';
    })));

    if (empty($normalizedUuids)) {
        return array();
    }

    $placeholders = implode(', ', array_fill(0, count($normalizedUuids), '?'));
    $sql = "
        SELECT JSON_UNQUOTE(JSON_EXTRACT(payload_json, '$.message_uuid')) AS message_uuid, MAX(id) AS latest_event_id
        FROM messenger_events
        WHERE JSON_UNQUOTE(JSON_EXTRACT(payload_json, '$.chat_uuid')) = ?
          AND JSON_UNQUOTE(JSON_EXTRACT(payload_json, '$.message_uuid')) IN ({$placeholders})
          AND action IN ('message_created', 'message_edited', 'message_deleted', 'attachment_added', 'attachment_deleted')
        GROUP BY JSON_UNQUOTE(JSON_EXTRACT(payload_json, '$.message_uuid'))
    ";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        return array();
    }

    $types = 's' . str_repeat('s', count($normalizedUuids));
    $values = array_merge(array($chatUuid), $normalizedUuids);
    bindDynamicParams($stmt, $types, $values);
    $stmt->execute();
    $result = $stmt->get_result();
    $rows = $result ? $result->fetch_all(MYSQLI_ASSOC) : array();
    $stmt->close();

    $items = array();
    foreach ($rows as $row) {
        $messageUuid = trim((string) ($row['message_uuid'] ?? ''));
        if ($messageUuid !== '') {
            $items[$messageUuid] = (int) ($row['latest_event_id'] ?? 0);
        }
    }

    return $items;
}

function messengerResolveActor($conn, $allowSession, $allowBearer)
{
    if ($allowBearer) {
        $bearerToken = messengerGetBearerTokenFromRequest();
        if ($bearerToken !== null) {
            $tokenHash = messengerHashToken($bearerToken);
            $now = messengerUtcNow();

            $stmt = $conn->prepare('
                SELECT * FROM messenger_api_tokens
                WHERE token_hash = ?
                  AND is_active = 1
                  AND revoked_at IS NULL
                  AND (expires_at IS NULL OR expires_at > ?)
                LIMIT 1
            ');

            if (!$stmt) {
                throw new MessengerApiException('token_invalid', 'Не удалось проверить Bearer-токен.', 500);
            }

            $stmt->bind_param('ss', $tokenHash, $now);
            $stmt->execute();
            $result = $stmt->get_result();
            $tokenRow = $result ? $result->fetch_assoc() : null;
            $stmt->close();

            if (!$tokenRow) {
                throw new MessengerApiException('token_invalid', 'Bearer-токен недействителен.', 401);
            }

            $updateStmt = $conn->prepare('UPDATE messenger_api_tokens SET last_used_at = ? WHERE id = ?');
            if ($updateStmt) {
                $updateStmt->bind_param('si', $now, $tokenRow['id']);
                $updateStmt->execute();
                $updateStmt->close();
            }

            return array(
                'side' => 'central',
                'auth_mode' => 'bearer',
                'user_id' => null,
                'user_name' => (string) ($tokenRow['label'] ?? 'central'),
                'request_user_name' => (string) ($tokenRow['label'] ?? 'central'),
                'token_prefix' => (string) ($tokenRow['token_prefix'] ?? ''),
            );
        }
    }

    if ($allowSession) {
        if (!isLoggedIn()) {
            throw new MessengerApiException('access_denied', 'Требуется авторизация.', 401);
        }

        if (!hasPermission('messenger')) {
            throw new MessengerApiException('access_denied', 'Недостаточно прав для работы с мессенджером.', 403);
        }

        $user = getCurrentUserByLogin($conn);
        if (!$user || isUserBlocked($user)) {
            throw new MessengerApiException('access_denied', 'Пользователь недоступен для работы с мессенджером.', 403);
        }

        return array(
            'side' => 'site',
            'auth_mode' => 'session',
            'user_id' => (int) ($user['id'] ?? 0),
            'user_name' => messengerGetUserDisplayName($user),
            'request_user_name' => messengerGetUserDisplayName($user),
            'user_row' => $user,
        );
    }

    throw new MessengerApiException('access_denied', 'Доступ запрещен.', 403);
}

function messengerGetBearerTokenFromRequest()
{
    $header = '';

    if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
        $header = (string) $_SERVER['HTTP_AUTHORIZATION'];
    } elseif (isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) {
        $header = (string) $_SERVER['REDIRECT_HTTP_AUTHORIZATION'];
    }

    if ($header === '' && function_exists('getallheaders')) {
        $headers = getallheaders();
        if (is_array($headers)) {
            foreach ($headers as $name => $value) {
                if (strtolower((string) $name) === 'authorization') {
                    $header = (string) $value;
                    break;
                }
            }
        }
    }

    if (!preg_match('/^\s*Bearer\s+(.+)\s*$/i', $header, $matches)) {
        return null;
    }

    $token = trim((string) ($matches[1] ?? ''));
    return $token === '' ? null : $token;
}

function messengerHashToken($token)
{
    return hash('sha256', (string) $token);
}

function messengerRequireTokenManagerAccess(array $actor)
{
    if (($actor['side'] ?? '') !== 'site' || !hasPermission('tokens')) {
        throw new MessengerApiException('access_denied', 'Управление Bearer-токенами доступно только пользователю с ролью "Токены".', 403);
    }
}

function messengerBuildApiTokenListItem(array $row)
{
    $nowTs = time();
    $expiresAtTs = isset($row['expires_at']) && $row['expires_at'] !== null ? strtotime((string) $row['expires_at']) : false;
    $isRevoked = !empty($row['revoked_at']) || empty($row['is_active']);
    $isExpired = !$isRevoked && $expiresAtTs !== false && $expiresAtTs <= $nowTs;
    $status = 'active';

    if ($isRevoked) {
        $status = 'revoked';
    } elseif ($isExpired) {
        $status = 'expired';
    }

    return array(
        'id' => (int) ($row['id'] ?? 0),
        'label' => (string) ($row['label'] ?? ''),
        'token_prefix' => (string) ($row['token_prefix'] ?? ''),
        'status' => $status,
        'is_active' => !$isRevoked && !$isExpired,
        'created_at' => messengerToIso8601($row['created_at'] ?? null),
        'expires_at' => messengerToIso8601($row['expires_at'] ?? null),
        'revoked_at' => messengerToIso8601($row['revoked_at'] ?? null),
        'last_used_at' => messengerToIso8601($row['last_used_at'] ?? null),
    );
}

function messengerListApiTokens($conn)
{
    $items = array();
    $result = $conn->query('SELECT * FROM messenger_api_tokens ORDER BY created_at DESC, id DESC');
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $items[] = messengerBuildApiTokenListItem($row);
        }
    }

    return $items;
}

function messengerNormalizeApiTokenLabel($label)
{
    $label = trim((string) $label);
    $label = preg_replace('/[\x00-\x1F\x7F]+/u', ' ', $label);
    $label = preg_replace('/\s+/u', ' ', $label);

    if ($label === '') {
        throw new MessengerApiException('validation_error', 'Укажите label для токена центрального клиента.', 422);
    }

    if (mb_strlen($label, 'UTF-8') > 255) {
        throw new MessengerApiException('validation_error', 'Label токена не должен превышать 255 символов.', 422);
    }

    return $label;
}

function messengerNormalizeApiTokenExpiresAt($value)
{
    if ($value === null) {
        return null;
    }

    $value = trim((string) $value);
    if ($value === '') {
        return null;
    }

    $timestamp = strtotime($value);
    if ($timestamp === false) {
        throw new MessengerApiException('validation_error', 'Некорректное значение expires_at.', 422);
    }

    if ($timestamp <= time()) {
        throw new MessengerApiException('validation_error', 'Срок действия токена должен быть в будущем.', 422);
    }

    return gmdate('Y-m-d H:i:s', $timestamp);
}

function messengerCreateApiToken($conn, array $actor, $label, $expiresAt = null)
{
    messengerRequireTokenManagerAccess($actor);

    $label = messengerNormalizeApiTokenLabel($label);
    $expiresAt = messengerNormalizeApiTokenExpiresAt($expiresAt);
    $createdAt = messengerUtcNow();
    $plainToken = 'mtsn_' . bin2hex(random_bytes(24));
    $tokenPrefix = substr($plainToken, 0, 16);
    $tokenHash = messengerHashToken($plainToken);

    if ($expiresAt === null) {
        $stmt = $conn->prepare('
            INSERT INTO messenger_api_tokens (
                token_prefix, token_hash, label, is_active, created_at, expires_at, revoked_at, last_used_at
            ) VALUES (?, ?, ?, 1, ?, NULL, NULL, NULL)
        ');
        if (!$stmt) {
            throw new MessengerApiException('validation_error', 'Не удалось создать Bearer-токен.', 500);
        }

        $stmt->bind_param('ssss', $tokenPrefix, $tokenHash, $label, $createdAt);
    } else {
        $stmt = $conn->prepare('
            INSERT INTO messenger_api_tokens (
                token_prefix, token_hash, label, is_active, created_at, expires_at, revoked_at, last_used_at
            ) VALUES (?, ?, ?, 1, ?, ?, NULL, NULL)
        ');
        if (!$stmt) {
            throw new MessengerApiException('validation_error', 'Не удалось создать Bearer-токен.', 500);
        }

        $stmt->bind_param('sssss', $tokenPrefix, $tokenHash, $label, $createdAt, $expiresAt);
    }

    if (!$stmt->execute()) {
        $stmt->close();
        throw new MessengerApiException('validation_error', 'Не удалось сохранить Bearer-токен.', 500);
    }

    $tokenId = (int) $stmt->insert_id;
    $stmt->close();

    $selectStmt = $conn->prepare('SELECT * FROM messenger_api_tokens WHERE id = ? LIMIT 1');
    if (!$selectStmt) {
        throw new MessengerApiException('validation_error', 'Не удалось загрузить созданный Bearer-токен.', 500);
    }

    $selectStmt->bind_param('i', $tokenId);
    $selectStmt->execute();
    $result = $selectStmt->get_result();
    $row = $result ? $result->fetch_assoc() : null;
    $selectStmt->close();

    if (!$row) {
        throw new MessengerApiException('validation_error', 'Созданный Bearer-токен не найден.', 500);
    }

    messengerAudit($conn, $actor, 'api_token', $tokenPrefix, 'create', 'ok');

    return array(
        'token' => $plainToken,
        'token_once' => true,
        'item' => messengerBuildApiTokenListItem($row),
    );
}

function messengerRevokeApiToken($conn, array $actor, $tokenId)
{
    messengerRequireTokenManagerAccess($actor);

    $tokenId = (int) $tokenId;
    if ($tokenId < 1) {
        throw new MessengerApiException('validation_error', 'Не указан token_id для отзыва.', 422);
    }

    $stmt = $conn->prepare('SELECT * FROM messenger_api_tokens WHERE id = ? LIMIT 1');
    if (!$stmt) {
        throw new MessengerApiException('validation_error', 'Не удалось загрузить Bearer-токен.', 500);
    }

    $stmt->bind_param('i', $tokenId);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result ? $result->fetch_assoc() : null;
    $stmt->close();

    if (!$row) {
        throw new MessengerApiException('validation_error', 'Bearer-токен не найден.', 404);
    }

    if (empty($row['revoked_at']) && !empty($row['is_active'])) {
        $revokedAt = messengerUtcNow();
        $updateStmt = $conn->prepare('UPDATE messenger_api_tokens SET is_active = 0, revoked_at = ? WHERE id = ?');
        if (!$updateStmt) {
            throw new MessengerApiException('validation_error', 'Не удалось отозвать Bearer-токен.', 500);
        }

        $updateStmt->bind_param('si', $revokedAt, $tokenId);
        $updateStmt->execute();
        $updateStmt->close();

        $row['is_active'] = 0;
        $row['revoked_at'] = $revokedAt;
    }

    messengerAudit($conn, $actor, 'api_token', (string) ($row['token_prefix'] ?? (string) $tokenId), 'revoke', 'ok');

    return array(
        'item' => messengerBuildApiTokenListItem($row),
    );
}

function messengerEncodeCursor(array $payload)
{
    return rtrim(strtr(base64_encode(json_encode($payload, JSON_UNESCAPED_UNICODE)), '+/', '-_'), '=');
}

function messengerDecodeCursor($cursor)
{
    $cursor = trim((string) $cursor);
    if ($cursor === '') {
        return null;
    }

    $decoded = base64_decode(strtr($cursor, '-_', '+/'), true);
    if ($decoded === false) {
        return null;
    }

    $payload = json_decode($decoded, true);
    return is_array($payload) ? $payload : null;
}

function messengerNormalizeBodyText($bodyText)
{
    $bodyText = str_replace(array("\r\n", "\r"), "\n", (string) $bodyText);
    $bodyText = preg_replace("/\n{2,}/", "\n", $bodyText);
    return trim($bodyText);
}

function messengerAssertBodyText($bodyText, $attachmentCount, $requireBody)
{
    $bodyText = messengerNormalizeBodyText($bodyText);

    if (mb_strlen($bodyText, 'UTF-8') > 5000) {
        throw new MessengerApiException('validation_error', 'Текст сообщения превышает лимит 5000 символов.', 422);
    }

    if ($requireBody && $bodyText === '') {
        throw new MessengerApiException('validation_error', 'Первое сообщение не может быть пустым.', 422);
    }

    if (!$requireBody && $bodyText === '' && $attachmentCount < 1) {
        throw new MessengerApiException('validation_error', 'Сообщение должно содержать текст или хотя бы одно вложение.', 422);
    }

    return $bodyText;
}

function messengerBuildTitleFromFirstMessage($bodyText)
{
    return messengerNormalizeBodyText($bodyText);
}

function messengerGetDisplaySiteCode($siteCode)
{
    $siteCode = strtoupper(trim((string) $siteCode));

    if ($siteCode === '8080') {
        return 'OSTR';
    }

    return $siteCode;
}

function messengerBuildDisplayName($siteCode, $chatNo, $bodyText)
{
    return messengerGetDisplaySiteCode($siteCode) . '-' . $chatNo . ' ' . messengerBuildTitleFromFirstMessage($bodyText);
}

function messengerNormalizeFilesArray($filesField)
{
    if (!is_array($filesField) || !isset($filesField['name'])) {
        return array();
    }

    $normalized = array();
    if (!is_array($filesField['name'])) {
        if ((int) ($filesField['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
            $normalized[] = array(
                'name' => $filesField['name'],
                'type' => $filesField['type'] ?? '',
                'tmp_name' => $filesField['tmp_name'] ?? '',
                'error' => $filesField['error'] ?? UPLOAD_ERR_NO_FILE,
                'size' => $filesField['size'] ?? 0,
            );
        }

        return $normalized;
    }

    $count = count($filesField['name']);
    for ($i = 0; $i < $count; $i++) {
        if ((int) ($filesField['error'][$i] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
            continue;
        }

        $normalized[] = array(
            'name' => $filesField['name'][$i] ?? '',
            'type' => $filesField['type'][$i] ?? '',
            'tmp_name' => $filesField['tmp_name'][$i] ?? '',
            'error' => $filesField['error'][$i] ?? UPLOAD_ERR_NO_FILE,
            'size' => $filesField['size'][$i] ?? 0,
        );
    }

    return $normalized;
}

function messengerGetAllowedFileDefinitions()
{
    return array(
        'jpg' => array('group' => 'image', 'max_size' => 25 * 1024 * 1024, 'mimes' => array('image/jpeg')),
        'jpeg' => array('group' => 'image', 'max_size' => 25 * 1024 * 1024, 'mimes' => array('image/jpeg')),
        'png' => array('group' => 'image', 'max_size' => 25 * 1024 * 1024, 'mimes' => array('image/png')),
        'gif' => array('group' => 'image', 'max_size' => 25 * 1024 * 1024, 'mimes' => array('image/gif')),
        'webp' => array('group' => 'image', 'max_size' => 25 * 1024 * 1024, 'mimes' => array('image/webp')),
        'mp4' => array('group' => 'video', 'max_size' => 100 * 1024 * 1024, 'mimes' => array('video/mp4')),
        'mov' => array('group' => 'video', 'max_size' => 100 * 1024 * 1024, 'mimes' => array('video/quicktime')),
        'webm' => array('group' => 'video', 'max_size' => 100 * 1024 * 1024, 'mimes' => array('video/webm')),
        'pdf' => array('group' => 'document', 'max_size' => 25 * 1024 * 1024, 'mimes' => array('application/pdf')),
        'txt' => array('group' => 'document', 'max_size' => 25 * 1024 * 1024, 'mimes' => array('text/plain')),
        'rtf' => array('group' => 'document', 'max_size' => 25 * 1024 * 1024, 'mimes' => array('application/rtf', 'text/rtf')),
        'doc' => array('group' => 'document', 'max_size' => 25 * 1024 * 1024, 'mimes' => array('application/msword')),
        'docx' => array('group' => 'document', 'max_size' => 25 * 1024 * 1024, 'mimes' => array('application/vnd.openxmlformats-officedocument.wordprocessingml.document')),
        'xls' => array('group' => 'document', 'max_size' => 25 * 1024 * 1024, 'mimes' => array('application/vnd.ms-excel')),
        'xlsx' => array('group' => 'document', 'max_size' => 25 * 1024 * 1024, 'mimes' => array('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')),
        'csv' => array('group' => 'document', 'max_size' => 25 * 1024 * 1024, 'mimes' => array('text/csv', 'application/csv', 'application/vnd.ms-excel')),
        'odt' => array('group' => 'document', 'max_size' => 25 * 1024 * 1024, 'mimes' => array('application/vnd.oasis.opendocument.text')),
        'ods' => array('group' => 'document', 'max_size' => 25 * 1024 * 1024, 'mimes' => array('application/vnd.oasis.opendocument.spreadsheet')),
        'zip' => array('group' => 'archive', 'max_size' => 25 * 1024 * 1024, 'mimes' => array('application/zip', 'application/x-zip-compressed')),
        'rar' => array('group' => 'archive', 'max_size' => 25 * 1024 * 1024, 'mimes' => array('application/vnd.rar', 'application/x-rar-compressed', 'application/x-rar', 'application/octet-stream')),
    );
}

function messengerNormalizeOriginalFilename($filename)
{
    $filename = trim((string) $filename);
    $filename = preg_replace('/[\x00-\x1F\x7F]+/u', '', $filename);
    $filename = basename($filename);
    if (mb_strlen($filename, 'UTF-8') > 255) {
        $filename = mb_substr($filename, 0, 255, 'UTF-8');
    }

    return $filename;
}

function messengerValidateFiles(array $files)
{
    if (count($files) > 10) {
        throw new MessengerApiException('validation_error', 'В одном сообщении допускается не более 10 вложений.', 422);
    }

    $definitions = messengerGetAllowedFileDefinitions();
    $validated = array();
    $totalSize = 0;
    $finfo = function_exists('finfo_open') ? finfo_open(FILEINFO_MIME_TYPE) : null;

    foreach ($files as $file) {
        $errorCode = (int) ($file['error'] ?? UPLOAD_ERR_NO_FILE);
        $originalName = messengerNormalizeOriginalFilename($file['name'] ?? '');
        if ($errorCode !== UPLOAD_ERR_OK) {
            throw new MessengerApiException(
                'validation_error',
                sprintf(
                    'Ошибка загрузки вложения%s. Код PHP upload: %d.',
                    $originalName !== '' ? ' "' . $originalName . '"' : '',
                    $errorCode
                ),
                422
            );
        }

        if ($originalName === '') {
            throw new MessengerApiException('file_type_invalid', 'Недопустимое имя файла.', 422);
        }

        $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
        if ($extension === '') {
            throw new MessengerApiException('file_type_invalid', sprintf('Файл "%s" должен иметь расширение.', $originalName), 422);
        }

        if (!isset($definitions[$extension])) {
            throw new MessengerApiException(
                'file_type_invalid',
                sprintf(
                    'Файл "%s" имеет недопустимое расширение ".%s". Разрешены: %s.',
                    $originalName,
                    $extension,
                    implode(', ', array_keys($definitions))
                ),
                422
            );
        }

        $declaredMime = trim((string) ($file['type'] ?? ''));
        $detectedMime = $declaredMime;
        if ($finfo && is_file((string) ($file['tmp_name'] ?? ''))) {
            $detectedMime = (string) finfo_file($finfo, $file['tmp_name']);
        }

        $allowedMimes = $definitions[$extension]['mimes'];
        if ($declaredMime !== '' && !in_array($declaredMime, $allowedMimes, true)) {
            throw new MessengerApiException(
                'file_type_invalid',
                sprintf(
                    'Файл "%s": заявленный MIME-тип "%s" не разрешен для расширения ".%s". Допустимо: %s.',
                    $originalName,
                    $declaredMime,
                    $extension,
                    implode(', ', $allowedMimes)
                ),
                422
            );
        }

        if ($detectedMime !== '' && !in_array($detectedMime, $allowedMimes, true)) {
            throw new MessengerApiException(
                'file_type_invalid',
                sprintf(
                    'Файл "%s": фактический MIME-тип "%s" не разрешен для расширения ".%s". Допустимо: %s.',
                    $originalName,
                    $detectedMime,
                    $extension,
                    implode(', ', $allowedMimes)
                ),
                422
            );
        }

        $sizeBytes = (int) ($file['size'] ?? 0);
        if ($sizeBytes < 1 || $sizeBytes > (int) $definitions[$extension]['max_size']) {
            throw new MessengerApiException('file_size_limit_exceeded', 'Размер вложения превышает допустимый лимит.', 422);
        }

        $totalSize += $sizeBytes;
        if ($totalSize > 150 * 1024 * 1024) {
            throw new MessengerApiException('file_size_limit_exceeded', 'Суммарный размер вложений превышает лимит 150 МБ.', 422);
        }

        $validated[] = array(
            'original_name' => $originalName,
            'extension' => $extension,
            'mime_type' => $detectedMime !== '' ? $detectedMime : $declaredMime,
            'size_bytes' => $sizeBytes,
            'tmp_name' => $file['tmp_name'],
        );
    }

    if ($finfo) {
        finfo_close($finfo);
    }

    return $validated;
}

function messengerStoreValidatedFile(array $validatedFile)
{
    $storageRoot = messengerEnsureStorageRoot();
    $relativeDir = gmdate('Y/m');
    $targetDir = $storageRoot . '/' . $relativeDir;
    if (!is_dir($targetDir) && !mkdir($targetDir, 0775, true) && !is_dir($targetDir)) {
        throw new MessengerApiException('validation_error', 'Не удалось создать директорию для вложения.', 500);
    }

    $storedName = bin2hex(random_bytes(16)) . '.' . $validatedFile['extension'];
    $targetPath = $targetDir . '/' . $storedName;

    if (!move_uploaded_file($validatedFile['tmp_name'], $targetPath)) {
        if (!rename($validatedFile['tmp_name'], $targetPath)) {
            throw new MessengerApiException('validation_error', 'Не удалось сохранить вложение на диск.', 500);
        }
    }

    return array(
        'stored_name' => $storedName,
        'storage_path' => $relativeDir . '/' . $storedName,
        'sha256' => hash_file('sha256', $targetPath),
    );
}

function messengerDbColumnsMap($conn, $tableName)
{
    $map = array();
    $tableName = preg_replace('/[^a-zA-Z0-9_]/', '', (string) $tableName);
    if ($tableName === '') {
        return $map;
    }

    $result = $conn->query('SHOW COLUMNS FROM ' . $tableName);
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            if (isset($row['Field'])) {
                $map[$row['Field']] = true;
            }
        }
    }

    return $map;
}

function messengerRequireExistingUserWithPermission($conn, $userId)
{
    $stmt = $conn->prepare('SELECT * FROM users WHERE id = ? LIMIT 1');
    if (!$stmt) {
        throw new MessengerApiException('site_user_not_found', 'Не удалось загрузить пользователя сайта.', 500);
    }

    $stmt->bind_param('i', $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result ? $result->fetch_assoc() : null;
    $stmt->close();

    if (!$user) {
        throw new MessengerApiException('site_user_not_found', 'Пользователь сайта не найден.', 404);
    }

    if (isUserBlocked($user) || !in_array('messenger', parseUserPermissions($user), true)) {
        throw new MessengerApiException('site_user_access_denied', 'У пользователя нет права messenger.', 422);
    }

    return $user;
}

function messengerFetchChatByUuid($conn, $chatUuid)
{
    $stmt = $conn->prepare('SELECT * FROM messenger_chats WHERE chat_uuid = ? LIMIT 1');
    if (!$stmt) {
        throw new MessengerApiException('validation_error', 'Не удалось загрузить чат.', 500);
    }

    $stmt->bind_param('s', $chatUuid);
    $stmt->execute();
    $result = $stmt->get_result();
    $chat = $result ? $result->fetch_assoc() : null;
    $stmt->close();

    if (!$chat) {
        throw new MessengerApiException('validation_error', 'Chat not found', 404);
    }

    return $chat;
}

function messengerFetchMessageByUuid($conn, $messageUuid)
{
    $stmt = $conn->prepare('SELECT * FROM messenger_messages WHERE message_uuid = ? LIMIT 1');
    if (!$stmt) {
        throw new MessengerApiException('validation_error', 'Не удалось загрузить сообщение.', 500);
    }

    $stmt->bind_param('s', $messageUuid);
    $stmt->execute();
    $result = $stmt->get_result();
    $message = $result ? $result->fetch_assoc() : null;
    $stmt->close();

    if (!$message) {
        throw new MessengerApiException('validation_error', 'Сообщение не найдено.', 404);
    }

    return $message;
}

function messengerFetchAttachmentByUuid($conn, $attachmentUuid)
{
    $stmt = $conn->prepare('SELECT * FROM messenger_attachments WHERE attachment_uuid = ? LIMIT 1');
    if (!$stmt) {
        throw new MessengerApiException('validation_error', 'Не удалось загрузить вложение.', 500);
    }

    $stmt->bind_param('s', $attachmentUuid);
    $stmt->execute();
    $result = $stmt->get_result();
    $attachment = $result ? $result->fetch_assoc() : null;
    $stmt->close();

    if (!$attachment) {
        throw new MessengerApiException('validation_error', 'Вложение не найдено.', 404);
    }

    return $attachment;
}

function messengerFetchActiveParticipants($conn, $chatId)
{
    $stmt = $conn->prepare('SELECT * FROM messenger_chat_participants WHERE chat_id = ? AND is_active = 1 ORDER BY user_name ASC, user_id ASC');
    if (!$stmt) {
        throw new MessengerApiException('validation_error', 'Не удалось загрузить участников чата.', 500);
    }

    $stmt->bind_param('i', $chatId);
    $stmt->execute();
    $result = $stmt->get_result();
    $items = $result ? $result->fetch_all(MYSQLI_ASSOC) : array();
    $stmt->close();

    return $items;
}

function messengerFetchAttachmentsByMessageId($conn, $messageId)
{
    $stmt = $conn->prepare('SELECT * FROM messenger_attachments WHERE message_id = ? ORDER BY id ASC');
    if (!$stmt) {
        throw new MessengerApiException('validation_error', 'Не удалось загрузить вложения сообщения.', 500);
    }

    $stmt->bind_param('i', $messageId);
    $stmt->execute();
    $result = $stmt->get_result();
    $items = $result ? $result->fetch_all(MYSQLI_ASSOC) : array();
    $stmt->close();

    return $items;
}

function messengerFetchMessagesByChatId($conn, $chatId)
{
    $stmt = $conn->prepare('SELECT * FROM messenger_messages WHERE chat_id = ? ORDER BY created_at ASC, id ASC');
    if (!$stmt) {
        throw new MessengerApiException('validation_error', 'Не удалось загрузить сообщения чата.', 500);
    }

    $stmt->bind_param('i', $chatId);
    $stmt->execute();
    $result = $stmt->get_result();
    $items = $result ? $result->fetch_all(MYSQLI_ASSOC) : array();
    $stmt->close();

    return $items;
}

function messengerGetChatTitle($conn, array $chat)
{
    $messageId = isset($chat['first_message_id']) ? (int) $chat['first_message_id'] : 0;
    if ($messageId < 1) {
        return '';
    }

    $stmt = $conn->prepare('SELECT body_text FROM messenger_messages WHERE id = ? LIMIT 1');
    if (!$stmt) {
        return '';
    }

    $stmt->bind_param('i', $messageId);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result ? $result->fetch_assoc() : null;
    $stmt->close();

    return messengerBuildTitleFromFirstMessage((string) ($row['body_text'] ?? ''));
}

function messengerBuildSignificantEventActionSql()
{
    return "action IN ('chat_created', 'chat_status_changed', 'participant_added', 'participant_left', 'message_created', 'message_edited', 'message_deleted', 'attachment_added', 'attachment_deleted')";
}

function messengerGetChatLatestEventId($conn, $chatUuid, $significantOnly = false)
{
    $sql = "
        SELECT id
        FROM messenger_events
        WHERE entity_uuid = ?
           OR JSON_UNQUOTE(JSON_EXTRACT(payload_json, '$.chat_uuid')) = ?
    ";
    if ($significantOnly) {
        $sql .= ' AND ' . messengerBuildSignificantEventActionSql();
    }
    $sql .= '
        ORDER BY id DESC
        LIMIT 1
    ';

    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        return 0;
    }

    $stmt->bind_param('ss', $chatUuid, $chatUuid);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result ? $result->fetch_assoc() : null;
    $stmt->close();

    return (int) ($row['id'] ?? 0);
}

function messengerGetChatEventRow($conn, $chatUuid, $eventId, $significantOnly = false)
{
    $sql = "
        SELECT *
        FROM messenger_events
        WHERE id = ?
          AND (entity_uuid = ? OR JSON_UNQUOTE(JSON_EXTRACT(payload_json, '$.chat_uuid')) = ?)
    ";
    if ($significantOnly) {
        $sql .= ' AND ' . messengerBuildSignificantEventActionSql();
    }
    $sql .= '
        LIMIT 1
    ';

    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        return null;
    }

    $stmt->bind_param('iss', $eventId, $chatUuid, $chatUuid);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result ? $result->fetch_assoc() : null;
    $stmt->close();

    return $row ?: null;
}

function messengerCountUnreadEvents($conn, $chatUuid, $lastReadEventId)
{
    $stmt = $conn->prepare("
        SELECT COUNT(*)
        FROM messenger_events
        WHERE id > ?
          AND (entity_uuid = ? OR JSON_UNQUOTE(JSON_EXTRACT(payload_json, '$.chat_uuid')) = ?)
          AND " . messengerBuildSignificantEventActionSql()
    );

    if (!$stmt) {
        return 0;
    }

    $stmt->bind_param('iss', $lastReadEventId, $chatUuid, $chatUuid);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result ? $result->fetch_row() : null;
    $stmt->close();

    return is_array($row) ? (int) ($row[0] ?? 0) : 0;
}

function messengerGetFirstUnreadMessageUuid($conn, array $chat, $lastReadEventId)
{
    $chatUuid = (string) ($chat['chat_uuid'] ?? '');
    if ($chatUuid === '') {
        return null;
    }

    $stmt = $conn->prepare("
        SELECT action, payload_json
        FROM messenger_events
        WHERE id > ?
          AND (entity_uuid = ? OR JSON_UNQUOTE(JSON_EXTRACT(payload_json, '$.chat_uuid')) = ?)
          AND " . messengerBuildSignificantEventActionSql() . "
        ORDER BY id ASC
    ");

    if (!$stmt) {
        return null;
    }

    $stmt->bind_param('iss', $lastReadEventId, $chatUuid, $chatUuid);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($result && ($row = $result->fetch_assoc())) {
        $action = (string) ($row['action'] ?? '');
        $payload = json_decode((string) ($row['payload_json'] ?? ''), true);
        if (!is_array($payload)) {
            continue;
        }

        if ($action === 'chat_created') {
            $firstMessageUuid = messengerFetchSingleValue(
                $conn,
                'SELECT message_uuid FROM messenger_messages WHERE id = ? LIMIT 1',
                'i',
                array((int) ($chat['first_message_id'] ?? 0))
            );
            if (is_string($firstMessageUuid) && $firstMessageUuid !== '') {
                $stmt->close();
                return $firstMessageUuid;
            }
        }

        $messageUuid = trim((string) ($payload['message_uuid'] ?? ''));
        if ($messageUuid !== '') {
            $stmt->close();
            return $messageUuid;
        }
    }

    $stmt->close();
    return null;
}

function messengerFindReadRow($conn, $chatId, $readerKey)
{
    $stmt = $conn->prepare('SELECT * FROM messenger_reads WHERE chat_id = ? AND reader_key = ? LIMIT 1');
    if (!$stmt) {
        return null;
    }

    $stmt->bind_param('is', $chatId, $readerKey);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result ? $result->fetch_assoc() : null;
    $stmt->close();

    return $row ?: null;
}

function messengerBuildReaderKey(array $actor)
{
    return $actor['side'] === 'central' ? 'central' : 'site:' . (int) $actor['user_id'];
}

function messengerUpsertReadState($conn, $chatId, $readerKey, $side, $userId, $userName, $lastReadEventId, $lastReadAt, $manualUnread = 0)
{
    $stmt = $conn->prepare('
        INSERT INTO messenger_reads (chat_id, reader_key, side, user_id, user_name, last_read_event_id, last_read_at, manual_unread)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE
            side = VALUES(side),
            user_id = VALUES(user_id),
            user_name = VALUES(user_name),
            last_read_event_id = VALUES(last_read_event_id),
            last_read_at = VALUES(last_read_at),
            manual_unread = VALUES(manual_unread)
    ');

    if (!$stmt) {
        throw new MessengerApiException('validation_error', 'Не удалось обновить состояние прочтения.', 500);
    }

    $normalizedUserId = $userId !== null ? (int) $userId : null;
    $normalizedLastReadEventId = $lastReadEventId !== null ? (int) $lastReadEventId : null;
    $normalizedManualUnread = !empty($manualUnread) ? 1 : 0;
    $stmt->bind_param('issisisi', $chatId, $readerKey, $side, $normalizedUserId, $userName, $normalizedLastReadEventId, $lastReadAt, $normalizedManualUnread);
    if (!$stmt->execute()) {
        $stmt->close();
        throw new MessengerApiException('validation_error', 'Не удалось сохранить состояние прочтения.', 500);
    }
    $stmt->close();
}

function messengerAssertActiveSiteParticipant($conn, $chatId, $userId)
{
    $stmt = $conn->prepare('SELECT * FROM messenger_chat_participants WHERE chat_id = ? AND user_id = ? AND is_active = 1 LIMIT 1');
    if (!$stmt) {
        throw new MessengerApiException('participant_required', 'Не удалось проверить участие в чате.', 500);
    }

    $stmt->bind_param('ii', $chatId, $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result ? $result->fetch_assoc() : null;
    $stmt->close();

    if (!$row) {
        throw new MessengerApiException('participant_required', 'Пользователь не является активным участником чата.', 403);
    }

    return $row;
}

function messengerAssertChatAvailableForActor($conn, array $chat, array $actor, $forWrite)
{
    if (!empty($chat['is_deleted'])) {
        throw new MessengerApiException('chat_deleted', 'Чат удален.', 409);
    }

    if ($actor['side'] === 'site') {
        messengerAssertActiveSiteParticipant($conn, (int) $chat['id'], (int) $actor['user_id']);
    }

    if ($forWrite && (string) $chat['status'] === 'closed') {
        throw new MessengerApiException('chat_closed', 'Чат закрыт для изменений.', 409);
    }
}

function messengerInsertEvent($conn, $entityType, $entityUuid, $action, array $payload, $createdAt = null)
{
    $eventUuid = messengerGenerateUuid();
    $createdAt = $createdAt ?: messengerUtcNow();
    $payloadJson = json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

    $stmt = $conn->prepare('INSERT INTO messenger_events (event_uuid, entity_type, entity_uuid, action, payload_json, created_at) VALUES (?, ?, ?, ?, ?, ?)');
    if (!$stmt) {
        throw new MessengerApiException('validation_error', 'Не удалось записать событие синхронизации.', 500);
    }

    $stmt->bind_param('ssssss', $eventUuid, $entityType, $entityUuid, $action, $payloadJson, $createdAt);
    if (!$stmt->execute()) {
        $stmt->close();
        throw new MessengerApiException('validation_error', 'Не удалось сохранить событие синхронизации.', 500);
    }

    $eventId = (int) $stmt->insert_id;
    $stmt->close();

    return array(
        'event_id' => $eventId,
        'event_uuid' => $eventUuid,
    );
}

function messengerAudit($conn, array $actor, $entityType, $entityUuid, $action, $resultCode, $requestId = null)
{
    $stmt = $conn->prepare('
        INSERT INTO messenger_audit_log (
            actor_side, actor_user_id, actor_user_name, auth_mode, entity_type, entity_uuid,
            action, request_id, result_code, ip_address, user_agent, created_at
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ');

    if (!$stmt) {
        return;
    }

    $ipAddress = trim((string) ($_SERVER['REMOTE_ADDR'] ?? ''));
    $userAgent = trim((string) ($_SERVER['HTTP_USER_AGENT'] ?? ''));
    $createdAt = messengerUtcNow();
    $actorUserId = $actor['user_id'] !== null ? (int) $actor['user_id'] : null;
    $actorUserName = (string) ($actor['request_user_name'] ?? $actor['user_name'] ?? '');
    $authMode = (string) ($actor['auth_mode'] ?? 'session');

    $stmt->bind_param(
        'sissssssssss',
        $actor['side'],
        $actorUserId,
        $actorUserName,
        $authMode,
        $entityType,
        $entityUuid,
        $action,
        $requestId,
        $resultCode,
        $ipAddress,
        $userAgent,
        $createdAt
    );
    $stmt->execute();
    $stmt->close();
}

function messengerGetStoredExternalResponse($conn, $requestId, $endpointName)
{
    $stmt = $conn->prepare('SELECT response_json FROM messenger_external_requests WHERE request_id = ? AND endpoint_name = ? LIMIT 1');
    if (!$stmt) {
        return null;
    }

    $stmt->bind_param('ss', $requestId, $endpointName);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result ? $result->fetch_assoc() : null;
    $stmt->close();

    if (!$row || !isset($row['response_json'])) {
        return null;
    }

    $decoded = json_decode((string) $row['response_json'], true);
    return is_array($decoded) ? $decoded : null;
}

function messengerStoreExternalResponse($conn, $requestId, $endpointName, array $response)
{
    $responseJson = json_encode($response, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    $createdAt = messengerUtcNow();
    $stmt = $conn->prepare('INSERT INTO messenger_external_requests (request_id, endpoint_name, response_json, created_at) VALUES (?, ?, ?, ?)');
    if (!$stmt) {
        return;
    }

    $stmt->bind_param('ssss', $requestId, $endpointName, $responseJson, $createdAt);
    $stmt->execute();
    $stmt->close();
}

function messengerCreateParticipantRow($conn, $chatId, array $userRow, array $actor, $joinedAt)
{
    $stmt = $conn->prepare('
        INSERT INTO messenger_chat_participants (
            chat_id, user_id, user_name, is_active, joined_at, left_at,
            added_by_side, added_by_user_id, added_by_user_name, updated_at
        ) VALUES (?, ?, ?, 1, ?, NULL, ?, ?, ?, ?)
    ');

    if (!$stmt) {
        throw new MessengerApiException('validation_error', 'Не удалось добавить участника чата.', 500);
    }

    $userId = (int) ($userRow['id'] ?? 0);
    $userName = messengerGetUserDisplayName($userRow);
    $addedByUserId = $actor['user_id'] !== null ? (int) $actor['user_id'] : null;
    $addedByUserName = (string) ($actor['request_user_name'] ?? $actor['user_name'] ?? '');
    $stmt->bind_param('iisssiss', $chatId, $userId, $userName, $joinedAt, $actor['side'], $addedByUserId, $addedByUserName, $joinedAt);
    if (!$stmt->execute()) {
        $stmt->close();
        throw new MessengerApiException('validation_error', 'Не удалось сохранить участника чата.', 500);
    }
    $stmt->close();

    return array(
        'user_id' => $userId,
        'user_name' => $userName,
        'is_active' => true,
    );
}

function messengerCreateAttachmentRows($conn, $chatId, $messageId, array $validatedFiles, array $actor, $createdAt)
{
    $items = array();

    foreach ($validatedFiles as $validatedFile) {
        $stored = messengerStoreValidatedFile($validatedFile);
        $attachmentUuid = messengerGenerateUuid();
        $stmt = $conn->prepare('
            INSERT INTO messenger_attachments (
                attachment_uuid, chat_id, message_id, stored_name, storage_path, original_name, mime_type,
                extension, size_bytes, sha256, uploaded_by_side, uploaded_by_user_id, uploaded_by_user_name,
                is_deleted, created_at, deleted_at
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 0, ?, NULL)
        ');

        if (!$stmt) {
            throw new MessengerApiException('validation_error', 'Не удалось записать вложение.', 500);
        }

        $uploadedByUserId = $actor['user_id'] !== null ? (int) $actor['user_id'] : null;
        $uploadedByUserName = (string) ($actor['request_user_name'] ?? $actor['user_name'] ?? '');
        $sizeBytes = (int) $validatedFile['size_bytes'];
        $stmt->bind_param(
            'siisssssississ',
            $attachmentUuid,
            $chatId,
            $messageId,
            $stored['stored_name'],
            $stored['storage_path'],
            $validatedFile['original_name'],
            $validatedFile['mime_type'],
            $validatedFile['extension'],
            $sizeBytes,
            $stored['sha256'],
            $actor['side'],
            $uploadedByUserId,
            $uploadedByUserName,
            $createdAt
        );
        if (!$stmt->execute()) {
            $stmt->close();
            throw new MessengerApiException('validation_error', 'Не удалось сохранить метаданные вложения.', 500);
        }

        $attachmentId = (int) $stmt->insert_id;
        $stmt->close();
        $items[] = array(
            'id' => $attachmentId,
            'attachment_uuid' => $attachmentUuid,
            'original_name' => $validatedFile['original_name'],
            'mime_type' => $validatedFile['mime_type'],
            'size_bytes' => $sizeBytes,
            'storage_path' => $stored['storage_path'],
            'uploaded_by_side' => $actor['side'],
            'uploaded_by_user_id' => $uploadedByUserId,
            'uploaded_by_user_name' => $uploadedByUserName,
            'created_at' => $createdAt,
        );
    }

    return $items;
}

function messengerCreateMessageRow($conn, $chatId, array $actor, $bodyText, $createdAt)
{
    $messageUuid = messengerGenerateUuid();
    $stmt = $conn->prepare('
        INSERT INTO messenger_messages (
            message_uuid, chat_id, author_side, author_user_id, author_user_name, body_text,
            is_deleted, created_at, updated_at, deleted_at
        ) VALUES (?, ?, ?, ?, ?, ?, 0, ?, ?, NULL)
    ');

    if (!$stmt) {
        throw new MessengerApiException('validation_error', 'Не удалось создать сообщение.', 500);
    }

    $authorUserId = $actor['user_id'] !== null ? (int) $actor['user_id'] : null;
    $authorUserName = (string) ($actor['request_user_name'] ?? $actor['user_name'] ?? '');
    $stmt->bind_param('sissssss', $messageUuid, $chatId, $actor['side'], $authorUserId, $authorUserName, $bodyText, $createdAt, $createdAt);
    if (!$stmt->execute()) {
        $stmt->close();
        throw new MessengerApiException('validation_error', 'Не удалось сохранить сообщение.', 500);
    }

    $messageId = (int) $stmt->insert_id;
    $stmt->close();

    return array(
        'id' => $messageId,
        'message_uuid' => $messageUuid,
        'author_side' => $actor['side'],
        'author_user_id' => $authorUserId,
        'author_user_name' => $authorUserName,
        'body_text' => $bodyText,
        'created_at' => $createdAt,
        'updated_at' => $createdAt,
    );
}

function messengerUpdateChatAfterChange($conn, $chatId, array $fields)
{
    if (empty($fields)) {
        return;
    }

    $parts = array();
    $types = '';
    $values = array();
    foreach ($fields as $column => $value) {
        $parts[] = $column . ' = ?';
        if (is_int($value)) {
            $types .= 'i';
        } else {
            $types .= 's';
        }
        $values[] = $value;
    }

    $types .= 'i';
    $values[] = $chatId;
    $sql = 'UPDATE messenger_chats SET ' . implode(', ', $parts) . ' WHERE id = ?';
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new MessengerApiException('validation_error', 'Не удалось обновить чат.', 500);
    }

    bindDynamicParams($stmt, $types, $values);
    if (!$stmt->execute()) {
        $stmt->close();
        throw new MessengerApiException('validation_error', 'Не удалось сохранить изменения чата.', 500);
    }
    $stmt->close();
}

function messengerBuildChatPayload($conn, array $chat)
{
    $settings = messengerGetSiteSettings($conn);
    $title = messengerGetChatTitle($conn, $chat);

    $payload = array(
        'chat_uuid' => (string) $chat['chat_uuid'],
        'chat_no' => (int) $chat['chat_no'],
        'site_code' => (string) $settings['site_code'],
        'title' => $title,
        'display_name' => messengerBuildDisplayName((string) $settings['site_code'], (int) $chat['chat_no'], $title),
        'status' => (string) $chat['status'],
        'last_activity_at' => messengerToIso8601($chat['last_activity_at']),
    );

    if (!empty($chat['deleted_at'])) {
        $payload['deleted_at'] = messengerToIso8601($chat['deleted_at']);
    }

    return $payload;
}

function messengerAugmentEventPayloadWithChatDisplay($conn, $action, $entityUuid, array $payload)
{
    $chatUuid = trim((string) ($payload['chat_uuid'] ?? ''));
    if ($chatUuid === '' && strpos((string) $action, 'chat_') === 0) {
        $chatUuid = trim((string) $entityUuid);
    }

    if ($chatUuid === '') {
        return $payload;
    }

    try {
        $chat = messengerFetchChatByUuid($conn, $chatUuid);
        $chatPayload = messengerBuildChatPayload($conn, $chat);
        $userIdsForDisplay = array();

        if (isset($payload['user_id']) && (int) $payload['user_id'] > 0) {
            $userIdsForDisplay[] = (int) $payload['user_id'];
        }
        if ((string) ($payload['author_side'] ?? '') === 'site' && isset($payload['author_user_id']) && (int) $payload['author_user_id'] > 0) {
            $userIdsForDisplay[] = (int) $payload['author_user_id'];
        }
        if ((string) ($payload['edited_by_side'] ?? '') === 'site' && isset($payload['edited_by_user_id']) && (int) $payload['edited_by_user_id'] > 0) {
            $userIdsForDisplay[] = (int) $payload['edited_by_user_id'];
        }

        $userDisplayNamesById = messengerGetUserDisplayNamesByIds($conn, $userIdsForDisplay);

        if (array_key_exists('user_name', $payload)) {
            $payload['user_name'] = messengerResolveStoredSiteUserName(
                $userDisplayNamesById,
                'site',
                $payload['user_id'] ?? null,
                (string) ($payload['user_name'] ?? '')
            );
        }

        if (array_key_exists('author_user_name', $payload)) {
            $payload['author_user_name'] = messengerResolveStoredSiteUserName(
                $userDisplayNamesById,
                (string) ($payload['author_side'] ?? ''),
                $payload['author_user_id'] ?? null,
                (string) ($payload['author_user_name'] ?? '')
            );
        }

        if (array_key_exists('edited_by_user_name', $payload)) {
            $payload['edited_by_user_name'] = messengerResolveStoredSiteUserName(
                $userDisplayNamesById,
                (string) ($payload['edited_by_side'] ?? ''),
                $payload['edited_by_user_id'] ?? null,
                (string) ($payload['edited_by_user_name'] ?? '')
            );
        }

        return array_merge($chatPayload, $payload);
    } catch (Throwable $throwable) {
        return $payload;
    }
}

function messengerListUsers($conn, array $actor, array $params)
{
    $columns = messengerDbColumnsMap($conn, 'users');
    $userNameSql = isset($columns['full_name'])
        ? 'full_name'
        : (isset($columns['fio']) ? 'fio' : 'login');
    $limit = isset($params['limit']) ? max(1, min(20, (int) $params['limit'])) : 20;
    $cursorPayload = messengerDecodeCursor($params['cursor'] ?? '');
    $q = trim((string) ($params['q'] ?? ''));
    $chatUuid = trim((string) ($params['chat_uuid'] ?? ''));

    $where = array('1 = 1');
    $types = '';
    $values = array();

    if (isset($columns['status'])) {
        $where[] = "(status IS NULL OR status <> 'blocked')";
    }
    if (isset($columns['blockuser'])) {
        $where[] = '(blockuser = 0 OR blockuser IS NULL)';
    }
    if (isset($columns['is_blocked'])) {
        $where[] = '(is_blocked = 0 OR is_blocked IS NULL)';
    }
    if (isset($columns['blocked'])) {
        $where[] = '(blocked = 0 OR blocked IS NULL)';
    }

    if ($q !== '') {
        $searchParts = array();
        foreach (array('full_name', 'fio', 'login') as $searchField) {
            if (isset($columns[$searchField])) {
                $searchParts[] = $searchField . ' LIKE ?';
                $types .= 's';
                $values[] = '%' . $q . '%';
            }
        }

        if (!empty($searchParts)) {
            $where[] = '(' . implode(' OR ', $searchParts) . ')';
        }
    }

    if ($actor['side'] === 'site' && $chatUuid !== '') {
        $chat = messengerFetchChatByUuid($conn, $chatUuid);
        $where[] = 'id NOT IN (SELECT user_id FROM messenger_chat_participants WHERE chat_id = ? AND is_active = 1)';
        $types .= 'i';
        $values[] = (int) $chat['id'];
    }

    if (is_array($cursorPayload) && isset($cursorPayload['user_name'], $cursorPayload['user_id'])) {
        $where[] = '((' . $userNameSql . ' > ?) OR (' . $userNameSql . ' = ? AND id > ?))';
        $types .= 'ssi';
        $values[] = (string) $cursorPayload['user_name'];
        $values[] = (string) $cursorPayload['user_name'];
        $values[] = (int) $cursorPayload['user_id'];
    }

    $sql = 'SELECT * FROM users WHERE ' . implode(' AND ', $where) . ' ORDER BY ' . $userNameSql . ' ASC, id ASC';
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new MessengerApiException('validation_error', 'Не удалось загрузить пользователей.', 500);
    }

    if ($types !== '') {
        bindDynamicParams($stmt, $types, $values);
    }

    $stmt->execute();
    $result = $stmt->get_result();
    $rows = $result ? $result->fetch_all(MYSQLI_ASSOC) : array();
    $stmt->close();

    $rows = array_values(array_filter($rows, function ($row) {
        return !isUserBlocked($row) && in_array('messenger', parseUserPermissions($row), true);
    }));

    $hasMore = count($rows) > $limit;
    if ($hasMore) {
        array_pop($rows);
    }

    $items = array();
    foreach ($rows as $row) {
        $items[] = array(
            'user_id' => (int) $row['id'],
            'user_name' => messengerGetUserDisplayName($row),
        );
    }

    $nextCursor = null;
    if ($hasMore && !empty($items)) {
        $lastItem = $items[count($items) - 1];
        $nextCursor = messengerEncodeCursor(array(
            'user_name' => $lastItem['user_name'],
            'user_id' => $lastItem['user_id'],
        ));
    }

    return array(
        'items' => $items,
        'next_cursor' => $nextCursor,
        'has_more' => $hasMore,
    );
}

function messengerCreateChat($conn, array $actor, $bodyText, array $siteUserIds, array $files)
{
    $validatedFiles = messengerValidateFiles($files);
    $bodyText = messengerAssertBodyText($bodyText, count($validatedFiles), true);
    $settings = messengerGetSiteSettings($conn);
    $createdAt = messengerUtcNow();

    $participantUsers = array();
    if ($actor['side'] === 'central') {
        $normalizedIds = array_values(array_unique(array_map('intval', $siteUserIds)));
        if (empty($normalizedIds)) {
            throw new MessengerApiException('participants_required', 'Нужно выбрать хотя бы одного пользователя сайта.', 422);
        }

        foreach ($normalizedIds as $userId) {
            $participantUsers[] = messengerRequireExistingUserWithPermission($conn, $userId);
        }
    } else {
        $participantUsers[] = $actor['user_row'];
    }

    $conn->begin_transaction();
    try {
        $chatNoResult = $conn->query('SELECT COALESCE(MAX(chat_no), 999) AS max_chat_no FROM messenger_chats');
        $chatNoRow = $chatNoResult ? $chatNoResult->fetch_assoc() : array('max_chat_no' => 999);
        $chatNo = ((int) ($chatNoRow['max_chat_no'] ?? 999)) + 1;
        $chatUuid = messengerGenerateUuid();
        $status = 'new';
        $createdByUserId = $actor['user_id'] !== null ? (int) $actor['user_id'] : null;
        $createdByUserName = (string) ($actor['request_user_name'] ?? $actor['user_name'] ?? '');

        $stmt = $conn->prepare('
            INSERT INTO messenger_chats (
                chat_uuid, chat_no, first_message_id, status, created_by_side, created_by_user_id,
                created_by_user_name, is_deleted, deleted_at, deleted_by_side, deleted_by_user_id,
                deleted_by_user_name, created_at, updated_at, last_activity_at, closed_at
            ) VALUES (?, ?, NULL, ?, ?, ?, ?, 0, NULL, NULL, NULL, NULL, ?, ?, ?, NULL)
        ');

        if (!$stmt) {
            throw new MessengerApiException('validation_error', 'Не удалось создать чат.', 500);
        }

        $stmt->bind_param('sississss', $chatUuid, $chatNo, $status, $actor['side'], $createdByUserId, $createdByUserName, $createdAt, $createdAt, $createdAt);
        if (!$stmt->execute()) {
            $stmt->close();
            throw new MessengerApiException('validation_error', 'Не удалось сохранить чат.', 500);
        }
        $chatId = (int) $stmt->insert_id;
        $stmt->close();

        $message = messengerCreateMessageRow($conn, $chatId, $actor, $bodyText, $createdAt);
        messengerUpdateChatAfterChange($conn, $chatId, array(
            'first_message_id' => (int) $message['id'],
            'updated_at' => $createdAt,
            'last_activity_at' => $createdAt,
        ));

        $attachments = messengerCreateAttachmentRows($conn, $chatId, (int) $message['id'], $validatedFiles, $actor, $createdAt);

        $participantPayloads = array();
        foreach ($participantUsers as $participantUserRow) {
            $participantPayloads[] = messengerCreateParticipantRow($conn, $chatId, $participantUserRow, $actor, $createdAt);
        }

        $chat = messengerFetchChatByUuid($conn, $chatUuid);
        $eventIds = array();
        $eventIds[] = messengerInsertEvent($conn, 'chat', $chatUuid, 'chat_created', messengerBuildChatPayload($conn, $chat), $createdAt);

        foreach ($participantPayloads as $participantPayload) {
            $participantEventPayload = array(
                'chat_uuid' => $chatUuid,
                'user_id' => $participantPayload['user_id'],
                'user_name' => $participantPayload['user_name'],
                'is_active' => true,
                'updated_at' => messengerToIso8601($createdAt),
                'last_activity_at' => messengerToIso8601($createdAt),
            );
            $eventIds[] = messengerInsertEvent($conn, 'participant', messengerGenerateUuid(), 'participant_added', $participantEventPayload, $createdAt);
        }

        $messagePayload = array(
            'chat_uuid' => $chatUuid,
            'message_uuid' => $message['message_uuid'],
            'author_side' => $message['author_side'],
            'author_user_id' => $message['author_user_id'],
            'author_user_name' => $message['author_user_name'],
            'body_text' => $message['body_text'],
            'created_at' => messengerToIso8601($message['created_at']),
            'updated_at' => messengerToIso8601($message['updated_at']),
            'is_first_message' => true,
            'last_activity_at' => messengerToIso8601($createdAt),
        );
        $eventIds[] = messengerInsertEvent($conn, 'message', $message['message_uuid'], 'message_created', $messagePayload, $createdAt);

        foreach ($attachments as $attachment) {
            $attachmentPayload = array(
                'chat_uuid' => $chatUuid,
                'message_uuid' => $message['message_uuid'],
                'attachment_uuid' => $attachment['attachment_uuid'],
                'original_name' => $attachment['original_name'],
                'mime_type' => $attachment['mime_type'],
                'size_bytes' => $attachment['size_bytes'],
                'last_activity_at' => messengerToIso8601($createdAt),
            );
            $eventIds[] = messengerInsertEvent($conn, 'attachment', $attachment['attachment_uuid'], 'attachment_added', $attachmentPayload, $createdAt);
        }

        $latestEventId = (int) $eventIds[count($eventIds) - 1]['event_id'];
        if ($actor['side'] === 'central') {
            messengerUpsertReadState($conn, $chatId, 'central', 'central', null, $createdByUserName, $latestEventId, $createdAt);
            foreach ($participantUsers as $participantUserRow) {
                $participantUserId = (int) ($participantUserRow['id'] ?? 0);
                messengerUpsertReadState($conn, $chatId, 'site:' . $participantUserId, 'site', $participantUserId, messengerGetUserDisplayName($participantUserRow), null, $createdAt);
            }
        } else {
            $siteUserId = (int) ($actor['user_id'] ?? 0);
            messengerUpsertReadState($conn, $chatId, 'site:' . $siteUserId, 'site', $siteUserId, $createdByUserName, $latestEventId, $createdAt);
            messengerUpsertReadState($conn, $chatId, 'central', 'central', null, 'central', null, $createdAt);
        }

        messengerAudit($conn, $actor, 'chat', $chatUuid, 'create', 'ok');
        $conn->commit();

        $participantsResponse = array();
        foreach ($participantUsers as $participantUserRow) {
            $participantsResponse[] = array(
                'user_id' => (int) ($participantUserRow['id'] ?? 0),
                'user_name' => messengerGetUserDisplayName($participantUserRow),
            );
        }

        return array(
            'chat_uuid' => $chatUuid,
            'chat_no' => $chatNo,
            'title' => messengerBuildTitleFromFirstMessage($bodyText),
            'first_message_uuid' => $message['message_uuid'],
            'attachment_uuids' => array_values(array_map(function ($attachment) {
                return $attachment['attachment_uuid'];
            }, $attachments)),
            'participants' => $participantsResponse,
            'event_ids' => array_values(array_map(function ($event) {
                return $event['event_id'];
            }, $eventIds)),
        );
    } catch (Throwable $throwable) {
        $conn->rollback();
        throw $throwable;
    }
}

function messengerSendMessage($conn, array $actor, $chatUuid, $bodyText, array $files)
{
    $chat = messengerFetchChatByUuid($conn, $chatUuid);
    messengerAssertChatAvailableForActor($conn, $chat, $actor, true);

    $validatedFiles = messengerValidateFiles($files);
    $bodyText = messengerAssertBodyText($bodyText, count($validatedFiles), false);
    $createdAt = messengerUtcNow();

    $conn->begin_transaction();
    try {
        $message = messengerCreateMessageRow($conn, (int) $chat['id'], $actor, $bodyText, $createdAt);
        $attachments = messengerCreateAttachmentRows($conn, (int) $chat['id'], (int) $message['id'], $validatedFiles, $actor, $createdAt);
        messengerUpdateChatAfterChange($conn, (int) $chat['id'], array(
            'updated_at' => $createdAt,
            'last_activity_at' => $createdAt,
        ));
        $chat = messengerFetchChatByUuid($conn, $chatUuid);

        $eventIds = array();
        $messagePayload = array(
            'chat_uuid' => $chatUuid,
            'message_uuid' => $message['message_uuid'],
            'author_side' => $message['author_side'],
            'author_user_id' => $message['author_user_id'],
            'author_user_name' => $message['author_user_name'],
            'body_text' => $message['body_text'],
            'created_at' => messengerToIso8601($message['created_at']),
            'updated_at' => messengerToIso8601($message['updated_at']),
            'is_first_message' => false,
            'last_activity_at' => messengerToIso8601($createdAt),
        );
        $eventIds[] = messengerInsertEvent($conn, 'message', $message['message_uuid'], 'message_created', $messagePayload, $createdAt);

        foreach ($attachments as $attachment) {
            $attachmentPayload = array(
                'chat_uuid' => $chatUuid,
                'message_uuid' => $message['message_uuid'],
                'attachment_uuid' => $attachment['attachment_uuid'],
                'original_name' => $attachment['original_name'],
                'mime_type' => $attachment['mime_type'],
                'size_bytes' => $attachment['size_bytes'],
                'last_activity_at' => messengerToIso8601($createdAt),
            );
            $eventIds[] = messengerInsertEvent($conn, 'attachment', $attachment['attachment_uuid'], 'attachment_added', $attachmentPayload, $createdAt);
        }

        $latestEventId = (int) $eventIds[count($eventIds) - 1]['event_id'];
        messengerUpsertReadState(
            $conn,
            (int) $chat['id'],
            messengerBuildReaderKey($actor),
            $actor['side'],
            $actor['user_id'] !== null ? (int) $actor['user_id'] : null,
            (string) ($actor['request_user_name'] ?? $actor['user_name'] ?? ''),
            $latestEventId,
            $createdAt
        );

        messengerAudit($conn, $actor, 'message', $message['message_uuid'], 'send', 'ok');
        $conn->commit();

        return array(
            'message_uuid' => $message['message_uuid'],
            'attachment_uuids' => array_values(array_map(function ($attachment) {
                return $attachment['attachment_uuid'];
            }, $attachments)),
            'event_ids' => array_values(array_map(function ($event) {
                return $event['event_id'];
            }, $eventIds)),
        );
    } catch (Throwable $throwable) {
        $conn->rollback();
        throw $throwable;
    }
}

function messengerEditMessage($conn, array $actor, $messageUuid, $bodyText)
{
    $message = messengerFetchMessageByUuid($conn, $messageUuid);
    $chat = messengerFetchChatByUuid($conn, (string) messengerFetchSingleValue($conn, 'SELECT chat_uuid FROM messenger_chats WHERE id = ?', 'i', array((int) $message['chat_id'])));
    messengerAssertChatAvailableForActor($conn, $chat, $actor, true);

    if (!empty($message['is_deleted'])) {
        throw new MessengerApiException('message_deleted', 'Сообщение уже удалено.', 409);
    }

    $isFirstMessage = ((int) $chat['first_message_id'] === (int) $message['id']);
    $isAllowed = false;
    if ($actor['side'] === 'site') {
        $isAllowed = $message['author_side'] === 'site' && (int) $message['author_user_id'] === (int) $actor['user_id'];
    } else {
        $isAllowed = $message['author_side'] === 'central' || $isFirstMessage;
    }

    if (!$isAllowed) {
        throw new MessengerApiException('access_denied', 'Недостаточно прав для редактирования сообщения.', 403);
    }

    $bodyText = messengerAssertBodyText($bodyText, 0, true);
    $editedAt = messengerUtcNow();

    $conn->begin_transaction();
    try {
        $versionNo = ((int) messengerFetchSingleValue($conn, 'SELECT COALESCE(MAX(version_no), 0) FROM messenger_message_edits WHERE message_id = ?', 'i', array((int) $message['id']))) + 1;
        $editStmt = $conn->prepare('
            INSERT INTO messenger_message_edits (
                message_id, version_no, previous_body_text, edited_by_side, edited_by_user_id, edited_by_user_name, edited_at
            ) VALUES (?, ?, ?, ?, ?, ?, ?)
        ');
        if (!$editStmt) {
            throw new MessengerApiException('validation_error', 'Не удалось сохранить историю правок.', 500);
        }

        $editedByUserId = $actor['user_id'] !== null ? (int) $actor['user_id'] : null;
        $editedByUserName = (string) ($actor['request_user_name'] ?? $actor['user_name'] ?? '');
        $editStmt->bind_param('iississ', $message['id'], $versionNo, $message['body_text'], $actor['side'], $editedByUserId, $editedByUserName, $editedAt);
        if (!$editStmt->execute()) {
            $editStmt->close();
            throw new MessengerApiException('validation_error', 'Не удалось записать историю правок.', 500);
        }
        $editStmt->close();

        $updateStmt = $conn->prepare('UPDATE messenger_messages SET body_text = ?, updated_at = ? WHERE id = ?');
        if (!$updateStmt) {
            throw new MessengerApiException('validation_error', 'Не удалось обновить сообщение.', 500);
        }
        $messageId = (int) $message['id'];
        $updateStmt->bind_param('ssi', $bodyText, $editedAt, $messageId);
        if (!$updateStmt->execute()) {
            $updateStmt->close();
            throw new MessengerApiException('validation_error', 'Не удалось сохранить новый текст сообщения.', 500);
        }
        $updateStmt->close();

        messengerUpdateChatAfterChange($conn, (int) $chat['id'], array(
            'updated_at' => $editedAt,
            'last_activity_at' => $editedAt,
        ));
        $chat = messengerFetchChatByUuid($conn, $chat['chat_uuid']);

        $eventPayload = array(
            'chat_uuid' => $chat['chat_uuid'],
            'message_uuid' => $messageUuid,
            'author_side' => $message['author_side'],
            'author_user_id' => $message['author_user_id'] !== null ? (int) $message['author_user_id'] : null,
            'author_user_name' => $message['author_user_name'],
            'body_text' => $bodyText,
            'created_at' => messengerToIso8601($message['created_at']),
            'updated_at' => messengerToIso8601($editedAt),
            'is_first_message' => $isFirstMessage,
            'last_activity_at' => messengerToIso8601($editedAt),
        );
        $event = messengerInsertEvent($conn, 'message', $messageUuid, 'message_edited', $eventPayload, $editedAt);
        messengerUpsertReadState($conn, (int) $chat['id'], messengerBuildReaderKey($actor), $actor['side'], $actor['user_id'] !== null ? (int) $actor['user_id'] : null, $editedByUserName, (int) $event['event_id'], $editedAt);

        messengerAudit($conn, $actor, 'message', $messageUuid, 'edit', 'ok');
        $conn->commit();

        return array(
            'chat_uuid' => $chat['chat_uuid'],
            'message_uuid' => $messageUuid,
            'body_text' => $bodyText,
            'updated_at' => messengerToIso8601($editedAt),
            'title' => $isFirstMessage ? messengerGetChatTitle($conn, $chat) : null,
            'event_ids' => array((int) $event['event_id']),
        );
    } catch (Throwable $throwable) {
        $conn->rollback();
        throw $throwable;
    }
}

function messengerDeleteEntity($conn, array $actor, $entityType, $entityUuid)
{
    $entityType = trim((string) $entityType);
    if (!in_array($entityType, array('chat', 'message', 'attachment'), true)) {
        throw new MessengerApiException('validation_error', 'Недопустимый тип сущности для удаления.', 422);
    }

    if ($entityType === 'chat') {
        if ($actor['side'] !== 'central') {
            throw new MessengerApiException('access_denied', 'Удаление чата доступно только central admin.', 403);
        }

        $chat = messengerFetchChatByUuid($conn, $entityUuid);
        if (!empty($chat['is_deleted'])) {
            return array(
                'entity_type' => 'chat',
                'entity_uuid' => $entityUuid,
                'chat_uuid' => $entityUuid,
                'deleted_at' => messengerToIso8601($chat['deleted_at']),
                'event_ids' => array(),
            );
        }

        $deletedAt = messengerUtcNow();
        $conn->begin_transaction();
        try {
            messengerUpdateChatAfterChange($conn, (int) $chat['id'], array(
                'is_deleted' => 1,
                'deleted_at' => $deletedAt,
                'deleted_by_side' => 'central',
                'deleted_by_user_name' => (string) ($actor['request_user_name'] ?? $actor['user_name'] ?? 'central'),
                'updated_at' => $deletedAt,
            ));
            $chat = messengerFetchChatByUuid($conn, $entityUuid);
            $payload = messengerBuildChatPayload($conn, $chat);
            $payload['deleted_at'] = messengerToIso8601($deletedAt);
            $event = messengerInsertEvent($conn, 'chat', $entityUuid, 'chat_deleted', $payload, $deletedAt);
            messengerAudit($conn, $actor, 'chat', $entityUuid, 'delete', 'ok');
            $conn->commit();

            return array(
                'entity_type' => 'chat',
                'entity_uuid' => $entityUuid,
                'chat_uuid' => $entityUuid,
                'deleted_at' => messengerToIso8601($deletedAt),
                'event_ids' => array((int) $event['event_id']),
            );
        } catch (Throwable $throwable) {
            $conn->rollback();
            throw $throwable;
        }
    }

    if ($entityType === 'message') {
        $message = messengerFetchMessageByUuid($conn, $entityUuid);
        $chatUuid = messengerFetchSingleValue($conn, 'SELECT chat_uuid FROM messenger_chats WHERE id = ?', 'i', array((int) $message['chat_id']));
        $chat = messengerFetchChatByUuid($conn, (string) $chatUuid);
        messengerAssertChatAvailableForActor($conn, $chat, $actor, true);
        if ((int) $chat['first_message_id'] === (int) $message['id']) {
            throw new MessengerApiException('first_message_delete_forbidden', 'Первое сообщение удалять нельзя.', 422);
        }

        if (!empty($message['is_deleted'])) {
            throw new MessengerApiException('message_deleted', 'Сообщение уже удалено.', 409);
        }

        $isOwnMessage = $actor['side'] === $message['author_side']
            && (($actor['side'] === 'central') || ((int) $message['author_user_id'] === (int) $actor['user_id']));
        if (!$isOwnMessage) {
            throw new MessengerApiException('access_denied', 'Удалять можно только свои сообщения.', 403);
        }

        $deletedAt = messengerUtcNow();
        $conn->begin_transaction();
        try {
            $stmt = $conn->prepare('UPDATE messenger_messages SET is_deleted = 1, deleted_at = ?, updated_at = ? WHERE id = ?');
            if (!$stmt) {
                throw new MessengerApiException('validation_error', 'Не удалось удалить сообщение.', 500);
            }
            $messageId = (int) $message['id'];
            $stmt->bind_param('ssi', $deletedAt, $deletedAt, $messageId);
            if (!$stmt->execute()) {
                $stmt->close();
                throw new MessengerApiException('validation_error', 'Не удалось сохранить удаление сообщения.', 500);
            }
            $stmt->close();

            $attachments = messengerFetchAttachmentsByMessageId($conn, (int) $message['id']);
            $deletedAttachmentUuids = array();
            $eventIds = array();
            foreach ($attachments as $attachment) {
                if (!empty($attachment['is_deleted'])) {
                    continue;
                }

                $stmt = $conn->prepare('UPDATE messenger_attachments SET is_deleted = 1, deleted_at = ? WHERE id = ?');
                if (!$stmt) {
                    throw new MessengerApiException('validation_error', 'Не удалось удалить вложения сообщения.', 500);
                }

                $attachmentId = (int) $attachment['id'];
                $stmt->bind_param('si', $deletedAt, $attachmentId);
                if (!$stmt->execute()) {
                    $stmt->close();
                    throw new MessengerApiException('validation_error', 'Не удалось сохранить удаление вложений сообщения.', 500);
                }
                $stmt->close();

                $attachmentPayload = array(
                    'chat_uuid' => $chat['chat_uuid'],
                    'message_uuid' => $message['message_uuid'],
                    'attachment_uuid' => $attachment['attachment_uuid'],
                    'original_name' => $attachment['original_name'],
                    'mime_type' => $attachment['mime_type'],
                    'size_bytes' => (int) $attachment['size_bytes'],
                    'deleted_at' => messengerToIso8601($deletedAt),
                    'last_activity_at' => messengerToIso8601($deletedAt),
                    'deleted_with_message' => true,
                );
                $attachmentEvent = messengerInsertEvent($conn, 'attachment', $attachment['attachment_uuid'], 'attachment_deleted', $attachmentPayload, $deletedAt);
                $eventIds[] = (int) $attachmentEvent['event_id'];
                $deletedAttachmentUuids[] = (string) $attachment['attachment_uuid'];
            }

            messengerUpdateChatAfterChange($conn, (int) $chat['id'], array(
                'updated_at' => $deletedAt,
                'last_activity_at' => $deletedAt,
            ));
            $payload = array(
                'chat_uuid' => $chat['chat_uuid'],
                'message_uuid' => $message['message_uuid'],
                'author_side' => $message['author_side'],
                'author_user_id' => $message['author_user_id'] !== null ? (int) $message['author_user_id'] : null,
                'author_user_name' => $message['author_user_name'],
                'created_at' => messengerToIso8601($message['created_at']),
                'updated_at' => messengerToIso8601($deletedAt),
                'deleted_at' => messengerToIso8601($deletedAt),
                'is_first_message' => false,
                'last_activity_at' => messengerToIso8601($deletedAt),
                'attachment_uuids' => $deletedAttachmentUuids,
            );
            $event = messengerInsertEvent($conn, 'message', $message['message_uuid'], 'message_deleted', $payload, $deletedAt);
            $eventIds[] = (int) $event['event_id'];
            messengerUpsertReadState($conn, (int) $chat['id'], messengerBuildReaderKey($actor), $actor['side'], $actor['user_id'] !== null ? (int) $actor['user_id'] : null, (string) ($actor['request_user_name'] ?? $actor['user_name'] ?? ''), (int) $event['event_id'], $deletedAt);
            messengerAudit($conn, $actor, 'message', $entityUuid, 'delete', 'ok');
            $conn->commit();

            return array(
                'entity_type' => 'message',
                'entity_uuid' => $entityUuid,
                'chat_uuid' => $chat['chat_uuid'],
                'deleted_at' => messengerToIso8601($deletedAt),
                'deleted_attachment_uuids' => $deletedAttachmentUuids,
                'event_ids' => $eventIds,
            );
        } catch (Throwable $throwable) {
            $conn->rollback();
            throw $throwable;
        }
    }

    $attachment = messengerFetchAttachmentByUuid($conn, $entityUuid);
    $chatUuid = messengerFetchSingleValue($conn, 'SELECT chat_uuid FROM messenger_chats WHERE id = ?', 'i', array((int) $attachment['chat_id']));
    $chat = messengerFetchChatByUuid($conn, (string) $chatUuid);
    messengerAssertChatAvailableForActor($conn, $chat, $actor, true);

    if (!empty($attachment['is_deleted'])) {
        throw new MessengerApiException('attachment_deleted', 'Вложение уже удалено.', 409);
    }

    $isOwnAttachment = $actor['side'] === $attachment['uploaded_by_side']
        && (($actor['side'] === 'central') || ((int) $attachment['uploaded_by_user_id'] === (int) $actor['user_id']));
    if (!$isOwnAttachment) {
        throw new MessengerApiException('access_denied', 'Удалять можно только свои вложения.', 403);
    }

    $deletedAt = messengerUtcNow();
    $conn->begin_transaction();
    try {
        $stmt = $conn->prepare('UPDATE messenger_attachments SET is_deleted = 1, deleted_at = ? WHERE id = ?');
        if (!$stmt) {
            throw new MessengerApiException('validation_error', 'Не удалось удалить вложение.', 500);
        }
        $attachmentId = (int) $attachment['id'];
        $stmt->bind_param('si', $deletedAt, $attachmentId);
        if (!$stmt->execute()) {
            $stmt->close();
            throw new MessengerApiException('validation_error', 'Не удалось сохранить удаление вложения.', 500);
        }
        $stmt->close();

        messengerUpdateChatAfterChange($conn, (int) $chat['id'], array(
            'updated_at' => $deletedAt,
            'last_activity_at' => $deletedAt,
        ));

        $payload = array(
            'chat_uuid' => $chat['chat_uuid'],
            'message_uuid' => messengerFetchSingleValue($conn, 'SELECT message_uuid FROM messenger_messages WHERE id = ?', 'i', array((int) $attachment['message_id'])),
            'attachment_uuid' => $attachment['attachment_uuid'],
            'original_name' => $attachment['original_name'],
            'mime_type' => $attachment['mime_type'],
            'size_bytes' => (int) $attachment['size_bytes'],
            'deleted_at' => messengerToIso8601($deletedAt),
            'last_activity_at' => messengerToIso8601($deletedAt),
        );
        $event = messengerInsertEvent($conn, 'attachment', $attachment['attachment_uuid'], 'attachment_deleted', $payload, $deletedAt);
        messengerUpsertReadState($conn, (int) $chat['id'], messengerBuildReaderKey($actor), $actor['side'], $actor['user_id'] !== null ? (int) $actor['user_id'] : null, (string) ($actor['request_user_name'] ?? $actor['user_name'] ?? ''), (int) $event['event_id'], $deletedAt);
        messengerAudit($conn, $actor, 'attachment', $entityUuid, 'delete', 'ok');
        $conn->commit();

        return array(
            'entity_type' => 'attachment',
            'entity_uuid' => $entityUuid,
            'chat_uuid' => $chat['chat_uuid'],
            'deleted_at' => messengerToIso8601($deletedAt),
            'event_ids' => array((int) $event['event_id']),
        );
    } catch (Throwable $throwable) {
        $conn->rollback();
        throw $throwable;
    }
}

function messengerChangeStatus($conn, array $actor, $chatUuid, $status)
{
    if ($actor['side'] !== 'central') {
        throw new MessengerApiException('access_denied', 'Изменение статуса доступно только central admin.', 403);
    }

    $allowedStatuses = array('new', 'in_progress', 'done', 'closed');
    if (!in_array($status, $allowedStatuses, true)) {
        throw new MessengerApiException('validation_error', 'Недопустимый статус чата.', 422);
    }

    $chat = messengerFetchChatByUuid($conn, $chatUuid);
    messengerAssertChatAvailableForActor($conn, $chat, $actor, false);

    $currentStatus = (string) $chat['status'];
    $allowedTransitions = array(
        'new' => array('in_progress'),
        'in_progress' => array('done', 'closed'),
        'done' => array('in_progress', 'closed'),
        'closed' => array('in_progress'),
    );
    if ($currentStatus === $status) {
        return array(
            'chat_uuid' => $chatUuid,
            'status' => $status,
            'event_ids' => array(),
        );
    }

    if (!isset($allowedTransitions[$currentStatus]) || !in_array($status, $allowedTransitions[$currentStatus], true)) {
        throw new MessengerApiException('conflict_state', 'Недопустимый переход статуса чата.', 409);
    }

    $changedAt = messengerUtcNow();
    $conn->begin_transaction();
    try {
        messengerUpdateChatAfterChange($conn, (int) $chat['id'], array(
            'status' => $status,
            'closed_at' => $status === 'closed' ? $changedAt : null,
            'updated_at' => $changedAt,
            'last_activity_at' => $changedAt,
        ));
        $chat = messengerFetchChatByUuid($conn, $chatUuid);
        $event = messengerInsertEvent($conn, 'chat', $chatUuid, 'chat_status_changed', messengerBuildChatPayload($conn, $chat), $changedAt);
        messengerUpsertReadState($conn, (int) $chat['id'], 'central', 'central', null, (string) ($actor['request_user_name'] ?? $actor['user_name'] ?? 'central'), (int) $event['event_id'], $changedAt);
        messengerAudit($conn, $actor, 'chat', $chatUuid, 'status', 'ok');
        $conn->commit();

        return array(
            'chat_uuid' => $chatUuid,
            'status' => $status,
            'event_ids' => array((int) $event['event_id']),
        );
    } catch (Throwable $throwable) {
        $conn->rollback();
        throw $throwable;
    }
}

function messengerMarkRead($conn, array $actor, $chatUuid, $lastReadEventId)
{
    $chat = messengerFetchChatByUuid($conn, $chatUuid);
    messengerAssertChatAvailableForActor($conn, $chat, $actor, false);

    $latestEventId = messengerGetChatLatestEventId($conn, $chatUuid, true);
    if ($lastReadEventId < 0 || $lastReadEventId > $latestEventId) {
        throw new MessengerApiException('validation_error', 'Некорректный last_read_event_id.', 422);
    }

    if ($lastReadEventId > 0 && !messengerGetChatEventRow($conn, $chatUuid, $lastReadEventId, true)) {
        throw new MessengerApiException('validation_error', 'last_read_event_id не относится к этому чату.', 422);
    }

    $readAt = messengerUtcNow();
    $readerKey = messengerBuildReaderKey($actor);
    $userId = $actor['user_id'] !== null ? (int) $actor['user_id'] : null;
    $userName = (string) ($actor['request_user_name'] ?? $actor['user_name'] ?? '');

    $conn->begin_transaction();
    try {
        messengerUpsertReadState($conn, (int) $chat['id'], $readerKey, $actor['side'], $userId, $userName, $lastReadEventId, $readAt, 0);
        $payload = array(
            'chat_uuid' => $chatUuid,
            'reader_key' => $readerKey,
            'side' => $actor['side'],
            'user_id' => $userId,
            'user_name' => $userName,
            'last_read_event_id' => $lastReadEventId,
            'last_read_at' => messengerToIso8601($readAt),
            'manual_unread' => false,
        );
        $event = messengerInsertEvent($conn, 'read', messengerGenerateUuid(), 'chat_read_changed', $payload, $readAt);
        messengerAudit($conn, $actor, 'chat', $chatUuid, 'read', 'ok');
        $conn->commit();

        return array(
            'chat_uuid' => $chatUuid,
            'last_read_event_id' => $lastReadEventId,
            'last_read_at' => messengerToIso8601($readAt),
            'manual_unread' => false,
            'event_ids' => array((int) $event['event_id']),
        );
    } catch (Throwable $throwable) {
        $conn->rollback();
        throw $throwable;
    }
}

function messengerGetPreviousSignificantEventId($conn, $chatUuid, $eventId)
{
    $stmt = $conn->prepare("
        SELECT id
        FROM messenger_events
        WHERE id < ?
          AND (entity_uuid = ? OR JSON_UNQUOTE(JSON_EXTRACT(payload_json, '$.chat_uuid')) = ?)
          AND " . messengerBuildSignificantEventActionSql() . "
        ORDER BY id DESC
        LIMIT 1
    ");

    if (!$stmt) {
        throw new MessengerApiException('validation_error', 'Не удалось определить предыдущее событие чата.', 500);
    }

    $stmt->bind_param('iss', $eventId, $chatUuid, $chatUuid);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result ? $result->fetch_assoc() : null;
    $stmt->close();

    return (int) ($row['id'] ?? 0);
}

function messengerMarkUnread($conn, array $actor, $chatUuid)
{
    $chat = messengerFetchChatByUuid($conn, $chatUuid);
    messengerAssertChatAvailableForActor($conn, $chat, $actor, false);

    $latestEventId = messengerGetChatLatestEventId($conn, $chatUuid, true);
    if ($latestEventId <= 0) {
        throw new MessengerApiException('validation_error', 'В чате нет событий для пометки как непрочитанных.', 422);
    }

    $readRow = messengerFindReadRow($conn, (int) $chat['id'], messengerBuildReaderKey($actor));
    $targetReadEventId = isset($readRow['last_read_event_id']) ? (int) $readRow['last_read_event_id'] : null;
    $readAt = messengerUtcNow();
    $readerKey = messengerBuildReaderKey($actor);
    $userId = $actor['user_id'] !== null ? (int) $actor['user_id'] : null;
    $userName = (string) ($actor['request_user_name'] ?? $actor['user_name'] ?? '');

    $conn->begin_transaction();
    try {
        messengerUpsertReadState($conn, (int) $chat['id'], $readerKey, $actor['side'], $userId, $userName, $targetReadEventId, $readAt, 1);
        $payload = array(
            'chat_uuid' => $chatUuid,
            'reader_key' => $readerKey,
            'side' => $actor['side'],
            'user_id' => $userId,
            'user_name' => $userName,
            'last_read_event_id' => $targetReadEventId,
            'last_read_at' => messengerToIso8601($readAt),
            'marked_unread' => true,
            'manual_unread' => true,
        );
        $event = messengerInsertEvent($conn, 'read', messengerGenerateUuid(), 'chat_read_changed', $payload, $readAt);
        messengerAudit($conn, $actor, 'chat', $chatUuid, 'unread', 'ok');
        $conn->commit();

        return array(
            'chat_uuid' => $chatUuid,
            'last_read_event_id' => $targetReadEventId,
            'last_read_at' => messengerToIso8601($readAt),
            'unread_count' => messengerCountUnreadEvents($conn, $chatUuid, $targetReadEventId),
            'manual_unread' => true,
            'event_ids' => array((int) $event['event_id']),
        );
    } catch (Throwable $throwable) {
        $conn->rollback();
        throw $throwable;
    }
}

function messengerAddParticipant($conn, array $actor, $chatUuid, $userId)
{
    if ($actor['side'] !== 'site') {
        throw new MessengerApiException('access_denied', 'Добавление участника доступно только пользователю сайта.', 403);
    }

    $chat = messengerFetchChatByUuid($conn, $chatUuid);
    messengerAssertChatAvailableForActor($conn, $chat, $actor, true);
    messengerAssertActiveSiteParticipant($conn, (int) $chat['id'], (int) $actor['user_id']);

    $userRow = messengerRequireExistingUserWithPermission($conn, $userId);
    $existingParticipant = messengerFindParticipantRow($conn, (int) $chat['id'], $userId);
    if ($existingParticipant && !empty($existingParticipant['is_active'])) {
        throw new MessengerApiException('participant_already_active', 'Пользователь уже является активным участником чата.', 409);
    }

    $changedAt = messengerUtcNow();
    $conn->begin_transaction();
    try {
        if ($existingParticipant) {
            $stmt = $conn->prepare('UPDATE messenger_chat_participants SET is_active = 1, left_at = NULL, user_name = ?, updated_at = ? WHERE id = ?');
            if (!$stmt) {
                throw new MessengerApiException('validation_error', 'Не удалось реактивировать участника.', 500);
            }
            $participantName = messengerGetUserDisplayName($userRow);
            $participantId = (int) $existingParticipant['id'];
            $stmt->bind_param('ssi', $participantName, $changedAt, $participantId);
            if (!$stmt->execute()) {
                $stmt->close();
                throw new MessengerApiException('validation_error', 'Не удалось сохранить реактивацию участника.', 500);
            }
            $stmt->close();
        } else {
            messengerCreateParticipantRow($conn, (int) $chat['id'], $userRow, $actor, $changedAt);
        }

        messengerUpsertReadState($conn, (int) $chat['id'], 'site:' . (int) $userId, 'site', (int) $userId, messengerGetUserDisplayName($userRow), null, $changedAt);
        messengerUpdateChatAfterChange($conn, (int) $chat['id'], array(
            'updated_at' => $changedAt,
            'last_activity_at' => $changedAt,
        ));

        $payload = array(
            'chat_uuid' => $chatUuid,
            'user_id' => (int) $userId,
            'user_name' => messengerGetUserDisplayName($userRow),
            'is_active' => true,
            'updated_at' => messengerToIso8601($changedAt),
            'last_activity_at' => messengerToIso8601($changedAt),
        );
        $event = messengerInsertEvent($conn, 'participant', messengerGenerateUuid(), 'participant_added', $payload, $changedAt);
        messengerUpsertReadState($conn, (int) $chat['id'], 'site:' . (int) $actor['user_id'], 'site', (int) $actor['user_id'], (string) ($actor['request_user_name'] ?? $actor['user_name'] ?? ''), (int) $event['event_id'], $changedAt);
        messengerAudit($conn, $actor, 'chat', $chatUuid, 'participants_add', 'ok');
        $conn->commit();

        return array(
            'chat_uuid' => $chatUuid,
            'user_id' => (int) $userId,
            'user_name' => messengerGetUserDisplayName($userRow),
            'is_active' => true,
            'event_ids' => array((int) $event['event_id']),
        );
    } catch (Throwable $throwable) {
        $conn->rollback();
        throw $throwable;
    }
}

function messengerFindParticipantRow($conn, $chatId, $userId)
{
    $stmt = $conn->prepare('SELECT * FROM messenger_chat_participants WHERE chat_id = ? AND user_id = ? LIMIT 1');
    if (!$stmt) {
        return null;
    }

    $stmt->bind_param('ii', $chatId, $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result ? $result->fetch_assoc() : null;
    $stmt->close();

    return $row ?: null;
}

function messengerLeaveChat($conn, array $actor, $chatUuid)
{
    if ($actor['side'] !== 'site') {
        throw new MessengerApiException('access_denied', 'Выход из чата доступен только пользователю сайта.', 403);
    }

    $chat = messengerFetchChatByUuid($conn, $chatUuid);
    messengerAssertChatAvailableForActor($conn, $chat, $actor, false);
    $participantRow = messengerAssertActiveSiteParticipant($conn, (int) $chat['id'], (int) $actor['user_id']);

    $activeParticipants = messengerFetchActiveParticipants($conn, (int) $chat['id']);
    if (count($activeParticipants) <= 1) {
        throw new MessengerApiException('last_site_participant_required', 'Последний активный участник не может выйти из чата.', 409);
    }

    $leftAt = messengerUtcNow();
    $conn->begin_transaction();
    try {
        $stmt = $conn->prepare('UPDATE messenger_chat_participants SET is_active = 0, left_at = ?, updated_at = ? WHERE id = ?');
        if (!$stmt) {
            throw new MessengerApiException('validation_error', 'Не удалось сохранить выход из чата.', 500);
        }
        $participantId = (int) $participantRow['id'];
        $stmt->bind_param('ssi', $leftAt, $leftAt, $participantId);
        if (!$stmt->execute()) {
            $stmt->close();
            throw new MessengerApiException('validation_error', 'Не удалось обновить запись участника.', 500);
        }
        $stmt->close();

        messengerUpdateChatAfterChange($conn, (int) $chat['id'], array(
            'updated_at' => $leftAt,
            'last_activity_at' => $leftAt,
        ));
        $payload = array(
            'chat_uuid' => $chatUuid,
            'user_id' => (int) $actor['user_id'],
            'user_name' => (string) ($actor['request_user_name'] ?? $actor['user_name'] ?? ''),
            'is_active' => false,
            'updated_at' => messengerToIso8601($leftAt),
            'last_activity_at' => messengerToIso8601($leftAt),
        );
        $event = messengerInsertEvent($conn, 'participant', messengerGenerateUuid(), 'participant_left', $payload, $leftAt);
        messengerAudit($conn, $actor, 'chat', $chatUuid, 'leave', 'ok');
        $conn->commit();

        return array(
            'chat_uuid' => $chatUuid,
            'left_user_id' => (int) $actor['user_id'],
            'removed_from_active_list' => true,
            'event_ids' => array((int) $event['event_id']),
        );
    } catch (Throwable $throwable) {
        $conn->rollback();
        throw $throwable;
    }
}

function messengerListChatsForSiteUser($conn, array $actor, array $params)
{
    if ($actor['side'] !== 'site') {
        throw new MessengerApiException('access_denied', 'Доступно только пользователю сайта.', 403);
    }

    $limit = isset($params['limit']) ? max(1, min(50, (int) $params['limit'])) : 50;
    $cursorPayload = messengerDecodeCursor($params['cursor'] ?? '');
    $stmt = $conn->prepare('
        SELECT c.*
        FROM messenger_chats c
        INNER JOIN messenger_chat_participants p ON p.chat_id = c.id AND p.user_id = ? AND p.is_active = 1
        WHERE c.is_deleted = 0
        ORDER BY c.last_activity_at DESC, c.chat_no DESC
    ');
    if (!$stmt) {
        throw new MessengerApiException('validation_error', 'Не удалось загрузить список чатов.', 500);
    }
    $userId = (int) $actor['user_id'];
    $stmt->bind_param('i', $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $rows = $result ? $result->fetch_all(MYSQLI_ASSOC) : array();
    $stmt->close();

    if (is_array($cursorPayload) && isset($cursorPayload['last_activity_at'], $cursorPayload['chat_no'])) {
        $rows = array_values(array_filter($rows, function ($row) use ($cursorPayload) {
            $rowTime = (string) $row['last_activity_at'];
            if ($rowTime < (string) $cursorPayload['last_activity_at']) {
                return true;
            }
            if ($rowTime === (string) $cursorPayload['last_activity_at'] && (int) $row['chat_no'] < (int) $cursorPayload['chat_no']) {
                return true;
            }

            return false;
        }));
    }

    $settings = messengerGetSiteSettings($conn);
    $items = array();
    foreach (array_slice($rows, 0, $limit + 1) as $row) {
        $latestEventId = messengerGetChatLatestEventId($conn, (string) $row['chat_uuid'], true);
        $readRow = messengerFindReadRow($conn, (int) $row['id'], 'site:' . (int) $actor['user_id']);
        $lastReadEventId = (int) ($readRow['last_read_event_id'] ?? 0);
        $items[] = array(
            'chat_uuid' => (string) $row['chat_uuid'],
            'chat_no' => (int) $row['chat_no'],
            'title' => messengerGetChatTitle($conn, $row),
            'display_name' => messengerBuildDisplayName((string) $settings['site_code'], (int) $row['chat_no'], messengerGetChatTitle($conn, $row)),
            'status' => (string) $row['status'],
            'last_activity_at' => messengerToIso8601($row['last_activity_at']),
            'read_only' => (string) $row['status'] === 'closed',
            'unread_count' => messengerCountUnreadEvents($conn, (string) $row['chat_uuid'], $lastReadEventId),
            'manual_unread' => !empty($readRow['manual_unread']),
        );
    }

    $hasMore = count($items) > $limit;
    if ($hasMore) {
        array_pop($items);
    }

    $nextCursor = null;
    if ($hasMore && !empty($items)) {
        $lastItem = $items[count($items) - 1];
        $nextCursor = messengerEncodeCursor(array(
            'last_activity_at' => str_replace('T', ' ', substr((string) $lastItem['last_activity_at'], 0, 19)),
            'chat_no' => (int) $lastItem['chat_no'],
        ));
    }

    return array(
        'items' => $items,
        'next_cursor' => $nextCursor,
        'has_more' => $hasMore,
    );
}

function messengerGetChatView($conn, array $actor, array $params)
{
    if ($actor['side'] !== 'site') {
        throw new MessengerApiException('access_denied', 'Доступно только пользователю сайта.', 403);
    }

    $chatUuid = trim((string) ($params['chat_uuid'] ?? ''));
    if ($chatUuid === '') {
        throw new MessengerApiException('validation_error', 'chat_uuid обязателен.', 422);
    }

    $beforeMessageUuid = trim((string) ($params['before_message_uuid'] ?? ''));
    $focusMessageUuid = trim((string) ($params['focus_message_uuid'] ?? ''));
    $limit = isset($params['limit']) ? max(1, min(100, (int) $params['limit'])) : null;

    if ($beforeMessageUuid !== '' && $focusMessageUuid !== '') {
        throw new MessengerApiException('validation_error', 'Нельзя передавать before_message_uuid и focus_message_uuid одновременно.', 422);
    }

    $chat = messengerFetchChatByUuid($conn, $chatUuid);
    messengerAssertChatAvailableForActor($conn, $chat, $actor, false);

    $messages = messengerFetchMessagesByChatId($conn, (int) $chat['id']);
    $messageIndexMap = array();
    foreach ($messages as $index => $message) {
        $messageIndexMap[(string) $message['message_uuid']] = $index;
    }

    $sliceStart = 0;
    $sliceLength = count($messages);
    if ($focusMessageUuid !== '' && isset($messageIndexMap[$focusMessageUuid])) {
        $focusIndex = $messageIndexMap[$focusMessageUuid];
        $sliceStart = max(0, $focusIndex - 20);
        $sliceLength = 41;
    } elseif ($beforeMessageUuid !== '' && isset($messageIndexMap[$beforeMessageUuid])) {
        $beforeIndex = $messageIndexMap[$beforeMessageUuid];
        $effectiveLimit = $limit !== null ? min(100, $limit) : 50;
        $sliceStart = max(0, $beforeIndex - $effectiveLimit);
        $sliceLength = $beforeIndex - $sliceStart;
    } else {
        $effectiveLimit = $limit !== null ? min(50, $limit) : 50;
        $sliceStart = max(0, count($messages) - $effectiveLimit);
        $sliceLength = $effectiveLimit;
    }

    $messageSlice = array_slice($messages, $sliceStart, $sliceLength);
    $latestEventId = messengerGetChatLatestEventId($conn, $chatUuid, true);
    $readRowsByReaderKey = messengerFetchReadRowsByChatId($conn, (int) $chat['id']);
    $readRow = isset($readRowsByReaderKey['site:' . (int) $actor['user_id']]) ? $readRowsByReaderKey['site:' . (int) $actor['user_id']] : null;
    $lastReadEventId = (int) ($readRow['last_read_event_id'] ?? 0);
    $firstUnreadMessageUuid = null;
    if ($lastReadEventId < $latestEventId) {
        $firstUnreadMessageUuid = messengerGetFirstUnreadMessageUuid($conn, $chat, $lastReadEventId);
    }

    $participantRows = messengerFetchActiveParticipants($conn, (int) $chat['id']);
    $userIdsForDisplay = array();
    foreach ($participantRows as $participantRow) {
        if (isset($participantRow['user_id'])) {
            $userIdsForDisplay[] = (int) $participantRow['user_id'];
        }
    }
    foreach ($messageSlice as $message) {
        if ((string) ($message['author_side'] ?? '') === 'site' && isset($message['author_user_id']) && $message['author_user_id'] !== null) {
            $userIdsForDisplay[] = (int) $message['author_user_id'];
        }
    }
    $userDisplayNamesById = messengerGetUserDisplayNamesByIds($conn, $userIdsForDisplay);
    $messageEventIds = messengerFetchLatestMessageEventIds($conn, $chatUuid, array_map(function ($message) {
        return (string) ($message['message_uuid'] ?? '');
    }, $messageSlice));

    $participants = array();
    foreach ($participantRows as $participantRow) {
        $participantRead = isset($readRowsByReaderKey['site:' . (int) $participantRow['user_id']]) ? $readRowsByReaderKey['site:' . (int) $participantRow['user_id']] : null;
        $participants[] = array(
            'user_id' => (int) $participantRow['user_id'],
            'user_name' => messengerResolveStoredSiteUserName(
                $userDisplayNamesById,
                'site',
                $participantRow['user_id'] ?? null,
                (string) $participantRow['user_name']
            ),
            'last_read_event_id' => isset($participantRead['last_read_event_id']) ? (int) $participantRead['last_read_event_id'] : null,
            'last_read_at' => isset($participantRead['last_read_at']) ? messengerToIso8601($participantRead['last_read_at']) : null,
        );
    }
    $chatReaders = messengerBuildChatReaders($participants, $readRowsByReaderKey, $userDisplayNamesById);

    $messageItems = array();
    foreach ($messageSlice as $message) {
        $messageUuid = (string) $message['message_uuid'];
        $messageEventId = (int) ($messageEventIds[$messageUuid] ?? 0);
        $attachments = array();
        foreach (messengerFetchAttachmentsByMessageId($conn, (int) $message['id']) as $attachmentRow) {
            $attachments[] = array(
                'attachment_uuid' => (string) $attachmentRow['attachment_uuid'],
                'original_name' => !empty($attachmentRow['is_deleted']) ? 'Вложение удалено' : (string) $attachmentRow['original_name'],
                'mime_type' => (string) $attachmentRow['mime_type'],
                'size_bytes' => (int) $attachmentRow['size_bytes'],
                'is_deleted' => !empty($attachmentRow['is_deleted']),
            );
        }

        $readBy = array();
        if ($messageEventId > 0) {
            foreach ($participants as $participant) {
                $participantUserId = (int) ($participant['user_id'] ?? 0);
                if ((string) ($message['author_side'] ?? '') === 'site' && $participantUserId > 0 && $participantUserId === (int) ($message['author_user_id'] ?? 0)) {
                    continue;
                }

                $participantLastReadEventId = isset($participant['last_read_event_id']) ? (int) $participant['last_read_event_id'] : 0;
                if ($participantLastReadEventId < $messageEventId) {
                    continue;
                }

                $readBy[] = array(
                    'user_id' => $participantUserId,
                    'user_name' => (string) ($participant['user_name'] ?? ''),
                    'last_read_at' => $participant['last_read_at'] ?? null,
                );
            }
        }

        $messageItems[] = array(
            'message_uuid' => $messageUuid,
            'author_side' => (string) $message['author_side'],
            'author_user_id' => $message['author_user_id'] !== null ? (int) $message['author_user_id'] : null,
            'author_user_name' => messengerResolveStoredSiteUserName(
                $userDisplayNamesById,
                (string) $message['author_side'],
                $message['author_user_id'] ?? null,
                (string) $message['author_user_name']
            ),
            'body_text' => !empty($message['is_deleted']) ? 'Сообщение удалено' : (string) $message['body_text'],
            'is_deleted' => !empty($message['is_deleted']),
            'is_edited' => ((int) messengerFetchSingleValue($conn, 'SELECT COUNT(*) FROM messenger_message_edits WHERE message_id = ?', 'i', array((int) $message['id']))) > 0,
            'created_at' => messengerToIso8601($message['created_at']),
            'updated_at' => messengerToIso8601($message['updated_at']),
            'attachments' => $attachments,
            'read_by' => $readBy,
        );
    }

    $title = messengerGetChatTitle($conn, $chat);
    $settings = messengerGetSiteSettings($conn);
    $hasMoreOlder = $sliceStart > 0;
    $nextBeforeMessageUuid = $hasMoreOlder ? (string) $messageSlice[0]['message_uuid'] : null;

    return array(
        'chat_uuid' => $chatUuid,
        'chat_no' => (int) $chat['chat_no'],
        'title' => $title,
        'display_name' => messengerBuildDisplayName((string) $settings['site_code'], (int) $chat['chat_no'], $title),
        'status' => (string) $chat['status'],
        'read_only' => (string) $chat['status'] === 'closed',
        'manual_unread' => !empty($readRow['manual_unread']),
        'participants' => $participants,
        'readers' => $chatReaders,
        'messages' => $messageItems,
        'latest_event_id' => $latestEventId,
        'first_unread_message_uuid' => $firstUnreadMessageUuid,
        'has_more_older' => $hasMoreOlder,
        'next_before_message_uuid' => $nextBeforeMessageUuid,
        'focus_message_uuid' => $focusMessageUuid !== '' ? $focusMessageUuid : null,
    );
}

function messengerSearchMessages($conn, array $actor, array $params)
{
    if ($actor['side'] !== 'site') {
        throw new MessengerApiException('access_denied', 'Доступно только пользователю сайта.', 403);
    }

    $query = trim((string) ($params['q'] ?? ''));
    if (mb_strlen($query, 'UTF-8') < 2 || mb_strlen($query, 'UTF-8') > 100) {
        throw new MessengerApiException('validation_error', 'Длина поискового запроса должна быть от 2 до 100 символов.', 422);
    }

    $limit = isset($params['limit']) ? max(1, min(50, (int) $params['limit'])) : 50;
    $cursorPayload = messengerDecodeCursor($params['cursor'] ?? '');

    $stmt = $conn->prepare('
        SELECT m.*, c.chat_uuid, c.chat_no
        FROM messenger_messages m
        INNER JOIN messenger_chats c ON c.id = m.chat_id AND c.is_deleted = 0
        INNER JOIN messenger_chat_participants p ON p.chat_id = c.id AND p.user_id = ? AND p.is_active = 1
        WHERE m.is_deleted = 0 AND m.body_text LIKE ?
        ORDER BY m.created_at DESC, m.message_uuid DESC
    ');
    if (!$stmt) {
        throw new MessengerApiException('validation_error', 'Не удалось выполнить поиск по сообщениям.', 500);
    }
    $userId = (int) $actor['user_id'];
    $likeQuery = '%' . $query . '%';
    $stmt->bind_param('is', $userId, $likeQuery);
    $stmt->execute();
    $result = $stmt->get_result();
    $rows = $result ? $result->fetch_all(MYSQLI_ASSOC) : array();
    $stmt->close();

    if (is_array($cursorPayload) && isset($cursorPayload['created_at'], $cursorPayload['message_uuid'])) {
        $rows = array_values(array_filter($rows, function ($row) use ($cursorPayload) {
            $createdAt = (string) $row['created_at'];
            if ($createdAt < (string) $cursorPayload['created_at']) {
                return true;
            }
            if ($createdAt === (string) $cursorPayload['created_at'] && strcmp((string) $row['message_uuid'], (string) $cursorPayload['message_uuid']) < 0) {
                return true;
            }

            return false;
        }));
    }

    $userDisplayNamesById = messengerGetUserDisplayNamesByIds($conn, array_map(function ($row) {
        return isset($row['author_user_id']) && (string) ($row['author_side'] ?? '') === 'site'
            ? (int) $row['author_user_id']
            : 0;
    }, array_slice($rows, 0, $limit + 1)));
    $settings = messengerGetSiteSettings($conn);
    $items = array();
    foreach (array_slice($rows, 0, $limit + 1) as $row) {
        $title = messengerGetChatTitle($conn, array(
            'first_message_id' => messengerFetchSingleValue($conn, 'SELECT first_message_id FROM messenger_chats WHERE id = ?', 'i', array((int) $row['chat_id'])),
        ));
        $items[] = array(
            'chat_uuid' => (string) $row['chat_uuid'],
            'message_uuid' => (string) $row['message_uuid'],
            'display_name' => messengerBuildDisplayName((string) $settings['site_code'], (int) $row['chat_no'], $title),
            'author_user_name' => messengerResolveStoredSiteUserName(
                $userDisplayNamesById,
                (string) $row['author_side'],
                $row['author_user_id'] ?? null,
                (string) $row['author_user_name']
            ),
            'created_at' => messengerToIso8601($row['created_at']),
            'snippet' => (string) $row['body_text'],
            'focus_message_uuid' => (string) $row['message_uuid'],
        );
    }

    $hasMore = count($items) > $limit;
    if ($hasMore) {
        array_pop($items);
    }

    $nextCursor = null;
    if ($hasMore && !empty($items)) {
        $lastItem = $items[count($items) - 1];
        $nextCursor = messengerEncodeCursor(array(
            'created_at' => str_replace('T', ' ', substr((string) $lastItem['created_at'], 0, 19)),
            'message_uuid' => $lastItem['message_uuid'],
        ));
    }

    return array(
        'items' => $items,
        'next_cursor' => $nextCursor,
        'has_more' => $hasMore,
    );
}

function messengerGetHistory($conn, array $actor, $messageUuid)
{
    if ($actor['side'] !== 'central') {
        throw new MessengerApiException('access_denied', 'История правок доступна только central admin.', 403);
    }

    $message = messengerFetchMessageByUuid($conn, $messageUuid);
    $stmt = $conn->prepare('SELECT * FROM messenger_message_edits WHERE message_id = ? ORDER BY version_no ASC');
    if (!$stmt) {
        throw new MessengerApiException('validation_error', 'Не удалось загрузить историю правок.', 500);
    }
    $messageId = (int) $message['id'];
    $stmt->bind_param('i', $messageId);
    $stmt->execute();
    $result = $stmt->get_result();
    $rows = $result ? $result->fetch_all(MYSQLI_ASSOC) : array();
    $stmt->close();

    $items = array();
    foreach ($rows as $row) {
        $items[] = array(
            'version_no' => (int) $row['version_no'],
            'previous_body_text' => (string) $row['previous_body_text'],
            'edited_by_side' => (string) $row['edited_by_side'],
            'edited_by_user_id' => $row['edited_by_user_id'] !== null ? (int) $row['edited_by_user_id'] : null,
            'edited_by_user_name' => (string) $row['edited_by_user_name'],
            'edited_at' => messengerToIso8601($row['edited_at']),
        );
    }

    return array(
        'message_uuid' => $messageUuid,
        'items' => $items,
    );
}

function messengerPullEvents($conn, array $params)
{
    $cursor = isset($params['cursor']) ? max(0, (int) $params['cursor']) : 0;
    $limit = isset($params['limit']) ? max(1, min(200, (int) $params['limit'])) : 200;
    $settings = messengerGetSiteSettings($conn);

    $stmt = $conn->prepare('SELECT * FROM messenger_events WHERE id > ? ORDER BY id ASC LIMIT ?');
    if (!$stmt) {
        throw new MessengerApiException('validation_error', 'Не удалось загрузить события синхронизации.', 500);
    }
    $stmt->bind_param('ii', $cursor, $limit);
    $stmt->execute();
    $result = $stmt->get_result();
    $rows = $result ? $result->fetch_all(MYSQLI_ASSOC) : array();
    $stmt->close();

    $events = array();
    $nextCursor = $cursor;
    foreach ($rows as $row) {
        $nextCursor = (int) $row['id'];
        $payload = json_decode((string) $row['payload_json'], true);
        if (is_array($payload)) {
            $payload = messengerAugmentEventPayloadWithChatDisplay(
                $conn,
                (string) $row['action'],
                (string) $row['entity_uuid'],
                $payload
            );
        }
        $events[] = array(
            'event_id' => (int) $row['id'],
            'event_uuid' => (string) $row['event_uuid'],
            'entity_type' => (string) $row['entity_type'],
            'entity_uuid' => (string) $row['entity_uuid'],
            'action' => (string) $row['action'],
            'created_at' => messengerToIso8601($row['created_at']),
            'payload' => $payload,
        );
    }

    return array(
        'site_uuid' => (string) $settings['site_uuid'],
        'site_code' => (string) $settings['site_code'],
        'display_site_code' => messengerGetDisplaySiteCode((string) $settings['site_code']),
        'next_cursor' => $nextCursor,
        'events' => $events,
    );
}

function messengerGetSiteInfo($conn)
{
    $settings = messengerGetSiteSettings($conn);
    return array(
        'site_uuid' => (string) $settings['site_uuid'],
        'site_name' => (string) $settings['site_name'],
        'site_code' => (string) $settings['site_code'],
        'display_site_code' => messengerGetDisplaySiteCode((string) $settings['site_code']),
    );
}

function messengerDownloadAttachment($conn, array $actor, $attachmentUuid)
{
    $attachment = messengerFetchAttachmentByUuid($conn, $attachmentUuid);
    if (!empty($attachment['is_deleted'])) {
        throw new MessengerApiException('attachment_deleted', 'Вложение удалено.', 409);
    }

    $chatUuid = messengerFetchSingleValue($conn, 'SELECT chat_uuid FROM messenger_chats WHERE id = ?', 'i', array((int) $attachment['chat_id']));
    $chat = messengerFetchChatByUuid($conn, (string) $chatUuid);
    messengerAssertChatAvailableForActor($conn, $chat, $actor, false);

    $fullPath = messengerGetStorageRoot() . '/' . ltrim((string) $attachment['storage_path'], '/');
    if (!is_file($fullPath)) {
        throw new MessengerApiException('validation_error', 'Файл вложения не найден на диске.', 404);
    }

    return array(
        'path' => $fullPath,
        'download_name' => (string) $attachment['original_name'],
        'mime_type' => (string) $attachment['mime_type'],
        'size_bytes' => (int) $attachment['size_bytes'],
    );
}

function messengerFetchSingleValue($conn, $sql, $types, array $values)
{
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        return null;
    }

    if ($types !== '') {
        bindDynamicParams($stmt, $types, $values);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result ? $result->fetch_row() : null;
    $stmt->close();

    return is_array($row) ? $row[0] : null;
}
