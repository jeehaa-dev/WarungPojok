<?php
/**
 * WARUNG POJOK - Tambah produk
 * Developer: KelasPojok-Dev
 */
require_once dirname(__DIR__) . '/includes/auth.php';

$kategori_list = $pdo->query('SELECT * FROM categories ORDER BY name')->fetchAll();
$error = '';
$data  = ['name' => '', 'category_id' => '', 'price' => '', 'stock' => '', 'description' => '',
          'status' => 'tersedia', 'is_featured' => 0];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();

    $data['name']        = trim($_POST['name'] ?? '');
    $data['category_id'] = (int)($_POST['category_id'] ?? 0);
    $data['price']       = (float)str_replace(['.', ','], '', $_POST['price'] ?? '0');
    $data['stock']       = (int)($_POST['stock'] ?? 0);
    $data['description'] = trim($_POST['description'] ?? '');
    $data['status']      = in_array($_POST['status'] ?? '', ['tersedia', 'habis', 'nonaktif'], true) ? $_POST['status'] : 'tersedia';
    $data['is_featured'] = isset($_POST['is_featured']) ? 1 : 0;

    if ($data['name'] === '') {
        $error = 'Nama produk wajib diisi.';
    } elseif ($data['price'] <= 0) {
        $error = 'Harga harus lebih besar dari nol.';
    } elseif ($data['stock'] < 0) {
        $error = 'Stok tidak boleh minus.';
    }

    $gambar = null;
    if (!$error) {
        $gambar = upload_gambar($_FILES['image'] ?? [], 'products', $err_upload);
        if ($err_upload) $error = $err_upload;
    }

    if (!$error) {
        // Slug harus unik.
        $slug = slugify($data['name']);
        $cek  = $pdo->prepare('SELECT COUNT(*) FROM products WHERE slug = ?');
        $cek->execute([$slug]);
        if ($cek->fetchColumn() > 0) {
            $slug .= '-' . substr(bin2hex(random_bytes(2)), 0, 4);
        }

        if ($data['stock'] <= 0 && $data['status'] === 'tersedia') {
            $data['status'] = 'habis';
        }

        $stmt = $pdo->prepare(
            'INSERT INTO products (category_id, name, slug, description, price, stock, image, is_featured, status)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)'
        );
        $stmt->execute([
            $data['category_id'] ?: null, $data['name'], $slug, $data['description'],
            $data['price'], $data['stock'], $gambar, $data['is_featured'], $data['status'],
        ]);

        set_flash('sukses', 'Produk "' . $data['name'] . '" ditambahkan.');
        redirect('admin/products.php');
    }
}

$adm_title  = 'Tambah produk';
$adm_active = 'produk';
include __DIR__ . '/includes/header.php';
?>

<div class="adm-panel">
  <?php if ($error): ?><div class="adm-pesan adm-pesan--error"><?= e($error) ?></div><?php endif; ?>

  <form class="adm-form" method="post" action="<?= url('admin/product-add.php') ?>" enctype="multipart/form-data">
    <?= csrf_field() ?>

    <div>
      <label for="name">Nama produk</label>
      <input type="text" id="name" name="name" maxlength="150" required value="<?= e($data['name']) ?>">
    </div>

    <div class="adm-form__dua">
      <div>
        <label for="category_id">Kategori</label>
        <select id="category_id" name="category_id">
          <option value="0">Tanpa kategori</option>
          <?php foreach ($kategori_list as $k): ?>
            <option value="<?= (int)$k['id'] ?>" <?= (int)$data['category_id'] === (int)$k['id'] ? 'selected' : '' ?>><?= e($k['name']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div>
        <label for="status">Status</label>
        <select id="status" name="status">
          <option value="tersedia" <?= $data['status'] === 'tersedia' ? 'selected' : '' ?>>Tersedia</option>
          <option value="habis"    <?= $data['status'] === 'habis' ? 'selected' : '' ?>>Habis</option>
          <option value="nonaktif" <?= $data['status'] === 'nonaktif' ? 'selected' : '' ?>>Nonaktif (disembunyikan)</option>
        </select>
      </div>
    </div>

    <div class="adm-form__dua">
      <div>
        <label for="price">Harga (rupiah)</label>
        <input type="number" id="price" name="price" min="0" step="500" required value="<?= e($data['price']) ?>">
      </div>
      <div>
        <label for="stock">Stok (porsi)</label>
        <input type="number" id="stock" name="stock" min="0" required value="<?= e($data['stock']) ?>">
      </div>
    </div>

    <div>
      <label for="description">Deskripsi</label>
      <textarea id="description" name="description" maxlength="1500"><?= e($data['description']) ?></textarea>
    </div>

    <div>
      <label for="image">Gambar produk</label>
      <input type="file" id="image" name="image" accept="image/*" data-preview="previewGambar">
      <p class="adm-bantuan">JPG, PNG, atau WEBP. Maksimal 2 MB. Paling bagus berbentuk persegi.</p>
      <img class="adm-preview" id="previewGambar" src="<?= asset('images/hero-dimsum.png') ?>" alt="Pratinjau gambar produk">
    </div>

    <div>
      <label><input type="checkbox" name="is_featured" value="1" <?= $data['is_featured'] ? 'checked' : '' ?>> Tandai sebagai produk unggulan (Best Seller)</label>
    </div>

    <div class="adm-aksi">
      <button class="adm-btn adm-btn--utama" type="submit">Simpan produk</button>
      <a class="adm-btn adm-btn--garis" href="<?= url('admin/products.php') ?>">Batal</a>
    </div>
  </form>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
