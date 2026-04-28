<?php

declare(strict_types=1);

require_once __DIR__ . '/app/config/init.php';
require_once APP_PATH . '/includes/header.php';

$tabletModel = new Tablet(db());
$brandModel = new Brand(db());

$search = $_GET['search'] ?? '';
$brandId = isset($_GET['brand']) ? (int) $_GET['brand'] : null;

if ($search || $brandId) {
    $tablets = $tabletModel->search($search, $brandId);
} else {
    $tablets = $tabletModel->all();
}

$brands = $brandModel->all();

renderHeader('Browse Tablets', '');
?>
<section class="section">
    <div class="container">
        <h1>Browse Tablets</h1>
        
        <form method="get" class="search-form">
            <div class="form-row">
                <div class="form-group">
                    <input type="text" name="search" placeholder="Search tablets..." value="<?= e($search) ?>">
                </div>
                <div class="form-group">
                    <select name="brand">
                        <option value="">All Brands</option>
                        <?php foreach ($brands as $brand): ?>
                            <option value="<?= (int) $brand['id'] ?>" <?= $brandId === (int) $brand['id'] ? 'selected' : '' ?>>
                                <?= e($brand['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit" class="button">Search</button>
            </div>
        </form>

        <div class="card-grid">
            <?php if (empty($tablets)): ?>
                <p>No tablets found.</p>
            <?php else: ?>
                <?php foreach ($tablets as $tablet): ?>
                    <article class="card">
                        <h3><?= e($tablet['brand_name']) ?> <?= e($tablet['name']) ?></h3>
                        <div class="meta">
                            <span class="pill"><?= e($tablet['has_display'] ? 'Display' : 'Pen tablet') ?></span>
                            <span class="pill"><?= e($tablet['size']) ?></span>
                        </div>
                        <p><?= e($tablet['notes'] ?: 'No notes yet.') ?></p>
                        <a class="button secondary" href="<?= e(url('tablet.php')) ?>?id=<?= (int) $tablet['id'] ?>">View</a>
                    </article>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</section>
<?php renderFooter(); ?>