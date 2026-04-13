<?php

require_once __DIR__ . '/_bootstrap.php';

function newsApiResolveTargetNews($conn, array $payload)
{
    $newsId = filter_var($payload['id'] ?? null, FILTER_VALIDATE_INT, array('options' => array('min_range' => 1)));
    if ($newsId !== false && $newsId !== null) {
        return fetchNewsById($conn, (int) $newsId);
    }

    $lookupSlug = sanitizeNewsInput($payload['lookup_slug'] ?? $payload['current_slug'] ?? '');
    if ($lookupSlug === '' && !array_key_exists('id', $payload)) {
        $lookupSlug = sanitizeNewsInput($payload['slug'] ?? '');
    }

    if ($lookupSlug !== '') {
        return fetchNewsBySlug($conn, $lookupSlug);
    }

    return null;
}

newsApiRequireMethod('POST');

$payload = newsApiReadInput();
$actor = newsApiResolveActor($conn, $payload);
$uploadedPhotos = $_FILES['photos'] ?? null;
$uploadedVideo = $_FILES['video'] ?? null;
$targetNews = newsApiResolveTargetNews($conn, $payload);
$createIfMissing = normalizeDeleteVideoFlag($payload['create_if_missing'] ?? null);

try {
    if ($targetNews === null) {
        if (!$createIfMissing) {
            newsApiRespondError('not_found', 'Событие для редактирования не найдено. Передайте id или lookup_slug.', 404);
        }

        $created = createNewsEntry($conn, $payload, $actor['author'], $uploadedPhotos, $uploadedVideo);
        $createdNews = $created['news'] ?? array();
        $createdPhotos = $created['photos'] ?? array();

        newsApiRespondSuccess(
            array_merge(
                array('operation' => 'created'),
                newsApiBuildNewsPayload(
                    $createdNews,
                    $createdPhotos,
                    $actor,
                    array(
                        'url' => (string) ($created['url'] ?? ''),
                        'slug' => (string) ($created['slug'] ?? ($createdNews['slug'] ?? '')),
                    )
                )
            ),
            201
        );
    }

    $updated = updateNewsEntry($conn, (int) ($targetNews['id'] ?? 0), $payload, $uploadedPhotos, $uploadedVideo);
    $updatedNews = $updated['news'] ?? array();
    $updatedPhotos = $updated['photos'] ?? array();

    newsApiRespondSuccess(
        array_merge(
            array('operation' => 'updated'),
            newsApiBuildNewsPayload(
                $updatedNews,
                $updatedPhotos,
                $actor,
                array(
                    'url' => (string) ($updated['url'] ?? ''),
                    'slug' => (string) ($updated['slug'] ?? ($updatedNews['slug'] ?? '')),
                )
            )
        ),
        200
    );
} catch (InvalidArgumentException $e) {
    newsApiRespondError('validation_error', $e->getMessage(), 422);
} catch (Throwable $e) {
    error_log('news api update fatal: ' . $e->getMessage());
    newsApiRespondError('internal_error', 'Внутренняя ошибка сервера.', 500);
}
