<?php

declare(strict_types=1);

function renderFooter(): void
{
    ?>
    </main>
    <button class="back-to-top" type="button" data-back-to-top aria-label="Back to top">↑</button>
    <footer class="site-footer">
        <div class="container footer-shell">
            <p>Graphics tablet reliability data, user reports, and failure trends.</p>
            <a href="<?= e(url('admin/login.php')) ?>">Admin</a>
        </div>
    </footer>
    <script src="<?= e(asset('js/app.js')) ?>" defer></script>
    </body>
    </html>
    <?php
}
