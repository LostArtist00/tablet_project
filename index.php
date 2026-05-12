<?php

declare(strict_types=1);

require_once __DIR__ . '/app/config/init.php';
require_once APP_PATH . '/includes/header.php';
require_once APP_PATH . '/includes/footer.php';

$tabletModel = new Tablet(db());
$brandModel = new Brand(db());
$reportModel = new FailureReport(db());

$tablets = $tabletModel->all();
$brands = $brandModel->all();
$recentReports = $reportModel->recent(5);
$totalTablets = $tabletModel->count();
$totalBrands = $brandModel->count();
$totalReports = (int) ($reportModel->summary()['total'] ?? 0);

renderHeader('Home', '');
?>
<section class="hero">
    <div class="hero-content">
        <h1>Help us improve the statistics</h1>
        <p class="hero-subtitle">Fill out this simple survey about your experience with a tablet.</p>
        <a href="<?= e(url('survey.php')) ?>" class="cta-button" style="font-size: 1.1rem; padding: 0.75rem 2rem;">Survey</a>
    </div>
</section>

<section class="features" id="features">
    <div class="features-container">

        <section class="tm-gallery" id="tabletGallery">
            <div class="search-bar">
                <h1>Tablet database</h1>
                <input type="search" id="tmGallerySearch" placeholder="Search tablets…" autocomplete="off">
            </div>
            <div class="filter-bar">
                <h3 class="filter-title">Filter by Brand</h3>
                <div class="brand-filters filters" id="tmBrandFilters">
                    <?php foreach ($brands as $brand): ?>
                        <label class="filter-btn">
                            <input type="checkbox" name="brand" value="<?= (int) $brand['id'] ?>" checked> <?= e($brand['name']) ?>
                        </label>
                    <?php endforeach; ?>
                </div>

                <h3 class="filter-title">Filter by Type</h3>
                <div class="type-filters filters">
                    <label class="filter-btn">
                        <input type="radio" name="tmType" value="" checked> All
                    </label>
                    <label class="filter-btn">
                        <input type="radio" name="tmType" value="display"> Tablet Display
                    </label>
                    <label class="filter-btn">
                        <input type="radio" name="tmType" value="graphics"> Graphics Tablet
                    </label>
                </div>
            </div>

            <div class="tm-gallery-page" id="tmGalleryItems">
                <?php foreach ($tablets as $t): ?>
                    <article class="tm-gallery-item" data-brand="<?= (int) $t['brand_id'] ?>" data-type="<?= $t['has_display'] ? 'display' : 'graphics' ?>" data-name="<?= e(strtolower($t['brand_name'] . ' ' . $t['name'])) ?>">
                        <figure>
                            <div class="tm-gallery-img" style="background:rgba(15,23,42,0.5);padding:3rem 1rem;text-align:center;color:var(--text-secondary);"><?= e($t['brand_name']) ?> / <?= e($t['name']) ?></div>
                        </figure>
                        <h4 class="tm-gallery-title"><?= e($t['brand_name']) ?> <?= e($t['name']) ?></h4>
                        <p class="tm-gallery-description"><?= e($t['notes'] ?: '') ?></p>
                        <span class="tm-gallery-price"><?= $t['price'] ? '$' . e((string) $t['price']) : '' ?></span>
                    </article>
                <?php endforeach; ?>
            </div>
        </section>

        <div class="features-layout">
            <div class="features-two-col">
                <div class="feature-card-large">
                    <div class="feature-icon">📊</div>
                    <h3>Community-Powered Reports</h3>
                    <p>Every report helps build a clearer picture of tablet reliability. See failure trends, average lifespan, and common issues across brands and models.</p>
                    <ul class="feature-checklist">
                        <li>Real-world failure statistics</li>
                        <li>Per-model reliability data</li>
                        <li>Issue frequency tracking</li>
                    </ul>
                </div>
                <div class="feature-card-large">
                    <div class="feature-icon">🔍</div>
                    <h3>Detailed Model Database</h3>
                    <p>Browse a comprehensive catalog of graphics and display tablets. Compare specs, check user feedback, and make informed decisions.</p>
                    <ul class="feature-checklist">
                        <li>Full spec comparisons</li>
                        <li>User-submitted feedback</li>
                        <li>Report history per device</li>
                    </ul>
                </div>
            </div>

            <div class="features-four-col">
                <div class="feature-card-small">
                    <div class="feature-icon-small">🚀</div>
                    <h4>Quick Survey</h4>
                    <p>Submit a report in under a minute with our step-by-step form.</p>
                </div>
                <div class="feature-card-small">
                    <div class="feature-icon-small">📈</div>
                    <h4>Live Statistics</h4>
                    <p>Track failure rates and trends across the community.</p>
                </div>
                <div class="feature-card-small">
                    <div class="feature-icon-small">🔄</div>
                    <h4>Multi-Brand</h4>
                    <p>Wacom, Huion, XP-Pen and more — all in one place.</p>
                </div>
                <div class="feature-card-small">
                    <div class="feature-icon-small">🌐</div>
                    <h4>Open Data</h4>
                    <p>All reports are publicly accessible for research.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="pricing" id="pricing">
    <div class="pricing-header">
        <h2>Tablet Survey in Numbers</h2>
        <p class="hero-subtitle">Real data from the community to help you understand tablet reliability.</p>
    </div>

    <div class="pricing-cards">
        <div class="pricing-card">
            <h3 class="plan-name"><?= $totalBrands ?></h3>
            <div class="plan-price" style="font-size:1rem;font-weight:normal;"><span>Brands Tracked</span></div>
            <ul class="plan-features">
                <li>Major manufacturers</li>
                <li>Niche brands included</li>
                <li>Regularly updated</li>
            </ul>
            <a href="<?= e(url('tablets.php')) ?>" class="cta-button">Browse Brands</a>
        </div>

        <div class="pricing-card popular">
            <h3 class="plan-name"><?= $totalTablets ?></h3>
            <div class="plan-price" style="font-size:1rem;font-weight:normal;"><span>Tablet Models</span></div>
            <ul class="plan-features">
                <li>Graphics &amp; display tablets</li>
                <li>Full spec details</li>
                <li>User report history</li>
            </ul>
            <a href="<?= e(url('tablets.php')) ?>" class="cta-button">View Models</a>
        </div>

        <div class="pricing-card">
            <h3 class="plan-name"><?= $totalReports ?></h3>
            <div class="plan-price" style="font-size:1rem;font-weight:normal;"><span>Community Reports</span></div>
            <ul class="plan-features">
                <li>Failure &amp; success stories</li>
                <li>Issue breakdowns</li>
                <li>Always growing</li>
            </ul>
            <a href="<?= e(url('survey.php')) ?>" class="cta-button">Add Yours</a>
        </div>
    </div>
