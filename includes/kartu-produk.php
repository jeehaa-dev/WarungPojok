<?php
/**
 * WARUNG POJOK - Kartu produk (dipakai di beranda & halaman menu)
 * Developer: KelasPojok-Dev
 * Membutuhkan variabel: $p (data produk, boleh berisi kolom category_name)
 */
$habis  = produk_habis($p);
$r      = rating_produk($p['id']);
$link   = url('detail-produk.php?slug=' . urlencode($p['slug']));
?>
<article class="kartu">
  <div class="kartu__media">
    <?php if ($habis): ?>
      <span class="badge badge--habis">Habis</span>
    <?php elseif (!empty($p['is_featured'])): ?>
      <span class="badge badge--best">Best Seller</span>
    <?php endif; ?>
    <a href="<?= $link ?>">
      <img src="<?= e(upload_url('products', $p['image'])) ?>" alt="<?= e($p['name']) ?>" loading="lazy" width="400" height="400">
    </a>
  </div>
  <div class="kartu__body">
    <p class="kartu__kategori"><?= e($p['category_name'] ?? 'Menu') ?></p>
    <h3 class="kartu__nama"><a href="<?= $link ?>"><?= e($p['name']) ?></a></h3>
    <div class="kartu__rating">
      <?= bintang($r['rata']) ?>
      <span><?= $r['jumlah'] ? number_format($r['rata'], 1, ',', '.') . ' (' . $r['jumlah'] . ' ulasan)' : 'Belum ada ulasan' ?></span>
    </div>
    <p class="kartu__harga"><?= rupiah($p['price']) ?></p>
    <div class="kartu__aksi">
      <a class="btn btn--kecil btn--garis btn--detail" href="<?= $link ?>">Lihat detail</a>
      <?php if ($habis): ?>
        <button class="btn btn--kecil" type="button" disabled>Stok habis</button>
      <?php else: ?>
        <form method="post" action="<?= url('keranjang-aksi.php') ?>">
          <?= csrf_field() ?>
          <input type="hidden" name="aksi" value="tambah">
          <input type="hidden" name="product_id" value="<?= (int)$p['id'] ?>">
          <input type="hidden" name="qty" value="1">
          <input type="hidden" name="kembali" value="<?= e($_SERVER['REQUEST_URI'] ?? '/') ?>">
          <button class="btn btn--kecil btn--merah" type="submit">Tambah</button>
        </form>
      <?php endif; ?>
    </div>
  </div>
</article>
