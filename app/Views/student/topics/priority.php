<?= $this->extend('student/subjects/layout') ?>

<?= $this->section('content') ?>
<h1>Duno Focus</h1>
<p class="muted">Your priority list</p>
<p>Unfinished topics are ranked using their importance, due date, and progress. Each score shows how it was calculated.</p>

<?php if ($topics === []): ?>
    <p class="muted">No unfinished topics yet. Topics marked done are left off this list.</p>
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
