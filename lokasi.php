<?php
/**
 * WARUNG POJOK - Lokasi / Alamat
 * Developer: KelasPojok-Dev
 */
require_once __DIR__ . '/config/app.php';

$maps_link  = setting('maps_link');
$maps_embed = setting('maps_embed');

$page_title = 'Lokasi';
$page_desc  = 'Alamat ' . setting('site_name') . ': ' . setting('address');
$active     = 'lokasi';
include __DIR__ . '/includes/header.php';
?>
<section class="bagian bagian--merah">
  <div class="container">
    <h1>Lokasi warung</h1>
    <p class="bagian__intro"><?= e(setting('address')) ?></p>
    <?php if ($maps_link): ?>
      <a class="btn btn--kuning" href="<?= e($maps_link) ?>" target="_blank" rel="noopener">Buka di Google Maps</a>
    <?php endif; ?>
  </div>
</section>

<section class="bagian bagian--krem">
  <div class="container dua-kolom">
    <div class="peta">
      <?php if ($maps_embed): ?>
        <?php /* Kode embed diisi admin lewat halaman pengaturan. */ ?>
        <iframe src="<?= e($maps_embed) ?>" loading="lazy" title="Peta lokasi <?= e(setting('site_name')) ?>"
                referrerpolicy="no-referrer-when-downgrade" allowfullscreen></iframe>
      <?php else: ?>
        <div class="peta__pengganti">
          <h3>Peta belum dipasang</h3>
          <p>Admin bisa menempelkan link embed Google Maps di halaman pengaturan. Sementara itu, gunakan tombol di bawah.</p>
          <?php if ($maps_link): ?>
            <a class="btn btn--merah" href="<?= e($maps_link) ?>" target="_blank" rel="noopener">Buka Google Maps</a>
          <?php endif; ?>
        </div>
      <?php endif; ?>
    </div>

    <div class="kontak-kartu">
      <h3>Petunjuk singkat</h3>
      <dl class="info-list">
        <dt>Alamat</dt><dd><?= e(setting('address')) ?></dd>
        <dt>Jam buka</dt><dd><?= e(setting('open_hours')) ?></dd>
        <dt>WhatsApp</dt><dd><?= e(setting('whatsapp')) ?></dd>
      </dl>
      <p>Kalau sulit menemukan warungnya, kirim titik lokasimu lewat WhatsApp dan kami arahkan.</p>
      <a class="btn btn--kecil btn--merah" href="<?= e(wa_link('Halo, saya sedang mencari lokasi warungnya.')) ?>" target="_blank" rel="noopener">Tanya arah</a>
    </div>
  </div>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>
