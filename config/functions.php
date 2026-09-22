<?php
/**
 * WARUNG POJOK - Fungsi Bantu (reusable)
 * Developer: KelasPojok-Dev
 */

/* =====================================================================
 * 1. OUTPUT & URL
 * ===================================================================*/

/** Sanitasi output untuk mencegah XSS. Dipakai di semua echo. */
function e($text)
{
    return htmlspecialchars((string)$text, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/** Membuat URL halaman publik. Contoh: url('menu.php') */
function url($path = '')
{
    return BASE_URL . '/' . ltrim($path, '/');
}

/**
 * URL file di folder assets.
 * Otomatis menambahkan parameter versi (?v=waktu-file-diubah) supaya
 * browser & hosting tidak menyajikan versi CSS/JS lama dari cache
 * setiap kali file ini diedit.
 */
function asset($path)
{
    $path     = ltrim($path, '/');
    $fullPath = APP_ROOT . '/assets/' . $path;
    $version  = is_file($fullPath) ? filemtime($fullPath) : time();
    return BASE_URL . '/assets/' . $path . '?v=' . $version;
}

/**
 * URL gambar hasil upload. $folder: products|gallery|logo|settings
 * Kalau file tidak ada, dipakai gambar cadangan.
 */
function upload_url($folder, $file, $fallback = 'assets/images/hero-dimsum.png')
{
    if ($file && is_file(UPLOAD_DIR . '/' . $folder . '/' . $file)) {
        return UPLOAD_URL . '/' . $folder . '/' . rawurlencode($file);
    }
    return BASE_URL . '/' . ltrim($fallback, '/');
}

/** Format angka jadi rupiah. */
function rupiah($angka)
{
    return 'Rp' . number_format((float)$angka, 0, ',', '.');
}

/** Ubah teks jadi slug URL. */
function slugify($text)
{
    $text = strtolower(trim($text));
    $text = preg_replace('/[^a-z0-9]+/', '-', $text);
    return trim($text, '-') ?: 'item-' . time();
}

/** Potong teks panjang. */
function potong($text, $max = 100)
{
    $text = trim(strip_tags($text));
    return mb_strlen($text) > $max ? mb_substr($text, 0, $max) . '…' : $text;
}

/** Tanggal versi Indonesia. */
function tgl_id($datetime)
{
    $bulan = [1=>'Januari','Februari','Maret','April','Mei','Juni','Juli',
              'Agustus','September','Oktober','November','Desember'];
    $ts = strtotime($datetime);
    return date('j', $ts) . ' ' . $bulan[(int)date('n', $ts)] . ' ' . date('Y', $ts);
}

/* =====================================================================
 * 2. PENGATURAN WEBSITE (tabel settings)
 * ===================================================================*/

/** Ambil semua pengaturan sekali saja lalu simpan di memori. */
function all_settings($refresh = false)
{
    static $cache = null;
    global $pdo;
    if ($cache === null || $refresh) {
        $cache = [];
        foreach ($pdo->query('SELECT setting_key, setting_value FROM settings') as $row) {
            $cache[$row['setting_key']] = $row['setting_value'];
        }
    }
    return $cache;
}

/** Ambil satu pengaturan. Contoh: setting('whatsapp') */
function setting($key, $default = '')
{
    $s = all_settings();
    return (isset($s[$key]) && $s[$key] !== '') ? $s[$key] : $default;
}

/** Simpan/update satu pengaturan. */
function set_setting($key, $value)
{
    global $pdo;
    $stmt = $pdo->prepare(
        'INSERT INTO settings (setting_key, setting_value) VALUES (?, ?)
         ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)'
    );
    $stmt->execute([$key, $value]);
    all_settings(true);
}

/* =====================================================================
 * 3. WHATSAPP & MAPS
 * ===================================================================*/

/** Nomor WhatsApp format internasional (62...). */
function wa_number()
{
    $no = preg_replace('/[^0-9]/', '', setting('whatsapp', '6285747245030'));
    if (strpos($no, '0') === 0)  $no = '62' . substr($no, 1);
    if (strpos($no, '62') !== 0) $no = '62' . $no;
    return $no;
}

/** Link wa.me lengkap dengan pesan otomatis. */
function wa_link($pesan = '')
{
    $link = 'https://wa.me/' . wa_number();
    if ($pesan !== '') {
        $link .= '?text=' . rawurlencode($pesan);
    }
    return $link;
}

/* =====================================================================
 * 4. FLASH MESSAGE
 * ===================================================================*/

function set_flash($tipe, $pesan)
{
    $_SESSION['flash'] = ['tipe' => $tipe, 'pesan' => $pesan];
}

function get_flash()
{
    if (empty($_SESSION['flash'])) return null;
    $f = $_SESSION['flash'];
    unset($_SESSION['flash']);
    return $f;
}

/** Redirect lalu hentikan script. */
function redirect($path)
{
    header('Location: ' . (preg_match('#^https?://#', $path) ? $path : url($path)));
    exit;
}

/* =====================================================================
 * 5. CSRF
 * ===================================================================*/

function csrf_token()
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/** Input hidden CSRF untuk diletakkan di dalam <form>. */
function csrf_field()
{
    return '<input type="hidden" name="csrf_token" value="' . e(csrf_token()) . '">';
}

/** Cek token CSRF. Kalau salah, proses dihentikan. */
function csrf_check()
{
    $token = $_POST['csrf_token'] ?? '';
    if (!$token || !hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
        http_response_code(419);
        die('Sesi form sudah kedaluwarsa. Silakan muat ulang halaman dan coba lagi.');
    }
}

/* =====================================================================
 * 6. UPLOAD GAMBAR
 * ===================================================================*/

/**
 * Upload gambar dengan validasi ekstensi, MIME type, dan ukuran.
 * Mengembalikan nama file baru, atau null kalau tidak ada file/gagal.
 * Pesan error diisi ke $error.
 */
function upload_gambar(array $file, $folder, &$error = null)
{
    if (!isset($file['error']) || $file['error'] === UPLOAD_ERR_NO_FILE) {
        return null; // tidak ada file diunggah
    }
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $error = 'Gambar gagal diunggah. Coba lagi dengan file yang lebih kecil.';
        return null;
    }
    if ($file['size'] > MAX_UPLOAD_SIZE) {
        $error = 'Ukuran gambar melebihi 2 MB.';
        return null;
    }

    $izin = ['jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'png' => 'image/png',
             'webp' => 'image/webp', 'gif' => 'image/gif'];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!isset($izin[$ext])) {
        $error = 'Format gambar harus JPG, PNG, WEBP, atau GIF.';
        return null;
    }

    // Cek MIME asli file, bukan hanya nama file.
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime  = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    if (!in_array($mime, array_values($izin), true)) {
        $error = 'File yang diunggah bukan gambar yang valid.';
        return null;
    }
    if (@getimagesize($file['tmp_name']) === false) {
        $error = 'File yang diunggah bukan gambar yang valid.';
        return null;
    }

    $tujuan = UPLOAD_DIR . '/' . $folder;
    if (!is_dir($tujuan)) {
        @mkdir($tujuan, 0755, true);
    }
    $nama = $folder . '-' . date('Ymd') . '-' . bin2hex(random_bytes(6)) . '.' . $ext;

    if (!move_uploaded_file($file['tmp_name'], $tujuan . '/' . $nama)) {
        $error = 'Gambar tidak bisa disimpan. Pastikan folder uploads/ bisa ditulis.';
        return null;
    }
    return $nama;
}

