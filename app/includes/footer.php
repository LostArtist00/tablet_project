<?php

declare(strict_types=1);

function renderFooter(): void
{
    ?>
    </main>
    <footer class="site-footer">
        <div class="container">
            <p>&copy; <?= date('Y') ?> Tablet Survey. All rights reserved.</p>
        </div>
    </footer>
    <script src="<?= e(asset('js/app.js')) ?>"></script>
</body>
</html>
    <?php
}