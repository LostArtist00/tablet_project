<?php

declare(strict_types=1);

function renderHeader(string $title = '', string $extra = ''): void
{
    ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title) ?> - Tablet Survey</title>
    <link rel="stylesheet" href="<?= e(asset('css/style.css')) ?>">
    <?= $extra ?>
</head>
<body>
    <header class="site-header">
        <div class="container">
            <a href="<?= e(url()) ?>" class="logo">Tablet Survey</a>
            <nav>
                <a href="<?= e(url('tablets.php')) ?>">Tablets</a>
                <a href="<?= e(url('report.php')) ?>">Report</a>
                <a href="<?= e(url('about.php')) ?>">About</a>
            </nav>
        </div>
    </header>
    <main>
    <?php
}