/** Hapus file gambar lama supaya storage tidak menumpuk. */
function hapus_gambar($folder, $file)
{
    if (!$file) return;
    $path = UPLOAD_DIR . '/' . $folder . '/' . basename($file);
    if (is_file($path)) @unlink($path);
}

/* =====================================================================
 * 7. PRODUK & ULASAN
 * ===================================================================*/

/** Status produk yang sebenarnya (stok 0 = habis). */
function produk_habis(array $p)
{
    return $p['status'] === 'habis' || (int)$p['stock'] <= 0;
}

/** Rata-rata rating dan jumlah ulasan yang sudah disetujui. */
function rating_produk($product_id)
{
    global $pdo;
    $stmt = $pdo->prepare(
        "SELECT ROUND(AVG(rating),1) AS rata, COUNT(*) AS jumlah
         FROM reviews WHERE product_id = ? AND status = 'disetujui'"
    );
    $stmt->execute([$product_id]);
    $r = $stmt->fetch();
    return ['rata' => (float)($r['rata'] ?? 0), 'jumlah' => (int)($r['jumlah'] ?? 0)];
}

/** Tampilan bintang untuk rating. */
function bintang($nilai)
{
    $nilai = (int)round($nilai);
    $out = '<span class="stars" aria-label="Rating ' . $nilai . ' dari 5">';
    for ($i = 1; $i <= 5; $i++) {
        $out .= '<span class="star' . ($i <= $nilai ? ' is-on' : '') . '">&#9733;</span>';
    }
    return $out . '</span>';
}

/* =====================================================================
 * 8. KERANJANG (session)
 * ===================================================================*/

/** Isi keranjang mentah: [product_id => qty] */
function cart_raw()
{
    return $_SESSION['cart'] ?? [];
}

/** Jumlah total item di keranjang (untuk badge navbar). */
function cart_count()
{
    return array_sum(cart_raw());
}

/**
 * Isi keranjang lengkap dengan data produk terbaru dari database.
 * Produk yang sudah dihapus/nonaktif otomatis dibuang.
 */
function cart_items()
{
    global $pdo;
    $raw = cart_raw();
    if (!$raw) return [];

    $ids = array_map('intval', array_keys($raw));
    $in  = implode(',', array_fill(0, count($ids), '?'));
    $stmt = $pdo->prepare(
        "SELECT * FROM products WHERE id IN ($in) AND status <> 'nonaktif'"
    );
    $stmt->execute($ids);

    $items = [];
    foreach ($stmt->fetchAll() as $p) {
        $qty = (int)$raw[$p['id']];
        if ($qty < 1) continue;
        if ($qty > (int)$p['stock']) $qty = (int)$p['stock']; // jangan melebihi stok
        if ($qty < 1) continue;
        $p['qty']      = $qty;
        $p['subtotal'] = $qty * (float)$p['price'];
        $items[] = $p;
    }
    return $items;
}

/** Total harga keranjang. */
function cart_total(array $items = null)
{
    $items = $items ?? cart_items();
    return array_sum(array_column($items, 'subtotal'));
}

/** Teks pesanan untuk dikirim ke WhatsApp. */
function pesan_whatsapp(array $items, $total, $nama = '', $catatan = '', $alamat = '', $kode = '')
{
    $baris = ["Halo " . setting('site_name', 'Warung Pojok') . ", saya ingin memesan:"];
    foreach ($items as $it) {
        $baris[] = '- ' . $it['name'] . ' x' . $it['qty'] . ' = ' . rupiah($it['subtotal']);
    }
    $baris[] = 'Total = ' . rupiah($total);
    if ($kode)    $baris[] = 'Kode pesanan: ' . $kode;
    if ($nama)    $baris[] = 'Nama: ' . $nama;
    if ($alamat)  $baris[] = 'Alamat: ' . $alamat;
    if ($catatan) $baris[] = 'Catatan: ' . $catatan;
    return implode("\n", $baris);
}
