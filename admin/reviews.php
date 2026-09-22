<?php
/**
 * WARUNG POJOK - Moderasi ulasan & rating
 * Developer: KelasPojok-Dev
 */
require_once dirname(__DIR__) . '/includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $id   = (int)($_POST['id'] ?? 0);
    $aksi = $_POST['aksi'] ?? '';

    if ($aksi === 'setujui') {
        $pdo->prepare("UPDATE reviews SET status = 'disetujui' WHERE id = ?")->execute([$id]);
        set_flash('sukses', 'Ulasan ditampilkan di website.');
    } elseif ($aksi === 'tolak') {
        $pdo->prepare("UPDATE reviews SET status = 'ditolak' WHERE id = ?")->execute([$id]);
        set_flash('info', 'Ulasan disembunyikan.');
    } elseif ($aksi === 'hapus') {
        $pdo->prepare('DELETE FROM reviews WHERE id = ?')->execute([$id]);
        set_flash('sukses', 'Ulasan dihapus.');
    }
    redirect('admin/reviews.php' . (isset($_POST['status']) ? '?status=' . urlencode($_POST['status']) : ''));
}

$status = $_GET['status'] ?? 'pending';
$valid  = ['pending', 'disetujui', 'ditolak', 'semua'];
if (!in_array($status, $valid, true)) $status = 'pending';

if ($status === 'semua') {
    $stmt = $pdo->query('SELECT r.*, p.name AS product_name FROM reviews r
                         JOIN products p ON p.id = r.product_id ORDER BY r.created_at DESC');
} else {
    $stmt = $pdo->prepare('SELECT r.*, p.name AS product_name FROM reviews r
                           JOIN products p ON p.id = r.product_id
                           WHERE r.status = ? ORDER BY r.created_at DESC');
    $stmt->execute([$status]);
}
$ulasan = $stmt->fetchAll();

$adm_title  = 'Ulasan';
$adm_active = 'ulasan';
include __DIR__ . '/includes/header.php';
?>
<div class="adm-panel">
  <div class="adm-aksi" style="margin-bottom:18px">
    <?php foreach (['pending' => 'Menunggu', 'disetujui' => 'Tampil', 'ditolak' => 'Ditolak', 'semua' => 'Semua'] as $key => $label): ?>
      <a class="adm-btn adm-btn--<?= $status === $key ? 'utama' : 'garis' ?> adm-btn--kecil"
         href="<?= url('admin/reviews.php?status=' . $key) ?>"><?= e($label) ?></a>
    <?php endforeach; ?>
  </div>

  <?php if (!$ulasan): ?>
    <p class="adm-kosong">Tidak ada ulasan pada kelompok ini.</p>
  <?php else: ?>
    <div class="adm-tabel-scroll">
      <table class="adm-tabel">
        <thead><tr><th>Produk</th><th>Pengulas</th><th>Rating</th><th>Komentar</th><th>Status</th><th class="kanan">Aksi</th></tr></thead>
        <tbody>
        <?php foreach ($ulasan as $u): ?>
          <tr>
            <td><?= e($u['product_name']) ?></td>
            <td><?= e($u['customer_name']) ?><br><span class="adm-bantuan"><?= tgl_id($u['created_at']) ?></span></td>
            <td><?= (int)$u['rating'] ?>/5</td>
            <td style="max-width:320px"><?= nl2br(e($u['comment'])) ?></td>
            <td>
              <?php $kelas = $u['status'] === 'disetujui' ? 'hijau' : ($u['status'] === 'ditolak' ? 'merah' : 'kuning'); ?>
              <span class="adm-label adm-label--<?= $kelas ?>"><?= e($u['status']) ?></span>
            </td>
            <td class="kanan">
              <div class="adm-aksi" style="justify-content:flex-end">
                <?php if ($u['status'] !== 'disetujui'): ?>
                  <form method="post" action="<?= url('admin/reviews.php') ?>">
                    <?= csrf_field() ?>
                    <input type="hidden" name="aksi" value="setujui">
                    <input type="hidden" name="status" value="<?= e($status) ?>">
                    <input type="hidden" name="id" value="<?= (int)$u['id'] ?>">
                    <button class="adm-btn adm-btn--utama adm-btn--kecil" type="submit">Setujui</button>
                  </form>
                <?php endif; ?>
                <?php if ($u['status'] !== 'ditolak'): ?>
                  <form method="post" action="<?= url('admin/reviews.php') ?>">
                    <?= csrf_field() ?>
                    <input type="hidden" name="aksi" value="tolak">
                    <input type="hidden" name="status" value="<?= e($status) ?>">
                    <input type="hidden" name="id" value="<?= (int)$u['id'] ?>">
                    <button class="adm-btn adm-btn--garis adm-btn--kecil" type="submit">Sembunyikan</button>
                  </form>
                <?php endif; ?>
                <form method="post" action="<?= url('admin/reviews.php') ?>">
                  <?= csrf_field() ?>
                  <input type="hidden" name="aksi" value="hapus">
                  <input type="hidden" name="status" value="<?= e($status) ?>">
                  <input type="hidden" name="id" value="<?= (int)$u['id'] ?>">
                  <button class="adm-btn adm-btn--bahaya adm-btn--kecil" type="submit"
                          data-konfirmasi="Hapus ulasan dari <?= e($u['customer_name']) ?>?">Hapus</button>
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
