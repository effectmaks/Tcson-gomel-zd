<?php
session_start();

require_once __DIR__ . '/lib/security.php';
require_once __DIR__ . '/lib/auth.php';
require_once __DIR__ . '/lib/messenger.php';

requirePermission('tokens');
include __DIR__ . '/db_connection.php';

messengerEnsureReady($conn);

$currentUser = getCurrentUserByLogin($conn);
$currentUserName = $currentUser ? messengerGetUserDisplayName($currentUser) : (string) ($_SESSION['login'] ?? 'Пользователь');
$csrfToken = getCsrfToken();
$seoTitleMeta = 'Bearer-токены central-клиента — ТЦСОН Железнодорожного района г. Гомеля';
$seoDescriptionMeta = 'Управление Bearer-токенами central-клиента для служебного мессенджера.';
$seoRobotsMeta = 'noindex,nofollow';
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="/img/favicon.png" type="image/x-icon">
    <link rel="stylesheet" href="/css/style.css?v=<?= filemtime($_SERVER['DOCUMENT_ROOT'] . '/css/style.css') ?>">
    <link rel="stylesheet" href="/css/normalize.css?v=<?= filemtime($_SERVER['DOCUMENT_ROOT'] . '/css/normalize.css') ?>">
    <link rel="stylesheet" href="/css/media.css?v=<?= filemtime($_SERVER['DOCUMENT_ROOT'] . '/css/media.css') ?>">
    <link rel="stylesheet" href="/css/media_mobile.css?v=<?= filemtime($_SERVER['DOCUMENT_ROOT'] . '/css/media_mobile.css') ?>">
    <title>Bearer-токены central-клиента — ТЦСОН</title>
</head>
<body>
<?php include __DIR__ . '/header.php'; ?>
<main class="main messenger-page">
    <section class="container">
        <div class="messenger-card messenger-card__section messenger-hero">
            <div class="messenger-row">
                <div>
                    <h1 class="messenger-title">Bearer-токены central-клиента</h1>
                    <p class="messenger-subtitle">Пользователь сайта: <?php echo e($currentUserName); ?>. Здесь выпускаются и отзываются токены для локального Docker-клиента.</p>
                </div>
                <div class="messenger-controls">
                    <a href="/auth.php" class="messenger-link-button secondary">Личный кабинет</a>
                    <?php if (hasPermission('messenger')): ?>
                    <a href="/messenger.php" class="messenger-link-button secondary">К заданиям</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div id="messengerFlash"></div>

        <section class="messenger-token-layout">
            <div class="messenger-card">
                <div class="messenger-card__section">
                    <h2 class="messenger-section-title messenger-section-title--spaced">Новый токен</h2>
                    <p class="messenger-subtitle">Полное значение показывается только один раз сразу после выпуска.</p>
                </div>
                <div class="messenger-card__section">
                    <form id="tokenCreateForm">
                        <div class="field-group">
                            <label for="tokenLabel">`label` клиентского экземпляра</label>
                            <input type="text" id="tokenLabel" name="label" placeholder="Например, central-docker-main" maxlength="255" required>
                        </div>
                        <div class="field-group messenger-stack-md">
                            <label for="tokenExpiresAt">`expires_at` опционально</label>
                            <input type="datetime-local" id="tokenExpiresAt" name="expires_at">
                        </div>
                        <div class="messenger-controls messenger-stack-md">
                            <button type="submit" id="createTokenButton">Выпустить токен</button>
                            <button type="button" class="secondary" id="refreshTokensButton">Обновить список</button>
                        </div>
                    </form>
                    <p class="messenger-helper messenger-stack-md">Отозванный токен перестает проходить Bearer-аутентификацию без перезапуска сайта.</p>
                    <div id="tokenSecretBlock" class="messenger-token-secret" hidden>
                        <div class="messenger-width-full">
                            <strong>Новый Bearer-токен</strong>
                            <div class="messenger-helper">Сохраните его сейчас. После скрытия полный токен больше не будет показан.</div>
                        </div>
                        <code id="tokenSecretValue"></code>
                        <button type="button" id="copyTokenButton" class="secondary">Скопировать</button>
                        <button type="button" id="hideTokenButton" class="secondary">Скрыть</button>
                    </div>
                </div>
            </div>

            <div class="messenger-card">
                <div class="messenger-card__section">
                    <div class="messenger-row">
                        <h2 class="messenger-section-title">Активные и архивные токены</h2>
                        <span class="messenger-meta">В списке нет полного значения токена.</span>
                    </div>
                </div>
                <div class="messenger-card__section">
                    <div id="tokenList" class="messenger-token-list"></div>
                </div>
            </div>
        </section>
    </section>
