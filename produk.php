<?php
/**
 * WARUNG POJOK - Alias halaman produk
 * Developer: KelasPojok-Dev
 * Semua kunjungan ke produk.php diarahkan ke katalog menu.php,
 * termasuk kalau ada parameter pencarian/kategori yang dibawa.
 */
require_once __DIR__ . '/config/app.php';

$query = $_GET ? '?' . http_build_query($_GET) : '';
redirect('menu.php' . $query);
