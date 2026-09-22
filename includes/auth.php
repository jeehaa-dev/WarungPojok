<?php
/**
 * WARUNG POJOK - Proteksi halaman admin
 * Developer: KelasPojok-Dev
 *
 * Panggil file ini di baris paling atas setiap halaman admin
 * (kecuali login.php) supaya hanya admin yang sudah login bisa masuk.
 */
require_once dirname(__DIR__) . '/config/app.php';

if (empty($_SESSION['admin_id'])) {
    set_flash('error', 'Silakan login terlebih dahulu.');
    header('Location: ' . url('admin/login.php'));
    exit;
}

// Auto logout setelah 2 jam tidak ada aktivitas.
if (isset($_SESSION['admin_last']) && (time() - $_SESSION['admin_last']) > 7200) {
    session_unset();
    session_destroy();
    session_start();
    set_flash('error', 'Sesi login berakhir. Silakan login lagi.');
    header('Location: ' . url('admin/login.php'));
    exit;
}
$_SESSION['admin_last'] = time();

$admin_nama = $_SESSION['admin_nama'] ?? 'Admin';
