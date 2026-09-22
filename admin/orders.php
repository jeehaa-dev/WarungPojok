<?php
/**
 * WARUNG POJOK - Kelola pesanan
 * Developer: KelasPojok-Dev
 */
require_once dirname(__DIR__) . '/includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $id = (int)($_POST['id'] ?? 0);
    $order_status   = $_POST['order_status'] ?? '';
    $payment_status = $_POST['payment_status'] ?? '';

    $status_valid = ['baru', 'diproses', 'selesai', 'batal'];
    $bayar_valid  = ['belum_bayar', 'menunggu_verifikasi', 'lunas'];

    if (in_array($order_status, $status_valid, true) && in_array($payment_status, $bayar_valid, true)) {
        $pdo->prepare('UPDATE orders SET order_status = ?, payment_status = ? WHERE id = ?')
            ->execute([$order_status, $payment_status, $id]);
        set_flash('sukses', 'Status pesanan diperbarui.');
    } else {
        set_flash('error', 'Status yang dipilih tidak dikenali.');
    }
    redirect('admin/orders.php?id=' . $id);
}

$detail_id = (int)($_GET['id'] ?? 0);
$detail = null;
$items  = [];

if ($detail_id) {
    $stmt = $pdo->prepare('SELECT * FROM orders WHERE id = ? LIMIT 1');
    $stmt->execute([$detail_id]);
    $detail = $stmt->fetch();
    if ($detail) {
        $stmt = $pdo->prepare('SELECT * FROM order_items WHERE order_id = ?');
        $stmt->execute([$detail_id]);
        $items = $stmt->fetchAll();
    }
}

$filter = $_GET['status'] ?? 'semua';
if ($filter === 'semua') {
    $pesanan = $pdo->query('SELECT * FROM orders ORDER BY created_at DESC')->fetchAll();
} else {
    $stmt = $pdo->prepare('SELECT * FROM orders WHERE order_status = ? ORDER BY created_at DESC');
    $stmt->execute([$filter]);
    $pesanan = $stmt->fetchAll();
}

$label_bayar = ['belum_bayar' => 'Belum bayar', 'menunggu_verifikasi' => 'Menunggu verifikasi', 'lunas' => 'Lunas'];

$adm_title  = 'Pesanan';
$adm_active = 'pesanan';
include __DIR__ . '/includes/header.php';
?>

