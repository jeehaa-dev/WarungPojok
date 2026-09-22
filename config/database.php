<?php
/**
 * WARUNG POJOK - Koneksi Database (PDO)
 * Developer: KelasPojok-Dev
 *
 * UBAH BAGIAN DI BAWAH INI SAAT PINDAH KE HOSTING.
 * Di Laragon biasanya: user = root, password = kosong.
 */

// ------- KONFIGURASI DATABASE -------
$DB_HOST = 'bhivetc28pc91m96ng8y-mysql.services.clever-cloud.com';
$DB_NAME = 'bhivetc28pc91m96ng8y';
$DB_USER = 'u34rcrlblim5iyxe';
$DB_PASS = 'u34rcrlblim5iyxe';
$DB_PORT = 3306;
// ------------------------------------

// Set true HANYA saat development. Di hosting biarkan false.
define('APP_DEBUG', false);

try {
    $dsn = "mysql:host={$DB_HOST};port={$DB_PORT};dbname={$DB_NAME};charset=utf8mb4";
    $pdo = new PDO($dsn, $DB_USER, $DB_PASS, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]);
} catch (PDOException $e) {
    // Error mentah tidak ditampilkan ke pengunjung.
    if (APP_DEBUG) {
        die('Koneksi database gagal: ' . $e->getMessage());
    }
    http_response_code(500);
    die('<h1 style="font-family:sans-serif">Website sedang bermasalah</h1>
         <p style="font-family:sans-serif">Koneksi ke database gagal. Periksa pengaturan di config/database.php.</p>');
}
