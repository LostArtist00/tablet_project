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
    <div class="hero-content">
        <h1>Help us improve the statistics</h1>
        <p class="hero-subtitle">Fill out this simple survey about your experience with a tablet.</p>
        <a href="<?= e(url('survey.php')) ?>" class="cta-button" style="font-size: 1.1rem; padding: 0.75rem 2rem;">Survey</a>
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
                <article class="card">
                    <div class="surface-image"><?= e($tablet['brand_name']) ?> / <?= e($tablet['name']) ?></div>
                    <div class="meta">
                        <span class="pill"><?= e($tablet['has_display'] ? 'Display' : 'Pen tablet') ?></span>
                        <span class="pill"><?= e($tablet['pressure_levels'] ?: 'Unknown pressure') ?></span>
                        <span class="pill"><?= (int) ($featuredStats[(int) $tablet['id']]['total_reports'] ?? 0) ?> reports</span>
                    </div>
                    <h3><?= e($tablet['brand_name']) ?> <?= e($tablet['name']) ?></h3>
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
        <article class="panel">
            <p class="eyebrow">Why Tablet Survey</p>
            <h2>Less hype, more ownership history.</h2>
            <p>Specs matter, but failure patterns matter more. Tablet Survey is built to surface recurring issues like dead zones, worn cables, driver instability, and panel defects in a format that stays easy to browse.</p>
        </article>
        <article class="panel">
            <p class="eyebrow">Contribute</p>
            <h2>Share a report in under a minute.</h2>
            <a class="button secondary" href="<?= e(url('report.php')) ?>">Submit Report</a>
        </article>
    </div>
</section>
<?php renderFooter(); ?>