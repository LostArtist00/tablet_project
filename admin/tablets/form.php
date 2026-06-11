<?php

declare(strict_types=1);

use App\Models\Auth;
use App\Models\Tablet;
use App\Models\Brand;

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

    $connLabels = [
        'usb_c' => 'USB-C',
        'usb_a' => 'USB-A',
        'hdmi' => 'HDMI',
        'displayport' => 'DisplayPort',
        'bluetooth' => 'Bluetooth',
        'wireless' => 'Wireless',
    ];
    $selectedConnections = [];
    foreach ($connLabels as $key => $label) {
        if (!empty($_POST['conn_' . $key])) {
            $selectedConnections[] = $label;
        }
    }

    $data = [
        'brand_id' => (int) $_POST['brand_id'],
        'name' => $_POST['name'],
        'size' => $_POST['size'] ?: null,
        'has_display' => isset($_POST['has_display']) ? 1 : 0,
        'price' => $_POST['price'] ?: null,
        'release_date' => $_POST['release_date'] ?: null,
        'pressure_levels' => $_POST['pressure_levels'] ?: null,
        'connection_type' => $selectedConnections ? implode(', ', $selectedConnections) : null,
        'notes' => $_POST['notes'] ?: null,
        'image_path' => $tablet['image_path'] ?? null,
        'image_pos_x' => (int) ($_POST['image_pos_x'] ?? 50),
        'image_pos_y' => (int) ($_POST['image_pos_y'] ?? 50),
    ];
    $data['display_resolution'] = $data['has_display'] ? ($_POST['display_resolution'] ?: null) : null;

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
        setFlash('Tablet created.');
    }
    redirect('admin/tablets/index.php');
}

$existingConnections = $tablet ? array_map('trim', explode(',', $tablet['connection_type'] ?? '')) : [];

