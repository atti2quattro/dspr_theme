(function () {
  'use strict';

  function initSliders() {
    document.querySelectorAll('.wp-block-gallery.is-style-slider').forEach(function (gallery) {
      if (gallery.dataset.sliderInit) return;
      gallery.dataset.sliderInit = '1';

      var figures = Array.from(gallery.querySelectorAll(':scope > figure.wp-block-image'));
      if (figures.length < 2) return;

      var current = 0;
      var total   = figures.length;
      var startX  = 0;

      // Legge il caption nativo del blocco gallery (figcaption diretto)
      var captionEl = gallery.querySelector(':scope > figcaption');
      var titleText = captionEl ? captionEl.textContent.trim() : '';

      // Track
      var track = document.createElement('div');
      track.className = 'dspr-slider__track';
      figures.forEach(function (fig) {
        fig.classList.add('dspr-slider__slide');
        track.appendChild(fig);
      });

      // Titolo (dal caption nativo)
      var titleEl = null;
      if (titleText) {
        titleEl = document.createElement('div');
        titleEl.className = 'dspr-slider__title';
        titleEl.textContent = titleText;
      }

      // Freccia prev (solo desktop via CSS)
      var btnPrev = document.createElement('button');
      btnPrev.className = 'dspr-slider__btn dspr-slider__btn--prev';
      btnPrev.setAttribute('aria-label', 'Immagine precedente');
      btnPrev.innerHTML = '&#8249;';

      // Freccia next (solo desktop via CSS)
      var btnNext = document.createElement('button');
      btnNext.className = 'dspr-slider__btn dspr-slider__btn--next';
      btnNext.setAttribute('aria-label', 'Immagine successiva');
      btnNext.innerHTML = '&#8250;';

      // Dots
      var dotsWrap = document.createElement('div');
      dotsWrap.className = 'dspr-slider__dots';
      dotsWrap.setAttribute('aria-hidden', 'true');
      for (var i = 0; i < total; i++) {
        var dot = document.createElement('span');
        dot.className = 'dspr-slider__dot' + (i === 0 ? ' dspr-slider__dot--active' : '');
        dot.dataset.index = i;
        dotsWrap.appendChild(dot);
      }

      // Counter
      var counter = document.createElement('div');
      counter.className = 'dspr-slider__counter';

      // Ricostruisce la gallery
      gallery.innerHTML = '';
      gallery.classList.add('dspr-slider');
      if (titleEl) gallery.appendChild(titleEl);
      gallery.appendChild(track);
      gallery.appendChild(btnPrev);
      gallery.appendChild(btnNext);
      gallery.appendChild(dotsWrap);
      gallery.appendChild(counter);

      function goTo(index) {
        current = (index + total) % total;
        track.style.transform = 'translateX(-' + (current * 100) + '%)';
        dotsWrap.querySelectorAll('.dspr-slider__dot').forEach(function (d, i) {
          d.classList.toggle('dspr-slider__dot--active', i === current);
        });
        counter.textContent = (current + 1) + ' / ' + total;
      }

      goTo(0);

      // Frecce
      btnPrev.addEventListener('click', function (e) { e.stopPropagation(); goTo(current - 1); });
      btnNext.addEventListener('click', function (e) { e.stopPropagation(); goTo(current + 1); });

      // Dots
      dotsWrap.addEventListener('click', function (e) {
        var dot = e.target.closest('.dspr-slider__dot');
        if (dot) goTo(parseInt(dot.dataset.index));
      });

      // Touch swipe
      gallery.addEventListener('touchstart', function (e) {
        startX = e.changedTouches[0].clientX;
      }, { passive: true });
      gallery.addEventListener('touchend', function (e) {
        var diff = startX - e.changedTouches[0].clientX;
        if (Math.abs(diff) > 40) diff > 0 ? goTo(current + 1) : goTo(current - 1);
      });

      // Tastiera
      gallery.setAttribute('tabindex', '0');
      gallery.addEventListener('keydown', function (e) {
        if (e.key === 'ArrowLeft')  goTo(current - 1);
        if (e.key === 'ArrowRight') goTo(current + 1);
      });
    });
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initSliders);
  } else {
    initSliders();
  }

})();
