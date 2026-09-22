/* =====================================================================
   WARUNG POJOK - JavaScript utama
   Developer: KelasPojok-Dev
   Isi: menu mobile, lightbox galeri, tutup notifikasi, tombol ke atas
   ===================================================================*/
(function () {
  'use strict';

  /* ---------- 1. Menu mobile (hamburger / off-canvas) ---------- */
  var toggle = document.getElementById('navToggle');
  var nav = document.getElementById('navMenu');

  if (toggle && nav) {
    var backdrop = document.createElement('div');
    backdrop.className = 'nav-backdrop';
    document.body.appendChild(backdrop);

    function setMenu(open) {
      nav.classList.toggle('is-open', open);
      backdrop.classList.toggle('is-open', open);
      toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
      toggle.setAttribute('aria-label', open ? 'Tutup menu' : 'Buka menu');
      document.body.style.overflow = open ? 'hidden' : '';
    }

    toggle.addEventListener('click', function () {
      setMenu(!nav.classList.contains('is-open'));
    });
    backdrop.addEventListener('click', function () { setMenu(false); });
    document.addEventListener('keydown', function (ev) {
      if (ev.key === 'Escape') setMenu(false);
    });
  }

  /* ---------- 2. Tutup notifikasi ---------- */
  document.querySelectorAll('.flash__close').forEach(function (btn) {
    btn.addEventListener('click', function () {
      var box = btn.closest('.flash');
      if (box) box.remove();
    });
  });

  /* ---------- 3. Lightbox galeri ---------- */
  var tombolGaleri = document.querySelectorAll('.galeri__item');
  if (tombolGaleri.length) {
    var box = document.createElement('div');
    box.className = 'lightbox';
    box.innerHTML =
      '<button class="lightbox__close" type="button" aria-label="Tutup gambar">&times;</button>' +
      '<div><img alt=""><p class="lightbox__caption"></p></div>';
    document.body.appendChild(box);

    var gambar = box.querySelector('img');
    var teks = box.querySelector('.lightbox__caption');

    function bukaLightbox(src, judul) {
      gambar.src = src;
      gambar.alt = judul || '';
      teks.textContent = judul || '';
      box.classList.add('is-open');
      box.querySelector('.lightbox__close').focus();
    }
    function tutupLightbox() {
      box.classList.remove('is-open');
      gambar.src = '';
    }

    tombolGaleri.forEach(function (btn) {
      btn.addEventListener('click', function () {
        bukaLightbox(btn.dataset.full || btn.querySelector('img').src, btn.dataset.judul);
      });
    });
    box.addEventListener('click', function (ev) {
      if (ev.target === box || ev.target.classList.contains('lightbox__close')) tutupLightbox();
    });
    document.addEventListener('keydown', function (ev) {
      if (ev.key === 'Escape') tutupLightbox();
    });
  }

  /* ---------- 4. Tombol kembali ke atas ---------- */
  var keAtas = document.getElementById('toTop');
  if (keAtas) {
    window.addEventListener('scroll', function () {
      keAtas.classList.toggle('is-show', window.scrollY > 500);
    }, { passive: true });
    keAtas.addEventListener('click', function () {
      window.scrollTo({ top: 0, behavior: 'smooth' });
    });
  }

  /* ---------- 5. Konfirmasi sebelum aksi menghapus ---------- */
  document.querySelectorAll('[data-konfirmasi]').forEach(function (el) {
    el.addEventListener('click', function (ev) {
      if (!window.confirm(el.dataset.konfirmasi)) ev.preventDefault();
    });
  });
})();
