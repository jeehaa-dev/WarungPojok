<?php
/**
 * WARUNG POJOK - Kirim pesanan ke WhatsApp
 * Developer: KelasPojok-Dev
 * Halaman ini menyiapkan link wa.me berisi rincian pesanan,
 * lalu membuka WhatsApp. Tetap ada tombol manual kalau tidak otomatis.
 */
require_once __DIR__ . '/config/app.php';

$kode = trim($_GET['kode'] ?? '');

// Ambil pesanan dari database supaya isinya pasti sama dengan yang tersimpan.
$pesan = '';
$order = null;
if ($kode !== '') {
    $stmt = $pdo->prepare('SELECT * FROM orders WHERE order_code = ? LIMIT 1');
    $stmt->execute([$kode]);
    $order = $stmt->fetch();
}

if ($order) {
    $stmt = $pdo->prepare('SELECT * FROM order_items WHERE order_id = ?');
    $stmt->execute([$order['id']]);
    $baris = ['Halo ' . setting('site_name') . ', saya ingin memesan:'];
    foreach ($stmt->fetchAll() as $it) {
        $baris[] = '- ' . $it['product_name'] . ' x' . $it['quantity'] . ' = ' . rupiah($it['subtotal']);
    }
    $baris[] = 'Total = ' . rupiah($order['total']);
    $baris[] = 'Kode pesanan: ' . $order['order_code'];
    $baris[] = 'Nama: ' . $order['customer_name'];
    if ($order['address']) $baris[] = 'Alamat: ' . $order['address'];
    if ($order['notes'])   $baris[] = 'Catatan: ' . $order['notes'];
    $pesan = implode("\n", $baris);
} else {
    $pesan = $_SESSION['wa_pesan'] ?? ('Halo ' . setting('site_name') . ', saya ingin memesan dimsum.');
}

$link = wa_link($pesan);

$page_title = 'Mengirim pesanan';
$page_desc  = 'Mengarahkan pesanan ke WhatsApp ' . setting('site_name') . '.';
$active     = '';
include __DIR__ . '/includes/header.php';
?>
<section class="bagian bagian--krem">
  <div class="container">
    <div class="kosong">
      <h1>Pesanan siap dikirim</h1>
      <?php if ($order): ?>
        <p>Kode pesananmu <strong><?= e($order['order_code']) ?></strong> dengan total <strong><?= rupiah($order['total']) ?></strong>.</p>
      <?php endif; ?>
      <p>WhatsApp akan terbuka otomatis dalam beberapa detik. Kalau tidak, tekan tombol di bawah.</p>

      <p>
        <a class="btn btn--merah" id="linkWa" href="<?= e($link) ?>" target="_blank" rel="noopener">Buka WhatsApp sekarang</a>
        <a class="btn btn--detail" href="<?= url('menu.php') ?>">Kembali ke menu</a>
      </p>

      <details class="ringkas-pesan">
        <summary>Lihat isi pesan</summary>
        <pre><?= e($pesan) ?></pre>
      </details>
    </div>
  </div>
</section>

<script>
  // Pengalihan otomatis ke WhatsApp setelah halaman tampil.
  setTimeout(function () {
    window.location.href = document.getElementById('linkWa').href;
  }, 1500);
</script>
<?php include __DIR__ . '/includes/footer.php'; ?>
