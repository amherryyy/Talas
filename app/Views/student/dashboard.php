<?= $this->extend('student/subjects/layout') ?>

<?= $this->section('content') ?>
<section class="workspace-hero" aria-labelledby="dashboard-heading">
    <div class="workspace-hero-copy">
        <p class="workspace-kicker">Your study space</p>
        <h1 id="dashboard-heading">Make a little progress today.</h1>
        <p>Choose what needs your attention, plan the time you have, and keep a record of the work you finish.</p>
        <div class="actions">
            <a class="button" href="<?= esc(site_url('student/priority')) ?>"><span class="solar-icon solar-icon-target" aria-hidden="true"></span>Find my next topic</a>
            <a class="button secondary" href="<?= esc(site_url('student/subjects')) ?>">Manage subjects</a>
        </div>
    </div>
    <div class="workspace-hero-art" aria-hidden="true">
        <span class="hero-orbit hero-orbit-one"></span>
        <span class="hero-orbit hero-orbit-two"></span>
        <img class="dashboard-mascot" src="<?= esc(base_url('assets/images/duno-mascot.png')) ?>" alt="" width="1228" height="1285" fetchpriority="high">
        <span class="hero-art-label">one step at a time</span>
    </div>
</section>

<dialog class="check-in-dialog" data-check-in-prompt data-prompt-date="<?= esc($checkInPromptDate) ?>" data-prompt-user="<?= esc($checkInPromptUserKey) ?>" data-prompt-satisfied="<?= $checkInPromptSatisfied ? 'true' : 'false' ?>" aria-labelledby="check-in-prompt-title" aria-describedby="check-in-prompt-description">
    <div class="check-in-dialog-head">
        <img class="check-in-mascot" src="<?= esc(base_url('assets/images/duno-mascot.png')) ?>" alt="" width="1228" height="1285" aria-hidden="true">
        <button type="button" class="check-in-dialog-close" data-check-in-dismiss aria-label="Close check-in prompt">×</button>
    </div>
    <p class="workspace-kicker">A small pause before you begin</p>
    <h2 id="check-in-prompt-title">How are you feeling about studying?</h2>
    <p id="check-in-prompt-description" class="check-in-dialog-copy">A quick private check-in can help you notice your energy and how demanding study feels today. It’s optional and takes a moment.</p>

    <?php if ($checkInErrors !== []): ?>
        <ul class="errors" role="alert">
            <?php foreach ($checkInErrors as $message): ?><li><?= esc($message) ?></li><?php endforeach ?>
        </ul>
    <?php endif ?>

    <form method="post" action="<?= esc(site_url('student/check-ins')) ?>" class="check-in-prompt-form">
        <?= csrf_field() ?>
        <input type="hidden" name="return_to" value="student">
        <fieldset class="check-in-scale">
            <legend>How heavy or stressful does studying feel right now?</legend>
            <div class="check-in-scale-options">
                <?php foreach ([1, 2, 3, 4, 5] as $rating): ?>
                    <label class="check-in-scale-choice">
                        <input type="radio" name="workload_rating" value="<?= $rating ?>" aria-label="<?= $rating ?> out of 5" required<?= (string) old('workload_rating', '') === (string) $rating ? ' checked' : '' ?>>
                        <span><?= $rating ?></span>
                    </label>
                <?php endforeach ?>
            </div>
            <div class="check-in-scale-endpoints"><span>Very light</span><span>Very heavy</span></div>
        </fieldset>
        <fieldset class="check-in-scale">
            <legend>How is your energy for studying?</legend>
            <div class="check-in-scale-options">
                <?php foreach ([1, 2, 3, 4, 5] as $rating): ?>
                    <label class="check-in-scale-choice">
                        <input type="radio" name="energy_rating" value="<?= $rating ?>" aria-label="<?= $rating ?> out of 5" required<?= (string) old('energy_rating', '') === (string) $rating ? ' checked' : '' ?>>
                        <span><?= $rating ?></span>
                    </label>
                <?php endforeach ?>
            </div>
            <div class="check-in-scale-endpoints"><span>Very low</span><span>Very high</span></div>
        </fieldset>
        <p class="check-in-private-note">Your check-in is private to you, optional, and not an assessment.</p>
        <div class="actions check-in-prompt-actions">
            <button type="submit">Save private check-in</button>
            <button type="button" class="button secondary" data-check-in-dismiss>Not now</button>
        </div>
    </form>
    <script src="<?= esc(base_url('assets/js/check-in-prompt.js?v=20261025-prompt1')) ?>" defer></script>
