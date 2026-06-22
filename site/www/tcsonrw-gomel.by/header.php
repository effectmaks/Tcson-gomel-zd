<!-- Yandex.Metrika counter -->
<script type="text/javascript">
    (function(m,e,t,r,i,k,a){
        m[i]=m[i]||function(){(m[i].a=m[i].a||[]).push(arguments)};
        m[i].l=1*new Date();
        for (var j = 0; j < document.scripts.length; j++) {if (document.scripts[j].src === r) { return; }}
        k=e.createElement(t),a=e.getElementsByTagName(t)[0],k.async=1,k.src=r,a.parentNode.insertBefore(k,a)
    })(window, document,'script','https://mc.yandex.ru/metrika/tag.js?id=108807139', 'ym');

    ym(108807139, 'init', {ssr:true, webvisor:true, clickmap:true, ecommerce:"dataLayer", referrer: document.referrer, url: location.href, accurateTrackBounce:true, trackLinks:true});
</script>
<noscript><div><img src="https://mc.yandex.ru/watch/108807139" style="position:absolute; left:-9999px;" alt="" /></div></noscript>
<!-- /Yandex.Metrika counter -->
<?php $pageBrandLogoSrc = isset($pageBrandLogoSrc) && is_string($pageBrandLogoSrc) && $pageBrandLogoSrc !== ''
    ? $pageBrandLogoSrc
    : '/img/logo-old-mini.webp'; ?>
<?php
$publicPageVisibilityState = function_exists('getCurrentPublicPageVisibilityState')
    ? getCurrentPublicPageVisibilityState()
    : null;
$shouldShowPublicPageVisibilityControls = $publicPageVisibilityState !== null
    && function_exists('isLoggedIn')
    && isLoggedIn();
