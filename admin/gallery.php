<?php
/**
 * WARUNG POJOK - Kelola galeri
 * Developer: KelasPojok-Dev
 */
require_once dirname(__DIR__) . '/includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $aksi = $_POST['aksi'] ?? '';

    if ($aksi === 'tambah') {
        $judul = trim($_POST['title'] ?? '');
        $ket   = trim($_POST['description'] ?? '');
        $error = null;
        $file  = upload_gambar($_FILES['image'] ?? [], 'gallery', $error);

        if ($judul === '') {
            set_flash('error', 'Judul foto wajib diisi.');
        } elseif ($error) {
            set_flash('error', $error);
        } elseif (!$file) {
            set_flash('error', 'Pilih dulu file fotonya.');
        } else {
            $pdo->prepare('INSERT INTO gallery (title, image, description) VALUES (?, ?, ?)')
                ->execute([$judul, $file, $ket]);
            set_flash('sukses', 'Foto ditambahkan ke galeri.');
        }
    } elseif ($aksi === 'hapus') {
        $id = (int)($_POST['id'] ?? 0);
        $stmt = $pdo->prepare('SELECT * FROM gallery WHERE id = ?');
        $stmt->execute([$id]);
        if ($foto = $stmt->fetch()) {
            $pdo->prepare('DELETE FROM gallery WHERE id = ?')->execute([$id]);
            hapus_gambar('gallery', $foto['image']);
            set_flash('sukses', 'Foto dihapus.');
        }
    }
    redirect('admin/gallery.php');
}

$galeri = $pdo->query('SELECT * FROM gallery ORDER BY created_at DESC')->fetchAll();

$adm_title  = 'Galeri';
$adm_active = 'galeri';
include __DIR__ . '/includes/header.php';
?>
<div class="adm-panel">
  <h2>Tambah foto</h2>
  <form class="adm-form" method="post" action="<?= url('admin/gallery.php') ?>" enctype="multipart/form-data">
    <?= csrf_field() ?>
    <input type="hidden" name="aksi" value="tambah">
    <div class="adm-form__dua">
      <div>
        <label for="title">Judul foto</label>
        <input type="text" id="title" name="title" maxlength="150" required>
      </div>
      <div>
        <label for="description">Keterangan singkat</label>
        <input type="text" id="description" name="description" maxlength="255">
      </div>
    </div>
    <div>
      <label for="image">File foto</label>
      <input type="file" id="image" name="image" accept="image/*" required data-preview="previewFoto">
      <p class="adm-bantuan">JPG, PNG, atau WEBP. Maksimal 2 MB.</p>
      <img class="adm-preview" id="previewFoto" src="<?= asset('images/hero-dimsum.png') ?>" alt="Pratinjau foto">
    </div>
    <div class="adm-aksi"><button class="adm-btn adm-btn--utama" type="submit">Unggah foto</button></div>
  </form>
</div>

<div class="adm-panel">
  <h2>Foto di galeri (<?= count($galeri) ?>)</h2>
  <?php if (!$galeri): ?>
    <p class="adm-kosong">Belum ada foto. Galeri termasuk ketentuan minimal website, jadi sebaiknya diisi minimal 4 foto.</p>
  <?php else: ?>
    <div class="adm-media">
      <?php foreach ($galeri as $g): ?>
        <figure>
          <img src="<?= e(upload_url('gallery', $g['image'])) ?>" alt="<?= e($g['title']) ?>" loading="lazy">
          <figcaption>
            <strong><?= e($g['title']) ?></strong><br>
            <span class="adm-bantuan"><?= e($g['description']) ?></span>
            <form method="post" action="<?= url('admin/gallery.php') ?>" style="margin-top:10px">
              <?= csrf_field() ?>
              <input type="hidden" name="aksi" value="hapus">
              <input type="hidden" name="id" value="<?= (int)$g['id'] ?>">
              <button class="adm-btn adm-btn--bahaya adm-btn--kecil" type="submit"
                      data-konfirmasi="Hapus foto <?= e($g['title']) ?>?">Hapus</button>
            </form>
          </figcaption>
        </figure>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>
