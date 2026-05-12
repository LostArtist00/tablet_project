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
    <nav>
        <div class="nav-container">
            <div class="logo">Tablet Survey</div>
            <div class="nav-links">
                <a href="<?= e(url('tablets.php')) ?>">Tablets</a>
                <a href="<?= e(url('about.php')) ?>">About</a>
                <a href="<?= e(url('report.php')) ?>">Report</a>
                <a href="<?= e(url('survey.php')) ?>" class="cta-button">Survey</a>
            </div>
            <button class="mobile-menu-toggle" id="mobileMenuToggle">
                <span></span>
                <span></span>
                <span></span>
            </button>
        </div>
        <div class="mobile-nav" id="mobileNav">
            <a href="<?= e(url('tablets.php')) ?>">Tablets</a>
            <a href="<?= e(url('survey.php')) ?>">Survey</a>
            <a href="<?= e(url('report.php')) ?>">Report</a>
            <a href="<?= e(url('about.php')) ?>">About</a>
            <a href="<?= e(url('survey.php')) ?>" class="cta-button">Survey</a>
        </div>
    </nav>
    <main>
    <?php
}