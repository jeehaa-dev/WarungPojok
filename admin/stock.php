<?php
/**
 * WARUNG POJOK - Kelola stok
 * Developer: KelasPojok-Dev
 * Admin bisa menambah, mengurangi, atau mengatur stok langsung.
 */
require_once dirname(__DIR__) . '/includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();

    $id    = (int)($_POST['id'] ?? 0);
    $aksi  = $_POST['aksi'] ?? 'set';
    $nilai = (int)($_POST['nilai'] ?? 0);

    $stmt = $pdo->prepare('SELECT * FROM products WHERE id = ? LIMIT 1');
    $stmt->execute([$id]);
    $produk = $stmt->fetch();

    if (!$produk) {
        set_flash('error', 'Produk tidak ditemukan.');
        redirect('admin/stock.php');
    }

    $stok_lama = (int)$produk['stock'];
    if ($aksi === 'tambah')      $stok_baru = $stok_lama + max(0, $nilai);
    elseif ($aksi === 'kurang')  $stok_baru = max(0, $stok_lama - max(0, $nilai));
    else                         $stok_baru = max(0, $nilai);

    // Status ikut menyesuaikan stok.
    $status = $produk['status'];
    if ($status !== 'nonaktif') {
        $status = $stok_baru > 0 ? 'tersedia' : 'habis';
    }

    $pdo->prepare('UPDATE products SET stock = ?, status = ? WHERE id = ?')
        ->execute([$stok_baru, $status, $id]);

    set_flash('sukses', 'Stok ' . $produk['name'] . ' diperbarui: ' . $stok_lama . ' menjadi ' . $stok_baru . '.');
    redirect('admin/stock.php');
}

$produk = $pdo->query(
    "SELECT p.*, c.name AS category_name FROM products p
     LEFT JOIN categories c ON c.id = p.category_id
     WHERE p.status <> 'nonaktif' ORDER BY p.stock ASC, p.name ASC"
)->fetchAll();

$adm_title  = 'Stok';
$adm_active = 'stok';
include __DIR__ . '/includes/header.php';
?>
<div class="adm-panel">
  <h2>Perbarui stok</h2>
  <p class="adm-bantuan">Isi jumlah lalu pilih tindakannya. Produk dengan stok 0 otomatis berstatus habis dan tombol belinya nonaktif di website.</p>

  <?php if (!$produk): ?>
    <p class="adm-kosong">Belum ada produk aktif.</p>
  <?php else: ?>
    <div class="adm-tabel-scroll">
      <table class="adm-tabel">
        <thead><tr><th>Produk</th><th>Kategori</th><th>Stok kini</th><th>Ubah stok</th></tr></thead>
        <tbody>
        <?php foreach ($produk as $p): ?>
          <tr>
            <td>
              <strong><?= e($p['name']) ?></strong><br>
              <span class="adm-label adm-label--<?= (int)$p['stock'] > 0 ? 'hijau' : 'merah' ?>"><?= e($p['status']) ?></span>
            </td>
            <td><?= e($p['category_name'] ?? '-') ?></td>
            <td><strong><?= (int)$p['stock'] ?></strong></td>
            <td>
              <form method="post" action="<?= url('admin/stock.php') ?>" class="adm-aksi">
                <?= csrf_field() ?>
                <input type="hidden" name="id" value="<?= (int)$p['id'] ?>">
                <label class="sr-only" for="nilai<?= (int)$p['id'] ?>">Jumlah</label>
                <input type="number" id="nilai<?= (int)$p['id'] ?>" name="nilai" value="5" min="0" style="width:90px">
                <button class="adm-btn adm-btn--garis adm-btn--kecil" type="submit" name="aksi" value="tambah">Tambah</button>
                <button class="adm-btn adm-btn--garis adm-btn--kecil" type="submit" name="aksi" value="kurang">Kurangi</button>
                <button class="adm-btn adm-btn--utama adm-btn--kecil" type="submit" name="aksi" value="set">Atur jadi</button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>
