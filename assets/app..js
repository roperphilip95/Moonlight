// assets/app.js
// Tiny vanilla slider + image lightbox

// ===== Slider =====
(function(){
  const wrap = document.querySelector('.slider-wrap');
  if (!wrap) return;

  const track = wrap.querySelector('.slides');
  const slides = Array.from(wrap.querySelectorAll('.slide'));
  const dots   = wrap.querySelector('.slider-dots');
  const prev   = wrap.querySelector('.slider-prev');
  const next   = wrap.querySelector('.slider-next');

  let i = 0, timer = null, len = slides.length;

  function go(n){
    i = (n + len) % len;
    track.style.transform = `translateX(-${i*100}%)`;
    if (dots){
      dots.querySelectorAll('button').forEach((b,idx)=>{
        b.classList.toggle('active', idx===i);
      });
    }
    restart();
  }
  function restart(){
    if (timer) clearInterval(timer);
    timer = setInterval(()=>go(i+1), 5000);
  }

  if (dots) {
    dots.innerHTML = slides.map((_,idx)=>`<button aria-label="Go to slide ${idx+1}"></button>`).join('');
    dots.querySelectorAll('button').forEach((b,idx)=> b.addEventListener('click', ()=>go(idx)));
  }
  prev && prev.addEventListener('click', ()=>go(i-1));
  next && next.addEventListener('click', ()=>go(i+1));
  wrap.addEventListener('mouseenter', ()=>timer && clearInterval(timer));
  wrap.addEventListener('mouseleave', restart);

  go(0);
})();

// ===== Lightbox =====
(function(){
  const imgs = document.querySelectorAll('[data-lightbox]');
  if (!imgs.length) return;

  const overlay = document.createElement('div');
  overlay.className = 'lightbox-overlay';
  overlay.innerHTML = `
    <div class="lightbox-inner">
      <button class="lightbox-close" aria-label="Close">&times;</button>
      <img alt="">
      <div class="lightbox-cap"></div>
    </div>`;
  document.body.appendChild(overlay);

  const imgEl = overlay.querySelector('img');
  const capEl = overlay.querySelector('.lightbox-cap');
  const closeBtn = overlay.querySelector('.lightbox-close');

  function open(src, cap){
    imgEl.src = src;
    capEl.textContent = cap || '';
    overlay.classList.add('open');
    document.body.style.overflow = 'hidden';
  }
  function close(){
    overlay.classList.remove('open');
    document.body.style.overflow = '';
    imgEl.src = '';
    capEl.textContent = '';
  }

  imgs.forEach(el=>{
    el.addEventListener('click', ()=>{
      open(el.getAttribute('data-lightbox'), el.getAttribute('data-caption'));
    });
  });
  overlay.addEventListener('click', (e)=> { if (e.target===overlay) close(); });
  closeBtn.addEventListener('click', close);
  document.addEventListener('keydown', (e)=> { if (e.key==='Escape') close(); });
})();
