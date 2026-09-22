<?php
/**
 * WARUNG POJOK - Pengaturan tampilan & informasi website
 * Developer: KelasPojok-Dev
 *
 * Semua yang diubah di sini disimpan ke tabel settings, jadi tampilan
 * website publik bisa diubah tanpa mengedit kode.
 */
require_once dirname(__DIR__) . '/includes/auth.php';

// Daftar pengaturan berupa teks: key => label
$teks = [
    'site_name'          => 'Nama UMKM',
    'tagline'            => 'Tagline singkat',
    'hero_title_1'       => 'Judul hero baris 1',
    'hero_title_2'       => 'Judul hero baris 2',
    'hero_badge'         => 'Tulisan stiker hero',
    'cta_primary_text'   => 'Teks tombol utama',
    'cta_secondary_text' => 'Teks tombol kedua',
    'whatsapp'           => 'Nomor WhatsApp (format 62...)',
    'email'              => 'Email',
    'address'            => 'Alamat',
    'open_hours'         => 'Jam buka',
    'maps_link'          => 'Link Google Maps',
    'instagram'          => 'Link Instagram',
    'facebook'           => 'Link Facebook',
    'tiktok'             => 'Link TikTok',
    'bank_name'          => 'Nama bank',
    'bank_account'       => 'Nomor rekening',
    'bank_holder'        => 'Nama pemilik rekening',
    'about_title'        => 'Judul halaman tentang',
    'advantage_1'        => 'Keunggulan 1',
    'advantage_2'        => 'Keunggulan 2',
    'advantage_3'        => 'Keunggulan 3',
    'advantage_4'        => 'Keunggulan 4',
    'developer_name'     => 'Nama developer (footer)',
    'developer_url'      => 'Link developer (opsional)',
    'copyright_text'     => 'Teks tambahan copyright (opsional)',
];

// Pengaturan berupa teks panjang
$panjang = [
    'hero_desc'           => 'Deskripsi hero',
    'about_story'         => 'Cerita usaha',
    'about_vision'        => 'Visi',
    'about_mission'       => 'Misi',
    'about_target'        => 'Target konsumen',
    'payment_instruction' => 'Instruksi pembayaran transfer',
    'maps_embed'          => 'Link embed Google Maps (bagian src dari kode iframe)',
];

// Pengaturan warna
$warna = [
    'color_primary' => 'Warna utama (background merah)',
    'color_accent'  => 'Warna aksen (kuning)',
    'color_leaf'    => 'Warna daun (hijau lime)',
    'color_orange'  => 'Warna oranye',
];

// Pengaturan gambar: key => [label, folder]
$gambar = [
    'logo'       => ['Logo website', 'logo'],
    'favicon'    => ['Favicon', 'logo'],
    'hero_image' => ['Gambar hero / produk unggulan', 'settings'],
    'hero_bg'    => ['Gambar latar belakang hero (daun & bentuk organik)', 'settings'],
    'bg_image'   => ['Background website (opsional)', 'settings'],
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $pesan_error = '';

    // 1. Simpan semua teks
    foreach (array_merge($teks, $panjang) as $key => $label) {
        if (isset($_POST[$key])) {
            set_setting($key, trim($_POST[$key]));
        }
    }

    // 2. Simpan warna (validasi format hex)
    foreach ($warna as $key => $label) {
        $nilai = trim($_POST[$key] ?? '');
        if (preg_match('/^#[0-9A-Fa-f]{6}$/', $nilai)) {
            set_setting($key, strtoupper($nilai));
        }
    }

    // 3. Nomor WhatsApp dirapikan jadi format internasional
    $wa = preg_replace('/[^0-9]/', '', setting('whatsapp'));
    if (strpos($wa, '0') === 0)  $wa = '62' . substr($wa, 1);
    if (strpos($wa, '62') !== 0) $wa = '62' . $wa;
    set_setting('whatsapp', $wa);

    // 4. Unggah gambar
    foreach ($gambar as $key => [$label, $folder]) {
        if (empty($_FILES[$key]['name'])) continue;
        $error = null;
        $file  = upload_gambar($_FILES[$key], $folder, $error);
        if ($error) {
            $pesan_error .= $label . ': ' . $error . ' ';
            continue;
        }
        if ($file) {
            $lama = setting($key);
            set_setting($key, $file);
            if ($lama) hapus_gambar($folder, $lama);
        }
    }

    // 5. Hapus background kalau diminta
    if (!empty($_POST['hapus_bg'])) {
        hapus_gambar('settings', setting('bg_image'));
        set_setting('bg_image', '');
    }

    if ($pesan_error) {
        set_flash('error', trim($pesan_error));
    } else {
        set_flash('sukses', 'Pengaturan disimpan. Buka website untuk melihat hasilnya.');
    }
    redirect('admin/settings.php');
}

