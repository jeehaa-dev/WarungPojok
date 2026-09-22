<?php
/**
 * WARUNG POJOK - Kontak
 * Developer: KelasPojok-Dev
 */
require_once __DIR__ . '/config/app.php';

$sosmed = ['Instagram' => setting('instagram'), 'Facebook' => setting('facebook'), 'TikTok' => setting('tiktok')];

$page_title = 'Kontak';
$page_desc  = 'Hubungi ' . setting('site_name') . ' lewat WhatsApp untuk pesanan dan pertanyaan.';
$active     = 'kontak';
include __DIR__ . '/includes/header.php';
?>
<section class="bagian bagian--merah">
  <div class="container">
    <h1>Hubungi kami</h1>
    <p class="bagian__intro">Cara tercepat adalah lewat WhatsApp. Kami balas pada jam buka warung.</p>
    <a class="btn btn--kuning" href="<?= e(wa_link('Halo ' . setting('site_name') . ', saya mau bertanya.')) ?>" target="_blank" rel="noopener">Chat WhatsApp</a>
  </div>
</section>

<section class="bagian bagian--krem">
  <div class="container kontak-grid">
    <div class="kontak-kartu">
      <h3>WhatsApp</h3>
      <p><?= e(setting('whatsapp')) ?></p>
      <p>Untuk pesanan, tanya stok, atau pesan dalam jumlah banyak.</p>
      <a class="btn btn--kecil btn--merah" href="<?= e(wa_link()) ?>" target="_blank" rel="noopener">Kirim pesan</a>
    </div>

    <div class="kontak-kartu">
      <h3>Email</h3>
      <?php if (setting('email')): ?>
        <p><a href="mailto:<?= e(setting('email')) ?>"><?= e(setting('email')) ?></a></p>
        <p>Cocok untuk kerja sama atau penawaran pemasok.</p>
      <?php else: ?>
        <p>Email belum tersedia. Gunakan WhatsApp untuk sementara.</p>
      <?php endif; ?>
    </div>

    <div class="kontak-kartu">
      <h3>Alamat &amp; jam buka</h3>
      <p><?= e(setting('address')) ?></p>
      <p><?= e(setting('open_hours')) ?></p>
      <a class="btn btn--kecil btn--detail" href="<?= url('lokasi.php') ?>">Lihat peta</a>
    </div>

    <?php if (array_filter($sosmed)): ?>
    <div class="kontak-kartu">
      <h3>Media sosial</h3>
      <ul class="footer__list">
        <?php foreach ($sosmed as $nama => $link): if (!$link) continue; ?>
          <li><a href="<?= e($link) ?>" target="_blank" rel="noopener"><?= e($nama) ?></a></li>
        <?php endforeach; ?>
      </ul>
    </div>
    <?php endif; ?>
  </div>
</section>

<section class="bagian bagian--krem-tua">
  <div class="container">
    <div class="cta">
      <h2>Mau pesan sekarang?</h2>
      <p>Susun dulu pesananmu di halaman menu, nanti isi keranjang otomatis jadi pesan WhatsApp.</p>
      <div class="cta__aksi">
        <a class="btn btn--kuning" href="<?= url('menu.php') ?>">Buka menu</a>
        <a class="btn btn--garis" href="<?= url('keranjang.php') ?>">Lihat keranjang</a>
      </div>
    </div>
  </div>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>
