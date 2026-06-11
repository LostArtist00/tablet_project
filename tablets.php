<?php

declare(strict_types=1);

use App\Models\Tablet;
use App\Models\Brand;

require_once __DIR__ . '/app/config/init.php';
require_once APP_PATH . '/includes/header.php';
require_once APP_PATH . '/includes/footer.php';

$tabletModel = new Tablet(db());
$brandModel = new Brand(db());

$tablets = $tabletModel->allWithStats();
$brands = $brandModel->all();

renderHeader('Browse Tablets', 'tablets.php');
?>
<section class="section">
    <div class="container">
        <div class="flex-between">
            <div>
                <p class="eyebrow">Browse</p>
                <h1>All Models</h1>
            </div>
        </div>

        <div class="tablet-filters">
            <div class="tablet-filter-group">
                <span class="eyebrow tablet-filter-label">Brand</span>
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
                <span class="eyebrow tablet-filter-label">Type</span>
                <div class="meta tablet-type-options">
                    <label class="pill">
                        <input type="radio" data-filter="type" value="" name="galleryType" checked hidden> All
                    </label>
                    <label class="pill">
                        <input type="radio" data-filter="type" value="display" name="galleryType" hidden> Display
                    </label>
                    <label class="pill">
                        <input type="radio" data-filter="type" value="graphics" name="galleryType" hidden> No Display
                    </label>
                </div>
            </div>
            <input type="search" id="gallerySearch" class="gallery-search" placeholder="Search..." autocomplete="off">
        </div>

        <div class="card-grid" id="galleryGrid">
            <?php foreach ($tablets as $t): ?>
                <article class="card tablet-card visible"
                    data-brand="<?= (int) $t['brand_id'] ?>"
                    data-type="<?= $t['has_display'] ? 'display' : 'graphics' ?>"
                    data-name="<?= e(strtolower($t['brand_name'] . ' ' . $t['name'])) ?>">
                    <a class="surface-image tablet-card__image" href="<?= e(url('tablet.php')) ?>?id=<?= (int) $t['id'] ?>" style="<?= $t['image_path'] ? 'position:relative;background:var(--surface);padding:0;overflow:hidden;' : '' ?>">
                        <?php if ($t['image_path']): ?>
                            <img src="<?= e(uploadUrl($t['image_path'])) ?>" alt="<?= e($t['brand_name']) ?> <?= e($t['name']) ?>" style="position:absolute;inset:0;width:100%;height:100%;object-fit:cover;object-position:<?= (int) ($t['image_pos_x'] ?? 50) ?>% <?= (int) ($t['image_pos_y'] ?? 50) ?>%;background:#fff;">
                        <?php else: ?>
                            <?= e($t['brand_name']) ?> / <?= e($t['name']) ?>
                        <?php endif; ?>
                    </a>
                    <h3><?= e($t['brand_name']) ?> <?= e($t['name']) ?></h3>
                    <div class="meta tablet-card__meta">
                        <span class="pill"><?= $t['has_display'] ? 'Display' : 'Pen tablet' ?></span>
                        <span class="pill"><?= e($t['size'] ?: 'N/A') ?></span>
                        <?php if ($t['connection_type']): ?>
                            <span class="pill"><?= e($t['connection_type']) ?></span>
                        <?php endif; ?>
                    </div>
                    <p class="muted"><?= e($t['notes'] ?: '') ?></p>
                    <p class="muted tablet-card__stats">
                        <?= (int) $t['total_reports'] ?> report<?= (int) $t['total_reports'] !== 1 ? 's' : '' ?>
                        <?php if ((float) $t['avg_years_working'] > 0): ?>
                            · Working avg: <?= e($t['avg_years_working']) ?> yrs
                        <?php endif; ?>
                        <?php if ((float) $t['avg_years_fault'] > 0): ?>
                            · Fault avg: <?= e($t['avg_years_fault']) ?> yrs
                        <?php endif; ?>
                    </p>
                    <a class="button secondary" href="<?= e(url('tablet.php')) ?>?id=<?= (int) $t['id'] ?>">Details</a>
                </article>
            <?php endforeach; ?>
            <div class="empty-state" style="display:none;">
                <p>No tablets match your filters.</p>
            </div>
        </div>
    </div>
</section>
<?php renderFooter(); ?>