$adm_title  = 'Pengaturan tampilan';
$adm_active = 'pengaturan';
include __DIR__ . '/includes/header.php';
?>

<form class="adm-form adm-form--lebar" method="post" action="<?= url('admin/settings.php') ?>" enctype="multipart/form-data">
  <?= csrf_field() ?>

  <div class="adm-panel">
    <h2>Identitas &amp; teks</h2>
    <div class="adm-form__dua">
      <?php foreach ($teks as $key => $label): ?>
        <div>
          <label for="<?= e($key) ?>"><?= e($label) ?></label>
          <input type="text" id="<?= e($key) ?>" name="<?= e($key) ?>" value="<?= e(setting($key)) ?>" maxlength="255">
        </div>
      <?php endforeach; ?>
    </div>
  </div>

  <div class="adm-panel">
    <h2>Teks panjang</h2>
    <?php foreach ($panjang as $key => $label): ?>
      <div style="margin-bottom:16px">
        <label for="<?= e($key) ?>"><?= e($label) ?></label>
        <textarea id="<?= e($key) ?>" name="<?= e($key) ?>" maxlength="2000"><?= e(setting($key)) ?></textarea>
        <?php if ($key === 'maps_embed'): ?>
          <p class="adm-bantuan">Buka Google Maps &rarr; Bagikan &rarr; Sematkan peta, lalu salin isi src="..." saja.</p>
        <?php endif; ?>
      </div>
    <?php endforeach; ?>
  </div>

  <div class="adm-panel">
    <h2>Warna website</h2>
    <p class="adm-bantuan">Warna ini dipakai di seluruh halaman publik, termasuk ornamen daun di hero.</p>
    <div class="adm-form__dua">
      <?php foreach ($warna as $key => $label): ?>
        <div>
          <label for="<?= e($key) ?>"><?= e($label) ?></label>
          <input type="color" id="<?= e($key) ?>" name="<?= e($key) ?>" value="<?= e(setting($key, '#D13D34')) ?>">
        </div>
      <?php endforeach; ?>
    </div>
  </div>

  <div class="adm-panel">
    <h2>Gambar</h2>
    <div class="adm-form__dua">
      <?php foreach ($gambar as $key => [$label, $folder]): ?>
        <div>
          <label for="<?= e($key) ?>"><?= e($label) ?></label>
          <input type="file" id="<?= e($key) ?>" name="<?= e($key) ?>" accept="image/*" data-preview="pratinjau-<?= e($key) ?>">
          <img class="adm-preview" id="pratinjau-<?= e($key) ?>"
               src="<?= e(upload_url($folder, setting($key), 'assets/images/logo.png')) ?>" alt="Pratinjau <?= e($label) ?>">
        </div>
      <?php endforeach; ?>
    </div>
    <?php if (setting('bg_image')): ?>
      <p style="margin-top:14px"><label><input type="checkbox" name="hapus_bg" value="1"> Hapus background dan kembali ke warna polos</label></p>
    <?php endif; ?>
  </div>

  <div class="adm-panel">
    <div class="adm-aksi">
      <button class="adm-btn adm-btn--utama" type="submit">Simpan semua pengaturan</button>
      <a class="adm-btn adm-btn--garis" href="<?= url('index.php') ?>" target="_blank" rel="noopener">Lihat hasilnya</a>
    </div>
  </div>
</form>

<?php include __DIR__ . '/includes/footer.php'; ?>
