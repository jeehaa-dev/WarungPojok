<?php
/**
 * WARUNG POJOK - Navbar publik
 * Developer: KelasPojok-Dev
 * Catatan: link login admin SENGAJA tidak ditampilkan di sini.
 */
$menu = [
    'beranda' => ['Beranda', 'index.php'],
    'menu'    => ['Menu', 'menu.php'],
    'tentang' => ['Tentang kami', 'tentang.php'],
    'galeri'  => ['Galeri', 'galeri.php'],
    'kontak'  => ['Kontak', 'kontak.php'],
    'lokasi'  => ['Lokasi', 'lokasi.php'],
];
$jml_keranjang = cart_count();
?>
<header class="navbar" id="navbar">
  <div class="container navbar__inner">
    <a class="brand" href="<?= url('index.php') ?>">
      <img class="brand__logo" src="<?= e($logo_url ?? upload_url('logo', setting('logo'), 'assets/images/logo.png')) ?>" alt="Logo <?= e(setting('site_name')) ?>" width="80" height="80">
      <span class="brand__name"><?= e(setting('site_name', 'WARUNG POJOK')) ?></span>
    </a>

    <button class="nav-toggle" id="navToggle" aria-expanded="false" aria-controls="navMenu" aria-label="Buka menu">
      <span></span><span></span><span></span>
    </button>

    <nav class="nav" id="navMenu" aria-label="Menu utama">
      <ul class="nav__list">
        <?php foreach ($menu as $key => [$label, $file]): ?>
          <li>
            <a class="nav__link<?= ($active ?? '') === $key ? ' is-active' : '' ?>"
               href="<?= url($file) ?>"<?= ($active ?? '') === $key ? ' aria-current="page"' : '' ?>>
              <?= e($label) ?>
            </a>
          </li>
        <?php endforeach; ?>
        <li class="nav__cart-mobile">
          <a class="nav__link" href="<?= url('keranjang.php') ?>">Keranjang (<?= (int)$jml_keranjang ?>)</a>
        </li>
      </ul>
    </nav>

    <a class="cart-link" href="<?= url('keranjang.php') ?>" aria-label="Lihat keranjang, <?= (int)$jml_keranjang ?> item">
      <svg viewBox="0 0 24 24" width="26" height="26" aria-hidden="true" focusable="false">
        <path fill="currentColor" d="M7 18a2 2 0 1 0 0 4 2 2 0 0 0 0-4Zm10 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4ZM6.2 6h14.3a.8.8 0 0 1 .78.98l-1.6 7A1.8 1.8 0 0 1 17.93 15H8.4a1.8 1.8 0 0 1-1.76-1.42L4.6 4.4A.8.8 0 0 0 3.82 3.8H2a.8.8 0 0 1 0-1.6h1.82A2.4 2.4 0 0 1 6.17 4.1L6.2 6Z"/>
      </svg>
      <span class="cart-link__text">Keranjang</span>
      <?php if ($jml_keranjang > 0): ?>
        <span class="cart-link__badge"><?= (int)$jml_keranjang ?></span>
      <?php endif; ?>
    </a>
  </div>
</header>
