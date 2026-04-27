<?php

declare(strict_types=1);

function renderAdminHeader(string $title): void
{
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?= e($title) ?> | Admin</title>
        <link rel="stylesheet" href="<?= e(asset('css/style.css')) ?>">
    </head>
    <body class="admin-body">
    <header class="site-header admin-header">
        <div class="container">
            <a href="<?= e(url('admin/index.php')) ?>" class="logo">Admin</a>
            <nav>
                <a href="<?= e(url('admin/index.php')) ?>">Dashboard</a>
                <a href="<?= e(url('admin/brands/index.php')) ?>">Brands</a>
                <a href="<?= e(url('admin/tablets/index.php')) ?>">Tablets</a>
                <a href="<?= e(url('admin/reports/index.php')) ?>">Reports</a>
                <a href="<?= e(url('admin/comments/index.php')) ?>">Comments</a>
                <a href="<?= e(url('admin/logout.php')) ?>">Logout</a>
            </nav>
        </div>
    </header>
    <main class="admin-main">
    <?php
}

function renderAdminFooter(): void
{
    ?>
    </main>
    <script src="<?= e(asset('js/app.js')) ?>"></script>
    </body>
    </html>
    <?php
}