/**
 * Il Dispari – Dark/Light mode toggle
 * Salva la preferenza in localStorage.
 * L'anti-flash è gestito da uno script inline in <head> (functions.php).
 */
(function () {
  'use strict';

  var STORAGE_KEY = 'dspr-theme';

  document.addEventListener('DOMContentLoaded', function () {
    var btn = document.querySelector('.theme-toggle');
    if (!btn) return;

    var icon  = btn.querySelector('.theme-toggle__icon');
    var label = btn.querySelector('.theme-toggle__label');

    function updateBtn(isDark) {
      if (icon)  icon.textContent  = isDark ? '☀' : '☾';
      if (label) label.textContent = isDark ? 'Chiaro' : 'Scuro';
      btn.setAttribute('aria-label', isDark ? 'Passa al tema chiaro' : 'Passa al tema scuro');
    }

    // Legge lo stato dal <html> (impostato dall'anti-flash in <head>)
    var isDark = document.documentElement.getAttribute('data-theme') === 'dark';
    // Sincronizza anche il body per compatibilità CSS
    if (isDark) document.body.setAttribute('data-theme', 'dark');
    updateBtn(isDark);

    btn.addEventListener('click', function () {
      isDark = !isDark;
      if (isDark) {
        document.documentElement.setAttribute('data-theme', 'dark');
        document.body.setAttribute('data-theme', 'dark');
        localStorage.setItem(STORAGE_KEY, 'dark');
      } else {
        document.documentElement.removeAttribute('data-theme');
        document.body.removeAttribute('data-theme');
        localStorage.setItem(STORAGE_KEY, 'light');
      }
      updateBtn(isDark);
    });
  });

})();