renderAdminHeader($id ? 'Edit Tablet' : 'Add Tablet');
?>
<div class="container">
    <h1><?= $id ? 'Edit Tablet' : 'Add Tablet' ?></h1>
    <form method="post" class="card admin-edit-form" enctype="multipart/form-data">
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
        <div class="form-row">
            <div class="form-group">
                <label>Size</label>
                <select name="size" id="size-select" onchange="toggleCustomSize()">
                    <option value="">Select size</option>
                    <option value="4 x 2.5 in" <?= ($tablet['size'] ?? '') === '4 x 2.5 in' ? 'selected' : '' ?>>4 x 2.5 in</option>
                    <option value="6 x 3.7 in" <?= ($tablet['size'] ?? '') === '6 x 3.7 in' ? 'selected' : '' ?>>6 x 3.7 in</option>
                    <option value="6 x 4 in" <?= ($tablet['size'] ?? '') === '6 x 4 in' ? 'selected' : '' ?>>6 x 4 in</option>
                    <option value="8.5 x 5.5 in" <?= ($tablet['size'] ?? '') === '8.5 x 5.5 in' ? 'selected' : '' ?>>8.5 x 5.5 in</option>
                    <option value="10 x 6 in" <?= ($tablet['size'] ?? '') === '10 x 6 in' ? 'selected' : '' ?>>10 x 6 in</option>
                    <option value="11 x 7 in" <?= ($tablet['size'] ?? '') === '11 x 7 in' ? 'selected' : '' ?>>11 x 7 in</option>
                    <option value="12 x 8 in" <?= ($tablet['size'] ?? '') === '12 x 8 in' ? 'selected' : '' ?>>12 x 8 in</option>
                    <option value="13.3 in (display)" <?= ($tablet['size'] ?? '') === '13.3 in' ? 'selected' : '' ?>>13.3 in (display)</option>
                    <option value="15.6 in (display)" <?= ($tablet['size'] ?? '') === '15.6 in' ? 'selected' : '' ?>>15.6 in (display)</option>
                    <option value="21.5 in (display)" <?= ($tablet['size'] ?? '') === '21.5 in' ? 'selected' : '' ?>>21.5 in (display)</option>
                    <option value="23.8 in (display)" <?= ($tablet['size'] ?? '') === '23.8 in' ? 'selected' : '' ?>>23.8 in (display)</option>
                    <option value="custom">Custom</option>
                </select>
                <input type="text" name="size_custom" id="size-custom" placeholder="e.g. 10 x 6 in" style="display:none;margin-top:0.5rem;" value="<?= e($tablet['size'] ?? '') ?>" />
            </div>
            <div class="form-group">
                <label>Has Display</label>
                <label class="toggle">
                    <input type="checkbox" name="has_display" value="1" <?= ($tablet && $tablet['has_display']) ? 'checked' : '' ?> onchange="toggleDisplayFields()">
                    <span class="toggle-slider"></span>
                    <span class="toggle-label"><?= ($tablet && $tablet['has_display']) ? 'Yes' : 'No' ?></span>
                </label>
            </div>
        </div>
        <div class="form-row" id="display-fields" style="<?= ($tablet && $tablet['has_display']) ? '' : 'display:none' ?>">
            <div class="form-group">
                <label>Display Resolution</label>
                <select name="display_resolution">
                    <option value="">Select resolution</option>
                    <option value="1920 x 1080" <?= ($tablet['display_resolution'] ?? '') === '1920 x 1080' ? 'selected' : '' ?>>1920 x 1080 (Full HD)</option>
                    <option value="2560 x 1440" <?= ($tablet['display_resolution'] ?? '') === '2560 x 1440' ? 'selected' : '' ?>>2560 x 1440 (QHD)</option>
                    <option value="2560 x 1600" <?= ($tablet['display_resolution'] ?? '') === '2560 x 1600' ? 'selected' : '' ?>>2560 x 1600</option>
                    <option value="3840 x 2160" <?= ($tablet['display_resolution'] ?? '') === '3840 x 2160' ? 'selected' : '' ?>>3840 x 2160 (4K UHD)</option>
                    <option value="custom">Custom</option>
                </select>
                <input type="text" name="display_resolution_custom" id="res-custom" placeholder="e.g. 1920 x 1080" style="display:none;margin-top:0.5rem;" value="<?= e($tablet['display_resolution'] ?? '') ?>" />
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>Price ($)</label>
                <input type="number" name="price" step="0.01" value="<?= e($tablet['price'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label>Release Date</label>
                <input type="date" name="release_date" value="<?= e($tablet['release_date'] ?? '') ?>">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>Pressure Levels</label>
                <select name="pressure_levels">
                    <option value="">Select</option>
                    <option value="1024" <?= ($tablet['pressure_levels'] ?? '') === '1024' ? 'selected' : '' ?>>1024</option>
                    <option value="2048" <?= ($tablet['pressure_levels'] ?? '') === '2048' ? 'selected' : '' ?>>2048</option>
                    <option value="4096" <?= ($tablet['pressure_levels'] ?? '') === '4096' ? 'selected' : '' ?>>4096</option>
                    <option value="8192" <?= ($tablet['pressure_levels'] ?? '') === '8192' ? 'selected' : '' ?>>8192</option>
                    <option value="16384" <?= ($tablet['pressure_levels'] ?? '') === '16384' ? 'selected' : '' ?>>16384</option>
                    <option value="custom">Custom</option>
                </select>
                <input type="text" name="pressure_levels_custom" id="pressure-custom" placeholder="e.g. 8192" style="display:none;margin-top:0.5rem;" value="<?= e($tablet['pressure_levels'] ?? '') ?>" />
            </div>
            <div class="form-group">
                <label>Connection Type</label>
                <div class="checkbox-group">
                    <?php
                    $stored = $tablet['connection_type'] ?? '';
                    $storedLabels = array_map('trim', explode(',', $stored));
                    $connOptions = [
                        'usb_c' => 'USB-C',
                        'usb_a' => 'USB-A',
                        'hdmi' => 'HDMI',
                        'displayport' => 'DisplayPort',
                        'bluetooth' => 'Bluetooth',
                        'wireless' => 'Wireless',
                    ];
                    foreach ($connOptions as $key => $label):
                    ?>
                        <label class="checkbox">
                            <input type="checkbox" name="conn_<?= $key ?>" value="1" <?= in_array($label, $storedLabels) ? 'checked' : '' ?> />
                            <span><?= $label ?></span>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <div class="form-group">
            <label>Image</label>
            <?php $hasImage = $tablet && $tablet['image_path']; ?>
            <div class="position-picker <?= $hasImage ? '' : 'is-empty' ?>">
                <?php if ($hasImage): ?>
                    <img src="<?= e(uploadUrl($tablet['image_path'])) ?>" alt="">
                <?php else: ?>
                    <img src="" alt="" hidden>
                    <span class="position-picker__empty">Choose an image to preview it here</span>
                <?php endif; ?>
                <div class="pos-dot"></div>
                <div class="position-picker__hint">Click to set focal point</div>
                <input type="hidden" name="image_pos_x" value="<?= (int) ($tablet['image_pos_x'] ?? 50) ?>">
                <input type="hidden" name="image_pos_y" value="<?= (int) ($tablet['image_pos_y'] ?? 50) ?>">
            </div>
            <?php if ($hasImage): ?>
                <div>
                    <label style="display:inline-flex;align-items:center;gap:0.4rem;margin-top:0.4rem;">
                        <input type="checkbox" name="remove_image" value="1"> Remove image
                    </label>
                </div>
            <?php endif; ?>
            <input type="file" name="image" accept="image/jpeg,image/png,image/gif,image/webp" data-image-preview>
            <p class="muted" style="font-size:0.85rem;margin-top:0.35rem;">Choose an image, then click the preview to center the card crop before saving.</p>
        </div>
        <div class="form-group">
            <label>Notes</label>
            <textarea name="notes" rows="3"><?= e($tablet['notes'] ?? '') ?></textarea>
        </div>
        <button type="submit" class="button">Save</button>
        <a href="index.php" class="button secondary">Cancel</a>
    </form>