</section>

<section class="about" id="about">
    <div class="about-container">
        <div class="about-content">
            <h2>Trusted by Tablet Enthusiasts Worldwide</h2>
            <p class="about-subtitle">Since 2024, we've been collecting and sharing real-world tablet reliability data to help the community make informed decisions.</p>

            <div class="stats-grid">
                <div class="stat-card">
                    <strong><?= $totalBrands ?></strong>
                    <span>Brands</span>
                </div>
                <div class="stat-card">
                    <strong><?= $totalTablets ?></strong>
                    <span>Models</span>
                </div>
                <div class="stat-card">
                    <strong><?= $totalReports ?></strong>
                    <span>Reports</span>
                </div>
                <div class="stat-card">
                    <strong>24/7</strong>
                    <span>Open Access</span>
                </div>
            </div>

            <div class="about-text">
                <p>Tablet Survey was born from a simple idea: tablet buyers deserve better data. Instead of relying on manufacturer specs and paid reviews, we collect real ownership experiences from actual users.</p>
                <p>Our database tracks failure patterns, common issues, and longevity across brands and models. Whether you're an artist, designer, or hobbyist, you can contribute and benefit from community-powered reliability insights.</p>
            </div>
        </div>
    </div>
</section>

<section class="contact" id="contact">
    <div class="contact-container">
        <div class="contact-header">
            <h2>Get Involved</h2>
            <p class="contact-subtitle">Have a tablet to report? Want to browse the data? Jump in and contribute.</p>
        </div>

        <div class="contact-content">
            <div class="contact-form-wrapper">
                <div class="form-header">
                    <h3>Submit a Report</h3>
                    <p>Tell us about your tablet experience — it only takes a minute.</p>
                </div>
                <div style="display:flex;flex-direction:column;gap:1rem;">
                    <a href="<?= e(url('survey.php')) ?>" class="cta-button" style="text-align:center;padding:1rem;font-size:1.1rem;">Take the Survey</a>
                    <a href="<?= e(url('report.php')) ?>" class="button secondary" style="text-align:center;padding:1rem;font-size:1.1rem;">Quick Report</a>
                </div>
            </div>

            <div class="contact-info">
                <div class="quick-contact">
                    <h3>Latest Reports</h3>
                    <div class="contact-methods">
                        <?php foreach ($recentReports as $r): ?>
                            <a href="<?= e(url('tablet.php')) ?>?id=<?= (int) $r['tablet_id'] ?>" class="contact-method">
                                <div class="method-details">
                                    <strong><?= e($r['brand_name']) ?> <?= e($r['tablet_name']) ?></strong>
                                    <span><?= e($r['status']) ?> &middot; <?= e(date('M j, Y', strtotime($r['created_at'] ?? 'now'))) ?></span>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php renderFooter(); ?>
