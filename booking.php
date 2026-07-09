/**
 * assets/js/bubbles.js
 * สร้างฟองอากาศ + คลื่นน้ำอัตโนมัติในทุกหน้า
 */
(function() {

  /* ── 1. Bubble Background ── */
  const bg = document.createElement('div');
  bg.className = 'bubble-bg';

  // สร้างฟอง 18 ลูก ขนาดและตำแหน่งสุ่ม
  const sizes  = [4,5,6,7,8,9,10,12,14,5,6,8,10,7,9,11,5,6];
  const delays = [0,.5,1,1.5,2,2.5,3,3.5,4,4.5,5,1.2,2.8,3.2,0.8,1.8,4.2,2.2];
  const speeds = [6,7,8,9,10,11,12,8,9,7,10,11,9,8,12,7,6,10];
  const lefts  = [3,8,14,20,27,33,40,47,54,61,67,73,79,85,91,95,50,30];

  sizes.forEach((s,i) => {
    const b = document.createElement('div');
    b.className = 'b';
    b.style.cssText = `
      width:${s}px; height:${s}px;
      left:${lefts[i]}%;
      animation-duration:${speeds[i]}s;
      animation-delay:${delays[i]}s;
    `;
    bg.appendChild(b);
  });

  document.body.prepend(bg);
  document.body.style.position = 'relative';

  /* ── 2. Wave ใต้ Navbar ── */
  // เพิ่ม wave SVG ใน hero sections อัตโนมัติ
  function addWaveToHero() {
    const heroSelectors = [
      '.page-hero','.hero-booking','.about-hero',
      '.stats-bar','.stat-bar','.video-section',
      '.cta-banner','.cta-section','.must-try',
      '.dash-hero',
    ];

    heroSelectors.forEach(sel => {
      document.querySelectorAll(sel).forEach(el => {
        if (el.querySelector('.wave-bottom')) return; // ไม่เพิ่มซ้ำ
        if (getComputedStyle(el).position === 'static') {
          el.style.position = 'relative';
        }
        el.style.overflow = 'hidden';

        const wave = document.createElement('div');
        wave.className = 'wave-bottom';
        wave.innerHTML = `
          <svg viewBox="0 0 1440 60" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none">
            <path d="M0,30 C240,55 480,5 720,30 C960,55 1200,5 1440,30 L1440,60 L0,60 Z"
                  fill="#f0f8ff" opacity="1"/>
            <path d="M0,40 C200,20 400,55 600,35 C800,15 1000,50 1200,30 C1320,18 1400,38 1440,40 L1440,60 L0,60 Z"
                  fill="rgba(72,202,228,0.2)"/>
          </svg>
        `;
        el.appendChild(wave);
      });
    });
  }

  /* ── 3. Wave Float ในส่วน background เขียว/เข้ม ── */
  function addFloatWave() {
    const darkSelectors = [
      '.values-section','.steps-section','.gallery-section',
    ];

    darkSelectors.forEach(sel => {
      document.querySelectorAll(sel).forEach(el => {
        if (el.querySelector('.wave-float')) return;
        if (getComputedStyle(el).position === 'static') {
          el.style.position = 'relative';
        }
        el.style.overflow = 'hidden';

        const wf = document.createElement('div');
        wf.className = 'wave-float';
        wf.innerHTML = `
          <svg viewBox="0 0 1440 80" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none">
            <path d="M0,40 C240,65 480,15 720,40 C960,65 1200,15 1440,40 L1440,80 L0,80 Z"
                  fill="rgba(0,180,216,0.08)"/>
          </svg>
          <svg viewBox="0 0 1440 80" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none">
            <path d="M0,50 C200,25 400,65 600,45 C800,25 1000,60 1200,40 C1320,28 1400,48 1440,50 L1440,80 L0,80 Z"
                  fill="rgba(0,119,182,0.06)"/>
          </svg>
        `;
        el.appendChild(wf);
      });
    });
  }

  /* ── 4. Navbar Wave ── */
  function addNavWave() {
    const nav = document.querySelector('nav.navbar');
    if (!nav || nav.querySelector('.wave-bottom')) return;
    nav.style.position = 'relative';
    nav.style.overflow = 'visible';

    const nw = document.createElement('div');
    nw.style.cssText = `
      position:absolute; bottom:-1px; left:0; right:0;
      height:20px; pointer-events:none; z-index:100;
    `;
    nw.innerHTML = `
      <svg viewBox="0 0 1440 20" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none" style="width:100%;height:100%;">
        <path d="M0,10 C360,20 720,0 1080,10 C1260,15 1380,5 1440,10 L1440,20 L0,20 Z"
              fill="rgba(0,180,216,0.3)"/>
      </svg>
    `;
    nav.appendChild(nw);
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
      addWaveToHero();
      addFloatWave();
      addNavWave();
    });
  } else {
    addWaveToHero();
    addFloatWave();
    addNavWave();
  }

})();
