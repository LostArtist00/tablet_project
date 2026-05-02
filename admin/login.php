<?php

declare(strict_types=1);

require_once __DIR__ . '/../../app/config/init.php';

$auth = new Auth(db());

if ($auth->check()) {
    redirect('admin/index.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    requireCsrfToken();
    
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if ($auth->attempt($username, $password)) {
        setFlash('Welcome back!');
        redirect('admin/index.php');
    } else {
        setFlash('Invalid credentials.', 'error');
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link rel="stylesheet" href="<?= e(asset('css/style.css')) ?>">
    <style>
        body { display: flex; align-items: center; justify-content: center; min-height: 100vh; }
        .login-box { width: 100%; max-width: 360px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="card login-box">
            <h1>Admin Login</h1>
            <?php if (isset($_SESSION['flash'])): ?>
                <p class="form-error"><?= e($_SESSION['flash']) ?></p>
            <?php endif; ?>
            <form method="post">
                <?= csrfField() ?>
                <div class="form-group">
                    <label>Username</label>
                    <input type="text" name="username" required autofocus>
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" required>
                </div>
                <button type="submit" class="button">Login</button>
            </form>
        </div>
    </div>
</body>
</html>