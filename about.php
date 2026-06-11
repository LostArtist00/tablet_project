<?php

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
                <p>Collects real-world reliability data for graphics tablets. People submit reports about their own tablets so others can see what actually breaks.</p>
                <p>Supported brands include Wacom, Huion, XP-Pen, Xencelabs, Gaomon, and One by Wacom.</p>
            </article>
            <article class="panel">
                <p class="eyebrow">How It Works</p>
                <h2>Submit Reports</h2>
                <p>Say whether your tablet is working, on its way out, or dead. Reports include how many years you got out of it and what exactly went wrong.</p>
                <p>Over time this helps spot which models hold up and which dont.</p>
            </article>
        </div>
    </div>
</section>
<?php renderFooter(); ?>