<?php
/**
 * WARUNG POJOK - Edit produk
 * Developer: KelasPojok-Dev
 */
require_once dirname(__DIR__) . '/includes/auth.php';

$id = (int)($_GET['id'] ?? $_POST['id'] ?? 0);
$stmt = $pdo->prepare('SELECT * FROM products WHERE id = ? LIMIT 1');
$stmt->execute([$id]);
$produk = $stmt->fetch();

if (!$produk) {
    set_flash('error', 'Produk tidak ditemukan.');
    redirect('admin/products.php');
}

$kategori_list = $pdo->query('SELECT * FROM categories ORDER BY name')->fetchAll();
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();

    $nama        = trim($_POST['name'] ?? '');
    $category_id = (int)($_POST['category_id'] ?? 0);
    $harga       = (float)str_replace(['.', ','], '', $_POST['price'] ?? '0');
    $stok        = (int)($_POST['stock'] ?? 0);
    $deskripsi   = trim($_POST['description'] ?? '');
    $status      = in_array($_POST['status'] ?? '', ['tersedia', 'habis', 'nonaktif'], true) ? $_POST['status'] : 'tersedia';
    $unggulan    = isset($_POST['is_featured']) ? 1 : 0;

    if ($nama === '') {
        $error = 'Nama produk wajib diisi.';
    } elseif ($harga <= 0) {
        $error = 'Harga harus lebih besar dari nol.';
    } elseif ($stok < 0) {
        $error = 'Stok tidak boleh minus.';
    }

    $gambar_baru = null;
    if (!$error) {
        $gambar_baru = upload_gambar($_FILES['image'] ?? [], 'products', $err_upload);
        if ($err_upload) $error = $err_upload;
    }

    if (!$error) {
        if ($stok <= 0 && $status === 'tersedia') $status = 'habis';
        if ($stok > 0 && $status === 'habis')     $status = 'tersedia';

        $gambar = $gambar_baru ?: $produk['image'];

        $stmt = $pdo->prepare(
            'UPDATE products SET category_id = ?, name = ?, description = ?, price = ?,
                    stock = ?, image = ?, is_featured = ?, status = ? WHERE id = ?'
        );
        $stmt->execute([
            $category_id ?: null, $nama, $deskripsi, $harga, $stok,
            $gambar, $unggulan, $status, $produk['id'],
        ]);

        // Hapus gambar lama supaya storage tidak menumpuk.
        if ($gambar_baru && $produk['image']) {
            hapus_gambar('products', $produk['image']);
        }

        set_flash('sukses', 'Perubahan pada "' . $nama . '" disimpan.');
        redirect('admin/products.php');
    }
    // Kalau gagal, tampilkan kembali nilai yang tadi diisi.
    $produk = array_merge($produk, [
        'name' => $nama, 'category_id' => $category_id, 'price' => $harga, 'stock' => $stok,
        'description' => $deskripsi, 'status' => $status, 'is_featured' => $unggulan,
    ]);
}

$adm_title  = 'Edit produk';
$adm_active = 'produk';
include __DIR__ . '/includes/header.php';
?>

<div class="adm-panel">
  <?php if ($error): ?><div class="adm-pesan adm-pesan--error"><?= e($error) ?></div><?php endif; ?>

  <form class="adm-form" method="post" action="<?= url('admin/product-edit.php') ?>" enctype="multipart/form-data">
    <?= csrf_field() ?>
    <input type="hidden" name="id" value="<?= (int)$produk['id'] ?>">

    <div>
      <label for="name">Nama produk</label>
      <input type="text" id="name" name="name" maxlength="150" required value="<?= e($produk['name']) ?>">
      <p class="adm-bantuan">Alamat halaman produk ini: <?= e($produk['slug']) ?></p>
    </div>

    <div class="adm-form__dua">
      <div>
        <label for="category_id">Kategori</label>
        <select id="category_id" name="category_id">
          <option value="0">Tanpa kategori</option>
          <?php foreach ($kategori_list as $k): ?>
            <option value="<?= (int)$k['id'] ?>" <?= (int)$produk['category_id'] === (int)$k['id'] ? 'selected' : '' ?>><?= e($k['name']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div>
        <label for="status">Status</label>
        <select id="status" name="status">
          <option value="tersedia" <?= $produk['status'] === 'tersedia' ? 'selected' : '' ?>>Tersedia</option>
          <option value="habis"    <?= $produk['status'] === 'habis' ? 'selected' : '' ?>>Habis</option>
          <option value="nonaktif" <?= $produk['status'] === 'nonaktif' ? 'selected' : '' ?>>Nonaktif (disembunyikan)</option>
        </select>
      </div>
    </div>

    <div class="adm-form__dua">
      <div>
        <label for="price">Harga (rupiah)</label>
        <input type="number" id="price" name="price" min="0" step="500" required value="<?= (int)$produk['price'] ?>">
      </div>
      <div>
        <label for="stock">Stok (porsi)</label>
        <input type="number" id="stock" name="stock" min="0" required value="<?= (int)$produk['stock'] ?>">
      </div>
    </div>

    <div>
      <label for="description">Deskripsi</label>
      <textarea id="description" name="description" maxlength="1500"><?= e($produk['description']) ?></textarea>
    </div>

    <div>
      <label for="image">Ganti gambar produk</label>
      <input type="file" id="image" name="image" accept="image/*" data-preview="previewGambar">
      <p class="adm-bantuan">Kosongkan kalau gambar tidak perlu diganti.</p>
      <img class="adm-preview" id="previewGambar" src="<?= e(upload_url('products', $produk['image'])) ?>" alt="Gambar produk saat ini">
    </div>

    <div>
      <label><input type="checkbox" name="is_featured" value="1" <?= $produk['is_featured'] ? 'checked' : '' ?>> Tandai sebagai produk unggulan (Best Seller)</label>
    </div>

    <div class="adm-aksi">
      <button class="adm-btn adm-btn--utama" type="submit">Simpan perubahan</button>
      <a class="adm-btn adm-btn--garis" href="<?= url('admin/products.php') ?>">Kembali</a>
    </div>
  </form>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
