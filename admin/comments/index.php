<?php

declare(strict_types=1);

use App\Models\Auth;
use App\Models\Comment;

require_once __DIR__ . '/../../app/config/init.php';
require_once APP_PATH . '/includes/admin.php';

$auth = new Auth(db());
$auth->requireAdmin();

$commentModel = new Comment(db());

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete'])) {
    requireCsrfToken();
    $commentModel->delete((int) $_POST['delete']);
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
                        <form method="post" style="display:inline" onsubmit="return confirm('Delete this comment?')">
                            <?= csrfField() ?>
                            <input type="hidden" name="delete" value="<?= (int) $comment['id'] ?>">
                            <button type="submit" style="background:none;border:none;color:inherit;cursor:pointer;padding:0;font:inherit;text-decoration:underline">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php renderAdminFooter(); ?>