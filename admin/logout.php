<?php
/**
 * WARUNG POJOK - Logout admin
 * Developer: KelasPojok-Dev
 */
require_once dirname(__DIR__) . '/config/app.php';

$keranjang = $_SESSION['cart'] ?? [];   // keranjang pengunjung tidak ikut terhapus
session_unset();
session_destroy();
session_start();
$_SESSION['cart'] = $keranjang;

set_flash('info', 'Kamu sudah keluar dari panel admin.');
redirect('admin/login.php');
