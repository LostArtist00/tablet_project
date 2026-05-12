<?php

declare(strict_types=1);

function renderFooter(): void
{
    ?>
    </main>
    <button class="back-to-top" type="button" data-back-to-top aria-label="Back to top">↑</button>
    <footer class="footer">
        <div class="footer-container">
            <div class="footer-content">
                <div class="footer-brand">
                    <div class="logo">Tablet Survey</div>
                    <p>Tracking graphics tablet reliability, user reports, and failure trends.</p>
                </div>
                <div class="footer-links">
                    <div class="footer-column">
                        <h4>Product</h4>
                        <a href="<?= e(url('tablets.php')) ?>">Tablets</a>
                        <a href="<?= e(url('survey.php')) ?>">Survey</a>
                        <a href="<?= e(url('report.php')) ?>">Report</a>
                    </div>
                    <div class="footer-column">
                        <h4>Company</h4>
                        <a href="<?= e(url('about.php')) ?>">About</a>
                        <a href="<?= e(url('admin/login.php')) ?>">Admin</a>
                    </div>
                    <div class="footer-column">
                        <h4>Resources</h4>
                        <a href="https://www.tooplate.com" target="_blank" rel="nofollow noopener">Tooplate</a>
                    </div>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2025 Tablet Survey. All rights reserved. | Designed by <a href="https://www.tooplate.com" target="_blank" rel="nofollow noopener">Tooplate</a></p>
                <div class="social-links">
                    <a href="#">Twitter</a>
                    <a href="#">LinkedIn</a>
                    <a href="#">GitHub</a>
                </div>
            </div>
        </div>
    </footer>
    <script src="<?= e(asset('js/app.js')) ?>"></script>
</body>
</html>
    <?php
}
