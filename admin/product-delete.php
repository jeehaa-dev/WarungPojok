<?php
/**
 * WARUNG POJOK - Hapus produk
 * Developer: KelasPojok-Dev
 * Hanya menerima POST + token CSRF supaya tidak bisa dipanggil lewat URL biasa.
 */
require_once dirname(__DIR__) . '/includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('admin/products.php');
}
csrf_check();

$id = (int)($_POST['id'] ?? 0);

$stmt = $pdo->prepare('SELECT * FROM products WHERE id = ? LIMIT 1');
$stmt->execute([$id]);
$produk = $stmt->fetch();

if (!$produk) {
    set_flash('error', 'Produk tidak ditemukan.');
    redirect('admin/products.php');
}

// Kalau produk pernah dipesan, lebih aman dinonaktifkan daripada dihapus
// supaya riwayat pesanan tetap utuh.
$stmt = $pdo->prepare('SELECT COUNT(*) FROM order_items WHERE product_id = ?');
$stmt->execute([$id]);
$pernah_dipesan = (int)$stmt->fetchColumn() > 0;

if ($pernah_dipesan) {
    $pdo->prepare("UPDATE products SET status = 'nonaktif' WHERE id = ?")->execute([$id]);
    set_flash('info', 'Produk ini ada di riwayat pesanan, jadi statusnya diubah menjadi nonaktif dan disembunyikan dari website.');
} else {
    $pdo->prepare('DELETE FROM products WHERE id = ?')->execute([$id]);
    hapus_gambar('products', $produk['image']);
    set_flash('sukses', 'Produk "' . $produk['name'] . '" dihapus.');
}

redirect('admin/products.php');
