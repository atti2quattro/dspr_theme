(function () {
  document.addEventListener('DOMContentLoaded', function () {
    var url   = encodeURIComponent(window.location.href);
    var title = encodeURIComponent(document.title);

    var fb = document.querySelector('.share-icon--fb');
    var wa = document.querySelector('.share-icon--wa');
    var tg = document.querySelector('.share-icon--tg');

    if (fb) fb.addEventListener('click', function () {
      window.open('https://www.facebook.com/sharer/sharer.php?u=' + url, '_blank', 'width=600,height=400');
    });
    if (wa) wa.addEventListener('click', function () {
      window.open('https://api.whatsapp.com/send?text=' + title + '%20' + url, '_blank');
    });
    if (tg) tg.addEventListener('click', function () {
      window.open('https://t.me/share/url?url=' + url + '&text=' + title, '_blank');
    });
  });
})();
