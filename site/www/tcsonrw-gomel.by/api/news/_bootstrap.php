<?php

header('Content-Type: application/json; charset=UTF-8');
header('X-Robots-Tag: noindex, nofollow', true);
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Accept, X-Requested-With, X-Service-Token, Authorization, X-CSRF-Token');
header('Access-Control-Max-Age: 600');

if (strtoupper((string) ($_SERVER['REQUEST_METHOD'] ?? 'GET')) === 'OPTIONS') {
    http_response_code(204);
    exit();
}

require_once dirname(__DIR__, 2) . '/lib/security.php';
require_once dirname(__DIR__, 2) . '/lib/auth.php';
require_once dirname(__DIR__, 2) . '/lib/news_management.php';
include dirname(__DIR__, 2) . '/db_connection.php';

function newsApiRespond(array $payload, $httpStatus = 200)
{
    http_response_code((int) $httpStatus);
    echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit();
}

function newsApiRespondSuccess(array $data, $httpStatus = 200)
{
    newsApiRespond(
        array(
            'ok' => true,
            'data' => $data,
            'error' => null,
        ),
        $httpStatus
    );
}

function newsApiRespondError($code, $message, $httpStatus = 400, array $details = array())
{
    newsApiRespond(
        array(
            'ok' => false,
            'data' => null,
            'error' => array(
                'code' => $code,
                'message' => $message,
                'details' => (object) $details,
            ),
        ),
        $httpStatus
    );
}

function newsApiAbortIfOversizedPost()
{
    if (!isRequestBodyExceedingPostLimit()) {
        return;
    }

    newsApiRespondError(
        'payload_too_large',
        'Размер загружаемых файлов превышает лимит сервера. Уменьшите видео или увеличьте post_max_size/upload_max_filesize.',
        413
    );
}

function newsApiRequireMethod($method)
{
    if (strtoupper((string) ($_SERVER['REQUEST_METHOD'] ?? 'GET')) !== strtoupper((string) $method)) {
        newsApiRespondError('method_not_allowed', 'Метод не поддерживается.', 405);
    }
}

function newsApiLoadServiceConfig()
{
    $configPath = dirname(__DIR__) . '/service/config.local.php';
    if (!is_file($configPath)) {
        newsApiRespondError('config_missing', 'Отсутствует локальная конфигурация service API.', 500);
    }

    $config = require $configPath;
    if (!is_array($config)) {
        newsApiRespondError('config_invalid', 'Некорректная конфигурация service API.', 500);
    }

    return $config;
}

function newsApiGetRequestToken()
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

function newsApiReadInput()
{
    $contentType = strtolower(trim((string) ($_SERVER['CONTENT_TYPE'] ?? '')));
    if (strpos($contentType, 'application/json') !== false) {
        $raw = file_get_contents('php://input');
        if (!is_string($raw) || trim($raw) === '') {
            newsApiRespondError('validation_error', 'Ожидается JSON-тело запроса.', 422);
        }

        $payload = json_decode($raw, true);
        if (!is_array($payload)) {
            newsApiRespondError('validation_error', 'Некорректный JSON.', 422);
        }

        return $payload;
    }

    return $_POST;
}

newsApiAbortIfOversizedPost();

