<?php

declare(strict_types=1);

function renderHeader(string $title, string $active = ''): void
{
    $navigation = [
        '' => 'Home',
        'tablets.php' => 'Tablets',
        'report.php' => 'Submit Report',
        'about.php' => 'About',
    ];
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?= e($title) ?> | Tablet Survey</title>
        <meta name="description" content="Tablet Survey is a graphics tablet reliability database built around repair stories, failure trends, and hard-earned user data.">
        <link rel="stylesheet" href="<?= e(asset('css/style.css')) ?>?v=<?= (int) filemtime(APP_PATH . '/assets/css/style.css') ?>">
    </head>
    <body>
    <header class="site-header">
        <div class="container nav-shell">
            <a class="brandmark" href="<?= e(url()) ?>">
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
                <?php foreach ($navigation as $href => $label): ?>
                    <a class="<?= $active === $href ? 'active' : '' ?>" href="<?= e(url($href)) ?>"><?= e($label) ?></a>
                <?php endforeach; ?>
                <button class="theme-toggle" type="button" data-theme-toggle>Theme</button>
            </nav>
        </div>
    </header>
    <main>
    <?php
}
