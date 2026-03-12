(function () {
  'use strict';

  function buildLightbox() {
    if (document.getElementById('dspr-lightbox')) return;
    const lb = document.createElement('div');
    lb.id = 'dspr-lightbox';
    lb.setAttribute('role', 'dialog');
    lb.setAttribute('aria-modal', 'true');
    lb.setAttribute('aria-label', 'Visualizzatore immagini');
    lb.innerHTML = `
      <div class="dspr-lb-overlay"></div>
      <div class="dspr-lb-container">
        <button class="dspr-lb-close" aria-label="Chiudi">&#x2715;</button>
        <button class="dspr-lb-prev" aria-label="Precedente">&#x2039;</button>
        <button class="dspr-lb-next" aria-label="Successiva">&#x203a;</button>
        <figure class="dspr-lb-figure">
          <img class="dspr-lb-img" src="" alt="" />
          <figcaption class="dspr-lb-caption"></figcaption>
        </figure>
        <div class="dspr-lb-counter"></div>
      </div>
    `;
    document.body.appendChild(lb);
  }

  function initGalleries() {
    buildLightbox();

    // Supporta sia il markup nested (WP 6.0+) che il vecchio ul/li
    const galleries = document.querySelectorAll('figure.wp-block-gallery');
    if (!galleries.length) return;

    const lb        = document.getElementById('dspr-lightbox');
    const lbImg     = lb.querySelector('.dspr-lb-img');
    const lbCaption = lb.querySelector('.dspr-lb-caption');
    const lbCounter = lb.querySelector('.dspr-lb-counter');
    const lbClose   = lb.querySelector('.dspr-lb-close');
    const lbPrev    = lb.querySelector('.dspr-lb-prev');
    const lbNext    = lb.querySelector('.dspr-lb-next');
    const overlay   = lb.querySelector('.dspr-lb-overlay');

    let items   = [];
    let current = 0;

    function show(index) {
      current = (index + items.length) % items.length;
      const item = items[current];
      lbImg.src             = item.full;
      lbImg.alt             = item.alt;
      lbCaption.textContent = item.caption;
      lbCaption.hidden      = !item.caption;
      lbCounter.textContent = (current + 1) + ' / ' + items.length;
      lbPrev.style.display  = items.length > 1 ? '' : 'none';
      lbNext.style.display  = items.length > 1 ? '' : 'none';
    }

    function getFullSrc(img) {
      // 1. data-full-url esplicito
      if (img.dataset.fullUrl) return img.dataset.fullUrl;
      // 2. srcset: prende l'url con la larghezza più alta
      if (img.srcset) {
        const sources = img.srcset.split(',').map(s => {
          const parts = s.trim().split(' ');
          return { url: parts[0], w: parseInt(parts[1]) || 0 };
        });
        sources.sort((a, b) => b.w - a.w);
        if (sources[0]) return sources[0].url;
      }
      // 3. Fallback: strip suffisso dimensionale
      return img.src.replace(/-\d+x\d+(\.\w+)$/, '$1');
    }

    function open(gallery, clickedIndex) {
      // Markup nested WP 6.0+: figure > figure.wp-block-image > img
      // Markup classico: figure > ul > li > figure > img
      const figures = Array.from(
        gallery.querySelectorAll(':scope > figure.wp-block-image, :scope > ul > li > figure')
      );

      items = figures.map(fig => {
        const img = fig.querySelector('img');
        const cap = fig.querySelector('figcaption');
        return {
          full:    img ? getFullSrc(img) : '',
          alt:     img ? (img.alt || '') : '',
          caption: cap ? cap.textContent.trim() : ''
        };
      }).filter(item => item.full);

      if (!items.length) return;
      show(clickedIndex);
      lb.classList.add('is-open');
      document.body.style.overflow = 'hidden';
      lbClose.focus();
    }

    function close() {
      lb.classList.remove('is-open');
      document.body.style.overflow = '';
      items = [];
    }

    lbClose.addEventListener('click', close);
    overlay.addEventListener('click', close);
    lbPrev.addEventListener('click', () => show(current - 1));
    lbNext.addEventListener('click', () => show(current + 1));

    document.addEventListener('keydown', function (e) {
      if (!lb.classList.contains('is-open')) return;
      if (e.key === 'Escape')     close();
      if (e.key === 'ArrowLeft')  show(current - 1);
      if (e.key === 'ArrowRight') show(current + 1);
    });

    let touchStartX = 0;
    lb.addEventListener('touchstart', e => {
      touchStartX = e.changedTouches[0].clientX;
    }, { passive: true });
    lb.addEventListener('touchend', e => {
      const diff = touchStartX - e.changedTouches[0].clientX;
      if (Math.abs(diff) > 50) diff > 0 ? show(current + 1) : show(current - 1);
    });

    // Aggancia click su ogni gallery
    galleries.forEach(gallery => {
      const figures = Array.from(
        gallery.querySelectorAll(':scope > figure.wp-block-image, :scope > ul > li > figure')
      );
      figures.forEach((fig, idx) => {
        fig.style.cursor = 'zoom-in';
        fig.addEventListener('click', e => {
          e.preventDefault();
          e.stopPropagation();
          open(gallery, idx);
        });
      });
    });
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initGalleries);
  } else {
    initGalleries();
  }

})();
