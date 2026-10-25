<?= $this->extend('student/subjects/layout') ?>

<?= $this->section('content') ?>
<h1>Your subjects</h1>
<p>Manage the subjects you want to study. Only you can see this list.</p>
<p><a class="button" href="<?= esc(site_url('student/subjects/new')) ?>"><span class="solar-icon solar-icon-add" aria-hidden="true"></span> Add a subject</a></p>

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
        <button type="submit"><span class="solar-icon solar-icon-magnifier" aria-hidden="true"></span> Search</button>
        <a class="button secondary" href="<?= esc(site_url('student/subjects')) ?>">Clear</a>
    </div>
</form>

<p class="muted" role="status"><?= count($subjects) ?> subject<?= count($subjects) === 1 ? '' : 's' ?> found.</p>
<?php if ($subjects === []): ?>
    <section class="empty-state" aria-labelledby="subjects-empty-heading">
        <span class="empty-state-mark" aria-hidden="true"><span class="solar-icon solar-icon-book-2"></span></span>
        <h2 id="subjects-empty-heading"><?= $query === '' ? 'Start with a subject.' : 'No subjects match that search.' ?></h2>
        <p><?= $query === '' ? 'Add a course or class to keep its topics and study sessions together.' : 'Try another search, or clear the filter to see your full subject list.' ?></p>
        <div class="actions">
            <?php if ($query !== ''): ?>
                <a class="button secondary" href="<?= esc(site_url('student/subjects')) ?>">Clear search</a>
            <?php else: ?>
                <a class="button" href="<?= esc(site_url('student/subjects/new')) ?>"><span class="solar-icon solar-icon-add" aria-hidden="true"></span>Add your first subject</a>
            <?php endif ?>
        </div>
    </section>
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