?>
<header class="header.php">
  <div class="min_size_page_h_f header-down">
    <div class="container">
      <div class="header-down-container">
        <div class="header-main">
          <a class="header-brand" href="/" aria-label="На главную">
            <img class="header-brand__emblem" src="/img/gerb.webp" alt="Герб Республики Беларусь">
            <img class="header-brand__logo" src="<?php echo htmlspecialchars($pageBrandLogoSrc, ENT_QUOTES, 'UTF-8'); ?>" alt="Логотип ТЦСОН Железнодорожного района г. Гомеля">
          </a>

          <div class="header-title">
            <a href="/" class="header-title__link">
              <span class="header-title__top">ТЕРРИТОРИАЛЬНЫЙ ЦЕНТР</span>
              <span class="header-title__middle">СОЦИАЛЬНОГО ОБСЛУЖИВАНИЯ НАСЕЛЕНИЯ</span>
              <span class="header-title__bottom">Железнодорожного района г. Гомеля</span>
            </a>
          </div>

          <div class="header-contacts">
            <div class="header-contact-card header-contact-card--trust">
              <div class="header-contact-card__icon header-contact-card__icon--service" aria-hidden="true"></div>
              <div class="header-contact-card__body">
                <a class="header-contact-card__phone" href="tel:+375232349956">8 (0232) 34-99-56</a>
                <div class="header-contact-card__note">горячая линия</div>
              </div>
            </div>

            <div class="header-contact-card header-contact-card--main">
              <div class="header-contact-card__icon header-contact-card__icon--phone" aria-hidden="true"></div>
              <div class="header-contact-card__body">
                <a class="header-contact-card__phone" href="tel:+375232210946">8 (0232) 21-09-46</a>
                <div class="header-contact-card__meta">пн-пт: 8:30 - 17:30</div>
                <a class="header-contact-card__email" href="mailto:officer@tcsonrw-gomel.by">officer@tcsonrw-gomel.by</a>
              </div>
            </div>

            <button id="specialButton" class="header-top-buttons__impaired-button" aria-label="Версия для слабовидящих">
              <span class="header-top-buttons__impaired-icon" aria-hidden="true"></span>
              <span class="header-top-buttons__impaired-text">
                <span>Версия для</span>
                <span>слабовидящих</span>
              </span>
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</header>
<?php if ($shouldShowPublicPageVisibilityControls): ?>
<?php
$publicPageVisibilityTitle = trim((string) ($publicPageVisibilityState['title'] ?? 'Страница'));
$publicPageVisibilityKey = (string) ($publicPageVisibilityState['key'] ?? '');
$publicPageVisibilityPublished = !empty($publicPageVisibilityState['is_published']);
$publicPageVisibilityStatusText = (string) ($publicPageVisibilityState['status_text'] ?? '');
$publicPagePublishedByLogin = trim((string) ($publicPageVisibilityState['published_by_login'] ?? ''));
$publicPageUpdatedByLogin = trim((string) ($publicPageVisibilityState['updated_by_login'] ?? ''));
$publicPageUpdatedAt = trim((string) ($publicPageVisibilityState['updated_at'] ?? ''));
$publicPageVisibilityCsrfToken = getCsrfToken();
?>
<style>
  .public-page-visibility {
    position: fixed;
    top: calc(var(--header-height, 110px) + 14px);
    right: max(16px, env(safe-area-inset-right));
    z-index: 4000;
  }

  .public-page-visibility__trigger {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-height: 42px;
    padding: 0 18px;
    border: 1px solid rgba(18, 89, 66, 0.24);
    border-radius: 999px;
    background: rgba(255, 255, 255, 0.96);
    box-shadow: 0 12px 28px rgba(13, 42, 31, 0.16);
    color: #125942;
    font-size: 14px;
    font-weight: 600;
    line-height: 1;
    cursor: pointer;
    transition: transform 0.2s ease, box-shadow 0.2s ease, background-color 0.2s ease;
  }

  .public-page-visibility__trigger:hover,
  .public-page-visibility__trigger:focus-visible {
    transform: translateY(-1px);
    box-shadow: 0 16px 32px rgba(13, 42, 31, 0.2);
    background: #ffffff;
  }

  .public-page-visibility__modal[hidden] {
    display: none;
  }

  .public-page-visibility__modal {
    position: fixed;
    inset: 0;
    z-index: 4001;
  }

  .public-page-visibility__backdrop {
    position: absolute;
    inset: 0;
    background: rgba(10, 24, 18, 0.38);
    backdrop-filter: blur(3px);
  }

  .public-page-visibility__dialog {
    position: relative;
    width: min(92vw, 420px);
    margin: calc(var(--header-height, 110px) + 18px) 20px 20px auto;
    padding: 22px 22px 20px;
    border-radius: 22px;
    background: #ffffff;
    box-shadow: 0 28px 60px rgba(12, 35, 26, 0.2);
  }

  .public-page-visibility__top {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 16px;
    margin-bottom: 18px;
  }

  .public-page-visibility__eyebrow {
    margin: 0 0 6px;
    color: #5d7268;
    font-size: 12px;
    font-weight: 700;
    letter-spacing: 0.08em;
    text-transform: uppercase;
  }

  .public-page-visibility__title {
    margin: 0;
    color: #17362a;
    font-size: 20px;
    line-height: 1.25;
  }

  .public-page-visibility__close {
    width: 38px;
    height: 38px;
    border: none;
    border-radius: 50%;
    background: #edf4f0;
    color: #17362a;
    font-size: 24px;
    line-height: 1;
    cursor: pointer;
    flex: 0 0 auto;
  }

  .public-page-visibility__toggle {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 18px;
    padding: 16px 18px;
    border-radius: 18px;
    background: #f4f8f5;
  }

  .public-page-visibility__toggle-label {
    display: block;
    margin: 0 0 4px;
    color: #17362a;
    font-size: 16px;
    font-weight: 700;
  }

  .public-page-visibility__toggle-hint {
    display: block;
    margin: 0;
    color: #62776d;
    font-size: 13px;
    line-height: 1.45;
  }

  .public-page-visibility__switch {
    position: relative;
    display: inline-flex;
    width: 54px;
    height: 32px;
    flex: 0 0 auto;
  }

  .public-page-visibility__switch input {
    position: absolute;
    width: 1px;
    height: 1px;
    margin: -1px;
    padding: 0;
    border: 0;
    overflow: hidden;
    clip: rect(0, 0, 0, 0);
  }

  .public-page-visibility__switch-track {
    position: absolute;
    inset: 0;
    border-radius: 999px;
    background: #bccdc3;
    transition: background-color 0.2s ease;
  }

  .public-page-visibility__switch-track::after {
    content: "";
    position: absolute;
    top: 3px;
    left: 3px;
    width: 26px;
    height: 26px;
    border-radius: 50%;
    background: #ffffff;
    box-shadow: 0 4px 10px rgba(12, 35, 26, 0.18);
    transition: transform 0.2s ease;
  }

  .public-page-visibility__switch input:checked + .public-page-visibility__switch-track {
    background: #1f7a59;
  }

  .public-page-visibility__switch input:checked + .public-page-visibility__switch-track::after {
    transform: translateX(22px);
  }

  .public-page-visibility__switch input:focus-visible + .public-page-visibility__switch-track {
    outline: 2px solid #125942;
    outline-offset: 2px;
  }

  .public-page-visibility__status {
    margin: 16px 0 0;
    color: #385447;
    font-size: 14px;
    line-height: 1.5;
  }

  .public-page-visibility__status.is-error {
    color: #9f2f2f;
  }

  .public-page-visibility__meta {
    margin-top: 12px;
    color: #5b7267;
    font-size: 12px;
    line-height: 1.45;
  }

  .public-page-visibility__meta-row + .public-page-visibility__meta-row {
    margin-top: 4px;
  }

  @media (max-width: 767px) {
    .public-page-visibility {
      top: calc(var(--header-height, 94px) + 12px);
      right: 14px;
      bottom: auto;
    }

    .public-page-visibility__dialog {
      width: min(100vw - 28px, 420px);
      margin: calc(var(--header-height, 94px) + 18px) 14px 20px auto;
    }
  }
