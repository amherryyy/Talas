<?= $this->extend('student/subjects/layout') ?>

<?= $this->section('content') ?>
<h1>Topics in <?= esc($subject['name']) ?></h1>
<p class="back-navigation"><a class="button secondary" href="<?= esc(site_url('student/subjects/' . $subject['id'])) ?>"><span aria-hidden="true">←</span> <?= esc(lang('App.backToSubject')) ?></a></p>
<p><a class="button" href="<?= esc(site_url('student/subjects/' . $subject['id'] . '/topics/new')) ?>"><?= esc(lang('App.addTopic')) ?></a></p>

<?php if ($topics === []): ?>
    <p class="muted">No topics yet. Add one to track what you plan to study.</p>
<?php else: ?>
    <ul class="subject-list">
        <?php foreach ($topics as $topic): ?>
            <li>
                <a href="<?= esc(site_url('student/subjects/' . $subject['id'] . '/topics/' . $topic['id'])) ?>"><?= esc($topic['title']) ?></a>
                <span class="muted"> · <?= esc(ucwords(str_replace('_', ' ', $topic['progress']))) ?></span>
                <?php if ($topic['due_date'] !== null): ?>
                    <span class="muted"> · Due <?= esc($topic['due_date']) ?></span>
                <?php endif ?>
            </li>
        <?php endforeach ?>
    </ul>
<?php endif ?>
<?= $this->endSection() ?>