<?php if ($detail): ?>
<div class="adm-panel">
  <h2>Pesanan <?= e($detail['order_code']) ?></h2>
  <div class="adm-form__dua">
    <div>
      <table class="adm-tabel">
        <thead><tr><th>Menu</th><th>Harga</th><th>Jumlah</th><th class="kanan">Subtotal</th></tr></thead>
        <tbody>
        <?php foreach ($items as $it): ?>
          <tr>
            <td><?= e($it['product_name']) ?></td>
            <td><?= rupiah($it['price']) ?></td>
            <td><?= (int)$it['quantity'] ?></td>
            <td class="kanan"><?= rupiah($it['subtotal']) ?></td>
          </tr>
        <?php endforeach; ?>
          <tr>
            <td colspan="3"><strong>Total</strong></td>
            <td class="kanan"><strong><?= rupiah($detail['total']) ?></strong></td>
          </tr>
        </tbody>
      </table>

      <?php if ($detail['proof_image']): ?>
        <h3 style="margin-top:20px">Bukti transfer</h3>
        <a href="<?= e(upload_url('bukti', $detail['proof_image'], '')) ?>" target="_blank" rel="noopener">
          <img class="adm-preview" style="width:220px;height:auto" src="<?= e(upload_url('bukti', $detail['proof_image'], '')) ?>" alt="Bukti transfer <?= e($detail['order_code']) ?>">
        </a>
      <?php endif; ?>
    </div>

    <div>
      <h3>Data pemesan</h3>
      <table class="adm-tabel">
        <tbody>
          <tr><th>Nama</th><td><?= e($detail['customer_name']) ?></td></tr>
          <tr><th>WhatsApp</th><td>
            <?= e($detail['whatsapp']) ?>
            <a class="adm-btn adm-btn--garis adm-btn--kecil" href="https://wa.me/<?= e(preg_replace('/[^0-9]/', '', preg_replace('/^0/', '62', $detail['whatsapp']))) ?>" target="_blank" rel="noopener">Chat</a>
          </td></tr>
          <tr><th>Alamat</th><td><?= $detail['address'] ? nl2br(e($detail['address'])) : '<span class="adm-bantuan">Ambil sendiri</span>' ?></td></tr>
          <tr><th>Catatan</th><td><?= $detail['notes'] ? nl2br(e($detail['notes'])) : '-' ?></td></tr>
          <tr><th>Metode</th><td><?= $detail['payment_method'] === 'transfer' ? 'Transfer bank' : 'WhatsApp' ?></td></tr>
          <tr><th>Dibuat</th><td><?= tgl_id($detail['created_at']) ?>, <?= date('H:i', strtotime($detail['created_at'])) ?></td></tr>
        </tbody>
      </table>

      <form class="adm-form" method="post" action="<?= url('admin/orders.php') ?>" style="margin-top:18px">
        <?= csrf_field() ?>
        <input type="hidden" name="id" value="<?= (int)$detail['id'] ?>">
        <div>
          <label for="order_status">Status pesanan</label>
          <select id="order_status" name="order_status">
            <?php foreach (['baru' => 'Baru', 'diproses' => 'Diproses', 'selesai' => 'Selesai', 'batal' => 'Batal'] as $k => $v): ?>
              <option value="<?= $k ?>" <?= $detail['order_status'] === $k ? 'selected' : '' ?>><?= $v ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div>
          <label for="payment_status">Status pembayaran</label>
          <select id="payment_status" name="payment_status">
            <?php foreach ($label_bayar as $k => $v): ?>
              <option value="<?= $k ?>" <?= $detail['payment_status'] === $k ? 'selected' : '' ?>><?= $v ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="adm-aksi">
          <button class="adm-btn adm-btn--utama" type="submit">Simpan status</button>
          <a class="adm-btn adm-btn--garis" href="<?= url('admin/orders.php') ?>">Tutup detail</a>
        </div>
      </form>
    </div>
  </div>
</div>
<?php endif; ?>

<div class="adm-panel">
  <div class="adm-aksi" style="margin-bottom:18px">
    <?php foreach (['semua' => 'Semua', 'baru' => 'Baru', 'diproses' => 'Diproses', 'selesai' => 'Selesai', 'batal' => 'Batal'] as $k => $v): ?>
      <a class="adm-btn adm-btn--<?= $filter === $k ? 'utama' : 'garis' ?> adm-btn--kecil"
         href="<?= url('admin/orders.php?status=' . $k) ?>"><?= $v ?></a>
    <?php endforeach; ?>
  </div>

  <?php if (!$pesanan): ?>
    <p class="adm-kosong">Belum ada pesanan pada kelompok ini.</p>
  <?php else: ?>
    <div class="adm-tabel-scroll">
      <table class="adm-tabel">
        <thead><tr><th>Kode</th><th>Tanggal</th><th>Pemesan</th><th>Metode</th><th>Total</th><th>Bayar</th><th>Status</th><th class="kanan">Aksi</th></tr></thead>
        <tbody>
        <?php foreach ($pesanan as $o): ?>
          <tr>
            <td><?= e($o['order_code']) ?></td>
            <td><?= tgl_id($o['created_at']) ?></td>
            <td><?= e($o['customer_name']) ?></td>
            <td><?= $o['payment_method'] === 'transfer' ? 'Transfer' : 'WhatsApp' ?></td>
            <td><?= rupiah($o['total']) ?></td>
            <td>
              <?php $kb = $o['payment_status'] === 'lunas' ? 'hijau' : ($o['payment_status'] === 'menunggu_verifikasi' ? 'kuning' : 'abu'); ?>
              <span class="adm-label adm-label--<?= $kb ?>"><?= e($label_bayar[$o['payment_status']]) ?></span>
            </td>
            <td>
              <?php $ks = $o['order_status'] === 'selesai' ? 'hijau' : ($o['order_status'] === 'batal' ? 'merah' : 'kuning'); ?>
              <span class="adm-label adm-label--<?= $ks ?>"><?= e($o['order_status']) ?></span>
            </td>
            <td class="kanan"><a class="adm-btn adm-btn--garis adm-btn--kecil" href="<?= url('admin/orders.php?id=' . (int)$o['id']) ?>">Detail</a></td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
