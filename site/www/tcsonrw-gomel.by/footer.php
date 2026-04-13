<?php $pageBrandLogoSrc = isset($pageBrandLogoSrc) && is_string($pageBrandLogoSrc) && $pageBrandLogoSrc !== ''
    ? $pageBrandLogoSrc
    : '/img/logo-tcson-gomel-zhd.webp'; ?>
<footer class="min_size_page_main footer">
  <div class="footer__ornament" aria-hidden="true"></div>

  <div class="footer__main">
    <div class="footer__container container">
      <div class="footer__main-grid">
        <section class="footer__brand">
          <a href="/" class="footer__brand-logo" aria-label="На главную">
            <img src="<?php echo htmlspecialchars($pageBrandLogoSrc, ENT_QUOTES, 'UTF-8'); ?>" alt="Логотип ТЦСОН Железнодорожного района г. Гомеля">
          </a>
          <div class="footer__brand-copy">
            <h2 class="footer__brand-title">ТЕРРИТОРИАЛЬНЫЙ ЦЕНТР СОЦИАЛЬНОГО ОБСЛУЖИВАНИЯ НАСЕЛЕНИЯ</h2>
            <p class="footer__brand-subtitle">Железнодорожного района г. Гомеля</p>
            <p class="footer__brand-text">Мы рядом, чтобы помогать людям, оказывать поддержку и заботу каждому, кто в этом нуждается.</p>
          </div>
          <div class="footer__brand-actions">
            <button id="specialButtonFooter" class="footer-action-button footer-action-button--vision" type="button" aria-label="Версия для слабовидящих">
              <span class="footer-action-button__icon footer-action-button__icon--vision" aria-hidden="true"></span>
              <span class="footer-action-button__text footer-action-button__text--vision">Версия для<br>слабовидящих</span>
            </button>
            <a class="footer-action-button" href="/sitemap.xml">
              <span class="footer-action-button__icon footer-action-button__icon--structure" aria-hidden="true"></span>
              <span class="footer-action-button__text">Карта сайта</span>
            </a>
          </div>
        </section>

        <section class="footer__column footer__column--nav">
          <h3 class="footer__heading">Навигация</h3>
          <div class="footer__heading-line" aria-hidden="true"><span></span></div>
          <nav aria-label="Нижняя навигация">
            <ul class="footer-nav list-reset">
              <li class="footer-nav__item">
                <a href="/#about-center" class="footer-nav__link">
                  <span class="footer-nav__icon footer-nav__icon--about" aria-hidden="true"></span>
                  <span>О центре</span>
                  <span class="footer-nav__arrow" aria-hidden="true"></span>
                </a>
              </li>
              <li class="footer-nav__item">
                <a href="/" class="footer-nav__link">
                  <span class="footer-nav__icon footer-nav__icon--departments" aria-hidden="true"></span>
                  <span>Отделения</span>
                  <span class="footer-nav__arrow" aria-hidden="true"></span>
                </a>
              </li>
              <li class="footer-nav__item">
                <a href="/#services" class="footer-nav__link">
                  <span class="footer-nav__icon footer-nav__icon--services" aria-hidden="true"></span>
                  <span>Наши услуги</span>
                  <span class="footer-nav__arrow" aria-hidden="true"></span>
                </a>
              </li>
              <li class="footer-nav__item">
                <a href="/#support" class="footer-nav__link">
                  <span class="footer-nav__icon footer-nav__icon--support" aria-hidden="true"></span>
                  <span>Социальная поддержка</span>
                  <span class="footer-nav__arrow" aria-hidden="true"></span>
                </a>
              </li>
              <li class="footer-nav__item">
                <a href="/listevents.php" class="footer-nav__link">
                  <span class="footer-nav__icon footer-nav__icon--news" aria-hidden="true"></span>
                  <span>События</span>
                  <span class="footer-nav__arrow" aria-hidden="true"></span>
                </a>
              </li>
              <li class="footer-nav__item">
                <a href="/#support" class="footer-nav__link">
                  <span class="footer-nav__icon footer-nav__icon--benefits" aria-hidden="true"></span>
                  <span>Госсоцподдержка инвалидов</span>
                  <span class="footer-nav__arrow" aria-hidden="true"></span>
                </a>
              </li>
              <li class="footer-nav__item">
                <a href="mailto:officer@tcsonrw-gomel.by" class="footer-nav__link">
                  <span class="footer-nav__icon footer-nav__icon--contacts" aria-hidden="true"></span>
                  <span>Контакты</span>
                  <span class="footer-nav__arrow" aria-hidden="true"></span>
                </a>
              </li>
              <li class="footer-nav__item">
                <a href="/auth.php" class="footer-nav__link">
                  <span class="footer-nav__icon footer-nav__icon--cabinet" aria-hidden="true"></span>
                  <span>Личный кабинет</span>
                  <span class="footer-nav__arrow" aria-hidden="true"></span>
                </a>
              </li>
            </ul>
          </nav>
        </section>

        <section class="footer__column footer__column--contacts">
          <h3 class="footer__heading">Контакты</h3>
          <div class="footer__heading-line" aria-hidden="true"><span></span></div>
          <div class="footer-contact-list">
            <div class="footer-contact-item">
              <span class="footer-contact-item__icon footer-contact-item__icon--location" aria-hidden="true"></span>
              <div class="footer-contact-item__body">
                <strong>Адрес:</strong>
                <span>246032, г. Гомель, ул. 50 лет БССР, д. 19</span>
              </div>
            </div>
            <div class="footer-contact-item">
              <span class="footer-contact-item__icon footer-contact-item__icon--phone" aria-hidden="true"></span>
              <div class="footer-contact-item__body">
                <strong>Телефон:</strong>
                <a href="tel:+375232210946">+375 (232) 21-09-46</a>
                <a href="tel:+375232349899">+375 (232) 34-98-99</a>
                <span>пн-пт: 8:30 - 17:30</span>
              </div>
            </div>
            <div class="footer-contact-item">
              <span class="footer-contact-item__icon footer-contact-item__icon--email" aria-hidden="true"></span>
              <div class="footer-contact-item__body">
                <strong>E-mail:</strong>
                <a href="mailto:officer@tcsonrw-gomel.by">officer@tcsonrw-gomel.by</a>
              </div>
            </div>
          </div>
        </section>

        <section class="footer__column footer__column--social">
          <h3 class="footer__heading">Мы в социальных сетях</h3>
          <div class="footer__heading-line" aria-hidden="true"><span></span></div>
          <div class="footer-social">
            <a href="https://t.me/tcsonrw_gomel" class="footer-social__link footer-social__link--telegram" aria-label="Telegram" target="_blank" rel="noopener noreferrer">
              <span class="footer-social__icon footer-social__icon--telegram" aria-hidden="true"></span>
            </a>
            <a href="https://www.instagram.com/" class="footer-social__link footer-social__link--instagram" aria-label="Instagram" target="_blank" rel="noopener noreferrer">
              <span class="footer-social__icon footer-social__icon--instagram" aria-hidden="true"></span>
            </a>
          </div>

          <div class="footer-feedback">
            <h4 class="footer__heading footer__heading--small">Обратная связь</h4>
            <div class="footer__heading-line footer__heading-line--small" aria-hidden="true"><span></span></div>
            <p class="footer-feedback__text">Для деловой переписки и обращений используйте официальный электронный адрес учреждения.</p>
            <a class="footer-feedback__button" href="mailto:officer@tcsonrw-gomel.by">
              <span class="footer-feedback__button-icon" aria-hidden="true"></span>
              <span class="footer-feedback__button-text">Написать сообщение</span>
            </a>
          </div>
        </section>
      </div>
    </div>
  </div>

  <div class="footer__lower">
    <div class="footer__container container">
      <div class="footer__meta-grid">
        <div class="footer-meta-card">
          <span class="footer-meta-card__icon footer-meta-card__icon--time" aria-hidden="true"></span>
          <div class="footer-meta-card__body">
            <h4>Режим работы</h4>
            <p>Понедельник - пятница: 8:30 - 17:30</p>
            <p>Суббота, воскресенье: выходной</p>
          </div>
        </div>
        <div class="footer-meta-card">
          <span class="footer-meta-card__icon footer-meta-card__icon--info" aria-hidden="true"></span>
          <div class="footer-meta-card__body">
            <h4>График приёма</h4>
            <p>Административные процедуры: пн, ср, чт, пт с 08:00 до 13:00.</p>
            <p>Вторник: с 14:00 до 20:00. Приемная: +375 (232) 21-09-46.</p>
          </div>
        </div>
      </div>

      <div class="footer__meta-divider" aria-hidden="true"><span></span></div>

      <div class="footer__copyright">
        <p>© <?php echo date('Y'); ?> ТЦСОН Железнодорожного района г. Гомеля.</p>
        <p>Все права защищены.</p>
      </div>
    </div>
  </div>
</footer>
<script>
  document.addEventListener('DOMContentLoaded', function () {
    var footerSpecialButton = document.getElementById('specialButtonFooter');
    var headerSpecialButton = document.getElementById('specialButton');

    if (!footerSpecialButton || !headerSpecialButton) {
      return;
    }

    footerSpecialButton.addEventListener('click', function () {
      headerSpecialButton.click();
    });
  });
</script>
<script src="/script/reveal-blocks.js?v=<?= filemtime(__DIR__ . '/script/reveal-blocks.js') ?>" defer></script>
