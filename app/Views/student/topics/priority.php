<?= $this->extend('student/subjects/layout') ?>

<?= $this->section('content') ?>
<h1>Duno Focus</h1>
<p class="muted">Your priority list</p>
<p>Unfinished topics rank by importance, due date, and progress. Each score shows why.</p>

<?php if ($topics === []): ?>
    <section class="empty-state" aria-labelledby="focus-empty-heading">
        <span class="empty-state-mark" aria-hidden="true"><span class="solar-icon solar-icon-target"></span></span>
        <h2 id="focus-empty-heading">Your next step starts with a topic.</h2>
        <p>No unfinished topics are on your list right now. Topics marked done stay out of Duno Focus.</p>
        <div class="actions">
            <a class="button" href="<?= esc(site_url('student/topics')) ?>"><span class="solar-icon solar-icon-book-2" aria-hidden="true"></span>Browse topics</a>
            <a class="button secondary" href="<?= esc(site_url('student/subjects')) ?>">Manage subjects</a>
        </div>
    </section>
<?php else: ?>
    <ol class="subject-list" aria-label="Topics ranked by priority">
        <?php foreach ($topics as $topic): ?>
            <li>
                <h2>
                    <a href="<?= esc(site_url('student/subjects/' . $topic['subject_id'] . '/topics/' . $topic['id'])) ?>"><?= esc($topic['title']) ?></a>
                </h2>
                <p class="muted"><?= esc($topic['subject_name']) ?> · Score <?= esc((string) $topic['priority_score']) ?></p>
                <p>Why this score:</p>
                <ul>
                    <?php foreach ($topic['priority_reasons'] as $reason): ?>
                        <li><?= esc($reason) ?></li>
                    <?php endforeach ?>
                </ul>
            </li>
        <?php endforeach ?>
    </ol>
<?php endif ?>
<?= $this->endSection() ?>
