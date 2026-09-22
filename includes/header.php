<?php
/**
 * WARUNG POJOK - Header halaman publik
 * Developer: KelasPojok-Dev
 *
 * Variabel yang bisa diisi sebelum include file ini:
 * $page_title, $page_desc, $active (menu navbar yang sedang aktif)
 */
if (!defined('BASE_URL')) {
    require_once dirname(__DIR__) . '/config/app.php';
}
$site_name  = setting('site_name', 'WARUNG POJOK');
$page_title = isset($page_title) ? $page_title . ' - ' . $site_name : $site_name . ' - Dimsum Mentai';
$page_desc  = $page_desc ?? setting('tagline');
$active     = $active ?? '';
$logo_url   = upload_url('logo', setting('logo'), 'assets/images/logo.png');
$bg_image   = setting('bg_image');
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($page_title) ?></title>
<meta name="description" content="<?= e(potong($page_desc, 155)) ?>">
<meta name="author" content="<?= e(setting('developer_name', 'KelasPojok-Dev')) ?>">
<meta property="og:title" content="<?= e($page_title) ?>">
<meta property="og:description" content="<?= e(potong($page_desc, 155)) ?>">
<meta property="og:type" content="website">
<meta property="og:image" content="<?= e(upload_url('settings', setting('hero_image'), 'assets/images/hero-dimsum.png')) ?>">
<link rel="icon" href="<?= e(upload_url('logo', setting('favicon'), 'assets/images/logo.png')) ?>">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@400;500;600;700&family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= asset('css/style.css') ?>">
<link rel="stylesheet" href="<?= asset('css/responsive.css') ?>">
<!-- Warna diambil dari pengaturan admin, karena itu ditulis sebagai CSS variable di sini. -->
<style>
:root{
  --merah: <?= e(setting('color_primary', '#D13D34')) ?>;
  --kuning: <?= e(setting('color_accent', '#F6D04A')) ?>;
  --lime: <?= e(setting('color_leaf', '#B0BA1C')) ?>;
  --oranye: <?= e(setting('color_orange', '#EC9736')) ?>;
}
<?php if ($bg_image): ?>
body{ background-image:url('<?= e(upload_url('settings', $bg_image, '')) ?>'); background-size:cover; background-attachment:fixed; }
<?php endif; ?>
</style>
</head>
<body>
<a class="skip-link" href="#konten">Lompat ke konten</a>
<?php include APP_ROOT . '/includes/navbar.php'; ?>
<main id="konten">
<?php include APP_ROOT . '/includes/flash-message.php'; ?>
