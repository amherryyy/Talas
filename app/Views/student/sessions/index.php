<?= $this->extend('student/subjects/layout') ?>

<?= $this->section('content') ?>
<h1>Duno Timer</h1>
<p>Time a session, review its duration, then choose whether to save it.</p>

<?php if ($errors !== []): ?>
    <ul class="errors" role="alert">
        <?php foreach ($errors as $message): ?><li><?= esc($message) ?></li><?php endforeach ?>
    </ul>
<?php endif ?>

<?php if ($subjects === []): ?>
    <p class="notice">Add a subject before recording a study session. <a href="<?= esc(site_url('student/subjects/new')) ?>">Add a subject</a></p>
<?php else: ?>
    <?php $selectedSubject = old('subject_id', ''); ?>
    <?php $selectedTopic = old('topic_id', ''); ?>
    <section aria-labelledby="timer-heading" data-study-timer data-reviewing="<?= $reviewing ? 'true' : 'false' ?>" data-timer-state="<?= $reviewing ? 'stopped' : 'idle' ?>">
        <h2 id="timer-heading">Time a session</h2>
        <form method="post" action="<?= esc(site_url('student/sessions')) ?>" data-session-form>
            <?= csrf_field() ?>
            <div class="timer-workspace-grid">
                <div class="timer-focus-card" data-timer-focus-card>
                    <div class="timer-focus-topline">
                        <span class="timer-focus-kicker">Duno Focus session</span>
                        <span class="timer-state-pill" data-timer-label>Ready when you are</span>
                    </div>
                    <div class="timer-reference-fields">
                        <div>
                            <label for="session-subject">Subject</label>
                            <select id="session-subject" name="subject_id" required data-subject-select>
                                <option value="">Choose a subject</option>
                                <?php foreach ($subjects as $subject): ?>
                                    <option value="<?= esc((string) $subject['id']) ?>"<?= (string) $subject['id'] === (string) $selectedSubject ? ' selected' : '' ?>><?= esc($subject['name']) ?></option>
                                <?php endforeach ?>
                            </select>
                        </div>
                        <div>
                            <label for="session-topic">Topic <span class="muted">(optional)</span></label>
                            <select id="session-topic" name="topic_id" data-topic-select>
                                <option value="">No topic</option>
                                <?php foreach ($topics as $topic): ?>
                                    <option value="<?= esc((string) $topic['id']) ?>" data-subject-id="<?= esc((string) $topic['subject_id']) ?>"<?= (string) $topic['id'] === (string) $selectedTopic ? ' selected' : '' ?>><?= esc($topic['title'] . ' — ' . $topic['subject_name']) ?></option>
                                <?php endforeach ?>
                            </select>
                        </div>
                    </div>
                    <p class="timer-display" role="timer" aria-label="Elapsed study time"><output data-elapsed>00:00:00</output></p>
                    <p class="timer-status" data-timer-status role="status">Choose a subject, then start when you’re ready. Your time is never saved automatically.</p>
                    <div class="actions timer-actions">
                        <button type="button" data-start>Start timer</button>
                        <button type="button" class="button secondary" data-pause disabled>Pause</button>
                        <button type="button" class="button secondary" data-resume disabled>Resume</button>
                        <button type="button" class="button secondary" data-stop disabled>Stop and review</button>
                        <button type="button" class="danger" data-discard disabled>Discard timer</button>
                    </div>
                </div>
                <aside class="timer-rhythm-card" aria-labelledby="timer-rhythm-title">
                    <img class="timer-mascot" src="<?= esc(duno_asset('assets/images/duno-mascot-welcome.webp')) ?>" alt="" width="512" height="536" aria-hidden="true">
                    <span class="timer-rhythm-mark" aria-hidden="true">◷</span>
                    <p class="timer-focus-kicker">A rhythm to try</p>
                    <h3 id="timer-rhythm-title">Give focus a little shape</h3>
                    <div class="timer-rhythm-option">
                        <strong>Pomodoro</strong>
                        <span>25 min focus · 5 min break</span>
                    </div>
                    <div class="timer-rhythm-option">
                        <strong>Longer block</strong>
                        <span>50 min focus · 10 min break</span>
                    </div>
                    <p class="muted">Pick what feels manageable. Pause the timer for breaks; only running time is counted.</p>
                </aside>
            </div>

            <fieldset data-review hidden>
                <legend>Review before saving</legend>
                <label for="duration-minutes">Completed study time (whole minutes)</label>
                <input id="duration-minutes" name="duration_minutes" type="number" min="1" max="480" step="1" required value="<?= esc((string) old('duration_minutes', '')) ?>">
                <p class="muted">Enter 1 to 480 minutes. Nothing is saved until you select Save session.</p>
                <div class="actions">
                    <button type="submit" data-save disabled>Save session</button>
                    <button type="button" class="button secondary" data-discard disabled>Discard this result</button>
                </div>
            </fieldset>
            <noscript><p class="notice">JavaScript is required to run the timer. No session will be recorded from this page without an explicit save.</p></noscript>
        </form>
    </section>
<?php endif ?>

<section aria-labelledby="history-heading">
    <h2 id="history-heading">Saved sessions</h2>
    <?php if ($sessions === []): ?>
        <p class="muted">No study sessions saved yet.</p>
    <?php else: ?>
        <ul class="subject-list">
            <?php foreach ($sessions as $studySession): ?>
                <li>
                    <h3><?= esc($studySession['subject_name']) ?><?= $studySession['topic_title'] !== null ? ' · ' . esc($studySession['topic_title']) : '' ?></h3>
                    <p><?= esc((string) $studySession['duration_minutes']) ?> minutes <span class="muted">· <?= esc(date('M j, Y g:i a', strtotime($studySession['completed_at']))) ?></span></p>
                    <div class="actions">
                        <a class="button secondary" href="<?= esc(site_url('student/sessions/' . $studySession['id'] . '/edit')) ?>">Edit</a>
                        <form method="post" action="<?= esc(site_url('student/sessions/' . $studySession['id'] . '/delete')) ?>">
                            <?= csrf_field() ?><button class="danger" type="submit">Delete</button>
                        </form>
                    </div>
                </li>
            <?php endforeach ?>
        </ul>
    <?php endif ?>
</section>
<script src="<?= esc(duno_asset('assets/js/study-sessions.js')) ?>" defer></script>
<?= $this->endSection() ?>
