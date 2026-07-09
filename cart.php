/**
 * assets/js/scroll-animation.js
 * Scroll Animation เท่านั้น — ไม่มี Page Loader
 */
(function() {
  const style = document.createElement('style');
  style.textContent = `
    .fade-up {
      opacity: 0;
      transform: translateY(30px);
      transition: opacity .6s ease, transform .6s ease;
    }
    .fade-up.visible { opacity: 1; transform: translateY(0); }
    .fade-up.delay-1 { transition-delay: .1s; }
    .fade-up.delay-2 { transition-delay: .2s; }
    .fade-up.delay-3 { transition-delay: .3s; }
  `;
  document.head.appendChild(style);

  function initAnim() {
    const sels = [
      '.trip-card', '.hotel-card', '.resto-card',
      '.product-mini-card', '.community-card', '.team-card',
      '.value-card', '.stat-card', '.step-card',
      '.booking-card', '.order-card', '.section-card',
      '.dish-card', '.quick-link',
    ];

    sels.forEach(sel => {
      document.querySelectorAll(sel).forEach((el, i) => {
        if (!el.classList.contains('fade-up')) {
          el.classList.add('fade-up');
          const d = Math.min(i % 4, 3);
          if (d > 0) el.classList.add('delay-' + d);
        }
      });
    });

    const obs = new IntersectionObserver(entries => {
      entries.forEach(e => {
        if (e.isIntersecting) {
          e.target.classList.add('visible');
          obs.unobserve(e.target);
        }
      });
    }, { threshold: 0.1 });

    document.querySelectorAll('.fade-up').forEach(el => obs.observe(el));
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initAnim);
  } else {
    initAnim();
  }
})();
