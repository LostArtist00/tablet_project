<?php

declare(strict_types=1);

require_once __DIR__ . '/app/config/init.php';
require_once APP_PATH . '/includes/header.php';
require_once APP_PATH . '/includes/footer.php';

$tabletModel = new Tablet(db());
$featured = $tabletModel->featured();
$allTablets = $tabletModel->all();
$featuredStats = [];

foreach ($featured as $tablet) {
    $featuredStats[(int) $tablet['id']] = $tabletModel->stats((int) $tablet['id']);
}

renderHeader('Home', '');
?>
<section class="hero">
    <div class="container hero-grid">
        <div class="reveal">
            <p class="eyebrow">Reliability Database</p>
            <h1>Tablet Survey tracks what happens after you buy the tablet.</h1>
            <p>Browse tablet profiles, compare working versus faulty reports, and see how long models actually last. Data dont lie.</p>
            <div class="actions">
                <a class="button" href="<?= e(url('tablets.php')) ?>">Browse Tablets</a>
                <a class="button secondary" href="<?= e(url('report.php')) ?>">Submit Report</a>
            </div>
            <div class="stats-grid">
                <article class="stat-card">
                    <strong><?= count($allTablets) ?></strong>
                    <span>tracked models</span>
                </article>
                <article class="stat-card">
                    <strong>8</strong>
                    <span>fault categories</span>
                </article>
                <article class="stat-card">
                    <strong>Profiles</strong>
                    <span>click any tablet for details</span>
                </article>
                <article class="stat-card">
                    <strong>Reports</strong>
                    <span>working and fault data</span>
                </article>
            </div>
        </div>
        <div class="hero-card hero-card--home reveal">
            <div class="surface-image" data-lightbox-trigger="Tablet Survey compares pen issues, dead zones, driver pain, and long-term survival rates.">
                Click a tablet card to open its profile
            </div>
            <div class="meta">
                <?php foreach (array_slice($allTablets, 0, 4) as $t): ?>
                    <span class="pill"><?= e($t['brand_name']) ?></span>
                <?php endforeach; ?>
            </div>
            <p class="muted">Each tablet gets its own profile page with model info, report counts, comments, and separate lifespan averages for working units and faulty ones.</p>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="flex-between">
            <div>
                <p class="eyebrow">Featured Tablets</p>
                <h2>Start with the models people actually debate.</h2>
            </div>
            <a class="button secondary" href="<?= e(url('tablets.php')) ?>">See all models</a>
        </div>
        <div class="card-grid">
            <?php foreach ($featured as $tablet): ?>
                <article class="card reveal">
                    <div class="surface-image" style="<?= $tablet['image_path'] ? 'background:var(--surface);padding:0;overflow:hidden;' : '' ?>">
                        <?php if ($tablet['image_path']): ?>
                            <img src="<?= e(uploadUrl($tablet['image_path'])) ?>" alt="<?= e($tablet['brand_name']) ?> <?= e($tablet['name']) ?>" style="width:100%;height:100%;object-fit:cover;object-position:<?= (int) ($tablet['image_pos_x'] ?? 50) ?>% <?= (int) ($tablet['image_pos_y'] ?? 50) ?>%;display:block;">
                        <?php else: ?>
                            <?= e($tablet['brand_name']) ?> / <?= e($tablet['name']) ?>
                        <?php endif; ?>
                    </div>
                    <div class="meta">
                        <span class="pill"><?= e($tablet['has_display'] ? 'Display' : 'Pen tablet') ?></span>
                        <span class="pill"><?= e($tablet['pressure_levels'] ?: 'Unknown pressure') ?></span>
                        <span class="pill"><?= (int) ($featuredStats[(int) $tablet['id']]['total_reports'] ?? 0) ?> reports</span>
                    </div>
                    <h3><?= e($tablet['brand_name']) ?> <?= e($tablet['name']) ?></h3>
                    <p><?= e($tablet['notes'] ?: 'Community-sourced notes coming soon.') ?></p>
                    <p class="muted">
                        Working avg: <?= e((string) ($featuredStats[(int) $tablet['id']]['avg_years_working'] ?? 0)) ?> years
                        · Fault avg: <?= e((string) ($featuredStats[(int) $tablet['id']]['avg_years_broken'] ?? 0)) ?> years
                    </p>
                    <a class="button secondary" href="<?= e(url('tablet.php')) ?>?id=<?= (int) $tablet['id'] ?>">View details</a>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="section">
    <div class="container grid-2">
        <article class="panel reveal">
            <p class="eyebrow">Why Tablet Survey</p>
            <h2>Less hype, more ownership history.</h2>
            <p>Specs matter, but failure patterns matter more. Tablet Survey is built to surface recurring issues like dead zones, worn cables, driver instability, and panel defects in a format that stays easy to browse.</p>
        </article>
        <article class="panel reveal">
            <p class="eyebrow">Contribute</p>
            <h2>Share a report in under a minute.</h2>
            <p>Users can log whether a tablet is still working, how long it lasted, and what actually went wrong. Comments stay separate so quick impressions do not dilute structured reliability data.</p>
        </article>
    </div>
</section>

<div class="lightbox" data-lightbox>
    <div class="lightbox-panel card">
        <h3>Tablet Survey Snapshot</h3>
        <p data-lightbox-content></p>
    </div>
</div>
<?php renderFooter(); ?>
