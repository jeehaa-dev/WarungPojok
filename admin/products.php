<?php
/**
 * WARUNG POJOK - Kelola produk (daftar)
 * Developer: KelasPojok-Dev
 */
require_once dirname(__DIR__) . '/includes/auth.php';

$cari     = trim($_GET['cari'] ?? '');
$kategori = (int)($_GET['kategori'] ?? 0);

$where = ['1=1'];
$params = [];
if ($cari !== '') {
    $where[] = 'p.name LIKE ?';
    $params[] = '%' . $cari . '%';
}
if ($kategori > 0) {
    $where[] = 'p.category_id = ?';
    $params[] = $kategori;
}
$sql = 'SELECT p.*, c.name AS category_name FROM products p
        LEFT JOIN categories c ON c.id = p.category_id
        WHERE ' . implode(' AND ', $where) . ' ORDER BY p.created_at DESC';
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$produk = $stmt->fetchAll();

$kategori_list = $pdo->query('SELECT * FROM categories ORDER BY name')->fetchAll();

$adm_title  = 'Produk';
$adm_active = 'produk';
include __DIR__ . '/includes/header.php';
?>

<div class="adm-panel">
  <div class="adm-aksi" style="justify-content:space-between">
    <form class="adm-filter" method="get" action="<?= url('admin/products.php') ?>">
      <div>
        <label for="cari">Cari produk</label>
        <input type="text" id="cari" name="cari" value="<?= e($cari) ?>" placeholder="Nama produk">
      </div>
      <div>
        <label for="kategori">Kategori</label>
        <select id="kategori" name="kategori">
          <option value="0">Semua kategori</option>
          <?php foreach ($kategori_list as $k): ?>
            <option value="<?= (int)$k['id'] ?>" <?= $kategori === (int)$k['id'] ? 'selected' : '' ?>><?= e($k['name']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <button class="adm-btn adm-btn--garis" type="submit">Terapkan</button>
    </form>
    <a class="adm-btn adm-btn--utama" href="<?= url('admin/product-add.php') ?>">Tambah produk</a>
  </div>

  <?php if (!$produk): ?>
    <p class="adm-kosong">Belum ada produk yang cocok. Tambahkan produk baru untuk mulai mengisi katalog.</p>
  <?php else: ?>
    <div class="adm-tabel-scroll">
      <table class="adm-tabel">
        <thead>
          <tr><th>Gambar</th><th>Nama</th><th>Kategori</th><th>Harga</th><th>Stok</th><th>Status</th><th class="kanan">Aksi</th></tr>
        </thead>
        <tbody>
        <?php foreach ($produk as $p): ?>
          <tr>
            <td><img class="thumb" src="<?= e(upload_url('products', $p['image'])) ?>" alt="" loading="lazy"></td>
            <td>
              <strong><?= e($p['name']) ?></strong><br>
              <?php if ($p['is_featured']): ?><span class="adm-label adm-label--kuning">Unggulan</span><?php endif; ?>
            </td>
            <td><?= e($p['category_name'] ?? '-') ?></td>
            <td><?= rupiah($p['price']) ?></td>
            <td><?= (int)$p['stock'] ?></td>
            <td>
              <?php
              $kelas = $p['status'] === 'tersedia' ? 'hijau' : ($p['status'] === 'habis' ? 'merah' : 'abu');
              ?>
              <span class="adm-label adm-label--<?= $kelas ?>"><?= e($p['status']) ?></span>
            </td>
            <td class="kanan">
              <div class="adm-aksi" style="justify-content:flex-end">
                <a class="adm-btn adm-btn--garis adm-btn--kecil" href="<?= url('detail-produk.php?slug=' . urlencode($p['slug'])) ?>" target="_blank" rel="noopener">Lihat</a>
                <a class="adm-btn adm-btn--garis adm-btn--kecil" href="<?= url('admin/product-edit.php?id=' . (int)$p['id']) ?>">Edit</a>
                <form method="post" action="<?= url('admin/product-delete.php') ?>">
                  <?= csrf_field() ?>
                  <input type="hidden" name="id" value="<?= (int)$p['id'] ?>">
                  <button class="adm-btn adm-btn--bahaya adm-btn--kecil" type="submit"
                          data-konfirmasi="Hapus produk <?= e($p['name']) ?>? Tindakan ini tidak bisa dibatalkan.">Hapus</button>
                </form>
              </div>
            </td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
