<?php
/**
 * WARUNG POJOK - Galeri foto
 * Developer: KelasPojok-Dev
 * Foto diambil dari tabel gallery dan dikelola lewat admin/gallery.php
 */
require_once __DIR__ . '/config/app.php';

$galeri = $pdo->query('SELECT * FROM gallery ORDER BY created_at DESC')->fetchAll();

$page_title = 'Galeri';
$page_desc  = 'Foto menu dan suasana ' . setting('site_name') . '.';
$active     = 'galeri';
include __DIR__ . '/includes/header.php';
?>
<section class="bagian bagian--krem">
  <div class="container">
    <h1 class="bagian__judul">Galeri</h1>
    <p class="bagian__intro">Klik foto untuk melihat ukuran penuh.</p>

    <?php if (!$galeri): ?>
      <div class="kosong">
        <h2>Belum ada foto</h2>
        <p>Admin bisa menambahkan foto lewat halaman kelola galeri.</p>
      </div>
    <?php else: ?>
      <div class="grid-galeri">
        <?php foreach ($galeri as $g): ?>
          <button class="galeri__item" type="button"
                  data-full="<?= e(upload_url('gallery', $g['image'])) ?>"
                  data-judul="<?= e($g['title'] . ($g['description'] ? ' - ' . $g['description'] : '')) ?>">
            <img src="<?= e(upload_url('gallery', $g['image'])) ?>" alt="<?= e($g['title']) ?>" loading="lazy" width="400" height="400">
          </button>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>
