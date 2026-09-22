<?php
/**
 * WARUNG POJOK - Proses checkout
 * Developer: KelasPojok-Dev
 * Menyimpan pesanan + itemnya ke database, mengurangi stok,
 * lalu mengarahkan ke WhatsApp atau halaman konfirmasi transfer.
 */
require_once __DIR__ . '/config/app.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('checkout.php');
}
csrf_check();

$items = cart_items();
$total = cart_total($items);

if (!$items) {
    set_flash('error', 'Keranjang kosong.');
    redirect('menu.php');
}

$nama    = trim($_POST['customer_name'] ?? '');
$wa      = trim($_POST['whatsapp'] ?? '');
$alamat  = trim($_POST['address'] ?? '');
$catatan = trim($_POST['notes'] ?? '');
$metode  = ($_POST['payment_method'] ?? 'whatsapp') === 'transfer' ? 'transfer' : 'whatsapp';

// --- Validasi input ---
if ($nama === '' || $wa === '') {
    set_flash('error', 'Nama dan nomor WhatsApp wajib diisi.');
    redirect('checkout.php');
}
if (!preg_match('/^[0-9+\-\s]{8,20}$/', $wa)) {
    set_flash('error', 'Nomor WhatsApp hanya boleh berisi angka, minimal 8 digit.');
    redirect('checkout.php');
}
if ($metode === 'transfer' && (setting('bank_name') === '' || setting('bank_account') === '')) {
    set_flash('error', 'Pembayaran transfer belum tersedia. Silakan pesan lewat WhatsApp.');
    redirect('checkout.php');
}

// Isi ulang form kalau nanti pelanggan kembali.
$_SESSION['checkout_nama'] = $nama;
$_SESSION['checkout_wa']   = $wa;

$kode = 'WP-' . date('Ymd') . '-' . strtoupper(bin2hex(random_bytes(2)));

try {
    $pdo->beginTransaction();

    // Kunci baris produk dan cek ulang stok sebelum menyimpan.
    foreach ($items as $it) {
        $stmt = $pdo->prepare('SELECT stock, name FROM products WHERE id = ? FOR UPDATE');
        $stmt->execute([$it['id']]);
        $cek = $stmt->fetch();
        if (!$cek || (int)$cek['stock'] < (int)$it['qty']) {
            $pdo->rollBack();
            set_flash('error', 'Stok ' . ($cek['name'] ?? 'produk') . ' berubah. Periksa lagi keranjangmu.');
            redirect('keranjang.php');
        }
    }

    $stmt = $pdo->prepare(
        'INSERT INTO orders (order_code, customer_name, whatsapp, address, notes,
                             payment_method, payment_status, total, order_status)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)'
    );
    $stmt->execute([
        $kode, $nama, $wa, $alamat, $catatan, $metode,
        $metode === 'transfer' ? 'belum_bayar' : 'belum_bayar',
        $total, 'baru',
    ]);
    $order_id = (int)$pdo->lastInsertId();

    $simpan_item = $pdo->prepare(
        'INSERT INTO order_items (order_id, product_id, product_name, price, quantity, subtotal)
         VALUES (?, ?, ?, ?, ?, ?)'
    );
    $kurangi_stok = $pdo->prepare('UPDATE products SET stock = stock - ? WHERE id = ?');
    $tandai_habis = $pdo->prepare("UPDATE products SET status = 'habis' WHERE id = ? AND stock <= 0");

    foreach ($items as $it) {
        $simpan_item->execute([$order_id, $it['id'], $it['name'], $it['price'], $it['qty'], $it['subtotal']]);
        $kurangi_stok->execute([$it['qty'], $it['id']]);
        $tandai_habis->execute([$it['id']]);
    }

    $pdo->commit();
} catch (PDOException $ex) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    set_flash('error', 'Pesanan gagal disimpan. Coba lagi sebentar.');
    redirect('checkout.php');
}

// Keranjang dikosongkan, pesanan sudah tersimpan.
$_SESSION['cart'] = [];
$_SESSION['order_terakhir'] = $kode;

if ($metode === 'whatsapp') {
    // Buat teks pesanan lalu arahkan ke WhatsApp.
    $_SESSION['wa_pesan'] = pesan_whatsapp($items, $total, $nama, $catatan, $alamat, $kode);
    redirect('whatsapp.php?kode=' . urlencode($kode));
}

redirect('konfirmasi.php?kode=' . urlencode($kode));
