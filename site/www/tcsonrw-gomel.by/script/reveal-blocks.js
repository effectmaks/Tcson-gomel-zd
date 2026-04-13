(function () {
  const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  const baseSelector = [
    'main section',
    'main article',
    'main aside',
    'main .service',
    'main .department',
    'main .banners__item',
    'main [class*="__card"]',
    'main [class*="__block"]',
    'main [class*="__item"]',
    'footer .footer__columns > *',
    'footer .footer__urls',
    'footer .footer__bottom'
  ].join(', ');

  function isEligible(node) {
    if (!(node instanceof HTMLElement)) {
      return false;
    }

    if (node.classList.contains('reveal-block')) {
      return false;
    }

    if (node.closest('.header, .header-down, .header-nav, .breadcrumbs, .slick-arrow, .slick-dots, .slick-track, .slick-list')) {
      return false;
    }

    if (node.closest('.footer__link, .footer__social, .footer__logo, .social__item')) {
      return false;
    }

    if (node.matches('li, ul, ol, nav, form, label, a.link, button, img, svg, path')) {
      return false;
    }

    if (node.hidden || node.getAttribute('aria-hidden') === 'true') {
      return false;
    }

    const style = window.getComputedStyle(node);

    if (style.display === 'none' || style.visibility === 'hidden') {
      return false;
    }

    if (style.position === 'fixed' || style.position === 'sticky') {
      return false;
    }

    if (node.offsetWidth < 180 || node.offsetHeight < 60) {
      return false;
    }

    return true;
  }

  function collectBlocks() {
    return Array.from(new Set(Array.from(document.querySelectorAll(baseSelector)).filter(isEligible)));
  }

  function showImmediately(nodes) {
    nodes.forEach(function (node) {
      node.classList.add('reveal-block', 'is-visible');
      node.style.removeProperty('--reveal-delay');
    });
  }

  function initRevealBlocks() {
    const blocks = collectBlocks();

    if (!blocks.length) {
      return;
    }

    if (prefersReducedMotion || !('IntersectionObserver' in window)) {
      showImmediately(blocks);
      return;
    }

    const observer = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (!entry.isIntersecting) {
          return;
        }

        entry.target.classList.add('is-visible');
        observer.unobserve(entry.target);
      });
    }, {
      threshold: 0.14,
      rootMargin: '0px 0px -8% 0px'
    });

    blocks.forEach(function (block, index) {
      block.classList.add('reveal-block');
      block.style.setProperty('--reveal-delay', ((index % 6) * 70) + 'ms');
      observer.observe(block);
    });
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initRevealBlocks);
  } else {
    initRevealBlocks();
  }

  window.initRevealBlocks = initRevealBlocks;
})();
