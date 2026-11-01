<?= $this->extend('student/subjects/layout') ?>

<?= $this->section('content') ?>
<section class="workspace-hero" aria-labelledby="dashboard-heading" data-tour-target="start">
    <span class="workspace-gradient-drift workspace-gradient-drift-one" aria-hidden="true"></span>
    <span class="workspace-gradient-drift workspace-gradient-drift-two" aria-hidden="true"></span>
    <span class="workspace-gradient-drift workspace-gradient-drift-three" aria-hidden="true"></span>
    <span class="workspace-gradient-drift workspace-gradient-drift-four" aria-hidden="true"></span>
    <div class="workspace-hero-copy">
        <p class="workspace-kicker">Your study space</p>
        <h1 id="dashboard-heading">Make a little progress today.</h1>
        <p>Choose what needs your attention, plan the time you have, and keep a record of the work you finish.</p>
        <div class="actions">
            <a class="button" href="<?= esc(site_url('student/priority')) ?>"><span class="solar-icon solar-icon-target" aria-hidden="true"></span>Find my next topic</a>
            <a class="button secondary" href="<?= esc(site_url('student/subjects')) ?>">Manage subjects</a>
        </div>
        <a class="workspace-hero-tour" href="<?= esc(site_url('student')) ?>#app-tour">
            <span class="workspace-tour-mark" aria-hidden="true">?</span>
            <span class="workspace-tour-copy"><small><?= esc(lang('App.tourIntro')) ?></small><strong><?= esc(lang('App.tourAction')) ?></strong></span>
            <span class="workspace-tour-arrow" aria-hidden="true">&#8594;</span>
        </a>
    </div>
    <div class="workspace-hero-art" aria-hidden="true">
        <span class="hero-art-glow"></span>
        <span class="hero-orbit hero-orbit-one"></span>
        <span class="hero-orbit hero-orbit-two"></span>
        <svg class="hero-art-constellation" viewBox="0 0 360 300" focusable="false">
            <ellipse class="constellation-track constellation-track-one" cx="180" cy="150" rx="132" ry="100"></ellipse>
            <ellipse class="constellation-track constellation-track-two" cx="180" cy="150" rx="112" ry="83" transform="rotate(-34 180 150)"></ellipse>
            <g class="constellation-orbit constellation-orbit-one"><circle cx="312" cy="150" r="6"></circle><path d="M312 138v24M300 150h24"></path></g>
            <g class="constellation-orbit constellation-orbit-two"><circle cx="180" cy="67" r="4"></circle><path d="M180 58v18M171 67h18"></path></g>
            <path class="constellation-star constellation-star-one" d="M65 91c2 11 6 15 17 17-11 2-15 6-17 17-2-11-6-15-17-17 11-2 15-6 17-17Z"></path>
            <path class="constellation-star constellation-star-two" d="M292 224c1.5 8 4 10.5 12 12-8 1.5-10.5 4-12 12-1.5-8-4-10.5-12-12 8-1.5 10.5-4 12-12Z"></path>
        </svg>
        <?= view('partials/duno_mascot', ['mascotClass' => 'dashboard-mascot-stage dashboard-mascot', 'mascotInteractive' => true, 'mascotLabel' => 'Tap Duno for a playful hello']) ?>
        <span class="hero-art-label">one step at a time</span>
    </div>
</section>

<dialog class="check-in-dialog" data-check-in-prompt data-prompt-date="<?= esc($checkInPromptDate) ?>" data-prompt-user="<?= esc($checkInPromptUserKey) ?>" data-prompt-satisfied="<?= $checkInPromptSatisfied ? 'true' : 'false' ?>" aria-labelledby="check-in-prompt-title" aria-describedby="check-in-prompt-description">
    <div class="check-in-dialog-head">
        <span class="mascot-stage" aria-hidden="true"><img class="check-in-mascot" src="<?= esc(duno_asset('assets/images/duno-mascot-support.webp')) ?>" alt="" width="512" height="536"></span>
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
        <?= view('student/partials/rating_scale', [
            'scaleName' => 'workload_rating',
            'scaleId' => 'prompt-workload-rating',
            'scaleLabel' => 'How heavy or stressful does studying feel right now?',
            'scaleValue' => (string) old('workload_rating', ''),
            'scaleLowLabel' => 'Very light',
            'scaleHighLabel' => 'Very heavy',
        ]) ?>
        <?= view('student/partials/rating_scale', [
            'scaleName' => 'energy_rating',
            'scaleId' => 'prompt-energy-rating',
            'scaleLabel' => 'How is your energy for studying?',
            'scaleValue' => (string) old('energy_rating', ''),
            'scaleLowLabel' => 'Very low',
            'scaleHighLabel' => 'Very high',
        ]) ?>
        <p class="check-in-private-note">Your check-in is private to you, optional, and not an assessment.</p>
        <div class="actions check-in-prompt-actions">
            <button type="submit">Save private check-in</button>
            <button type="button" class="button secondary" data-check-in-dismiss>Not now</button>
        </div>
    </form>
    <script src="<?= esc(duno_asset('assets/js/check-in-prompt.js')) ?>" defer></script>
