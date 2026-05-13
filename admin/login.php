<?php

declare(strict_types=1);

require_once __DIR__ . '/../app/config/init.php';

$auth = new Auth(db());

if ($auth->check()) {
    redirect('admin/index.php');
}

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    requireCsrfToken();

    if ($auth->attempt((string) ($_POST['username'] ?? ''), (string) ($_POST['password'] ?? ''))) {
        redirect('admin/index.php');
    }

    $error = 'Invalid admin credentials.';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login | Tablet Survey</title>
    <link rel="stylesheet" href="<?= e(asset('css/style.css')) ?>">
</head>
<body>
    <div class="login-shell">
        <form method="post" class="form-card" style="width:min(460px,100%);">
            <?= csrfField() ?>
            <p class="eyebrow">Admin Login</p>
            <h1>Manage Tablet Survey</h1>
            <?php if ($error): ?>
                <div class="flash"><?= e($error) ?></div>
            <?php endif; ?>
            <label>
                Username
                <input type="text" name="username" required />
            </label>
            <label>
                Password
                <input type="password" name="password" required />
            </label>
            <div class="actions" style="margin-top:1rem;">
                <button type="submit">Login</button>
                <a class="button secondary" href="<?= e(url()) ?>">Back to site</a>
            </div>
        </form>
    </div>
</body>
</html>
