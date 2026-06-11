<?php

declare(strict_types=1);

use App\Models\Auth;
use App\Models\FailureReport;

require_once __DIR__ . '/../../app/config/init.php';
require_once APP_PATH . '/includes/admin.php';

$auth = new Auth(db());
$auth->requireAdmin();

$reportModel = new FailureReport(db());

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete'])) {
    requireCsrfToken();
    $reportModel->delete((int) $_POST['delete']);
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
                        <form method="post" style="display:inline" onsubmit="return confirm('Delete this report?')">
                            <?= csrfField() ?>
                            <input type="hidden" name="delete" value="<?= (int) $report['id'] ?>">
                            <button type="submit" style="background:none;border:none;color:inherit;cursor:pointer;padding:0;font:inherit;text-decoration:underline">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php renderAdminFooter(); ?>