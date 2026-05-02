<?php

declare(strict_types=1);

require_once __DIR__ . '/../../app/config/init.php';
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
$topIssues = $reportModel->topIssues(6);
$recentActivity = $reportModel->recentActivity(12);
?>
<div class="container">
    <div class="dashboard-header">
        <div>
            <p class="eyebrow">Admin</p>
            <h1>Dashboard</h1>
        </div>
        <a href="../report.php" class="button">View Site</a>
    </div>

    <div class="admin-grid admin-grid--stats">
        <div class="stat-card">
            <p class="eyebrow">Total Brands</p>
            <div class="dashboard-number"><?= $brandCount ?></div>
            <a href="brands/" class="button secondary">Manage</a>
        </div>
        <div class="stat-card">
            <p class="eyebrow">Total Tablets</p>
            <div class="dashboard-number"><?= $tablets ?></div>
            <a href="tablets/" class="button secondary">Manage</a>
        </div>
        <div class="stat-card">
            <p class="eyebrow">Reports</p>
            <div class="dashboard-number"><?= (int) ($reportSummary['total'] ?? 0) ?></div>
            <a href="reports/" class="button secondary">View All</a>
        </div>
        <div class="stat-card">
            <p class="eyebrow">Comments</p>
            <div class="dashboard-number"><?= $commentCount ?></div>
            <a href="comments/" class="button secondary">Manage</a>
        </div>
    </div>

    <div class="admin-grid">
        <div class="admin-span-2">
            <div class="panel">
                <div class="dashboard-section__header">
                    <div>
                        <p class="eyebrow">Reports</p>
                        <h2>Recent Reports</h2>
                    </div>
                </div>
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
        </div>

        <div class="panel">
            <div class="dashboard-section__header">
                <div>
                    <p class="eyebrow">Issues</p>
                    <h2>Top Issues</h2>
                </div>
            </div>
            <ul class="issue-list">
                <?php foreach ($topIssues as $issue): ?>
                    <li>
                        <div class="issue-name">
                            <strong><?= e(ucfirst(str_replace('_', ' ', $issue['subcategory']))) ?></strong>
                            <span class="muted"><?= e($issue['category']) ?></span>
                        </div>
                        <span class="issue-count"><?= (int) $issue['cnt'] ?></span>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>

        <div class="panel">
            <div class="dashboard-section__header">
                <div>
                    <p class="eyebrow">Activity</p>
                    <h2>Monthly Reports</h2>
                </div>
            </div>
            <div class="spark-grid">
                <?php foreach ($recentActivity as $month): ?>
                    <div class="spark-item">
                        <div class="spark-bar-wrap">
                            <div class="spark-bar" style="height: <?= min(100, (int) $month['cnt'] * 10) ?>%"></div>
                        </div>
                        <strong><?= (int) $month['cnt'] ?></strong>
                        <span><?= date('M', mktime(0, 0, 0, $month['month'], 1)) ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>