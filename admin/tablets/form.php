<?php

declare(strict_types=1);

require_once __DIR__ . '/../../app/config/init.php';
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
        'image_path' => $tablet['image_path'] ?? null,
        'image_fit' => $_POST['image_fit'] ?? 'cover',
    ];

    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        if (in_array($ext, $allowed, true)) {
            $filename = 'tablet_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
            $dest = APP_ROOT . '/uploads/tablets/' . $filename;
            move_uploaded_file($_FILES['image']['tmp_name'], $dest);
            $data['image_path'] = 'tablets/' . $filename;

            if ($tablet && $tablet['image_path']) {
                $old = APP_ROOT . '/uploads/' . $tablet['image_path'];
                if (file_exists($old)) unlink($old);
            }
        }
    }

    if (isset($_POST['remove_image']) && $tablet && $tablet['image_path']) {
        $old = APP_ROOT . '/uploads/' . $tablet['image_path'];
        if (file_exists($old)) unlink($old);
        $data['image_path'] = null;
    }

    if ($id) {
        $tabletModel->update($id, $data);
        setFlash('Tablet updated.');
    } else {
        $id = $tabletModel->create($data);
        $data['image_path'] = 'tablets/tablet_' . $id . '_' . time() . '.' . $ext ?? 'jpg';
        setFlash('Tablet created.');
    }
    redirect('admin/tablets/index.php');
}

renderAdminHeader($id ? 'Edit Tablet' : 'Add Tablet');
?>
<div class="container">
    <h1><?= $id ? 'Edit Tablet' : 'Add Tablet' ?></h1>
    <form method="post" class="card" style="max-width:600px;" enctype="multipart/form-data">
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
            <label>Image</label>
            <?php if ($tablet && $tablet['image_path']): ?>
                <div style="margin-bottom:0.5rem;">
                    <img src="<?= e(uploadUrl($tablet['image_path'])) ?>" alt="" style="max-width:200px;max-height:150px;border-radius:8px;border:1px solid var(--border);">
                    <label style="display:inline-flex;align-items:center;gap:0.4rem;margin-top:0.4rem;">
                        <input type="checkbox" name="remove_image" value="1"> Remove image
                    </label>
                </div>
            <?php endif; ?>
            <input type="file" name="image" accept="image/jpeg,image/png,image/gif,image/webp">
        </div>
        <div class="form-group">
            <label>Image Fit</label>
            <select name="image_fit">
                <option value="cover" <?= ($tablet['image_fit'] ?? 'cover') === 'cover' ? 'selected' : '' ?>>Cover (crop to fill)</option>
                <option value="contain" <?= ($tablet['image_fit'] ?? '') === 'contain' ? 'selected' : '' ?>>Contain (fit inside)</option>
                <option value="fill" <?= ($tablet['image_fit'] ?? '') === 'fill' ? 'selected' : '' ?>>Fill (stretch)</option>
            </select>
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