</dialog>
<noscript><p class="notice">You can add an optional private energy and study-load check-in any time. <a href="<?= esc(site_url('student/check-ins')) ?>">Open check-ins</a>.</p></noscript>

<?= $this->include('student/partials/progression_card', ['progression' => $progression]) ?>

<section class="dashboard-subjects" aria-labelledby="dashboard-subjects-heading">
    <div class="dashboard-section-heading">
        <div><p class="workspace-kicker">Your current program</p><h2 id="dashboard-subjects-heading">Subjects</h2></div>
    </div>
    <?php if ($dashboardSubjects === []): ?>
        <div class="dashboard-subject-empty-card">
            <p class="dashboard-subjects-empty">Add a subject to start organizing its topics.</p>
            <a class="dashboard-subject-action" href="<?= esc(site_url('student/subjects/new')) ?>">Add your first subject <span aria-hidden="true">&#8594;</span></a>
        </div>
    <?php else: ?>
        <div class="dashboard-subject-panel">
            <ul class="dashboard-subject-list">
            <?php foreach ($dashboardSubjects as $dashboardSubject): ?>
                <li>
                    <div class="dashboard-subject-main">
                        <?php
                        $dashboardSubjectName = trim((string) $dashboardSubject['name']);
                        if ($dashboardSubjectName === '' || preg_match('/[\p{L}\p{N}]/u', $dashboardSubjectName) !== 1) {
                            $dashboardSubjectName = 'Untitled subject';
                        }
                        $dashboardTopicCount = (int) $dashboardSubject['topic_count'];
                        $dashboardTopicPreview = $dashboardSubject['topic_preview'];
                        if ($dashboardTopicCount > count($dashboardTopicPreview)) {
                            $dashboardTopicPreview[] = '+' . ($dashboardTopicCount - count($dashboardTopicPreview)) . ' more';
                        }
                        ?>
                        <a class="dashboard-subject-name" href="<?= esc(site_url('student/subjects/' . $dashboardSubject['id'])) ?>"><?= esc($dashboardSubjectName !== '' ? $dashboardSubjectName : 'Untitled subject') ?></a>
                        <div class="dashboard-subject-meta">
                            <?php if ($dashboardTopicCount === 0): ?>
                                <span>No topics yet</span>
                            <?php else: ?>
                                <span class="dashboard-subject-count"><?= $dashboardTopicCount ?> <?= $dashboardTopicCount === 1 ? 'topic' : 'topics' ?></span>
                                <span aria-hidden="true">&middot;</span>
                                <span class="dashboard-subject-preview"><?= esc(implode(', ', $dashboardTopicPreview)) ?></span>
                            <?php endif ?>
                        </div>
                    </div>
                    <a class="dashboard-subject-action" href="<?= esc(site_url('student/subjects/' . $dashboardSubject['id'] . '/topics')) ?>"><?= $dashboardTopicCount === 0 ? 'Add a topic' : 'Manage topics' ?> <span aria-hidden="true">&#8594;</span></a>
                </li>
            <?php endforeach ?>
            </ul>
        <?php if ($subjectCount > count($dashboardSubjects)): ?>
            <div class="dashboard-subjects-more">
                <span>Showing <?= count($dashboardSubjects) ?> of <?= (int) $subjectCount ?> subjects</span>
                <a href="<?= esc(site_url('student/subjects')) ?>">View all subjects <span aria-hidden="true">&#8594;</span></a>
            </div>
        <?php endif ?>
        </div>
    <?php endif ?>
