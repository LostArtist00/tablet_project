<?php

declare(strict_types=1);

use App\Models\Tablet;

require_once __DIR__ . '/app/config/init.php';
require_once APP_PATH . '/includes/header.php';
require_once APP_PATH . '/includes/footer.php';

use App\Models\Brand;
use App\Models\FailureReport;

$tabletModel = new Tablet(db());
$brandModel = new Brand(db());
$reportModel = new FailureReport(db());

$featured = $tabletModel->featured();
$allTablets = $tabletModel->all();
$brands = $brandModel->all();
$reportSummary = $reportModel->summary();
$reportCount = (int) ($reportSummary['total'] ?? 0);
$faultCount = count($reportModel->topIssues());
$featuredStats = [];

$brandPicks = [];
foreach ($allTablets as $t) {
    $bid = (int) $t['brand_id'];
    if (!isset($brandPicks[$bid])) {
        $brandPicks[$bid] = $t;
        if (count($brandPicks) === 5) break;
    }
}

$heroTablets = array_values(array_filter($allTablets, fn($t) => $t['image_path']));

foreach ($featured as $tablet) {
    $featuredStats[(int) $tablet['id']] = $tabletModel->stats((int) $tablet['id']);
}

renderHeader('Home', '');
?>
<section class="hero">
    <div class="container hero-grid">
        <div class="reveal">
            <p class="eyebrow">Reliability Database</p>
            <h1>See how tablets hold up after years of use.</h1>
            <p>Browse models, compare working vs broken ratios, and check how long they actually last.</p>
            <div class="actions">
                <a class="button" href="<?= e(url('tablets.php')) ?>">Browse Tablets</a>
                <a class="button secondary" href="<?= e(url('report.php')) ?>">Submit Report</a>
            </div>
            <div class="stats-grid">
                <article class="stat-card">
                    <strong><?= count($allTablets) ?></strong>
                    <span>models tracked</span>
                </article>
                <article class="stat-card">
                    <strong><?= $faultCount ?></strong>
                    <span>fault categories</span>
                </article>
                <article class="stat-card">
                    <strong><?= count($featured) ?></strong>
                    <span>featured models</span>
                </article>
                <article class="stat-card">
                    <strong><?= $reportCount ?? 0 ?></strong>
                    <span>reports submitted</span>
                </article>
            </div>
        </div>
            <div class="hero-card hero-card--home reveal">
            <div class="surface-image hero-images" data-lightbox-trigger="Community data on pen issues, dead zones, driver problems, and how long tablets actually last.">
                <?php if ($heroTablets): ?>
                    <?php foreach (array_slice($heroTablets, 0, 4) as $t): ?>
                        <img src="<?= e(uploadUrl($t['image_path'])) ?>" alt="<?= e($t['brand_name']) ?> <?= e($t['name']) ?>" style="object-position:<?= (int) ($t['image_pos_x'] ?? 50) ?>% <?= (int) ($t['image_pos_y'] ?? 50) ?>%;">
                    <?php endforeach; ?>
                <?php else: ?>
                    <img src="<?= e(asset('images/hero-tablet.jpg')) ?>" alt="Tablet illustration" style="width:100%;height:100%;object-fit:cover;border-radius:inherit;" onerror="this.style.display='none';this.nextElementSibling.style.display='grid'" />
                    <span style="display:none;width:100%;height:100%;place-items:center;">Select a model above to view its profile</span>
                <?php endif; ?>
            </div>
            <p class="muted" style="margin:0;text-align:center;font-size:0.85rem;">Tablet examples</p>
            </div>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="flex-between">
            <div>
                <p class="eyebrow">Featured Tablets</p>
                <h2>Browse the most-reported models.</h2>
            </div>
            <a class="button secondary" href="<?= e(url('tablets.php')) ?>">All Models</a>
        </div>
        <div class="card-grid">
            <?php foreach ($featured as $tablet): ?>
                <article class="card reveal">
                    <a class="surface-image" href="<?= e(url('tablet.php')) ?>?id=<?= (int) $tablet['id'] ?>" style="<?= $tablet['image_path'] ? 'position:relative;background:var(--surface);padding:0;overflow:hidden;' : '' ?>">
                        <?php if ($tablet['image_path']): ?>
                            <img src="<?= e(uploadUrl($tablet['image_path'])) ?>" alt="<?= e($tablet['brand_name']) ?> <?= e($tablet['name']) ?>" style="position:absolute;inset:0;width:100%;height:100%;object-fit:cover;object-position:<?= (int) ($tablet['image_pos_x'] ?? 50) ?>% <?= (int) ($tablet['image_pos_y'] ?? 50) ?>%;">
                        <?php else: ?>
                            <?= e($tablet['brand_name']) ?> / <?= e($tablet['name']) ?>
                        <?php endif; ?>
                    </a>
                    <div class="meta">
                        <span class="pill"><?= e($tablet['has_display'] ? 'Display' : 'Pen tablet') ?></span>
                        <span class="pill"><?= e($tablet['pressure_levels'] ?: 'Unknown pressure') ?></span>
                        <span class="pill"><?= (int) ($featuredStats[(int) $tablet['id']]['total_reports'] ?? 0) ?> reports</span>
                    </div>
                    <h3><?= e($tablet['brand_name']) ?> <?= e($tablet['name']) ?></h3>
                    <p><?= e($tablet['notes'] ?: 'No notes yet. Add a report to contribute.') ?></p>
                    <p class="muted">
                        Working avg: <?= e(number_format((float) ($featuredStats[(int) $tablet['id']]['avg_years_working'] ?? 0), 1)) ?> years
                        · Fault avg: <?= e(number_format((float) ($featuredStats[(int) $tablet['id']]['avg_years_broken'] ?? 0), 1)) ?> years
                    </p>
                    <a class="button secondary" href="<?= e(url('tablet.php')) ?>?id=<?= (int) $tablet['id'] ?>">View details</a>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <div>
            <p class="eyebrow">Community Stats</p>
            <h2>Reliability Overview</h2>
        </div>
        <div class="stats-grid">
            <article class="stat-card">
                <strong><?= $reportCount ?></strong>
                <span>total reports</span>
            </article>
            <article class="stat-card">
                <strong><?= (int) ($reportSummary['working'] ?? 0) ?></strong>
                <span>still working</span>
            </article>
            <article class="stat-card">
                <strong><?= (int) ($reportSummary['broken'] ?? 0) ?></strong>
                <span>broken units</span>
            </article>
            <article class="stat-card">
                <strong><?= (int) ($reportSummary['partially_working'] ?? 0) ?></strong>
                <span>partially working</span>
            </article>
        </div>
    </div>
</section>

<section class="section">
    <div class="container grid-2">
            <article class="panel reveal">
                <p class="eyebrow">Why This Exists</p>
                <h2>Real data from real owners.</h2>
                <p>Spec sheets tell you what a tablet should do. This project tracks what actually breaks: dead zones, driver crashes, worn cables, and all the common failure points.</p>
            </article>
            <article class="panel reveal">
                <p class="eyebrow">Contribute</p>
                <h2>Takes about a minute.</h2>
                <p>Tell us if your tablet still works, how long it lasted, and what went wrong. Quick comments and structured reports stay separate so the data stays clean.</p>
            </article>
    </div>
</section>


</div>
<?php renderFooter(); ?>
