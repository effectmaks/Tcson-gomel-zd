<?php

function ensurePhotoSortInfrastructure($conn)
{
    $result = $conn->query("SHOW COLUMNS FROM photos LIKE 'sort_order'");
    $columnExists = $result instanceof mysqli_result && $result->num_rows > 0;
    if ($result instanceof mysqli_result) {
        $result->free();
    }

    if (!$columnExists) {
        if (!$conn->query('ALTER TABLE photos ADD COLUMN sort_order INT NULL AFTER news_id')) {
            throw new RuntimeException('Не удалось подготовить таблицу photos для сохранения порядка.');
        }
    }

    $nullCountResult = $conn->query('SELECT COUNT(*) AS total_nulls FROM photos WHERE sort_order IS NULL');
    $nullCount = 0;
    if ($nullCountResult instanceof mysqli_result) {
        $nullCount = (int) ($nullCountResult->fetch_assoc()['total_nulls'] ?? 0);
        $nullCountResult->free();
    }

    if (!$columnExists || $nullCount > 0) {
        normalizePhotoSortOrders($conn);
    }
}

function normalizePhotoSortOrders($conn, $newsId = null)
{
    if ($newsId !== null) {
        $stmt = $conn->prepare(
            'SELECT id, news_id
             FROM photos
             WHERE news_id = ?
             ORDER BY CASE WHEN sort_order IS NULL THEN 1 ELSE 0 END ASC, sort_order ASC, id ASC'
        );
        if (!$stmt) {
            return;
        }

        $stmt->bind_param('i', $newsId);
    } else {
        $stmt = $conn->prepare(
            'SELECT id, news_id
             FROM photos
             ORDER BY news_id ASC, CASE WHEN sort_order IS NULL THEN 1 ELSE 0 END ASC, sort_order ASC, id ASC'
        );
        if (!$stmt) {
            return;
        }
    }

    $stmt->execute();
    $result = $stmt->get_result();
    $rows = $result ? $result->fetch_all(MYSQLI_ASSOC) : array();
    $stmt->close();

    if (empty($rows)) {
        return;
    }

    $updateStmt = $conn->prepare('UPDATE photos SET sort_order = ? WHERE id = ?');
    if (!$updateStmt) {
        return;
    }

    $currentNewsId = null;
    $position = 0;

    foreach ($rows as $row) {
        $rowNewsId = (int) ($row['news_id'] ?? 0);
        if ($rowNewsId !== $currentNewsId) {
            $currentNewsId = $rowNewsId;
            $position = 1;
        } else {
            $position++;
        }

        $photoId = (int) ($row['id'] ?? 0);
        if ($photoId <= 0) {
            continue;
        }

        $updateStmt->bind_param('ii', $position, $photoId);
        $updateStmt->execute();
    }

    $updateStmt->close();
}

