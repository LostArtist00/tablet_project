<?php

declare(strict_types=1);

require_once __DIR__ . '/../app/config/init.php';
require_once APP_PATH . '/includes/admin.php';

$auth = new Auth(db());
$auth->requireAdmin();

$reportModel = new FailureReport(db());

if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $reportModel->delete((int) $_GET['delete']);
    setFlash('Report deleted.');
    redirect('admin/reports/index.php');
}

$reports = $reportModel->recent(100);

renderAdminHeader('Reports');
?>
<div class="container">
    <div class="flex-between">
        <h1>Reports</h1>
        <a href="../report.php" class="button secondary">View Form</a>
    </div>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Tablet</th>
                <th>Status</th>
                <th>Years</th>
                <th>Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($reports as $report): ?>
                <tr>
                    <td><?= (int) $report['id'] ?></td>
                    <td><?= e($report['brand_name']) ?> <?= e($report['tablet_name']) ?></td>
                    <td><span class="badge <?= e($report['status']) ?>"><?= e($report['status']) ?></span></td>
                    <td><?= e($report['years_used']) ?></td>
                    <td><?= date('M j, Y', strtotime($report['created_at'])) ?></td>
                    <td>
                        <a href="index.php?delete=<?= (int) $report['id'] ?>" onclick="return confirm('Delete?')">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php renderAdminFooter(); ?>