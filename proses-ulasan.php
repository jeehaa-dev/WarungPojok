<?php
/**
 * WARUNG POJOK - Proses simpan ulasan & rating
 * Developer: KelasPojok-Dev
 */
require_once __DIR__ . '/config/app.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('menu.php');
}
csrf_check();

$product_id = (int)($_POST['product_id'] ?? 0);
$slug       = trim($_POST['slug'] ?? '');
$nama       = trim($_POST['customer_name'] ?? '');
$rating     = (int)($_POST['rating'] ?? 0);
$komentar   = trim($_POST['comment'] ?? '');

// Validasi input
$kembali = 'detail-produk.php?slug=' . urlencode($slug);

if ($nama === '' || $komentar === '') {
    set_flash('error', 'Nama dan komentar wajib diisi.');
    redirect($kembali);
}
if (mb_strlen($nama) > 100 || mb_strlen($komentar) > 600) {
    set_flash('error', 'Nama maksimal 100 karakter dan komentar maksimal 600 karakter.');
    redirect($kembali);
}
if ($rating < 1 || $rating > 5) {
    set_flash('error', 'Pilih rating antara 1 sampai 5 bintang.');
    redirect($kembali);
}

// Pastikan produknya ada
$stmt = $pdo->prepare("SELECT id FROM products WHERE id = ? AND status <> 'nonaktif'");
$stmt->execute([$product_id]);
if (!$stmt->fetch()) {
    set_flash('error', 'Produk tidak ditemukan.');
    redirect('menu.php');
}

// Batasi kirim ulasan berulang dalam waktu singkat
if (isset($_SESSION['ulasan_terakhir']) && (time() - $_SESSION['ulasan_terakhir']) < 30) {
    set_flash('error', 'Tunggu sebentar sebelum mengirim ulasan lagi.');
    redirect($kembali);
}

$stmt = $pdo->prepare(
    'INSERT INTO reviews (product_id, customer_name, rating, comment, status)
     VALUES (?, ?, ?, ?, ?)'
);
$stmt->execute([$product_id, $nama, $rating, $komentar, 'pending']);
$_SESSION['ulasan_terakhir'] = time();

set_flash('sukses', 'Terima kasih. Ulasanmu akan tampil setelah disetujui admin.');
redirect($kembali);
