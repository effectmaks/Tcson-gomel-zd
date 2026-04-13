<!-- Yandex.Metrika counter -->
<script>
    (function(m,e,t,r,i,k,a){
        m[i]=m[i]||function(){(m[i].a=m[i].a||[]).push(arguments)};
        m[i].l=1*new Date();
        for (var j = 0; j < document.scripts.length; j++) {if (document.scripts[j].src === r) { return; }}
        k=e.createElement(t),a=e.getElementsByTagName(t)[0],k.async=1,k.src=r,a.parentNode.insertBefore(k,a)
    })(window, document,'script','https://mc.yandex.ru/metrika/tag.js?id=106899778', 'ym');

    ym(106899778, 'init', {ssr:true, webvisor:true, clickmap:true, ecommerce:"dataLayer", referrer: document.referrer, url: location.href, accurateTrackBounce:true, trackLinks:true});
</script>
<noscript><div><img src="https://mc.yandex.ru/watch/106899778" style="position:absolute; left:-9999px;" alt=""></div></noscript>
<!-- /Yandex.Metrika counter -->
<?php $pageBrandLogoSrc = isset($pageBrandLogoSrc) && is_string($pageBrandLogoSrc) && $pageBrandLogoSrc !== ''
    ? $pageBrandLogoSrc
    : '/img/logo-tcson-gomel-zhd.webp'; ?>
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
                <a class="header-contact-card__phone" href="tel:+375232349956">+375 (232) 34-99-56</a>
                <div class="header-contact-card__note">горячая линия</div>
              </div>
            </div>

            <div class="header-contact-card header-contact-card--main">
              <div class="header-contact-card__icon header-contact-card__icon--phone" aria-hidden="true"></div>
              <div class="header-contact-card__body">
                <a class="header-contact-card__phone" href="tel:+375232210946">+375 (232) 21-09-46</a>
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
