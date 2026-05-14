<?php

declare(strict_types=1);

use App\Models\Auth;
use App\Models\Brand;

require_once __DIR__ . '/../../app/config/init.php';
require_once APP_PATH . '/includes/admin.php';

$auth = new Auth(db());
$auth->requireAdmin();

$brandModel = new Brand(db());

$id = isset($_GET['id']) ? (int) $_GET['id'] : null;
$brand = $id ? $brandModel->byId($id) : null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    requireCsrfToken();
    
    $name = trim($_POST['name']);
    
    if ($brandModel->exists($name, $id)) {
        setFlash('Brand already exists.', 'error');
    } elseif ($id) {
        $brandModel->update($id, $name);
        setFlash('Brand updated.');
        redirect('index.php');
    } else {
        $brandModel->create($name);
        setFlash('Brand created.');
        redirect('index.php');
    }
}

renderAdminHeader($id ? 'Edit Brand' : 'Add Brand');
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
            <input type="text" name="name" value="<?= e($brand['name'] ?? '') ?>" required autofocus>
        </div>
        <button type="submit" class="button"><?= $id ? 'Update' : 'Create' ?> Brand</button>
        <a href="index.php" class="button secondary">Cancel</a>
    </form>
</div>
<?php renderAdminFooter(); ?>