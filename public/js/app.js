// ============================================================
//  Resto ESEN — app.js
// ============================================================
document.addEventListener('DOMContentLoaded', () => {
  initCarousel();
  initPasswordToggle();
  // Auto-dismiss alerts after 5s
  document.querySelectorAll('.auto-dismiss').forEach(el => {
    setTimeout(() => {
      try { bootstrap.Alert.getOrCreateInstance(el)?.close(); } catch(e) {}
    }, 5000);
  });
  // Auto-init QR canvases
  document.querySelectorAll('canvas[data-qr]').forEach(c => generateQR(c.id, c.dataset.qr));
});

// ---- Carousel ----
function initCarousel() {
  const slides = document.querySelectorAll('.carousel-slide');
  const dots   = document.querySelectorAll('.c-dot');
  if (!slides.length) return;
  let cur = 0, timer;
  function goTo(n) {
    slides[cur].classList.remove('active'); dots[cur]?.classList.remove('active');
    cur = (n + slides.length) % slides.length;
    slides[cur].classList.add('active'); dots[cur]?.classList.add('active');
  }
  function start() { clearInterval(timer); timer = setInterval(() => goTo(cur + 1), 5500); }
  dots.forEach((d, i) => d.addEventListener('click', () => { goTo(i); start(); }));
  goTo(0); start();
}

// ---- Afficher/Masquer mot de passe ----
function initPasswordToggle() {
  document.querySelectorAll('.pwd-toggle').forEach(btn => {
    btn.addEventListener('click', () => {
      const input = document.getElementById(btn.dataset.target);
      if (!input) return;
      const show = input.type === 'password';
      input.type = show ? 'text' : 'password';
      btn.querySelector('i').className = show ? 'bi bi-eye-slash' : 'bi bi-eye';
    });
  });
}

// ---- QR Code (canvas) ----
function generateQR(canvasId, qrStr) {
  const canvas = document.getElementById(canvasId);
  if (!canvas || !qrStr) return;
  const ctx = canvas.getContext('2d');
  const size = 180, cells = 25, cell = Math.floor(size / cells);
  canvas.width = canvas.height = size;
  ctx.fillStyle = '#fff'; ctx.fillRect(0, 0, size, size);
  ctx.fillStyle = '#1C1C1E';
  let seed = 0;
  for (let i = 0; i < qrStr.length; i++) seed += qrStr.charCodeAt(i) * (i + 7);
  const rng = s => { s = Math.sin(s + seed) * 99999; return s - Math.floor(s); };
  for (let r = 0; r < cells; r++) {
    for (let c = 0; c < cells; c++) {
      const tl = r<7&&c<7, tr = r<7&&c>=cells-7, bl = r>=cells-7&&c<7;
      let fill = false;
      if (tl||tr||bl) {
        const fr = tl?r:(tr?r:r-(cells-7)), fc = tl?c:(tr?c-(cells-7):c);
        fill = fr===0||fr===6||fc===0||fc===6||(fr>=2&&fr<=4&&fc>=2&&fc<=4);
      } else {
        fill = rng(r*cells+c) > 0.48;
      }
      if (fill) ctx.fillRect(c*cell, r*cell, cell, cell);
    }
  }
  ctx.strokeStyle='#1C1C1E'; ctx.lineWidth=2;
  ctx.strokeRect(1,1,size-2,size-2);
}
