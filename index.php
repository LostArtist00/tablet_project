<?php

declare(strict_types=1);

require_once __DIR__ . '/app/config/init.php';
require_once APP_PATH . '/includes/header.php';

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
    <div class="container">
        <h1>Tablet Survey</h1>
        <p>Track and compare graphics tablet reliability data from real users.</p>
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
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <h2>Featured Tablets</h2>
        <div class="card-grid">
            <?php foreach ($featured as $tablet): ?>
                <article class="card">
                    <h3><?= e($tablet['brand_name']) ?> <?= e($tablet['name']) ?></h3>
                    <div class="meta">
                        <span class="pill"><?= e($tablet['has_display'] ? 'Display' : 'Pen tablet') ?></span>
                        <span class="pill"><?= e($tablet['pressure_levels'] ?: 'Unknown pressure') ?></span>
                        <span class="pill"><?= (int) ($featuredStats[(int) $tablet['id']]['total_reports'] ?? 0) ?> reports</span>
                    </div>
                    <p><?= e($tablet['notes'] ?: 'No notes yet.') ?></p>
                    <a class="button secondary" href="<?= e(url('tablet.php')) ?>?id=<?= (int) $tablet['id'] ?>">View details</a>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php renderFooter(); ?>