</dialog>
<noscript><p class="notice">You can add an optional private energy and study-load check-in any time. <a href="<?= esc(site_url('student/check-ins')) ?>">Open check-ins</a>.</p></noscript>

<?= $this->include('student/partials/progression_card', ['progression' => $progression]) ?>

<section class="dashboard-tools" aria-labelledby="tools-heading">
    <div class="dashboard-section-heading">
        <div><p class="workspace-kicker">Your toolkit</p><h2 id="tools-heading">Where would you like to start?</h2></div>
        <a href="<?= esc(site_url('student/subjects')) ?>">View subjects <span aria-hidden="true">→</span></a>
    </div>
    <div class="dashboard-tool-grid">
        <a class="dashboard-tool-card focus-tool" href="<?= esc(site_url('student/priority')) ?>">
            <span class="dashboard-tool-icon"><span class="solar-icon solar-icon-target" aria-hidden="true"></span></span>
            <span class="dashboard-tool-title">Duno Focus</span>
            <span class="dashboard-tool-copy">See unfinished topics ranked by their due dates, importance, and progress.</span>
            <span class="dashboard-tool-link">Choose a topic <span aria-hidden="true">↗</span></span>
        </a>
        <a class="dashboard-tool-card plan-tool" href="<?= esc(site_url('student/plan')) ?>">
            <span class="dashboard-tool-icon"><span class="solar-icon solar-icon-calendar" aria-hidden="true"></span></span>
            <span class="dashboard-tool-title">Duno Plan</span>
            <span class="dashboard-tool-copy">Turn the time you have today into a plan you can adjust.</span>
            <span class="dashboard-tool-link">Shape today <span aria-hidden="true">↗</span></span>
        </a>
        <a class="dashboard-tool-card timer-tool" href="<?= esc(site_url('student/sessions')) ?>">
            <span class="dashboard-tool-icon"><span class="solar-icon solar-icon-stopwatch" aria-hidden="true"></span></span>
            <span class="dashboard-tool-title">Duno Timer</span>
            <span class="dashboard-tool-copy">Time a study session and save it when you choose.</span>
            <span class="dashboard-tool-link">Start a session <span aria-hidden="true">↗</span></span>
        </a>
        <a class="dashboard-tool-card aralytics-tool" href="<?= esc(site_url('student/aralytics')) ?>">
            <span class="dashboard-tool-icon"><span class="solar-icon solar-icon-chart-2" aria-hidden="true"></span></span>
            <span class="dashboard-tool-title">Aralytics</span>
            <span class="dashboard-tool-copy">Look back at your saved study time and sessions.</span>
            <span class="dashboard-tool-link">Review activity <span aria-hidden="true">↗</span></span>
        </a>
    </div>
</section>

<section class="guide-section" id="study-ideas-heading" aria-labelledby="guide-heading">
    <div class="dashboard-section-heading">
        <div><p class="workspace-kicker">A few ideas to try</p><h2 id="guide-heading">Duno Guide</h2></div>
        <p class="muted">General suggestions. Pick what fits your course.</p>
    </div>
    <ul class="suggestion-list">
        <?php foreach ($suggestions as $suggestion): ?>
            <li>
                <article class="suggestion-card" aria-labelledby="suggestion-<?= esc($suggestion['id']) ?>">
                    <h3 id="suggestion-<?= esc($suggestion['id']) ?>"><?= esc($suggestion['title']) ?></h3>
                    <p><?= esc($suggestion['body']) ?></p>
                    <p class="suggestion-recommendation"><?= esc($suggestion['recommendation']) ?></p>
                    <a href="<?= esc($suggestionSourceUrl) ?>" rel="noopener noreferrer"><?= esc($suggestionSourceName) ?></a>
                </article>
            </li>
        <?php endforeach ?>
    </ul>
</section>
<?= $this->endSection() ?>
