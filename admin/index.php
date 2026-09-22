<?php
/**
 * WARUNG POJOK - Dashboard admin
 * Developer: KelasPojok-Dev
 */
require_once dirname(__DIR__) . '/includes/auth.php';

$jml_produk   = (int)$pdo->query("SELECT COUNT(*) FROM products WHERE status <> 'nonaktif'")->fetchColumn();
$jml_habis    = (int)$pdo->query("SELECT COUNT(*) FROM products WHERE stock <= 0 OR status = 'habis'")->fetchColumn();
$jml_pesanan  = (int)$pdo->query("SELECT COUNT(*) FROM orders WHERE order_status = 'baru'")->fetchColumn();
$jml_ulasan   = (int)$pdo->query("SELECT COUNT(*) FROM reviews WHERE status = 'pending'")->fetchColumn();
$total_bulan  = (float)$pdo->query(
    "SELECT COALESCE(SUM(total),0) FROM orders
     WHERE order_status <> 'batal' AND MONTH(created_at) = MONTH(CURDATE()) AND YEAR(created_at) = YEAR(CURDATE())"
)->fetchColumn();

$stok_menipis = $pdo->query(
    "SELECT id, name, stock FROM products WHERE status <> 'nonaktif' AND stock <= 5 ORDER BY stock ASC LIMIT 6"
)->fetchAll();

$pesanan_baru = $pdo->query(
    'SELECT * FROM orders ORDER BY created_at DESC LIMIT 6'
)->fetchAll();

$adm_title  = 'Dashboard';
$adm_active = 'dashboard';
include __DIR__ . '/includes/header.php';
?>

<div class="adm-grid">
  <div class="adm-stat"><span>Produk aktif</span><strong><?= $jml_produk ?></strong></div>
  <div class="adm-stat"><span>Produk habis</span><strong><?= $jml_habis ?></strong></div>
  <div class="adm-stat"><span>Pesanan baru</span><strong><?= $jml_pesanan ?></strong></div>
  <div class="adm-stat"><span>Ulasan menunggu</span><strong><?= $jml_ulasan ?></strong></div>
  <div class="adm-stat"><span>Nilai pesanan bulan ini</span><strong style="font-size:1.35rem"><?= rupiah($total_bulan) ?></strong></div>
</div>

<div class="adm-panel">
  <h2>Yang bisa dikerjakan sekarang</h2>
  <div class="adm-aksi">
    <a class="adm-btn adm-btn--utama" href="<?= url('admin/product-add.php') ?>">Tambah produk</a>
    <a class="adm-btn adm-btn--garis" href="<?= url('admin/stock.php') ?>">Perbarui stok</a>
    <a class="adm-btn adm-btn--garis" href="<?= url('admin/reviews.php') ?>">Moderasi ulasan</a>
    <a class="adm-btn adm-btn--garis" href="<?= url('admin/settings.php') ?>">Ubah tampilan website</a>
  </div>
</div>

<div class="adm-panel">
  <h2>Stok menipis</h2>
  <?php if (!$stok_menipis): ?>
    <p class="adm-kosong">Semua stok masih aman.</p>
  <?php else: ?>
    <div class="adm-tabel-scroll">
      <table class="adm-tabel">
        <thead><tr><th>Produk</th><th>Sisa stok</th><th class="kanan">Aksi</th></tr></thead>
        <tbody>
        <?php foreach ($stok_menipis as $p): ?>
          <tr>
            <td><?= e($p['name']) ?></td>
            <td>
              <?php if ((int)$p['stock'] <= 0): ?>
                <span class="adm-label adm-label--merah">Habis</span>
              <?php else: ?>
                <span class="adm-label adm-label--kuning"><?= (int)$p['stock'] ?> porsi</span>
              <?php endif; ?>
            </td>
            <td class="kanan"><a class="adm-btn adm-btn--garis adm-btn--kecil" href="<?= url('admin/stock.php') ?>">Isi stok</a></td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>
</div>

<div class="adm-panel">
  <h2>Pesanan terakhir</h2>
  <?php if (!$pesanan_baru): ?>
    <p class="adm-kosong">Belum ada pesanan yang tersimpan. Pesanan dari halaman checkout akan muncul di sini.</p>
  <?php else: ?>
    <div class="adm-tabel-scroll">
      <table class="adm-tabel">
        <thead><tr><th>Kode</th><th>Pemesan</th><th>Metode</th><th>Total</th><th>Status</th><th class="kanan">Detail</th></tr></thead>
        <tbody>
        <?php foreach ($pesanan_baru as $o): ?>
          <tr>
            <td><?= e($o['order_code']) ?></td>
            <td><?= e($o['customer_name']) ?></td>
            <td><?= $o['payment_method'] === 'transfer' ? 'Transfer' : 'WhatsApp' ?></td>
            <td><?= rupiah($o['total']) ?></td>
            <td><span class="adm-label adm-label--abu"><?= e(str_replace('_', ' ', $o['order_status'])) ?></span></td>
            <td class="kanan"><a class="adm-btn adm-btn--garis adm-btn--kecil" href="<?= url('admin/orders.php?id=' . (int)$o['id']) ?>">Lihat</a></td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
