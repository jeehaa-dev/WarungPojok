<?php
/**
 * WARUNG POJOK - Proses keranjang (tambah, ubah, hapus, kosongkan)
 * Developer: KelasPojok-Dev
 * Keranjang disimpan di session, jadi pelanggan tidak perlu punya akun.
 */
require_once __DIR__ . '/config/app.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('keranjang.php');
}
csrf_check();

$aksi    = $_POST['aksi'] ?? '';
$kembali = $_POST['kembali'] ?? 'keranjang.php';

// Batasi redirect hanya ke halaman di website ini.
if (preg_match('#^https?://#', $kembali) || strpos($kembali, '//') === 0) {
    $kembali = 'keranjang.php';
}
$kembali = ltrim(str_replace(BASE_URL, '', $kembali), '/') ?: 'keranjang.php';

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

switch ($aksi) {

    case 'tambah':
    case 'ubah':
        $id  = (int)($_POST['product_id'] ?? 0);
        $qty = (int)($_POST['qty'] ?? 1);

        $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ? AND status <> 'nonaktif'");
        $stmt->execute([$id]);
        $produk = $stmt->fetch();

        if (!$produk) {
            set_flash('error', 'Menu tidak ditemukan.');
            break;
        }
        if (produk_habis($produk)) {
            set_flash('error', $produk['name'] . ' sedang habis.');
            break;
        }

        $sekarang = (int)($_SESSION['cart'][$id] ?? 0);
        $baru     = ($aksi === 'tambah') ? $sekarang + max(1, $qty) : max(0, $qty);

        if ($baru <= 0) {
            unset($_SESSION['cart'][$id]);
            set_flash('info', $produk['name'] . ' dihapus dari keranjang.');
            break;
        }
        // Jumlah tidak boleh melebihi stok.
        if ($baru > (int)$produk['stock']) {
            $baru = (int)$produk['stock'];
            set_flash('info', 'Stok ' . $produk['name'] . ' tinggal ' . $baru . ' porsi.');
        } else {
            set_flash('sukses', ($aksi === 'tambah' ? 'Ditambahkan: ' : 'Jumlah diperbarui: ') . $produk['name'] . '.');
        }
        $_SESSION['cart'][$id] = $baru;
        break;

    case 'hapus':
        $id = (int)($_POST['product_id'] ?? 0);
        unset($_SESSION['cart'][$id]);
        set_flash('info', 'Item dihapus dari keranjang.');
        break;

    case 'kosongkan':
        $_SESSION['cart'] = [];
        set_flash('info', 'Keranjang dikosongkan.');
        break;

    default:
        set_flash('error', 'Aksi keranjang tidak dikenali.');
}

redirect($kembali);
