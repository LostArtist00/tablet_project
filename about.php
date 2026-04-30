<?php

declare(strict_types=1);

require_once __DIR__ . '/app/config/init.php';
require_once APP_PATH . '/includes/header.php';
require_once APP_PATH . '/includes/footer.php';

renderHeader('About', '');
?>
<section class="section">
    <div class="container">
        <div class="grid-2">
            <article class="panel">
                <p class="eyebrow">About</p>
                <h2>Tablet Survey</h2>
                <p>Tablet Survey is a community-driven platform for tracking graphics tablet reliability and failure data. Users submit reports about their tablet experiences to help others make informed decisions.</p>
                <p>Supported brands include Wacom, Huion, XP-Pen, Xencelabs, Gaomon, and One by Wacom.</p>
            </article>
            <article class="panel">
                <p class="eyebrow">How It Works</p>
                <h2>Submit Reports</h2>
                <p>Users can submit a report indicating whether their tablet is working, partially working, or broken. Reports include details like years used, failure reasons, and specific issues encountered.</p>
                <p>This data helps track reliability patterns across different models and brands.</p>
            </article>
        </div>
    </div>
</section>
<?php renderFooter(); ?>