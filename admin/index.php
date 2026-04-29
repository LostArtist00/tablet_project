<?php

declare(strict_types=1);

require_once __DIR__ . '/app/config/init.php';
require_once APP_PATH . '/includes/admin.php';

$auth = new Auth(db());
$auth->requireAdmin();

$brandModel = new Brand(db());
$tabletModel = new Tablet(db());
$reportModel = new FailureReport(db());
$commentModel = new Comment(db());

$brands = $brandModel->all();
$tablets = $tabletModel->count();
$brandCount = $brandModel->count();
$reportSummary = $reportModel->summary();
$commentCount = $commentModel->count();

renderAdminHeader('Dashboard');
?>
<div class="container">
    <h1>Dashboard</h1>
    
    <div class="stats-grid">
        <div class="stat-card">
            <strong><?= $brandCount ?></strong>
            <span>Brands</span>
        </div>
        <div class="stat-card">
            <strong><?= $tablets ?></strong>
            <span>Tablets</span>
        </div>
        <div class="stat-card">
            <strong><?= (int) ($reportSummary['total'] ?? 0) ?></strong>
            <span>Reports</span>
        </div>
        <div class="stat-card">
            <strong><?= $commentCount ?></strong>
            <span>Comments</span>
        </div>
    </div>

    <h2>Recent Reports</h2>
    <table>
        <thead>
            <tr>
                <th>Tablet</th>
                <th>Status</th>
                <th>Years</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($reportModel->recent(10) as $report): ?>
                <tr>
                    <td><?= e($report['brand_name']) ?> <?= e($report['tablet_name']) ?></td>
                    <td><span class="badge <?= e($report['status']) ?>"><?= e($report['status']) ?></span></td>
                    <td><?= e($report['years_used']) ?></td>
                    <td><?= date('M j, Y', strtotime($report['created_at'])) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php renderAdminFooter(); ?>