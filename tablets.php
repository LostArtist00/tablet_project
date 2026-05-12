<?php

declare(strict_types=1);

require_once __DIR__ . '/app/config/init.php';
require_once APP_PATH . '/includes/header.php';
require_once APP_PATH . '/includes/footer.php';

$tabletModel = new Tablet(db());
$brandModel = new Brand(db());

$tablets = $tabletModel->all();
$brands = $brandModel->all();

renderHeader('Browse Tablets', '');
?>
<section class="section">
    <div class="container">
        <h1>Tablet Database</h1>
        <p>Browse tablets, compare specs, and check reliability reports.</p>

        <div class="gallery-filter-bar">
            <div class="gallery-filter-group">
                <span class="filter-label">Filter by Brand</span>
                <div class="filter-options" id="brandFilters">
                    <?php foreach ($brands as $brand): ?>
                        <label class="filter-checkbox">
                            <input type="checkbox" data-filter="brand" value="<?= (int) $brand['id'] ?>" checked>
                            <?= e($brand['name']) ?>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="gallery-filter-group">
                <span class="filter-label">Filter by Type</span>
                <div class="filter-options" id="typeFilters">
                    <label class="filter-radio">
                        <input type="radio" data-filter="type" value="" name="galleryType" checked>
                        All
                    </label>
                    <label class="filter-radio">
                        <input type="radio" data-filter="type" value="display" name="galleryType">
                        Display
                    </label>
                    <label class="filter-radio">
                        <input type="radio" data-filter="type" value="graphics" name="galleryType">
                        Graphics
                    </label>
                </div>
            </div>
            <div class="gallery-filter-group" style="flex:1;min-width:200px;">
                <span class="filter-label">Search</span>
                <input type="search" id="gallerySearch" class="gallery-search" placeholder="Search tablets..." autocomplete="off" style="margin-bottom:0;">
            </div>
        </div>

        <div class="gallery-grid" id="galleryGrid">
            <?php foreach ($tablets as $t): ?>
                <article class="gallery-item visible"
                    data-brand="<?= (int) $t['brand_id'] ?>"
                    data-type="<?= $t['has_display'] ? 'display' : 'graphics' ?>"
                    data-name="<?= e(strtolower($t['brand_name'] . ' ' . $t['name'])) ?>">
                    <div class="gallery-item-image"><?= e($t['brand_name']) ?> / <?= e($t['name']) ?></div>
                    <h3><?= e($t['brand_name']) ?> <?= e($t['name']) ?></h3>
                    <div class="gallery-meta">
                        <span class="pill"><?= $t['has_display'] ? 'Display' : 'Pen tablet' ?></span>
                        <span class="pill"><?= e($t['size'] ?: 'N/A') ?></span>
                    </div>
                    <div class="gallery-price"><?= $t['price'] ? '$' . e((string) $t['price']) : '' ?></div>
                    <p class="muted"><?= e($t['notes'] ?: '') ?></p>
                    <a class="gallery-link" href="<?= e(url('tablet.php')) ?>?id=<?= (int) $t['id'] ?>">View details</a>
                </article>
            <?php endforeach; ?>
            <div class="gallery-empty" style="display:none;">
                <p>No tablets match your filters.</p>
            </div>
        </div>
    </div>
</section>
<?php renderFooter(); ?>
