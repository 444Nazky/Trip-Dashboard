<?php
// Trip Angkutan Admin - Authentication Gateway

session_start();

if (isset($_SESSION['admin_logged_in']) {
    header('Location: index.php');
    exit;
}

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        $error = 'Username dan password harus diisi.';
    } else {
        $ch = curl_init('http://localhost:3000/api/auth/admin-login');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['username' => $username, 'password' => $password]));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        $resp = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($code === 200) {
            $data = json_decode($resp, true);
            if (!empty($data['token'])) {
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin_token'] = $data['token'];
                $_SESSION['admin_username'] = $username;
                session_regenerate_id(true);
                header('Location: index.php');
                exit;
            }
        }
        $error = 'Kredensial tidak valid.';
    }
}
?><!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login - Trip Angkutan Admin</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
  * { margin: 0; padding: 0; box-sizing: border-box; }
  body {
    font-family: 'Inter', sans-serif;
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #0F172A;
  }
  .login-card {
    background: #1E293B;
    border: 1px solid #334155;
    border-radius: 16px;
    padding: 40px;
    width: 100%;
    max-width: 380px;
    box-shadow: 0 25px 50px rgba(0,0,0,0.5);
  }
  .logo {
    width: 56px;
    height: 56px;
    background: #3B82F6;
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 28px;
    margin: 0 auto 24px;
  }
  h1 { color: #F8FAFC; font-size: 20px; font-weight: 700; text-align: center; margin-bottom: 4px; }
  .sub { color: #64748B; font-size: 13px; text-align: center; margin-bottom: 28px; }
  .form-group { margin-bottom: 14px; }
  label { display: block; color: #94A3B8; font-size: 12px; font-weight: 600; margin-bottom: 6px; text-transform: uppercase; letter-spacing: 0.05em; }
  input[type=text], input[type=password] {
    width: 100%;
    padding: 10px 14px;
    border-radius: 8px;
    border: 1px solid #334155;
    background: #0F172A;
    color: #F8FAFC;
    font-size: 14px;
    font-family: 'Inter', sans-serif;
    outline: none;
    transition: border-color 0.2s;
  }
  input:focus { border-color: #3B82F6; }
  .error {
    background: rgba(239,68,68,0.15);
    border: 1px solid rgba(239,68,68,0.4);
    border-radius: 8px;
    padding: 10px 14px;
    color: #FCA5A5;
    font-size: 13px;
    margin-bottom: 16px;
  }
  button {
    width: 100%;
    background: #3B82F6;
    color: white;
    border: none;
    border-radius: 8px;
    padding: 11px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    font-family: 'Inter', sans-serif;
    transition: background 0.2s;
  }
  button:hover { background: #2563EB; }
  .hint { margin-top: 16px; text-align: center; font-size: 11px; color: #475569; line-height: 1.5; }
</style>
</head>
<body>
<div class="login-card">
  <div class="logo">🚛</div>
  <h1>Trip Angkutan</h1>
  <p class="sub">Masuk ke panel administrasi</p>

  <?php if ($error): ?>
  <div class="error"><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>

  <form method="POST" autocomplete="off">
    <div class="form-group">
      <label for="username">Username</label>
      <input type="text" id="username" name="username" placeholder="admin" required autofocus>
    </div>
    <div class="form-group">
      <label for="password">Password</label>
      <input type="password" id="password" name="password" placeholder="••••••" required>
    </div>
    <button type="submit">Masuk</button>
  </form>

  <p class="hint">
    Akses petugas — hubungi administrator jika lupa kredensial.
  </p>
</div>
</body>
</html>
