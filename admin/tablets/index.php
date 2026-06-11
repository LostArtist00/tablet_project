<?php

declare(strict_types=1);

use App\Models\Auth;
use App\Models\Tablet;

require_once __DIR__ . '/../../app/config/init.php';
require_once APP_PATH . '/includes/admin.php';

$auth = new Auth(db());
$auth->requireAdmin();

$tabletModel = new Tablet(db());

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete'])) {
    requireCsrfToken();
    $tabletModel->delete((int) $_POST['delete']);
    setFlash('Tablet deleted.');
    redirect('admin/tablets/index.php');
}

$tablets = $tabletModel->all();

renderAdminHeader('Tablets');
?>
<div class="container">
    <div class="flex-between">
        <h1>Tablets</h1>
        <a href="form.php" class="button">Add Tablet</a>
    </div>
    <table>
        <thead>
            <tr>
                <th>Image</th>
                <th>ID</th>
                <th>Brand</th>
                <th>Model</th>
                <th>Size</th>
                <th>Type</th>
                <th>Connection</th>
                <th>Price</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($tablets as $tablet): ?>
                <tr>
                    <td>
                        <?php if ($tablet['image_path']): ?>
                            <img src="<?= e(uploadUrl($tablet['image_path'])) ?>" alt="" style="width:60px;height:45px;object-fit:cover;object-position:<?= (int) ($tablet['image_pos_x'] ?? 50) ?>% <?= (int) ($tablet['image_pos_y'] ?? 50) ?>%;border-radius:6px;border:1px solid var(--border);">
                        <?php else: ?>
                            <span style="color:var(--text);opacity:0.4;font-size:0.8rem;">none</span>
                        <?php endif; ?>
                    </td>
                    <td><?= (int) $tablet['id'] ?></td>
                    <td><?= e($tablet['brand_name']) ?></td>
                    <td><?= e($tablet['name']) ?></td>
                    <td><?= e($tablet['size']) ?></td>
                    <td><span class="pill"><?= e($tablet['has_display'] ? 'Display' : 'Pen') ?></span></td>
                    <td><span class="muted" style="font-size:0.85rem;"><?= e($tablet['connection_type'] ?? '—') ?></span></td>
                    <td>$<?= e($tablet['price']) ?></td>
                    <td>
                        <a href="form.php?id=<?= (int) $tablet['id'] ?>">Edit</a> |
                        <form method="post" style="display:inline" onsubmit="return confirm('Delete this tablet?')">
                            <?= csrfField() ?>
                            <input type="hidden" name="delete" value="<?= (int) $tablet['id'] ?>">
                            <button type="submit" style="background:none;border:none;color:inherit;cursor:pointer;padding:0;font:inherit;text-decoration:underline">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php renderAdminFooter(); ?>