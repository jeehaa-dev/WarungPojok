<?php
/**
 * WARUNG POJOK - Kelola kategori
 * Developer: KelasPojok-Dev
 */
require_once dirname(__DIR__) . '/includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $aksi = $_POST['aksi'] ?? '';
    $nama = trim($_POST['name'] ?? '');
    $id   = (int)($_POST['id'] ?? 0);

    if ($aksi === 'tambah' || $aksi === 'edit') {
        if ($nama === '') {
            set_flash('error', 'Nama kategori wajib diisi.');
            redirect('admin/categories.php');
        }
        $slug = slugify($nama);
        $cek = $pdo->prepare('SELECT COUNT(*) FROM categories WHERE slug = ? AND id <> ?');
        $cek->execute([$slug, $id]);
        if ((int)$cek->fetchColumn() > 0) {
            $slug .= '-' . substr(bin2hex(random_bytes(2)), 0, 4);
        }

        if ($aksi === 'tambah') {
            $pdo->prepare('INSERT INTO categories (name, slug) VALUES (?, ?)')->execute([$nama, $slug]);
            set_flash('sukses', 'Kategori "' . $nama . '" ditambahkan.');
        } else {
            $pdo->prepare('UPDATE categories SET name = ?, slug = ? WHERE id = ?')->execute([$nama, $slug, $id]);
            set_flash('sukses', 'Kategori diperbarui.');
        }
    } elseif ($aksi === 'hapus') {
        // Produk yang memakai kategori ini tidak ikut terhapus (category_id jadi NULL).
        $pdo->prepare('DELETE FROM categories WHERE id = ?')->execute([$id]);
        set_flash('info', 'Kategori dihapus. Produknya tetap ada, hanya tanpa kategori.');
    }
    redirect('admin/categories.php');
}

$kategori = $pdo->query(
    'SELECT c.*, COUNT(p.id) AS jumlah FROM categories c
     LEFT JOIN products p ON p.category_id = c.id
     GROUP BY c.id ORDER BY c.name'
)->fetchAll();

$edit = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare('SELECT * FROM categories WHERE id = ?');
    $stmt->execute([(int)$_GET['edit']]);
    $edit = $stmt->fetch();
}

$adm_title  = 'Kategori';
$adm_active = 'kategori';
include __DIR__ . '/includes/header.php';
?>
<div class="adm-panel">
  <h2><?= $edit ? 'Edit kategori' : 'Tambah kategori' ?></h2>
  <form class="adm-form" method="post" action="<?= url('admin/categories.php') ?>">
    <?= csrf_field() ?>
    <input type="hidden" name="aksi" value="<?= $edit ? 'edit' : 'tambah' ?>">
    <?php if ($edit): ?><input type="hidden" name="id" value="<?= (int)$edit['id'] ?>"><?php endif; ?>
    <div>
      <label for="name">Nama kategori</label>
      <input type="text" id="name" name="name" maxlength="80" required value="<?= e($edit['name'] ?? '') ?>">
    </div>
    <div class="adm-aksi">
      <button class="adm-btn adm-btn--utama" type="submit"><?= $edit ? 'Simpan perubahan' : 'Tambah kategori' ?></button>
      <?php if ($edit): ?><a class="adm-btn adm-btn--garis" href="<?= url('admin/categories.php') ?>">Batal</a><?php endif; ?>
    </div>
  </form>
</div>

<div class="adm-panel">
  <h2>Daftar kategori</h2>
  <?php if (!$kategori): ?>
    <p class="adm-kosong">Belum ada kategori.</p>
  <?php else: ?>
    <div class="adm-tabel-scroll">
      <table class="adm-tabel">
        <thead><tr><th>Nama</th><th>Alamat URL</th><th>Jumlah produk</th><th class="kanan">Aksi</th></tr></thead>
        <tbody>
        <?php foreach ($kategori as $k): ?>
          <tr>
            <td><strong><?= e($k['name']) ?></strong></td>
            <td><?= e($k['slug']) ?></td>
            <td><?= (int)$k['jumlah'] ?></td>
            <td class="kanan">
              <div class="adm-aksi" style="justify-content:flex-end">
                <a class="adm-btn adm-btn--garis adm-btn--kecil" href="<?= url('admin/categories.php?edit=' . (int)$k['id']) ?>">Edit</a>
                <form method="post" action="<?= url('admin/categories.php') ?>">
                  <?= csrf_field() ?>
                  <input type="hidden" name="aksi" value="hapus">
                  <input type="hidden" name="id" value="<?= (int)$k['id'] ?>">
                  <button class="adm-btn adm-btn--bahaya adm-btn--kecil" type="submit"
                          data-konfirmasi="Hapus kategori <?= e($k['name']) ?>?">Hapus</button>
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
