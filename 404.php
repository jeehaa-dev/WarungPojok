<?php
/**
 * WARUNG POJOK - Halaman tidak ditemukan (404)
 * Developer: KelasPojok-Dev
 */
require_once __DIR__ . '/config/app.php';
if (http_response_code() !== 404) {
    http_response_code(404);
}
$page_title = 'Halaman tidak ditemukan';
$page_desc  = 'Halaman yang dituju tidak tersedia di website ' . setting('site_name') . '.';
$active     = '';
include __DIR__ . '/includes/header.php';
?>
<section class="container notfound">
  <h1>404</h1>
  <h2>Halaman ini tidak ada</h2>
  <p>Mungkin menunya sudah diganti atau alamatnya salah ketik. Coba mulai dari daftar menu kami.</p>
  <p>
    <a class="btn btn--merah" href="<?= url('menu.php') ?>">Lihat menu</a>
    <a class="btn btn--detail" href="<?= url('index.php') ?>">Kembali ke beranda</a>
  </p>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>