</main>

<script>
    const tokenPageState = {
        csrfToken: <?php echo json_encode($csrfToken, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?>,
        apiTokens: [],
        lastIssuedToken: ''
    };

    const refs = {
        flash: document.getElementById('messengerFlash'),
        tokenCreateForm: document.getElementById('tokenCreateForm'),
        tokenLabel: document.getElementById('tokenLabel'),
        tokenExpiresAt: document.getElementById('tokenExpiresAt'),
        refreshTokensButton: document.getElementById('refreshTokensButton'),
        tokenList: document.getElementById('tokenList'),
        tokenSecretBlock: document.getElementById('tokenSecretBlock'),
        tokenSecretValue: document.getElementById('tokenSecretValue'),
        copyTokenButton: document.getElementById('copyTokenButton'),
        hideTokenButton: document.getElementById('hideTokenButton')
    };

    function showFlash(type, message) {
        refs.flash.innerHTML = `<div class="messenger-${type}">${message}</div>`;
    }

    async function fetchJson(url, options = {}) {
        const response = await fetch(url, options);
        const data = await response.json();
        if (!response.ok || !data.ok) {
            const error = data && data.error ? data.error : { code: 'validation_error', message: 'Ошибка запроса' };
            throw new Error(`${error.code}: ${error.message}`);
        }
        return data.data;
    }

    function escapeHtml(value) {
        return String(value ?? '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function formatDateTime(value) {
        if (!value) {
            return '—';
        }

        const date = new Date(value);
        if (Number.isNaN(date.getTime())) {
            return '—';
        }

        return date.toLocaleString('ru-RU');
    }

    function tokenStatusLabel(status) {
        if (status === 'active') {
            return 'Активен';
        }

        if (status === 'expired') {
            return 'Истек';
        }

        if (status === 'revoked') {
            return 'Отозван';
        }

        return status;
    }

    function showIssuedToken(token) {
        tokenPageState.lastIssuedToken = token || '';
        if (!tokenPageState.lastIssuedToken) {
            refs.tokenSecretBlock.hidden = true;
            refs.tokenSecretValue.textContent = '';
            return;
        }

        refs.tokenSecretBlock.hidden = false;
        refs.tokenSecretValue.textContent = tokenPageState.lastIssuedToken;
    }

    async function copyTextToClipboard(value) {
        const text = String(value ?? '');
        if (!text) {
            return;
        }

        if (navigator.clipboard && window.isSecureContext) {
            try {
                await navigator.clipboard.writeText(text);
                return;
            } catch (error) {
                // Fall through to execCommand fallback when Clipboard API is blocked.
            }
        }

        const textarea = document.createElement('textarea');
        textarea.value = text;
        textarea.setAttribute('readonly', '');
        textarea.style.position = 'fixed';
        textarea.style.top = '-9999px';
        textarea.style.left = '-9999px';
        textarea.style.opacity = '0';
        document.body.appendChild(textarea);
        textarea.focus();
        textarea.select();
        textarea.setSelectionRange(0, textarea.value.length);

        try {
            if (!document.execCommand('copy')) {
                throw new Error('execCommand copy failed');
            }
        } finally {
            document.body.removeChild(textarea);
        }
    }

    function renderTokens() {
        if (tokenPageState.apiTokens.length === 0) {
            refs.tokenList.innerHTML = '<div class="messenger-empty">Bearer-токены еще не выпускались.</div>';
            return;
        }

        refs.tokenList.innerHTML = tokenPageState.apiTokens.map((token) => `
            <article class="messenger-token-item">
                <div class="messenger-row">
                    <strong>${escapeHtml(token.label)}</strong>
                    <span class="messenger-status token-status-${escapeHtml(token.status)}">${tokenStatusLabel(token.status)}</span>
                </div>
                <div class="messenger-meta messenger-stack-sm">
                    prefix: ${escapeHtml(token.token_prefix)}<br>
                    created_at: ${escapeHtml(formatDateTime(token.created_at))}<br>
                    expires_at: ${escapeHtml(formatDateTime(token.expires_at))}<br>
                    last_used_at: ${escapeHtml(formatDateTime(token.last_used_at))}
                    ${token.revoked_at ? `<br>revoked_at: ${escapeHtml(formatDateTime(token.revoked_at))}` : ''}
                </div>
                <div class="messenger-controls messenger-stack-md">
                    ${token.status === 'active' ? `<button type="button" class="secondary" data-revoke-token="${token.id}">Отозвать</button>` : ''}
                </div>
            </article>
        `).join('');

        refs.tokenList.querySelectorAll('[data-revoke-token]').forEach((button) => {
            button.addEventListener('click', () => revokeToken(button.getAttribute('data-revoke-token')).catch((error) => showFlash('error', error.message)));
        });
    }

    function postJson(url, payload) {
        return fetchJson(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-Token': tokenPageState.csrfToken
            },
            body: JSON.stringify(payload)
        });
    }

    async function loadTokens() {
        const data = await fetchJson('/api/messenger/tokens.php');
        tokenPageState.apiTokens = data.items || [];
        renderTokens();
    }

    async function createToken(event) {
        event.preventDefault();

        const expiresAtValue = refs.tokenExpiresAt.value
            ? new Date(refs.tokenExpiresAt.value).toISOString()
            : null;
        const data = await postJson('/api/messenger/tokens.php', {
            action: 'create',
            label: refs.tokenLabel.value,
            expires_at: expiresAtValue
        });

        refs.tokenCreateForm.reset();
        showIssuedToken(data.token || '');
        showFlash('success', 'Bearer-токен выпущен. Сохраните полное значение из блока ниже.');
        await loadTokens();
    }

    async function revokeToken(tokenId) {
        if (!window.confirm('Отозвать Bearer-токен? После этого central-клиент с ним перестанет проходить авторизацию.')) {
            return;
        }

        await postJson('/api/messenger/tokens.php', {
            action: 'revoke',
            token_id: Number(tokenId)
        });
        showFlash('success', 'Bearer-токен отозван.');
        await loadTokens();
    }

    refs.tokenCreateForm.addEventListener('submit', (event) => createToken(event).catch((error) => showFlash('error', error.message)));
    refs.refreshTokensButton.addEventListener('click', () => loadTokens().catch((error) => showFlash('error', error.message)));
    refs.copyTokenButton.addEventListener('click', async () => {
        if (!tokenPageState.lastIssuedToken) {
            return;
        }

        try {
            await copyTextToClipboard(tokenPageState.lastIssuedToken);
            showFlash('success', 'Bearer-токен скопирован в буфер обмена.');
        } catch (error) {
            showFlash('error', 'Не удалось скопировать Bearer-токен в буфер обмена. Скопируйте значение вручную.');
        }
    });
    refs.hideTokenButton.addEventListener('click', () => showIssuedToken(''));

    loadTokens().catch((error) => showFlash('error', error.message));
</script>
<?php include __DIR__ . '/footer.php'; ?>
</body>
</html>
