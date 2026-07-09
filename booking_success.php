// assets/js/main.js — JavaScript หลักของเว็บไซต์ชุมชนบ้านแหลมสน

document.addEventListener('DOMContentLoaded', () => {
  console.log('🌊 บ้านแหลมสน — ready');

  // ---- ปิด flash message อัตโนมัติหลัง 4 วินาที ----
  const flash = document.querySelector('.flash-message');
  if (flash) {
    setTimeout(() => {
      flash.style.transition = 'opacity .5s';
      flash.style.opacity = '0';
      setTimeout(() => flash.remove(), 500);
    }, 4000);
  }
});