</style>
<div
  class="public-page-visibility"
  data-public-page-visibility
  data-page-key="<?php echo e($publicPageVisibilityKey); ?>"
  data-published="<?php echo $publicPageVisibilityPublished ? '1' : '0'; ?>"
  data-status-text="<?php echo e($publicPageVisibilityStatusText); ?>"
  data-csrf-token="<?php echo e($publicPageVisibilityCsrfToken); ?>"
  data-endpoint="/api/page-visibility.php"
>
  <button type="button" class="public-page-visibility__trigger" data-public-page-visibility-open>Настроить</button>
  <div class="public-page-visibility__modal" data-public-page-visibility-modal hidden>
    <div class="public-page-visibility__backdrop" data-public-page-visibility-close></div>
    <div class="public-page-visibility__dialog" role="dialog" aria-modal="true" aria-labelledby="publicPageVisibilityTitle">
      <div class="public-page-visibility__top">
        <div>
          <p class="public-page-visibility__eyebrow">Настройки страницы</p>
          <h2 class="public-page-visibility__title" id="publicPageVisibilityTitle"><?php echo e($publicPageVisibilityTitle); ?></h2>
        </div>
        <button type="button" class="public-page-visibility__close" aria-label="Закрыть окно" data-public-page-visibility-close>&times;</button>
      </div>
      <label class="public-page-visibility__toggle">
        <span>
          <span class="public-page-visibility__toggle-label">Опубликовать</span>
          <span class="public-page-visibility__toggle-hint">Выключенное состояние оставляет страницу доступной только после авторизации.</span>
        </span>
        <span class="public-page-visibility__switch">
          <input type="checkbox" data-public-page-visibility-toggle <?php echo $publicPageVisibilityPublished ? 'checked' : ''; ?>>
          <span class="public-page-visibility__switch-track" aria-hidden="true"></span>
        </span>
      </label>
      <p class="public-page-visibility__status" data-public-page-visibility-status><?php echo e($publicPageVisibilityStatusText); ?></p>
      <div class="public-page-visibility__meta">
        <div class="public-page-visibility__meta-row" data-public-page-visibility-publisher>
          <?php if ($publicPagePublishedByLogin !== ''): ?>
            Последний публиковавший: <strong><?php echo e($publicPagePublishedByLogin); ?></strong>
          <?php else: ?>
            Последний публиковавший: не зафиксирован
          <?php endif; ?>
        </div>
        <div class="public-page-visibility__meta-row" data-public-page-visibility-updated>
          <?php if ($publicPageUpdatedByLogin !== ''): ?>
            Последнее изменение: <strong><?php echo e($publicPageUpdatedByLogin); ?></strong><?php echo $publicPageUpdatedAt !== '' ? ' • ' . e($publicPageUpdatedAt) : ''; ?>
          <?php else: ?>
            Последнее изменение: не зафиксировано
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</div>
<script>
  (function () {
    var root = document.querySelector('[data-public-page-visibility]');
    if (!root || root.dataset.initialized === '1') {
      return;
    }

    root.dataset.initialized = '1';

    var openButton = root.querySelector('[data-public-page-visibility-open]');
    var modal = root.querySelector('[data-public-page-visibility-modal]');
    var closeButtons = root.querySelectorAll('[data-public-page-visibility-close]');
    var toggle = root.querySelector('[data-public-page-visibility-toggle]');
    var statusNode = root.querySelector('[data-public-page-visibility-status]');
    var publisherNode = root.querySelector('[data-public-page-visibility-publisher]');
    var updatedNode = root.querySelector('[data-public-page-visibility-updated]');
    var previousValue = root.dataset.published === '1';

    function setStatus(text, isError) {
      statusNode.textContent = text;
      statusNode.classList.toggle('is-error', Boolean(isError));
    }

    function renderMetaLine(node, prefix, strongText, suffix, emptyText) {
      node.textContent = '';

      if (!strongText) {
        node.textContent = prefix + emptyText;
        return;
      }

      node.appendChild(document.createTextNode(prefix));
      var strongNode = document.createElement('strong');
      strongNode.textContent = strongText;
      node.appendChild(strongNode);

      if (suffix) {
        node.appendChild(document.createTextNode(suffix));
      }
    }

    function setPublisherInfo(publishedByLogin, updatedByLogin, updatedAt) {
      renderMetaLine(
        publisherNode,
        'Последний публиковавший: ',
        publishedByLogin,
        '',
        'не зафиксирован'
      );

      renderMetaLine(
        updatedNode,
        'Последнее изменение: ',
        updatedByLogin,
        updatedAt ? ' • ' + updatedAt : '',
        'не зафиксировано'
      );
    }

    function openModal() {
      modal.hidden = false;
      document.body.style.overflow = 'hidden';
    }

    function closeModal() {
      modal.hidden = true;
      document.body.style.overflow = '';
      setStatus(root.dataset.statusText || '', false);
    }

    function syncStatusFromToggle() {
      var text = toggle.checked
        ? 'Страница доступна всем посетителям.'
        : 'Страница доступна только авторизованным пользователям.';
      root.dataset.statusText = text;
      setStatus(text, false);
    }

    function persistVisibility() {
      var formData = new URLSearchParams();
      formData.set('page_key', root.dataset.pageKey || '');
      formData.set('is_published', toggle.checked ? '1' : '0');
      formData.set('csrf_token', root.dataset.csrfToken || '');

      toggle.disabled = true;
      setStatus('Сохраняем настройки страницы...', false);

      fetch(root.dataset.endpoint || '/api/page-visibility.php', {
        method: 'POST',
        credentials: 'same-origin',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
          'X-Requested-With': 'XMLHttpRequest'
        },
        body: formData.toString()
      })
        .then(function (response) {
          return response.json().catch(function () {
            return null;
          }).then(function (payload) {
            return { ok: response.ok, payload: payload };
          });
        })
        .then(function (result) {
          if (!result.ok || !result.payload || !result.payload.ok) {
            throw new Error(result.payload && result.payload.message ? result.payload.message : 'Не удалось сохранить настройки страницы.');
          }

          previousValue = Boolean(result.payload.is_published);
          root.dataset.published = previousValue ? '1' : '0';
          root.dataset.statusText = result.payload.status_text || '';
          toggle.checked = previousValue;
          setStatus(root.dataset.statusText, false);
          setPublisherInfo(
            result.payload.published_by_login || '',
            result.payload.updated_by_login || '',
            result.payload.updated_at || ''
          );
        })
        .catch(function (error) {
          toggle.checked = previousValue;
          setStatus(error.message || 'Не удалось сохранить настройки страницы.', true);
        })
        .finally(function () {
          toggle.disabled = false;
        });
    }

    openButton.addEventListener('click', openModal);
    toggle.addEventListener('change', function () {
      syncStatusFromToggle();
      persistVisibility();
    });

    closeButtons.forEach(function (button) {
      button.addEventListener('click', closeModal);
    });

    document.addEventListener('keydown', function (event) {
      if (!modal.hidden && event.key === 'Escape') {
        closeModal();
      }
    });

    syncStatusFromToggle();
  })();
</script>
<?php endif; ?>
