<?php

declare(strict_types=1);

use App\Models\Auth;
use App\Models\Brand;

require_once __DIR__ . '/../../app/config/init.php';
require_once APP_PATH . '/includes/admin.php';

$auth = new Auth(db());
$auth->requireAdmin();

$brandModel = new Brand(db());

if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $brandModel->delete((int) $_GET['delete']);
    setFlash('Brand deleted.');
    redirect('admin/brands/index.php');
}

$brands = $brandModel->all();

renderAdminHeader('Brands');
?>
<div class="container">
    <div class="flex-between">
        <h1>Brands</h1>
        <a href="form.php" class="button">Add Brand</a>
    </div>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($brands as $brand): ?>
                <tr>
                    <td><?= (int) $brand['id'] ?></td>
                    <td><?= e($brand['name']) ?></td>
                    <td>
                        <a href="form.php?id=<?= (int) $brand['id'] ?>">Edit</a> |
                        <a href="index.php?delete=<?= (int) $brand['id'] ?>" onclick="return confirm('Delete?')">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php renderAdminFooter(); ?>