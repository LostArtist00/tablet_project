<?php

declare(strict_types=1);

require_once __DIR__ . '/app/config/init.php';
require_once APP_PATH . '/includes/header.php';
require_once APP_PATH . '/includes/footer.php';

$tabletModel = new Tablet(db());
$brandModel = new Brand(db());

$tablets = $tabletModel->all();
$brands = $brandModel->all();

renderHeader('Browse Tablets', 'tablets.php');
?>
<section class="section">
    <div class="container">
        <div class="flex-between">
            <div>
                <p class="eyebrow">Database</p>
                <h1>Tablet Models</h1>
            </div>
        </div>

        <div class="tablet-filters">
            <div class="tablet-filter-group">
                <span class="eyebrow tablet-filter-label">Filter by Brand</span>
                <div class="checkbox-grid" id="brandFilters">
                    <?php foreach ($brands as $brand): ?>
                        <label class="checkbox">
                            <input type="checkbox" data-filter="brand" value="<?= (int) $brand['id'] ?>" checked>
                            <?= e($brand['name']) ?>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="tablet-filter-group">
                <span class="eyebrow tablet-filter-label">Filter by Type</span>
                <div class="meta tablet-type-options">
                    <label class="pill">
                        <input type="radio" data-filter="type" value="" name="galleryType" checked hidden> All
                    </label>
                    <label class="pill">
                        <input type="radio" data-filter="type" value="display" name="galleryType" hidden> Display
                    </label>
                    <label class="pill">
                        <input type="radio" data-filter="type" value="graphics" name="galleryType" hidden> Graphics
                    </label>
                </div>
            </div>
            <input type="search" id="gallerySearch" class="gallery-search" placeholder="Search tablets..." autocomplete="off">
        </div>

        <div class="card-grid" id="galleryGrid">
            <?php foreach ($tablets as $t): ?>
                <article class="card tablet-card visible"
                    data-brand="<?= (int) $t['brand_id'] ?>"
                    data-type="<?= $t['has_display'] ? 'display' : 'graphics' ?>"
                    data-name="<?= e(strtolower($t['brand_name'] . ' ' . $t['name'])) ?>">
                    <div class="surface-image tablet-card__image" style="<?= $t['image_path'] ? 'background:var(--surface);padding:0;overflow:hidden;' : '' ?>">
                        <?php if ($t['image_path']): ?>
                            <img src="<?= e(uploadUrl($t['image_path'])) ?>" alt="<?= e($t['brand_name']) ?> <?= e($t['name']) ?>" style="width:100%;height:100%;object-fit:<?= e($t['image_fit'] ?? 'cover') ?>;display:block;">
                        <?php else: ?>
                            <?= e($t['brand_name']) ?> / <?= e($t['name']) ?>
                        <?php endif; ?>
                    </div>
                    <h3><?= e($t['brand_name']) ?> <?= e($t['name']) ?></h3>
                    <div class="meta tablet-card__meta">
                        <span class="pill"><?= $t['has_display'] ? 'Display' : 'Pen tablet' ?></span>
                        <span class="pill"><?= e($t['size'] ?: 'N/A') ?></span>
                    </div>
                    <p class="muted"><?= e($t['notes'] ?: '') ?></p>
                    <a class="button secondary" href="<?= e(url('tablet.php')) ?>?id=<?= (int) $t['id'] ?>">View details</a>
                </article>
            <?php endforeach; ?>
            <div class="empty-state" style="display:none;">
                <p>No tablets match your filters.</p>
            </div>
        </div>
    </div>
</section>
<?php renderFooter(); ?>
