<?= $this->extend('student/subjects/layout') ?>

<?= $this->section('content') ?>
<h1>Your subjects</h1>
<p>Manage the subjects you want to study. Only you can see this list.</p>
<p><a class="button" href="<?= esc(site_url('student/subjects/new')) ?>">Add a subject</a></p>

<?php if ($errors !== []): ?>
    <ul class="errors" role="alert">
        <?php foreach ($errors as $error): ?>
            <li><?= esc($error) ?></li>
        <?php endforeach ?>
    </ul>
<?php endif ?>

<form method="post" action="<?= esc(site_url('student/subjects/search')) ?>">
    <?= csrf_field() ?>
    <label for="subject-query">Search subjects</label>
    <input id="subject-query" name="query" type="search" maxlength="120" value="<?= esc($query) ?>" autocomplete="off">
    <div class="actions">
        <button type="submit">Search</button>
        <a class="button secondary" href="<?= esc(site_url('student/subjects')) ?>">Clear</a>
    </div>
</form>

<p class="muted" role="status"><?= count($subjects) ?> subject<?= count($subjects) === 1 ? '' : 's' ?> found.</p>
<?php if ($subjects === []): ?>
    <p class="muted"><?= $query === '' ? 'You have not added any subjects yet.' : 'No subjects match your search.' ?></p>
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
