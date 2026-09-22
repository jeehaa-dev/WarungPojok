<?php
/**
 * WARUNG POJOK - Profil UMKM / Tentang kami
 * Developer: KelasPojok-Dev
 */
require_once __DIR__ . '/config/app.php';

$foto = $pdo->query('SELECT * FROM gallery ORDER BY created_at DESC LIMIT 3')->fetchAll();

$page_title = 'Tentang kami';
$page_desc  = potong(setting('about_story'), 155);
$active     = 'tentang';
include __DIR__ . '/includes/header.php';
?>

<section class="bagian bagian--merah">
  <div class="container">
    <h1><?= e(setting('about_title', 'Tentang ' . setting('site_name'))) ?></h1>
    <p class="bagian__intro"><?= e(setting('tagline')) ?></p>
  </div>
</section>

<section class="bagian bagian--krem">
  <div class="container dua-kolom">
    <div>
      <h2>Cerita kami</h2>
      <p><?= nl2br(e(setting('about_story'))) ?></p>

      <h2>Visi</h2>
      <p><?= e(setting('about_vision')) ?></p>

      <h2>Misi</h2>
      <p><?= e(setting('about_mission')) ?></p>

      <h2>Untuk siapa</h2>
      <p><?= e(setting('about_target')) ?></p>
    </div>

    <div>
      <div class="kontak-kartu">
        <h3>Profil singkat</h3>
        <dl class="info-list">
          <dt>Nama usaha</dt><dd><?= e(setting('site_name')) ?></dd>
          <dt>Produk utama</dt><dd>Dimsum mentai</dd>
          <dt>Jam buka</dt><dd><?= e(setting('open_hours')) ?></dd>
          <dt>Alamat</dt><dd><?= e(setting('address')) ?></dd>
          <dt>WhatsApp</dt><dd><?= e(setting('whatsapp')) ?></dd>
        </dl>
        <a class="btn btn--kecil btn--merah" href="<?= url('menu.php') ?>">Lihat menu kami</a>
      </div>
    </div>
  </div>
</section>

<section class="bagian bagian--krem-tua">
  <div class="container">
    <h2>Yang kami jaga</h2>
    <div class="grid-nilai">
      <div class="nilai"><h3><?= e(setting('advantage_1')) ?></h3><p>Adonan dibentuk pagi, dikukus saat pesanan masuk.</p></div>
      <div class="nilai"><h3><?= e(setting('advantage_2')) ?></h3><p>Saus mentai tanpa pengawet tambahan.</p></div>
      <div class="nilai"><h3><?= e(setting('advantage_3')) ?></h3><p>Harga yang masih masuk akal untuk camilan harian.</p></div>
      <div class="nilai"><h3><?= e(setting('advantage_4')) ?></h3><p>Antar sendiri untuk area dekat warung.</p></div>
    </div>
  </div>
</section>

<?php if ($foto): ?>
<section class="bagian bagian--krem">
  <div class="container">
    <h2>Suasana warung</h2>
    <div class="grid-galeri">
      <?php foreach ($foto as $g): ?>
        <button class="galeri__item" type="button"
                data-full="<?= e(upload_url('gallery', $g['image'])) ?>" data-judul="<?= e($g['title']) ?>">
          <img src="<?= e(upload_url('gallery', $g['image'])) ?>" alt="<?= e($g['title']) ?>" loading="lazy" width="400" height="400">
        </button>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<?php include __DIR__ . '/includes/footer.php'; ?>
