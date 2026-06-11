<?php

declare(strict_types=1);

use App\Models\Tablet;
use App\Models\Brand;
use App\Models\FailureReport;

require_once __DIR__ . '/app/config/init.php';
require_once APP_PATH . '/includes/header.php';
require_once APP_PATH . '/includes/footer.php';

$brandModel = new Brand(db());
$tabletModel = new Tablet(db());
$brands = $brandModel->all();
$tablets = $tabletModel->all();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $report = new FailureReport(db());
    $issues = [];
    foreach (($_POST['issues'] ?? []) as $issue) {
        $issues[] = ['category' => $issue, 'subcategory' => '', 'severity' => 'moderate'];
    }
    $data = [
        'tablet_id' => (int) ($_POST['tablet_id'] ?? 0),
        'nickname' => '',
        'status' => $_POST['status'] ?? 'working',
        'years_used' => !empty($_POST['years_owned']) ? (float) $_POST['years_owned'] : null,
        'warranty_expired' => 0,
        'severity' => 'moderate',
        'repair_status' => 'none',
        'failure_reason' => null,
        'extra_comment' => $_POST['notes'] ?? '',
        'issues' => $issues,
    ];
    if ($data['tablet_id'] && $report->create($data)) {
        setFlash('Thanks for the report!', 'success');
        redirect('tablets.php');
    } else {
        setFlash('Please select a tablet.', 'error');
    }
}

renderHeader('Survey', '');
?>
<section class="tm-survey" id="tabletSurvey">
        <form id="surveyForm" action="<?= e(url('survey.php')) ?>" method="post" novalidate>
        <?= csrfField() ?>

        <div class="step-info">
            <span class="step-dot active"></span>
            <span class="step-dot"></span>
            <span class="step-dot"></span>
            <span class="step-dot"></span>
        </div>

        <fieldset class="step" data-step="1">
            <legend>1. Pick a brand</legend>
            <div class="survey-options">
                <?php foreach ($brands as $brand): ?>
                    <label class="survey-option">
                        <input type="radio" name="brand_id" value="<?= (int) $brand['id'] ?>" required>
                        <?= e($brand['name']) ?>
                    </label>
                <?php endforeach; ?>
            </div>
            <button type="button" class="next-btn">Next</button>
        </fieldset>

        <fieldset class="step" data-step="2">
            <legend>2. Screen or no screen?</legend>
            <div class="survey-options">
                <label class="survey-option">
                    <input type="radio" name="has_display" value="0" checked>
                    Graphics Tablet
                </label>
                <label class="survey-option">
                    <input type="radio" name="has_display" value="1">
                    Display Tablet
                </label>
            </div>
            <button type="button" class="prev-btn">Prev</button>
            <button type="button" class="next-btn">Next</button>
        </fieldset>

        <fieldset class="step" data-step="3">
            <legend>3. Which model?</legend>
            <input type="search" id="surveySearch" class="survey-search" placeholder="Search tablets..." autocomplete="off">
            <div class="survey-tablet-list" id="surveyTabletList">
                <?php foreach ($tablets as $t): ?>
                    <label class="survey-tablet-item" data-brand="<?= (int) $t['brand_id'] ?>" data-type="<?= $t['has_display'] ? 'display' : 'graphics' ?>">
                        <input type="radio" name="tablet_id" value="<?= (int) $t['id'] ?>" required>
                        <?= e($t['brand_name']) ?> <?= e($t['name']) ?>
                    </label>
                <?php endforeach; ?>
            </div>
            <button type="button" class="prev-btn">Prev</button>
            <button type="button" class="next-btn">Next</button>
        </fieldset>

        <fieldset class="step" data-step="4">
            <legend>4. How's it holding up?</legend>
            <div class="survey-options">
                <label class="survey-option">
                    <input type="radio" name="status" value="working" checked>
                    Works fine
                </label>
                <label class="survey-option">
                    <input type="radio" name="status" value="broken">
                    Dead
                </label>
            </div>

            <div class="form-group">
                <label for="duration">How long did you have it? <span class="muted">(optional)</span></label>
                <input type="number" id="duration" name="years_owned" min="1" placeholder="years">
            </div>

            <div class="form-group">
                <label>What went wrong? <span class="muted">hover each issue for details</span></label>
                <div class="survey-issues-list">
                    <label title="Cracked glass, dead pixels, or the display flickering or cutting out. Common on display tablets after drops or extended use."><input type="checkbox" name="issues[]" value="screen"> Screen damage</label>
                    <label title="The pen tip, buttons, or pressure sensitivity are unreliable. Can include jittery lines, no cursor movement, or the pen not being detected at all."><input type="checkbox" name="issues[]" value="stylus"> Stylus issues</label>
                    <label title="Driver crashes, failure to detect the tablet, or problems that started right after a driver or OS update. Also covers Windows Ink / WinTab conflicts."><input type="checkbox" name="issues[]" value="drivers"> Driver / software</label>
                    <label title="Battery drains too fast, won't hold a charge, or the tablet only works while plugged in. Relevant for wireless models."><input type="checkbox" name="issues[]" value="battery"> Battery issues</label>
                    <label title="The bundled USB / HDMI / USB-C cable is fraying, loose, or only works when held at a certain angle. A very common wear point."><input type="checkbox" name="issues[]" value="cables"> Cable faults</label>
                    <label title="A portion of the drawing surface does not register the pen cursor, making that area unusable. Often caused by impact or manufacturing defects."><input type="checkbox" name="issues[]" value="dead_zone"> Dead zone on surface</label>
                    <label title="The tablet intermittently disconnects from the computer, the port feels loose, or video signal drops out during use."><input type="checkbox" name="issues[]" value="connection"> Connection drops</label>
                    <label title="Pressure sensitivity is missing or inconsistent — lines come out the same thickness regardless of how hard you press. Often a driver or calibration issue."><input type="checkbox" name="issues[]" value="pressure"> Pressure problems</label>
                </div>
            </div>

            <div class="form-group">
                <label for="comment">Anything else to add?</label>
                <textarea id="comment" name="notes" rows="3" placeholder="Your thoughts..."></textarea>
            </div>

            <button type="button" class="prev-btn">Prev</button>
            <button type="submit" class="submit-btn">Submit</button>
        </fieldset>
    </form>
</section>

<?php renderFooter(); ?>
