<?php
// admin/login.php - halaman login admin (split-screen)
require __DIR__ . '/../config/koneksi.php';
require __DIR__ . '/../includes/config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Kalau udah login, redirect ke dashboard
if (!empty($_SESSION['admin_id'])) {
    header('Location: dashboard.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        $error = 'Username dan password wajib diisi.';
    } else {
        $stmt = mysqli_prepare($koneksi, "SELECT id, username, password, nama FROM admin WHERE username = ? LIMIT 1");
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        $admin = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

        if ($admin && password_verify($password, $admin['password'])) {
            session_regenerate_id(true);
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_username'] = $admin['username'];
            $_SESSION['admin_nama'] = $admin['nama'];
            header('Location: dashboard.php');
            exit;
        } else {
            $error = 'Username atau password salah.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login Admin - <?= e($site['nama']) ?></title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>

<div class="login-wrapper">
  <!-- Left: image -->
  <div class="login-image" style="background-image: url('../assets/images/hero-meja-makan.svg');"></div>

  <!-- Right: form panel -->
  <div class="login-form-area">
    <div class="login-brand">UD KARYA ABADI</div>

    <div class="login-box">
      <h1>Login Admin</h1>
      <p class="login-sub">Masuk untuk mengelola informasi website UD Karya Abadi.</p>

      <?php if ($error): ?>
        <div class="alert-error"><?= e($error) ?></div>
      <?php endif; ?>

      <form method="post" action="">
        <?= csrf_field() ?>
        <div class="form-group">
          <label for="username">Username</label>
          <input type="text" id="username" name="username" placeholder="Masukkan username"
                 value="<?= e($_POST['username'] ?? '') ?>" required autofocus>
        </div>

        <div class="form-group">
          <label for="password">Password</label>
          <div class="password-wrap">
            <input type="password" id="password" name="password" placeholder="Masukkan password" required>
            <button type="button" class="toggle-pw" onclick="togglePassword()" aria-label="Lihat password">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                <circle cx="12" cy="12" r="3"></circle>
              </svg>
            </button>
          </div>
        </div>

        <button type="submit" class="btn-primary">Masuk</button>
      </form>

      <p class="login-note">Halaman khusus admin</p>
    </div>
  </div>
</div>

<script>
function togglePassword() {
  var input = document.getElementById('password');
  input.type = input.type === 'password' ? 'text' : 'password';
}
</script>
</body>
</html>
