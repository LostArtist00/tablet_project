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
    <button class="back-to-top" onclick="window.scrollTo({top:0, behavior:'smooth'})">↑</button>
    <script src="<?= e(asset('js/app.js')) ?>"></script>
</body>
</html>
    <?php
}