</div>
<script>
const picker = document.querySelector('.position-picker');
const imageInput = document.querySelector('[data-image-preview]');

function setFocalPoint(x, y) {
    if (!picker) return;
    const clampedX = Math.max(0, Math.min(100, x));
    const clampedY = Math.max(0, Math.min(100, y));
    const img = picker.querySelector('img');

    picker.querySelector('input[name="image_pos_x"]').value = clampedX;
    picker.querySelector('input[name="image_pos_y"]').value = clampedY;
    picker.querySelector('.pos-dot').style.left = clampedX + '%';
    picker.querySelector('.pos-dot').style.top = clampedY + '%';
    if (img) img.style.objectPosition = clampedX + '% ' + clampedY + '%';
}

function toggleCustomSize() {
    const sel = document.getElementById('size-select');
    const custom = document.getElementById('size-custom');
    if (sel.value === 'custom') {
        custom.style.display = 'block';
        custom.name = 'size';
        sel.name = '';
    } else {
        custom.style.display = 'none';
        custom.name = 'size_disabled';
        sel.name = 'size';
    }
}

function toggleDisplayFields() {
    const checked = document.querySelector('[name="has_display"]').checked;
    document.getElementById('display-fields').style.display = checked ? '' : 'none';
    document.querySelector('.toggle-label').textContent = checked ? 'Yes' : 'No';
}

document.querySelector('[name="display_resolution"]')?.addEventListener('change', function() {
    const custom = document.getElementById('res-custom');
    if (this.value === 'custom') {
        custom.style.display = 'block';
        custom.name = 'display_resolution';
        this.name = '';
    } else {
        custom.style.display = 'none';
        custom.name = 'display_resolution_disabled';
        this.name = 'display_resolution';
    }
});

document.querySelector('[name="pressure_levels"]')?.addEventListener('change', function() {
    const custom = document.getElementById('pressure-custom');
    if (this.value === 'custom') {
        custom.style.display = 'block';
        custom.name = 'pressure_levels';
        this.name = '';
    } else {
        custom.style.display = 'none';
        custom.name = 'pressure_levels_disabled';
        this.name = 'pressure_levels';
    }
});

toggleCustomSize();

