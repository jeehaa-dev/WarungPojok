<?php
/**
 * WARUNG POJOK - Header panel admin
 * Developer: KelasPojok-Dev
 * Variabel: $adm_title, $adm_active
 */
if (empty($_SESSION['admin_id'])) {
    // Pengaman tambahan kalau file ini dipanggil tanpa auth.php
    require_once dirname(__DIR__, 2) . '/includes/auth.php';
}
$adm_title  = $adm_title ?? 'Dashboard';
$adm_active = $adm_active ?? '';
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex, nofollow">
<meta name="author" content="<?= e(setting('developer_name', 'KelasPojok-Dev')) ?>">
<title><?= e($adm_title) ?> - Admin <?= e(setting('site_name')) ?></title>
<link rel="icon" href="<?= e(upload_url('logo', setting('favicon'), 'assets/images/logo.png')) ?>">
<link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@400;500;600&family=Nunito:wght@400;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= asset('css/admin.css') ?>">
</head>
<body class="admin">
<div class="adm-layout">
<?php include __DIR__ . '/navbar.php'; ?>
  <main class="adm-main">
    <div class="adm-topbar">
      <h1><?= e($adm_title) ?></h1>
      <div class="adm-topbar__kanan">
        <span>Masuk sebagai <strong><?= e($_SESSION['admin_nama'] ?? 'Admin') ?></strong></span>
        <a class="adm-btn adm-btn--garis adm-btn--kecil" href="<?= url('index.php') ?>" target="_blank" rel="noopener">Lihat website</a>
      </div>
    </div>
<?php
$flash = get_flash();
if ($flash):
  $kelas = $flash['tipe'] === 'error' ? 'error' : ($flash['tipe'] === 'info' ? 'info' : 'sukses');
?>
    <div class="adm-pesan adm-pesan--<?= e($kelas) ?>" role="status"><?= e($flash['pesan']) ?></div>
<?php endif; ?>
