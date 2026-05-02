<?php

declare(strict_types=1);

require_once __DIR__ . '/../../app/config/init.php';
require_once APP_PATH . '/includes/admin.php';

$auth = new Auth(db());
$auth->requireAdmin();

$commentModel = new Comment(db());

if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $commentModel->delete((int) $_GET['delete']);
    setFlash('Comment deleted.');
    redirect('admin/comments/index.php');
}

$comments = $commentModel->all();

renderAdminHeader('Comments');
?>
<div class="container">
    <h1>Comments</h1>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Tablet</th>
                <th>Author</th>
                <th>Comment</th>
                <th>Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($comments as $comment): ?>
                <tr>
                    <td><?= (int) $comment['id'] ?></td>
                    <td><?= (int) $comment['tablet_id'] ?></td>
                    <td><?= e($comment['author_name']) ?></td>
                    <td><?= e(substr($comment['comment_text'], 0, 50)) ?>...</td>
                    <td><?= date('M j, Y', strtotime($comment['created_at'])) ?></td>
                    <td>
                        <a href="index.php?delete=<?= (int) $comment['id'] ?>" onclick="return confirm('Delete?')">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php renderAdminFooter(); ?>