</section>

<section class="dashboard-tools" aria-labelledby="tools-heading">
    <div class="dashboard-section-heading">
        <div><p class="workspace-kicker">Your toolkit</p><h2 id="tools-heading">Study tools</h2></div>
    </div>
    <div class="dashboard-tool-grid">
        <a class="dashboard-tool-card focus-tool" href="<?= esc(site_url('student/priority')) ?>" data-tour-target="focus">
            <span class="dashboard-tool-icon"><span class="solar-icon solar-icon-target" aria-hidden="true"></span></span>
            <span class="dashboard-tool-title">Duno Focus</span>
            <span class="dashboard-tool-copy">See unfinished topics ranked by difficulty, importance, due dates, and progress.</span>
            <span class="dashboard-tool-link">Choose a topic <span aria-hidden="true">↗</span></span>
        </a>
        <a class="dashboard-tool-card plan-tool" href="<?= esc(site_url('student/plan')) ?>" data-tour-target="plan">
            <span class="dashboard-tool-icon"><span class="solar-icon solar-icon-calendar" aria-hidden="true"></span></span>
            <span class="dashboard-tool-title">Duno Plan</span>
            <span class="dashboard-tool-copy">Turn the time you have today into a plan you can adjust.</span>
            <span class="dashboard-tool-link">Shape today <span aria-hidden="true">↗</span></span>
        </a>
        <a class="dashboard-tool-card timer-tool" href="<?= esc(site_url('student/sessions')) ?>" data-tour-target="timer">
            <span class="dashboard-tool-icon"><span class="solar-icon solar-icon-stopwatch" aria-hidden="true"></span></span>
            <span class="dashboard-tool-title">Duno Timer</span>
            <span class="dashboard-tool-copy">Time a study session and save it when you choose.</span>
            <span class="dashboard-tool-link">Start a session <span aria-hidden="true">↗</span></span>
        </a>
        <a class="dashboard-tool-card aralytics-tool" href="<?= esc(site_url('student/aralytics')) ?>" data-tour-target="aralytics">
            <span class="dashboard-tool-icon"><span class="solar-icon solar-icon-chart-2" aria-hidden="true"></span></span>
            <span class="dashboard-tool-title">Aralytics</span>
            <span class="dashboard-tool-copy">Look back at your saved study time and sessions.</span>
            <span class="dashboard-tool-link">Review activity <span aria-hidden="true">↗</span></span>
        </a>
    </div>
</section>

