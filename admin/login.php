<?php
/**
 * WARUNG POJOK - Login admin (halaman tersembunyi)
 * Developer: KelasPojok-Dev
 * URL: /admin/login.php  (tidak pernah ditautkan dari navbar publik)
 */
require_once dirname(__DIR__) . '/config/app.php';

// Sudah login? langsung ke dashboard.
if (!empty($_SESSION['admin_id'])) {
    redirect('admin/index.php');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();

    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    // Batasi percobaan login supaya tidak mudah ditebak paksa.
    $_SESSION['login_gagal'] = $_SESSION['login_gagal'] ?? 0;
    $_SESSION['login_jeda']  = $_SESSION['login_jeda'] ?? 0;

    if ($_SESSION['login_gagal'] >= 5 && (time() - $_SESSION['login_jeda']) < 60) {
        $error = 'Terlalu banyak percobaan. Tunggu satu menit lalu coba lagi.';
    } elseif ($username === '' || $password === '') {
        $error = 'Username dan password wajib diisi.';
    } else {
        $stmt = $pdo->prepare('SELECT * FROM admins WHERE username = ? LIMIT 1');
        $stmt->execute([$username]);
        $admin = $stmt->fetch();

        if ($admin && password_verify($password, $admin['password'])) {
            // Ganti ID session untuk mencegah session fixation.
            session_regenerate_id(true);
            $_SESSION['admin_id']   = (int)$admin['id'];
            $_SESSION['admin_nama'] = $admin['nama'];
            $_SESSION['admin_last'] = time();
            $_SESSION['login_gagal'] = 0;

            set_flash('sukses', 'Selamat datang, ' . $admin['nama'] . '.');
            redirect('admin/index.php');
        }
        $_SESSION['login_gagal']++;
        $_SESSION['login_jeda'] = time();
        $error = 'Username atau password salah.';
    }
}

$flash = get_flash();
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex, nofollow">
<meta name="author" content="<?= e(setting('developer_name', 'KelasPojok-Dev')) ?>">
<title>Login Admin - <?= e(setting('site_name')) ?></title>
<link rel="icon" href="<?= e(upload_url('logo', setting('favicon'), 'assets/images/logo.png')) ?>">
<link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@400;500;600&family=Nunito:wght@400;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= asset('css/admin.css') ?>">
</head>
<body class="admin">
<div class="adm-login">
  <div class="adm-login__kotak">
    <img src="<?= e(upload_url('logo', setting('logo'), 'assets/images/logo.png')) ?>" alt="Logo <?= e(setting('site_name')) ?>" width="64" height="64">
    <h1>Login admin</h1>
    <p class="adm-bantuan">Halaman ini khusus pengelola <?= e(setting('site_name')) ?>.</p>

    <?php if ($flash): ?>
      <div class="adm-pesan adm-pesan--<?= $flash['tipe'] === 'error' ? 'error' : 'info' ?>"><?= e($flash['pesan']) ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
      <div class="adm-pesan adm-pesan--error"><?= e($error) ?></div>
    <?php endif; ?>

    <form class="adm-form" method="post" action="<?= url('admin/login.php') ?>">
      <?= csrf_field() ?>
      <div>
        <label for="username">Username</label>
        <input type="text" id="username" name="username" autocomplete="username" required autofocus>
      </div>
      <div>
        <label for="password">Password</label>
        <input type="password" id="password" name="password" autocomplete="current-password" required>
      </div>
      <button class="adm-btn adm-btn--utama" type="submit">Masuk</button>
    </form>
  </div>
</div>
</body>
</html>
