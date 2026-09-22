<?php
/**
 * WARUNG POJOK - Menu samping panel admin
 * Developer: KelasPojok-Dev
 */
$adm_menu = [
    'dashboard' => ['Dashboard', 'index.php'],
    'produk'    => ['Produk', 'products.php'],
    'stok'      => ['Stok', 'stock.php'],
    'kategori'  => ['Kategori', 'categories.php'],
    'galeri'    => ['Galeri', 'gallery.php'],
    'ulasan'    => ['Ulasan', 'reviews.php'],
    'pesanan'   => ['Pesanan', 'orders.php'],
    'pengaturan'=> ['Pengaturan tampilan', 'settings.php'],
];
?>
  <aside class="adm-side">
    <a class="adm-side__brand" href="<?= url('admin/index.php') ?>">
      <img src="<?= e(upload_url('logo', setting('logo'), 'assets/images/logo.png')) ?>" alt="" width="40" height="40">
      <span><?= e(setting('site_name')) ?></span>
    </a>
    <nav aria-label="Menu admin">
      <ul class="adm-nav">
        <?php foreach ($adm_menu as $key => [$label, $file]): ?>
          <li>
            <a href="<?= url('admin/' . $file) ?>" class="<?= ($adm_active ?? '') === $key ? 'is-active' : '' ?>"><?= e($label) ?></a>
          </li>
        <?php endforeach; ?>
        <li><a href="<?= url('admin/logout.php') ?>">Keluar</a></li>
      </ul>
    </nav>
    <div class="adm-side__bawah">
      &copy; <?= date('Y') ?> <?= e(setting('site_name')) ?><br>
      Dikembangkan oleh
      <?php $dv = setting('developer_url'); ?>
      <?php if ($dv): ?><a href="<?= e($dv) ?>" target="_blank" rel="noopener"><?= e(setting('developer_name', 'KelasPojok-Dev')) ?></a>
      <?php else: ?><strong><?= e(setting('developer_name', 'KelasPojok-Dev')) ?></strong><?php endif; ?>
    </div>
  </aside>