<section class="guide-section" id="study-ideas-heading" aria-labelledby="guide-heading" data-tour-target="guide">
    <div class="dashboard-section-heading">
        <div><p class="workspace-kicker">A clear next step for your study session</p><h2 id="guide-heading">Duno Guide</h2></div>
        <p class="muted">Choose what you are studying and what you want to do. Duno will lay out a practical approach.</p>
    </div>
    <div class="guide-builder" data-study-guide>
        <div class="guide-builder-controls">
            <div class="guide-picker-grid">
                <div>
                    <label id="guide-subject-label" for="guide-subject">Subject <span class="muted">(optional)</span></label>
                    <select id="guide-subject" data-guide-subject data-guide-picker="subject">
                        <option value="" data-guide-subject-id="">General study approach</option>
                        <?php foreach ($guideSubjects as $guideSubject): ?>
                            <?php $guideFamily = \App\Content\StudyGuideOutcomes::programFamily($guideSubject['program_id'] ?? null); ?>
                            <option value="<?= esc($guideFamily ?? 'general') ?>" data-guide-subject-id="<?= esc((string) $guideSubject['id']) ?>"><?= esc($guideSubject['name']) ?></option>
                        <?php endforeach ?>
                    </select>
                </div>
                <div>
                    <label id="guide-topic-label" for="guide-topic">Topic <span class="muted">(optional)</span></label>
                    <select id="guide-topic" data-guide-topic data-guide-picker="topic" disabled>
                        <option value="">Choose a subject first</option>
                        <?php foreach ($guideTopics as $guideTopic): ?>
                            <option value="<?= esc((string) $guideTopic['id']) ?>" data-subject-id="<?= esc((string) $guideTopic['subject_id']) ?>" data-topic-title="<?= esc($guideTopic['title'], 'attr') ?>" hidden><?= esc($guideTopic['title']) ?> · <?= esc($guideTopic['subject_name']) ?></option>
                        <?php endforeach ?>
                    </select>
                </div>
            </div>
            <fieldset class="guide-goal-picker">
                <legend>What do you want to do?</legend>
                <div class="guide-goal-options" role="group" aria-label="Choose a study goal">
                    <button type="button" class="guide-goal-button" data-guide-goal="understand" aria-pressed="false"><span class="guide-goal-icon" aria-hidden="true">01</span><span><strong>Understand an idea</strong><small>Make sense of a concept or connection</small></span></button>
                    <button type="button" class="guide-goal-button" data-guide-goal="remember" aria-pressed="false"><span class="guide-goal-icon" aria-hidden="true">02</span><span><strong>Remember key points</strong><small>Recall important details without notes</small></span></button>
                    <button type="button" class="guide-goal-button" data-guide-goal="solve" aria-pressed="false"><span class="guide-goal-icon" aria-hidden="true">03</span><span><strong>Practice solving</strong><small>Work through a problem step by step</small></span></button>
                </div>
            </fieldset>
        </div>

        <div class="guide-approach" data-guide-approach aria-live="polite" aria-atomic="false">
            <div class="guide-approach-heading">
                <p class="workspace-kicker">YOUR STUDY APPROACH</p>
                <h3 data-guide-approach-title>Pick a goal to begin</h3>
                <p data-guide-approach-summary>Choose one outcome for this session. Change it anytime.</p>
            </div>
            <p class="guide-approach-status" data-guide-outcome-status>Select a subject or topic for tailored guidance. Verified outcomes appear for supported NVSU BSCS, BSIS, and BSIT programs.</p>
            <ol class="guide-method-list" data-guide-method-list>
                <?php foreach ($suggestions as $suggestion): ?>
                    <li>
                        <article class="guide-method-card" data-guide-method="<?= esc($suggestion['id']) ?>" hidden>
                            <span class="guide-method-index" aria-hidden="true"></span>
                            <div class="guide-method-copy">
                                <h4><?= esc($suggestion['title']) ?></h4>
                                <p><?= esc($suggestion['body']) ?></p>
                            </div>
                            <ol class="guide-method-steps">
                                <?php foreach ($suggestion['steps'] as $step): ?><li><?= esc($step) ?></li><?php endforeach ?>
                            </ol>
                            <p class="guide-method-evidence"><?= esc($suggestion['recommendation']) ?></p>
                            <p class="guide-outcome-label" data-guide-outcome-label hidden>Related verified CHED program outcomes</p>
                            <ul class="guide-outcome-list" aria-label="Related CHED program outcomes" hidden>
                                <?php foreach (['cs', 'is', 'it'] as $guideFamily): ?>
                                    <?php foreach (\App\Content\StudyGuideOutcomes::forSuggestion($suggestion['id'], $guideFamily) as $outcome): ?>
                                        <li data-guide-outcome="<?= esc($guideFamily) ?>" hidden><strong><?= esc($outcome['code']) ?>:</strong> <?= esc($outcome['label']) ?></li>
                                    <?php endforeach ?>
                                <?php endforeach ?>
                            </ul>
                            <a href="<?= esc($suggestionSourceUrl) ?>" rel="noopener noreferrer">Read the study-method source</a>
                        </article>
                    </li>
                <?php endforeach ?>
            </ol>
            <p class="guide-approach-empty" data-guide-empty>Choose a goal above to see a short sequence.</p>
        </div>
    </div>
    <p class="guide-outcome-source">Methods are paraphrased from the <a href="<?= esc($suggestionSourceUrl) ?>" rel="noopener noreferrer">WWC practice guide (2007)</a>. Outcome links cite <a href="<?= esc(\App\Content\StudyGuideOutcomes::SOURCE_URL) ?>" rel="noopener noreferrer">CHED CMO No. 25 (2015)</a>. Duno&rsquo;s connections are editorial.</p>
</section>
<script src="<?= esc(duno_asset('assets/js/study-guide-outcomes.js')) ?>" defer></script>
<?= $this->endSection() ?>
