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

$categories = [];
$stmt = db()->query('SELECT * FROM issue_categories ORDER BY category, subcategory');
foreach ($stmt->fetchAll() as $row) {
    $categories[$row['category']][] = $row;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    requireCsrfToken();
    
    $tabletId = (int) $_POST['tablet_id'];
    
    $issues = [];
    if (!empty($_POST['issues'])) {
        foreach ($_POST['issues'] as $issueKey) {
            $parts = explode('|', $issueKey);
            if (count($parts) === 2) {
                $issues[] = [
                    'category' => $parts[0],
                    'subcategory' => $parts[1],
                ];
            }
        }
    }
    
    $reportModel->create([
        'tablet_id' => $tabletId,
        'nickname' => $_POST['nickname'],
        'status' => $_POST['status'],
        'years_used' => $_POST['years_used'] ?: null,
        'warranty_expired' => isset($_POST['warranty_expired']) ? 1 : 0,
        'severity' => $_POST['severity'],
        'repair_status' => $_POST['repair_status'],
        'failure_reason' => $_POST['failure_reason'] ?? null,
        'extra_comment' => $_POST['extra_comment'] ?? null,
        'issues' => $issues,
    ]);
    
    setFlash('Report submitted. Thank you!');
    redirect('tablets.php');
}

renderHeader('Submit Report', '');
?>
<section class="section">
    <div class="container">
        <h1>Submit Report</h1>
        <p>Share your tablet experience to help others.</p>
        
        <form method="post" class="report-form">
            <?= csrfField() ?>
            
            <div class="form-group">
                <label>Select Tablet</label>
                <select name="tablet_id" required>
                    <option value="">Choose a tablet...</option>
                    <?php foreach ($tablets as $t): ?>
                        <option value="<?= (int) $t['id'] ?>" <?= old('tablet_id') == $t['id'] ? 'selected' : '' ?>>
                            <?= e($t['brand_name']) ?> <?= e($t['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label>Your Name (optional)</label>
                <input type="text" name="nickname" value="<?= e(old('nickname')) ?>" placeholder="Anonymous">
            </div>
            
            <div class="form-group">
                <label>Status</label>
                <select name="status" required>
                    <option value="working">Still Working</option>
                    <option value="partially_working">Partially Working</option>
                    <option value="broken">Broken</option>
                </select>
            </div>
            
            <div class="form-group">
                <label>Years Used</label>
                <input type="number" name="years_used" step="0.5" min="0" value="<?= e(old('years_used')) ?>">
            </div>
            
            <div class="form-group">
                <label>
                    <input type="checkbox" name="warranty_expired" value="1" <?= old('warranty_expired') ? 'checked' : '' ?>>
                    Warranty expired
                </label>
            </div>
            
            <div class="form-group">
                <label>Severity</label>
                <select name="severity">
                    <option value="minor">Minor</option>
                    <option value="moderate" selected>Moderate</option>
                    <option value="severe">Severe</option>
                    <option value="critical">Critical</option>
                </select>
            </div>
            
            <div class="form-group">
                <label>Repair Status</label>
                <select name="repair_status">
                    <option value="none">None</option>
                    <option value="self_repaired">Self Repaired</option>
                    <option value="warranty_claim">Warranty Claim</option>
                    <option value="replaced">Replaced</option>
                    <option value="abandoned">Abandoned</option>
                </select>
            </div>
            
            <div class="form-group">
                <label>Issues (if any)</label>
                <?php foreach ($categories as $cat => $items): ?>
                    <fieldset>
                        <legend><?= e(ucfirst(str_replace('_', ' ', $cat))) ?></legend>
                        <?php foreach ($items as $item): ?>
                            <label>
                                <input type="checkbox" name="issues[]" value="<?= e($cat . '|' . $item['subcategory']) ?>">
                                <?= e($item['subcategory']) ?>
                            </label>
                        <?php endforeach; ?>
                    </fieldset>
                <?php endforeach; ?>
            </div>
            
            <div class="form-group">
                <label>What happened?</label>
                <textarea name="failure_reason" rows="3"><?= e(old('failure_reason')) ?></textarea>
            </div>
            
            <div class="form-group">
                <label>Additional comments</label>
                <textarea name="extra_comment" rows="2"><?= e(old('extra_comment')) ?></textarea>
            </div>
            
            <button type="submit" class="button">Submit Report</button>
        </form>
    </div>
</section>
<?php renderFooter(); ?>