function newsApiResolveActor($conn, array $payload)
{
    $requestToken = newsApiGetRequestToken();
    if ($requestToken !== '') {
        $config = newsApiLoadServiceConfig();
        $expectedToken = trim((string) ($config['service_token'] ?? ''));
        if ($expectedToken === '' || !hash_equals($expectedToken, $requestToken)) {
            newsApiRespondError('access_denied', 'Недействительный service token.', 403);
        }

        $authorLogin = sanitizeNewsInput($payload['author_login'] ?? 'api-service');
        $authorName = sanitizeNewsInput($payload['author_name'] ?? 'API Service');
        $authorUserId = filter_var($payload['author_user_id'] ?? null, FILTER_VALIDATE_INT, array('options' => array('min_range' => 1)));

        return array(
            'mode' => 'service_token',
            'author' => array(
                'user_id' => $authorUserId === false ? null : (int) $authorUserId,
                'login' => $authorLogin !== '' ? $authorLogin : 'api-service',
                'name' => $authorName !== '' ? $authorName : 'API Service',
            ),
        );
    }

    if (!isLoggedIn() || !hasPermission('news')) {
        newsApiRespondError('access_denied', 'Требуется service token или авторизованная сессия с правом news.', 403);
    }

    requireCsrfToken();

    $currentUser = getCurrentUserByLogin($conn);
    $userId = isset($currentUser['id']) ? (int) $currentUser['id'] : null;
    $userLogin = (string) ($currentUser['login'] ?? ($_SESSION['login'] ?? ''));
    $userName = (string) ($currentUser['full_name'] ?? $currentUser['fio'] ?? $userLogin);

    return array(
        'mode' => 'session',
        'author' => array(
            'user_id' => $userId,
            'login' => $userLogin,
            'name' => $userName,
        ),
    );
}

function newsApiBuildAbsoluteUrl($path)
{
    $path = (string) $path;
    if ($path === '' || preg_match('#^https?://#i', $path)) {
        return $path;
    }

    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = trim((string) ($_SERVER['HTTP_HOST'] ?? ''));
    if ($host === '') {
        return $path;
    }

    return $scheme . '://' . $host . $path;
}

function newsApiBuildPhotoItems(array $photos)
{
    $photoItems = array();
    foreach ($photos as $photo) {
        $safeFilename = sanitizeStoredFilename($photo['filename'] ?? '');
        if ($safeFilename === '') {
            continue;
        }

        $relativeUrl = '/photos/' . $safeFilename;
        $photoItems[] = array(
            'id' => (int) ($photo['id'] ?? 0),
            'filename' => $safeFilename,
            'sort_order' => isset($photo['sort_order']) ? (int) $photo['sort_order'] : null,
            'url' => $relativeUrl,
            'public_url' => newsApiBuildAbsoluteUrl($relativeUrl),
        );
    }

    return $photoItems;
}

function newsApiBuildVideoItem($news)
{
    $videoFilename = getNewsVideoFilename($news);
    if ($videoFilename === null) {
        return null;
    }

    $relativeUrl = '/videos/' . $videoFilename;
    return array(
        'filename' => $videoFilename,
        'url' => $relativeUrl,
        'public_url' => newsApiBuildAbsoluteUrl($relativeUrl),
    );
}

function newsApiBuildNewsPayload($news, array $photos, array $actor, array $context = array())
{
    $relativeNewsUrl = (string) ($context['url'] ?? buildNewsUrl($news));
    $photoItems = newsApiBuildPhotoItems($photos);
    $videoItem = newsApiBuildVideoItem($news);

    return array(
        'id' => (int) ($news['id'] ?? 0),
        'type' => normalizeNewsType($news['type'] ?? '') ?: (string) ($news['type'] ?? ''),
        'title' => (string) ($news['title'] ?? ''),
        'slug' => (string) ($context['slug'] ?? ($news['slug'] ?? '')),
        'description' => (string) ($news['description'] ?? ''),
        'freim' => (string) ($news['freim'] ?? ''),
        'date' => (string) ($news['date'] ?? ''),
        'url' => $relativeNewsUrl,
        'public_url' => newsApiBuildAbsoluteUrl($relativeNewsUrl),
        'photo_count' => count($photoItems),
        'photos' => $photoItems,
        'video' => $videoItem,
        'has_video' => $videoItem !== null,
        'created_by' => array(
            'user_id' => isset($news['created_by_user_id']) && $news['created_by_user_id'] !== null ? (int) $news['created_by_user_id'] : null,
            'login' => (string) ($news['created_by_login'] ?? ''),
            'name' => (string) ($news['created_by_name'] ?? ''),
        ),
        'auth_mode' => (string) ($actor['mode'] ?? ''),
    );
}
