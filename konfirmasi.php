<?php
/**
 * WARUNG POJOK - Konfirmasi pembayaran transfer
 * Developer: KelasPojok-Dev
 * Menampilkan data rekening (dari pengaturan admin, bukan data karangan)
 * dan menerima unggahan bukti transfer.
 */
require_once __DIR__ . '/config/app.php';

$kode = trim($_GET['kode'] ?? $_POST['kode'] ?? '');

$stmt = $pdo->prepare('SELECT * FROM orders WHERE order_code = ? LIMIT 1');
$stmt->execute([$kode]);
$order = $stmt->fetch();

if (!$order) {
    set_flash('error', 'Pesanan tidak ditemukan. Periksa lagi kode pesananmu.');
    redirect('menu.php');
}

// --- Proses unggah bukti pembayaran ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $error = null;
    $nama_file = upload_gambar($_FILES['proof_image'] ?? [], 'bukti', $error);

    if ($error) {
        set_flash('error', $error);
    } elseif (!$nama_file) {
        set_flash('error', 'Pilih dulu file bukti transfernya.');
    } else {
        hapus_gambar('bukti', $order['proof_image']);
        $stmt = $pdo->prepare(
            "UPDATE orders SET proof_image = ?, payment_status = 'menunggu_verifikasi' WHERE id = ?"
        );
        $stmt->execute([$nama_file, $order['id']]);
        set_flash('sukses', 'Bukti transfer terkirim. Admin akan memverifikasi pesananmu.');
    }
    redirect('konfirmasi.php?kode=' . urlencode($kode));
}

$stmt = $pdo->prepare('SELECT * FROM order_items WHERE order_id = ?');
$stmt->execute([$order['id']]);
$items = $stmt->fetchAll();

$status_label = [
    'belum_bayar'         => 'Belum dibayar',
    'menunggu_verifikasi' => 'Menunggu verifikasi admin',
    'lunas'               => 'Sudah lunas',
];

$page_title = 'Konfirmasi pembayaran';
$page_desc  = 'Instruksi pembayaran pesanan ' . $order['order_code'] . '.';
$active     = '';
include __DIR__ . '/includes/header.php';
?>

<section class="bagian bagian--krem">
  <div class="container">
    <h1 class="bagian__judul">Pesanan <?= e($order['order_code']) ?> tercatat</h1>
    <p class="bagian__intro">Simpan kode ini. Sebutkan saat menghubungi kami.</p>

    <div class="dua-kolom">
      <div>
        <div class="info-bank">
          <h2>Pembayaran transfer</h2>
          <?php if (setting('bank_name') && setting('bank_account')): ?>
            <dl>
              <dt>Bank</dt><dd><?= e(setting('bank_name')) ?></dd>
              <dt>Nomor rekening</dt><dd><strong><?= e(setting('bank_account')) ?></strong></dd>
              <dt>Atas nama</dt><dd><?= e(setting('bank_holder')) ?></dd>
              <dt>Jumlah</dt><dd><strong><?= rupiah($order['total']) ?></strong></dd>
            </dl>
            <p><?= nl2br(e(setting('payment_instruction'))) ?></p>
          <?php else: ?>
            <p>Data rekening belum diisi admin. Sementara ini, konfirmasi pembayaran lewat WhatsApp kami.</p>
            <a class="btn btn--kecil btn--merah" href="<?= e(wa_link('Halo, saya mau konfirmasi pembayaran pesanan ' . $order['order_code'])) ?>" target="_blank" rel="noopener">Konfirmasi via WhatsApp</a>
          <?php endif; ?>
        </div>

        <form class="form" method="post" action="<?= url('konfirmasi.php') ?>" enctype="multipart/form-data" data-loading style="margin-top:22px">
          <h2>Unggah bukti transfer</h2>
          <?= csrf_field() ?>
          <input type="hidden" name="kode" value="<?= e($order['order_code']) ?>">
          <div class="form__baris">
            <label for="bukti">File bukti (JPG, PNG, maksimal 2 MB)</label>
            <input type="file" id="bukti" name="proof_image" accept="image/*" required>
          </div>
          <button class="btn btn--merah btn--blok" type="submit">Kirim bukti transfer</button>
          <?php if ($order['proof_image']): ?>
            <p class="form__bantuan">Bukti sudah diunggah. Mengirim file baru akan menggantikan yang lama.</p>
          <?php endif; ?>
        </form>
      </div>

      <aside class="ringkasan">
        <h2>Rincian pesanan</h2>
        <?php foreach ($items as $it): ?>
          <div class="ringkasan__baris">
            <span><?= e($it['product_name']) ?> &times;<?= (int)$it['quantity'] ?></span>
            <span><?= rupiah($it['subtotal']) ?></span>
          </div>
        <?php endforeach; ?>
        <div class="ringkasan__total">
          <span>Total</span>
          <span><?= rupiah($order['total']) ?></span>
        </div>
        <dl class="info-list">
          <dt>Nama</dt><dd><?= e($order['customer_name']) ?></dd>
          <dt>WhatsApp</dt><dd><?= e($order['whatsapp']) ?></dd>
          <dt>Status bayar</dt><dd><?= e($status_label[$order['payment_status']] ?? $order['payment_status']) ?></dd>
        </dl>
        <a class="btn btn--detail btn--blok" href="<?= url('menu.php') ?>">Pesan lagi</a>
      </aside>
    </div>
  </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
