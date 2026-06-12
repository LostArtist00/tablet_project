<?php

declare(strict_types=1);

use App\Models\Tablet;
use App\Models\FailureReport;
use App\Models\Comment;

require_once __DIR__ . '/app/config/init.php';
require_once APP_PATH . '/includes/header.php';
require_once APP_PATH . '/includes/footer.php';

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if (!$id) {
    redirect('tablets.php');
}

$tabletModel = new Tablet(db());
$reportModel = new FailureReport(db());
$commentModel = new Comment(db());

$tablet = $tabletModel->byId($id);

if (!$tablet) {
    redirect('tablets.php');
}

$reports = $reportModel->byTablet($id);
$comments = $commentModel->byTablet($id);
$stats = $tabletModel->stats($id);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    requireCsrfToken();
    
    if ($_POST['action'] === 'add_comment') {
        $commentModel->create([
            'tablet_id' => $id,
            'author_name' => $_POST['author_name'],
            'comment_text' => $_POST['comment_text'],
        ]);
        setFlash('Comment added.');
        redirect('tablet.php?id=' . $id);
    }
}

renderHeader(e($tablet['brand_name'] . ' ' . $tablet['name']), '');
?>
<section class="section">
    <div class="container">
        <article class="card tablet-detail">
            <?php if ($tablet['image_path']): ?>
                <div class="tablet-detail__media">
                    <img src="<?= e(uploadUrl($tablet['image_path'])) ?>" alt="<?= e($tablet['brand_name']) ?> <?= e($tablet['name']) ?>" style="object-position:<?= (int) ($tablet['image_pos_x'] ?? 50) ?>% <?= (int) ($tablet['image_pos_y'] ?? 50) ?>%;">
                </div>
            <?php endif; ?>
            <div class="tablet-detail__content">
                <h1><?= e($tablet['brand_name']) ?> <?= e($tablet['name']) ?></h1>
                <div class="meta tablet-detail__meta">
                    <span class="pill"><?= e($tablet['has_display'] ? 'Display' : 'Pen tablet') ?></span>
                    <span class="pill"><?= e($tablet['size']) ?></span>
                    <span class="pill"><?= e($tablet['pressure_levels']) ?> pressure</span>
                </div>

                <p><strong>Price:</strong> $<?= e($tablet['price']) ?></p>
                <p><strong>Connection:</strong> <?= e($tablet['connection_type'] ?: 'N/A') ?></p>
                <?php if ($tablet['has_display'] && $tablet['display_resolution']): ?>
                    <p><strong>Display:</strong> <?= e($tablet['display_resolution']) ?></p>
                <?php endif; ?>
                <p><?= e($tablet['notes']) ?></p>
            </div>
        </article>

        <div class="stats-grid">
            <div class="stat-card">
                <strong><?= (int) ($stats['total_reports'] ?? 0) ?></strong>
                <span>reports</span>
            </div>
            <div class="stat-card">
                <strong><?= (int) ($stats['working'] ?? 0) ?></strong>
                <span>working</span>
            </div>
            <div class="stat-card">
                <strong><?= (int) ($stats['broken'] ?? 0) ?></strong>
                <span>broken</span>
            </div>
            <div class="stat-card">
                <strong><?= e(number_format((float) ($stats['avg_years_working'] ?? 0), 1)) ?></strong>
                <span>avg working yrs</span>
            </div>
            <div class="stat-card">
                <strong><?= e(number_format((float) ($stats['avg_years_broken'] ?? 0), 1)) ?></strong>
                <span>avg fault yrs</span>
            </div>
        </div>

        <h2>Reports (<?= count($reports) ?>)</h2>
        <?php if (empty($reports)): ?>
            <p>No reports yet.</p>
        <?php else: ?>
            <?php foreach ($reports as $report): ?>
                <article class="card tablet-report-card">
                    <div class="meta">
                        <span class="pill"><?= e($report['status']) ?></span>
                        <span class="pill"><?= e($report['years_used']) ?> years</span>
                    </div>
                    <p><?= e($report['failure_reason']) ?></p>
                    <small>by <?= e($report['nickname']) ?> on <?= date('M j, Y', strtotime($report['created_at'])) ?></small>
                </article>
            <?php endforeach; ?>
        <?php endif; ?>

        <h2>Comments (<?= count($comments) ?>)</h2>
        <?php foreach ($comments as $comment): ?>
            <article class="card tablet-report-card">
                <p><?= e($comment['comment_text']) ?></p>
                <small>by <?= e($comment['author_name']) ?></small>
            </article>
        <?php endforeach; ?>

        <h3>Add Comment</h3>
        <form method="post" class="tablet-comment-form">
            <?= csrfField() ?>
            <input type="hidden" name="action" value="add_comment">
            <div class="form-group">
                <label>Name</label>
                <input type="text" name="author_name" required>
            </div>
            <div class="form-group">
                <label>Comment</label>
                <textarea name="comment_text" rows="3" required></textarea>
            </div>
            <button type="submit" class="button">Post Comment</button>
        </form>
    </div>
</section>
<?php renderFooter(); ?>
