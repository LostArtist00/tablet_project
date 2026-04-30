<?php

declare(strict_types=1);

require_once __DIR__ . '/app/config/init.php';
require_once APP_PATH . '/includes/admin.php';

$auth = new Auth(db());
$auth->requireAdmin();

$brandModel = new Brand(db());

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    requireCsrfToken();
    
    $name = trim($_POST['name']);
    
    if ($brandModel->exists($name)) {
        setFlash('Brand already exists.', 'error');
    } else {
        $brandModel->create($name);
        setFlash('Brand created.');
        redirect('admin/brands/index.php');
    }
}

renderAdminHeader('Add Brand');
?>
<div class="container">
    <h1>Add Brand</h1>
    <?php if (isset($_SESSION['flash'])): ?>
        <p class="form-error"><?= e($_SESSION['flash']) ?></p>
    <?php endif; ?>
    <form method="post" class="card" style="max-width: 400px;">
        <?= csrfField() ?>
        <div class="form-group">
            <label>Brand Name</label>
            <input type="text" name="name" required autofocus>
        </div>
        <button type="submit" class="button">Create Brand</button>
        <a href="index.php" class="button secondary">Cancel</a>
    </form>
</div>
<?php renderAdminFooter(); ?>