/* =====================================================================
   WARUNG POJOK - JavaScript panel admin
   Developer: KelasPojok-Dev
   Isi: konfirmasi hapus, pratinjau gambar sebelum diunggah
   ===================================================================*/
(function () {
  'use strict';

  /* Konfirmasi sebelum tindakan yang tidak bisa dibatalkan */
  document.querySelectorAll('[data-konfirmasi]').forEach(function (el) {
    el.addEventListener('click', function (ev) {
      if (!window.confirm(el.dataset.konfirmasi)) ev.preventDefault();
    });
  });

  /* Pratinjau gambar sebelum disimpan */
  document.querySelectorAll('input[type=file][data-preview]').forEach(function (input) {
    input.addEventListener('change', function () {
      var target = document.getElementById(input.dataset.preview);
      var file = input.files && input.files[0];
      if (!target || !file) return;

      if (file.size > 2 * 1024 * 1024) {
        window.alert('Ukuran gambar melebihi 2 MB. Pilih file yang lebih kecil.');
        input.value = '';
        return;
      }
      target.src = URL.createObjectURL(file);
    });
  });

  /* Tombol simpan menampilkan status saat form dikirim */
  document.querySelectorAll('form').forEach(function (form) {
    form.addEventListener('submit', function () {
      var btn = form.querySelector('button[type=submit]:not([name])');
      if (btn && !btn.disabled) {
        setTimeout(function () {
          btn.disabled = true;
          btn.textContent = 'Menyimpan…';
        }, 0);
      }
    });
  });
})();
