<?php

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

function e($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function redirectTo($path, $statusCode = 302)
{
    header('Location: ' . $path, true, (int) $statusCode);
    exit();
}

function getIntFromGet($key)
{
    if (!isset($_GET[$key])) {
        return null;
    }

    $value = filter_var($_GET[$key], FILTER_VALIDATE_INT, array('options' => array('min_range' => 1)));
    return $value === false ? null : $value;
}

function getIntFromPost($key)
{
    if (!isset($_POST[$key])) {
        return null;
    }

    $value = filter_var($_POST[$key], FILTER_VALIDATE_INT, array('options' => array('min_range' => 1)));
    return $value === false ? null : $value;
}

function getCsrfToken()
{
    if (empty($_SESSION['csrf_token']) || !is_string($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

function isValidCsrfToken($token)
{
    return is_string($token)
        && isset($_SESSION['csrf_token'])
        && is_string($_SESSION['csrf_token'])
        && hash_equals($_SESSION['csrf_token'], $token);
}

function requireCsrfToken()
{
    $token = $_POST['csrf_token'] ?? null;

    if ($token === null || $token === '') {
        $headerCandidates = array(
            $_SERVER['HTTP_X_CSRF_TOKEN'] ?? null,
            $_SERVER['REDIRECT_HTTP_X_CSRF_TOKEN'] ?? null,
        );

        if (function_exists('getallheaders')) {
            $headers = getallheaders();
            if (is_array($headers)) {
                foreach ($headers as $name => $value) {
                    if (strtolower((string) $name) === 'x-csrf-token') {
                        $headerCandidates[] = $value;
                    }
                }
            }
        }

        foreach ($headerCandidates as $candidate) {
            if (is_string($candidate) && $candidate !== '') {
                $token = $candidate;
                break;
            }
        }
    }

    if (!isValidCsrfToken($token)) {
        http_response_code(403);
        exit('Недействительный CSRF-токен.');
    }
}

function bindDynamicParams($stmt, $types, array $values)
{
    $params = array();
    $params[] = &$types;

    foreach ($values as $index => $value) {
        $params[] = &$values[$index];
    }

    call_user_func_array(array($stmt, 'bind_param'), $params);
}

function formatRuDate($dateValue)
{
    $dateObject = date_create((string) $dateValue);
    if (!$dateObject) {
        return '';
    }

    $monthNames = array(
        1 => 'января',
        2 => 'февраля',
        3 => 'марта',
        4 => 'апреля',
        5 => 'мая',
        6 => 'июня',
        7 => 'июля',
        8 => 'августа',
        9 => 'сентября',
        10 => 'октября',
        11 => 'ноября',
        12 => 'декабря',
    );

    $day = date_format($dateObject, 'j');
    $month = (int) date_format($dateObject, 'n');
    $year = date_format($dateObject, 'Y');

    if (!isset($monthNames[$month])) {
        return '';
    }

    return $day . ' ' . $monthNames[$month] . ' ' . $year;
}

function normalizeNewsType($type)
{
    $normalized = mb_strtolower(trim((string) $type), 'UTF-8');
    if ($normalized === 'новость' || $normalized === 'мероприятие') {
        return $normalized;
    }

    return null;
}

function getNewsTypeLabel($type)
{
    $normalized = normalizeNewsType($type);
    if ($normalized === 'новость') {
        return 'Новость';
    }

    if ($normalized === 'мероприятие') {
        return 'Мероприятие';
    }

    return '';
}

function sanitizeStoredFilename($filename)
{
    $clean = basename((string) $filename);
    return preg_replace('/[^A-Za-z0-9._-]/', '', $clean);
}

function parseIniSizeToBytes($value)
{
    if (is_int($value) || is_float($value)) {
        return max(0, (int) $value);
    }

    $normalized = trim((string) $value);
    if ($normalized === '') {
        return 0;
    }

    $unit = strtolower(substr($normalized, -1));
    $number = (float) $normalized;

    switch ($unit) {
        case 'g':
            $number *= 1024;
        case 'm':
            $number *= 1024;
        case 'k':
            $number *= 1024;
            break;
    }

    return max(0, (int) round($number));
}

function isRequestBodyExceedingPostLimit()
{
    $requestMethod = strtoupper((string) ($_SERVER['REQUEST_METHOD'] ?? 'GET'));
    if ($requestMethod !== 'POST') {
        return false;
    }

    $contentLength = (int) ($_SERVER['CONTENT_LENGTH'] ?? 0);
    if ($contentLength <= 0) {
        return false;
    }

    if (!empty($_POST) || !empty($_FILES)) {
        return false;
    }

    $postMaxSize = parseIniSizeToBytes(ini_get('post_max_size'));
    if ($postMaxSize <= 0) {
        return false;
    }

    return $contentLength > $postMaxSize;
}

function getSafeIframeHtml($html)
{
    $html = trim((string) $html);
    if ($html === '' || stripos($html, '<iframe') === false) {
        return '';
    }

    if (!class_exists('DOMDocument')) {
        return '';
    }

    $wrappedHtml = '<!DOCTYPE html><html><body>' . $html . '</body></html>';
    $dom = new DOMDocument();
    libxml_use_internal_errors(true);
    $loaded = $dom->loadHTML($wrappedHtml, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
    libxml_clear_errors();

    if (!$loaded) {
        return '';
    }

    $iframes = $dom->getElementsByTagName('iframe');
    if ($iframes->length === 0) {
        return '';
    }

    $iframe = $iframes->item(0);
    $src = trim((string) $iframe->getAttribute('src'));
    if (!preg_match('#^https?://#i', $src)) {
        return '';
    }

    $safeAttrs = array(
        'src' => $src,
        'title' => trim((string) $iframe->getAttribute('title')),
        'width' => trim((string) $iframe->getAttribute('width')),
        'height' => trim((string) $iframe->getAttribute('height')),
        'allow' => trim((string) $iframe->getAttribute('allow')),
        'loading' => trim((string) $iframe->getAttribute('loading')),
        'referrerpolicy' => trim((string) $iframe->getAttribute('referrerpolicy')),
    );

    $parts = array();
    foreach ($safeAttrs as $name => $value) {
        if ($value !== '') {
            $parts[] = $name . '="' . e($value) . '"';
        }
    }

    if ($iframe->hasAttribute('allowfullscreen')) {
        $parts[] = 'allowfullscreen';
    }

    if (!in_array('loading="lazy"', $parts, true)) {
        $parts[] = 'loading="lazy"';
    }

    return '<iframe ' . implode(' ', $parts) . '></iframe>';
}

function sanitizeNewsInput($value)
{
    return trim((string) $value);
}
