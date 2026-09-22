/* =====================================================================
   WARUNG POJOK - JavaScript keranjang & menu
   Developer: KelasPojok-Dev
   Isi: tombol +/- jumlah, auto submit filter, toast, validasi checkout
   ===================================================================*/
(function () {
  'use strict';

  /* ---------- 1. Tombol tambah/kurang jumlah ---------- */
  document.querySelectorAll('.jumlah').forEach(function (kotak) {
    var input = kotak.querySelector('input[type=number]');
    if (!input) return;

    kotak.querySelectorAll('button[data-aksi]').forEach(function (btn) {
      btn.addEventListener('click', function () {
        var nilai = parseInt(input.value, 10) || 1;
        var min = parseInt(input.min, 10) || 1;
        var max = parseInt(input.max, 10) || 99;

        nilai += (btn.dataset.aksi === 'tambah' ? 1 : -1);
        if (nilai < min) nilai = min;
        if (nilai > max) {
          nilai = max;
          tampilkanToast('Stok tersisa ' + max + ' porsi.');
        }
        input.value = nilai;
        input.dispatchEvent(new Event('change', { bubbles: true }));
      });
    });
  });

  /* ---------- 2. Ubah jumlah di keranjang langsung tersimpan ---------- */
  document.querySelectorAll('form[data-autosubmit] input[type=number]').forEach(function (input) {
    var jeda;
    input.addEventListener('change', function () {
      clearTimeout(jeda);
      jeda = setTimeout(function () { input.form.submit(); }, 350);
    });
  });

  /* ---------- 3. Filter kategori & urutan otomatis terkirim ---------- */
  document.querySelectorAll('[data-filter-form] select').forEach(function (sel) {
    sel.addEventListener('change', function () { sel.form.submit(); });
  });

  /* ---------- 4. Toast sederhana ---------- */
  function tampilkanToast(pesan) {
    var el = document.createElement('div');
    el.className = 'toast';
    el.textContent = pesan;
    document.body.appendChild(el);
    requestAnimationFrame(function () { el.classList.add('is-show'); });
    setTimeout(function () {
      el.classList.remove('is-show');
      setTimeout(function () { el.remove(); }, 250);
    }, 2200);
  }
  window.tampilkanToast = tampilkanToast;

  /* ---------- 5. Loading state pada tombol submit ---------- */
  document.querySelectorAll('form[data-loading]').forEach(function (form) {
    form.addEventListener('submit', function () {
      var btn = form.querySelector('button[type=submit]');
      if (btn) {
        btn.disabled = true;
        btn.dataset.teksAsli = btn.textContent;
        btn.textContent = 'Memproses…';
      }
    });
  });
})();
