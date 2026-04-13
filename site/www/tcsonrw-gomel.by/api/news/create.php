<?php

require_once __DIR__ . '/_bootstrap.php';

newsApiRequireMethod('POST');

$payload = newsApiReadInput();
$actor = newsApiResolveActor($conn, $payload);
$uploadedPhotos = $_FILES['photos'] ?? null;
$uploadedVideo = $_FILES['video'] ?? null;

try {
    $created = createNewsEntry($conn, $payload, $actor['author'], $uploadedPhotos, $uploadedVideo);
    $news = $created['news'] ?? array();
    $photos = $created['photos'] ?? array();

    newsApiRespondSuccess(
        newsApiBuildNewsPayload(
            $news,
            $photos,
            $actor,
            array(
                'url' => (string) ($created['url'] ?? ''),
                'slug' => (string) ($created['slug'] ?? ($news['slug'] ?? '')),
            )
        ),
        201
    );
} catch (InvalidArgumentException $e) {
    newsApiRespondError('validation_error', $e->getMessage(), 422);
} catch (Throwable $e) {
    error_log('news api create fatal: ' . $e->getMessage());
    newsApiRespondError('internal_error', 'Внутренняя ошибка сервера.', 500);
}
