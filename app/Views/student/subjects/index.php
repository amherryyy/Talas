<?= $this->extend('student/subjects/layout') ?>

<?= $this->section('content') ?>
<h1>Your subjects</h1>
<p>Organize the subjects you want to study. Only you can access your list.</p>
<p><a class="button" href="<?= esc(site_url('student/subjects/new')) ?>">Add a subject</a></p>

<?php if ($subjects === []): ?>
    <p class="muted">You have not added any subjects yet.</p>
<?php else: ?>
    <ul class="subject-list">
        <?php foreach ($subjects as $subject): ?>
            <li>
                <a href="<?= esc(site_url('student/subjects/' . $subject['id'])) ?>"><?= esc($subject['name']) ?></a>
            </li>
        <?php endforeach ?>
    </ul>
<?php endif ?>
<?= $this->endSection() ?>
