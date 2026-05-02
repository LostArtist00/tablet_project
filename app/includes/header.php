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
        <div class="container nav-shell">
            <a href="<?= e(url()) ?>" class="brandmark">
                <span class="brandmark-logo">TS</span>
                <span>
                    <strong>Tablet Survey</strong>
                    <small>data dont lie</small>
                </span>
            </a>
            <button class="nav-toggle" type="button" aria-label="Toggle navigation" data-nav-toggle>
                <span></span>
                <span></span>
                <span></span>
            </button>
            <nav class="site-nav" data-nav>
                <a href="<?= e(url('tablets.php')) ?>">Tablets</a>
                <a href="<?= e(url('report.php')) ?>">Report</a>
                <a href="<?= e(url('about.php')) ?>">About</a>
                <button class="theme-toggle" type="button" data-theme-toggle>Theme</button>
            </nav>
        </div>
    </header>
    <main>
    <?php
}