if (picker) {
    setFocalPoint(
        parseInt(picker.querySelector('input[name="image_pos_x"]').value, 10) || 50,
        parseInt(picker.querySelector('input[name="image_pos_y"]').value, 10) || 50
    );

    picker.addEventListener('click', function(e) {
        if (picker.classList.contains('is-empty')) return;
        const rect = picker.getBoundingClientRect();
        setFocalPoint(
            Math.round(((e.clientX - rect.left) / rect.width) * 100),
            Math.round(((e.clientY - rect.top) / rect.height) * 100)
        );
    });
}

imageInput?.addEventListener('change', function() {
    const file = this.files && this.files[0];
    if (!file || !picker) return;

    const img = picker.querySelector('img');
    const empty = picker.querySelector('.position-picker__empty');
    img.src = URL.createObjectURL(file);
    img.hidden = false;
    if (empty) empty.hidden = true;
    picker.classList.remove('is-empty');
    setFocalPoint(50, 50);
});
</script>
<style>
.admin-edit-form {
    max-width: 720px;
}
.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
}
.form-group {
    display: grid;
    gap: 0.45rem;
    margin-bottom: 1rem;
}
.checkbox-group {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 0.5rem;
    padding: 0.5rem 0;
}
.checkbox-group .checkbox {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 0.75rem;
    border-radius: 10px;
    border: 1px solid var(--border);
    background: var(--surface-2);
    cursor: pointer;
}
.checkbox-group .checkbox input {
    width: 16px;
    height: 16px;
    accent-color: var(--accent);
    margin: 0;
}
.toggle {
    position: relative;
    display: flex;
    align-items: center;
    gap: 0.75rem;
    cursor: pointer;
    padding: 0.35rem 0;
}
.toggle input {
    position: absolute;
    opacity: 0;
    width: 0;
    height: 0;
}
.toggle-slider {
    position: relative;
    width: 44px;
    height: 24px;
    background: var(--surface-2);
    border-radius: 12px;
    border: 1px solid var(--border);
    transition: 0.2s;
    flex-shrink: 0;
}
.toggle-slider::after {
    content: '';
    position: absolute;
    width: 18px;
    height: 18px;
    border-radius: 50%;
    background: var(--text);
    top: 2px;
    left: 2px;
    transition: 0.2s;
}
.toggle input:checked + .toggle-slider {
    background: var(--accent);
    border-color: var(--accent);
}
.toggle input:checked + .toggle-slider::after {
    left: 22px;
    background: #fff;
}
.toggle-label {
    font-size: 0.9rem;
    color: var(--text-strong);
}
.position-picker {
    position: relative;
    max-height: 360px;
    overflow: hidden;
    border-radius: 14px;
    border: 1px solid var(--border);
    background: var(--surface-2);
    cursor: crosshair;
    display: flex;
    align-items: center;
    justify-content: center;
    min-height: 160px;
}
.position-picker img {
    width: 100%;
    height: 100%;
    max-height: 360px;
    object-fit: cover;
    display: block;
}
.pos-dot {
    position: absolute;
    width: 24px;
    height: 24px;
    border-radius: 50%;
    background: rgba(74, 154, 142, 0.85);
    border: 2px solid #fff;
    box-shadow: 0 0 0 2px rgba(0,0,0,0.3);
    transform: translate(-50%, -50%);
    pointer-events: none;
    transition: left 0.1s, top 0.1s;
    z-index: 2;
}
.position-picker__empty {
    color: var(--text);
    font-size: 0.9rem;
    opacity: 0.6;
}
.position-picker__hint {
    position: absolute;
    bottom: 0.5rem;
    left: 50%;
    transform: translateX(-50%);
    background: rgba(0,0,0,0.6);
    color: #fff;
    font-size: 0.75rem;
    padding: 0.25rem 0.75rem;
    border-radius: 999px;
    pointer-events: none;
    z-index: 2;
    opacity: 0;
    transition: opacity 0.2s;
}
.position-picker:hover .position-picker__hint {
    opacity: 1;
}
.position-picker.is-empty .pos-dot,
.position-picker.is-empty .position-picker__hint {
    display: none;
}
</style>
<?php renderAdminFooter(); ?>