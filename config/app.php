<?php
/**
 * WARUNG POJOK - Konfigurasi Aplikasi
 * Developer: KelasPojok-Dev
 *
 * File ini dipanggil oleh semua halaman. Isinya:
 * - start session
 * - menentukan BASE_URL otomatis
 * - memanggil koneksi database dan fungsi bantu
 */

if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params(['httponly' => true, 'samesite' => 'Lax']);
    session_start();
}

// Root folder project di server (dipakai untuk path file upload)
define('APP_ROOT', dirname(__DIR__));

/**
 * BASE_URL dideteksi otomatis supaya website tetap jalan baik di
 * http://warung-pojok.test maupun di http://localhost/warung-pojok/.
 *
 * Kalau deteksi otomatis meleset di hosting, isi manual, contoh:
 * define('BASE_URL', 'https://warungpojok.com');
 */
if (!defined('BASE_URL')) {
    $docRoot = isset($_SERVER['DOCUMENT_ROOT'])
        ? str_replace('\\', '/', rtrim(realpath($_SERVER['DOCUMENT_ROOT']) ?: '', '/'))
        : '';
    $appPath = str_replace('\\', '/', APP_ROOT);
    $sub = ($docRoot && strpos($appPath, $docRoot) === 0)
        ? substr($appPath, strlen($docRoot))
        : '';
    define('BASE_URL', rtrim($sub, '/'));
}

// Lokasi folder upload
define('UPLOAD_DIR', APP_ROOT . '/uploads');
define('UPLOAD_URL', BASE_URL . '/uploads');

// Batas upload gambar: 2 MB
define('MAX_UPLOAD_SIZE', 2 * 1024 * 1024);

require_once APP_ROOT . '/config/database.php';
require_once APP_ROOT . '/config/functions.php';
