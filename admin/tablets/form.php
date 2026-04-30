<?php

declare(strict_types=1);

require_once __DIR__ . '/app/config/init.php';
require_once APP_PATH . '/includes/admin.php';

$auth = new Auth(db());
$auth->requireAdmin();

$tabletModel = new Tablet(db());
$brandModel = new Brand(db());

$id = isset($_GET['id']) ? (int) $_GET['id'] : null;
$brands = $brandModel->all();

$tablet = $id ? $tabletModel->byId($id) : null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    requireCsrfToken();
    
    $data = [
        'brand_id' => (int) $_POST['brand_id'],
        'name' => $_POST['name'],
        'size' => $_POST['size'] ?: null,
        'has_display' => isset($_POST['has_display']) ? 1 : 0,
        'price' => $_POST['price'] ?: null,
        'release_date' => $_POST['release_date'] ?: null,
        'pressure_levels' => $_POST['pressure_levels'] ?: null,
        'connection_type' => $_POST['connection_type'] ?: null,
        'notes' => $_POST['notes'] ?: null,
    ];
    
    if ($id) {
        $tabletModel->update($id, $data);
        setFlash('Tablet updated.');
    } else {
        $tabletModel->create($data);
        setFlash('Tablet created.');
    }
    redirect('admin/tablets/index.php');
}

renderAdminHeader($id ? 'Edit Tablet' : 'Add Tablet');
?>
<div class="container">
    <h1><?= $id ? 'Edit Tablet' : 'Add Tablet' ?></h1>
    <?php if (isset($_SESSION['flash'])): ?>
        <p class="form-error"><?= e($_SESSION['flash']) ?></p>
    <?php endif; ?>
    <form method="post" class="card" style="max-width: 600px;">
        <?= csrfField() ?>
        <div class="form-group">
            <label>Brand</label>
            <select name="brand_id" required>
                <?php foreach ($brands as $brand): ?>
                    <option value="<?= (int) $brand['id'] ?>" <?= ($tablet && $tablet['brand_id'] == $brand['id']) ? 'selected' : '' ?>>
                        <?= e($brand['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label>Model Name</label>
            <input type="text" name="name" value="<?= e($tablet['name'] ?? '') ?>" required>
        </div>
        <div class="form-group">
            <label>Size</label>
            <input type="text" name="size" value="<?= e($tablet['size'] ?? '') ?>" placeholder="e.g. 10 x 6 in">
        </div>
        <div class="form-group">
            <label>
                <input type="checkbox" name="has_display" value="1" <?= ($tablet && $tablet['has_display']) ? 'checked' : '' ?>>
                Has Display
            </label>
        </div>
        <div class="form-group">
            <label>Price</label>
            <input type="number" name="price" step="0.01" value="<?= e($tablet['price'] ?? '') ?>">
        </div>
        <div class="form-group">
            <label>Release Date</label>
            <input type="date" name="release_date" value="<?= e($tablet['release_date'] ?? '') ?>">
        </div>
        <div class="form-group">
            <label>Pressure Levels</label>
            <input type="text" name="pressure_levels" value="<?= e($tablet['pressure_levels'] ?? '') ?>" placeholder="e.g. 8192">
        </div>
        <div class="form-group">
            <label>Connection Type</label>
            <input type="text" name="connection_type" value="<?= e($tablet['connection_type'] ?? '') ?>" placeholder="e.g. USB-C">
        </div>
        <div class="form-group">
            <label>Notes</label>
            <textarea name="notes" rows="3"><?= e($tablet['notes'] ?? '') ?></textarea>
        </div>
        <button type="submit" class="button">Save</button>
        <a href="index.php" class="button secondary">Cancel</a>
    </form>
</div>
<?php renderAdminFooter(); ?>