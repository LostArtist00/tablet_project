<?php

declare(strict_types=1);

use App\Models\Auth;
use App\Models\Brand;

require_once __DIR__ . '/../../app/config/init.php';
require_once APP_PATH . '/includes/admin.php';

$auth = new Auth(db());
$auth->requireAdmin();

$brandModel = new Brand(db());

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete'])) {
    requireCsrfToken();
    $brandModel->delete((int) $_POST['delete']);
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
                        <form method="post" style="display:inline" onsubmit="return confirm('Delete this brand?')">
                            <?= csrfField() ?>
                            <input type="hidden" name="delete" value="<?= (int) $brand['id'] ?>">
                            <button type="submit" style="background:none;border:none;color:inherit;cursor:pointer;padding:0;font:inherit;text-decoration:underline">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php renderAdminFooter(); ?>