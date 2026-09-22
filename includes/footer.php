<?php
/**
 * WARUNG POJOK - Footer halaman publik
 * Developer: KelasPojok-Dev
 * Baris copyright developer ada di bagian paling bawah file ini.
 */
$dev_name = setting('developer_name', 'KelasPojok-Dev');
$dev_url  = setting('developer_url');
$sosmed   = [
    'Instagram' => setting('instagram'),
    'Facebook'  => setting('facebook'),
    'TikTok'    => setting('tiktok'),
];
?>
</main>

<footer class="footer">
  <div class="container footer__grid">
    <div class="footer__col">
      <div class="brand brand--footer">
        <img class="brand__logo" src="<?= e(upload_url('logo', setting('logo'), 'assets/images/logo.png')) ?>" alt="" width="46" height="46">
        <span class="brand__name"><?= e(setting('site_name')) ?></span>
      </div>
      <p class="footer__text"><?= e(setting('tagline')) ?></p>
      <p class="footer__text"><?= e(setting('open_hours')) ?></p>
    </div>

    <div class="footer__col">
      <h2 class="footer__heading">Halaman</h2>
      <ul class="footer__list">
        <li><a href="<?= url('index.php') ?>">Beranda</a></li>
        <li><a href="<?= url('menu.php') ?>">Menu</a></li>
        <li><a href="<?= url('tentang.php') ?>">Tentang kami</a></li>
        <li><a href="<?= url('galeri.php') ?>">Galeri</a></li>
        <li><a href="<?= url('kontak.php') ?>">Kontak</a></li>
        <li><a href="<?= url('lokasi.php') ?>">Lokasi</a></li>
      </ul>
    </div>

    <div class="footer__col">
      <h2 class="footer__heading">Hubungi kami</h2>
      <ul class="footer__list">
        <li><a href="<?= e(wa_link('Halo ' . setting('site_name') . ', saya mau tanya menu.')) ?>" target="_blank" rel="noopener">WhatsApp <?= e(setting('whatsapp')) ?></a></li>
        <?php if (setting('email')): ?><li><a href="mailto:<?= e(setting('email')) ?>"><?= e(setting('email')) ?></a></li><?php endif; ?>
        <li><?= e(setting('address')) ?></li>
      </ul>
      <?php if (array_filter($sosmed)): ?>
        <div class="footer__social">
          <?php foreach ($sosmed as $nama => $link): if (!$link) continue; ?>
            <a href="<?= e($link) ?>" target="_blank" rel="noopener"><?= e($nama) ?></a>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>

    <div class="footer__col footer__col--qr">
      <h2 class="footer__heading">Bagikan warung ini</h2>
      <p class="footer__text">Pindai kode ini untuk membuka menu di ponsel.</p>
      <?php
      // QR Code dibuat dari URL halaman menu. Butuh koneksi internet saat dibuka.
      $qr_target = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https' : 'http')
                 . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost') . url('menu.php');
      ?>
      <img class="qr" width="140" height="140" loading="lazy"
           src="https://api.qrserver.com/v1/create-qr-code/?size=280x280&margin=8&data=<?= rawurlencode($qr_target) ?>"
           alt="QR code menuju halaman menu <?= e(setting('site_name')) ?>">
    </div>
  </div>

  <div class="footer__bottom">
    <div class="container">
      <p class="copyright">
        &copy; <?= date('Y') ?> <?= e(setting('site_name')) ?>.
        <?php if (setting('copyright_text')): ?>
          <?= e(setting('copyright_text')) ?>
        <?php endif; ?>
        Dikembangkan oleh
        <?php if ($dev_url): ?>
          <a href="<?= e($dev_url) ?>" target="_blank" rel="noopener"><?= e($dev_name) ?></a>
        <?php else: ?>
          <strong><?= e($dev_name) ?></strong>
        <?php endif; ?>.
      </p>
    </div>
  </div>
</footer>

<a class="wa-float" href="<?= e(wa_link('Halo ' . setting('site_name') . ', saya mau pesan dimsum.')) ?>" target="_blank" rel="noopener" aria-label="Pesan lewat WhatsApp">
  <svg viewBox="0 0 24 24" width="28" height="28" aria-hidden="true" focusable="false">
    <path fill="currentColor" d="M12.04 2c-5.46 0-9.9 4.44-9.9 9.9 0 1.74.46 3.45 1.32 4.95L2 22l5.3-1.38a9.87 9.87 0 0 0 4.74 1.2h.01c5.46 0 9.9-4.44 9.9-9.9 0-2.64-1.03-5.13-2.9-7A9.82 9.82 0 0 0 12.04 2Zm5.8 14.14c-.25.69-1.44 1.32-1.99 1.36-.53.05-1.02.23-3.43-.72-2.9-1.14-4.73-4.1-4.87-4.29-.14-.19-1.16-1.54-1.16-2.94 0-1.4.73-2.08 1-2.37.25-.28.55-.35.73-.35h.53c.17 0 .4-.06.63.48.24.58.81 2 .88 2.14.07.14.12.31.02.5-.09.19-.14.31-.28.48-.14.17-.3.37-.42.5-.14.14-.29.29-.12.57.16.29.73 1.2 1.56 1.94 1.07.96 1.98 1.25 2.26 1.39.28.14.44.12.6-.07.17-.19.7-.81.88-1.09.19-.28.37-.23.63-.14.25.1 1.67.79 1.95.93.29.14.48.21.55.33.07.12.07.69-.18 1.38Z"/>
  </svg>
</a>

<button class="to-top" id="toTop" aria-label="Kembali ke atas">&uarr;</button>

<script src="<?= asset('js/main.js') ?>"></script>
<script src="<?= asset('js/cart.js') ?>"></script>
</body>
</html>
