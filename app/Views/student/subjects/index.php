<?= $this->extend('student/subjects/layout') ?>

<?= $this->section('content') ?>
<h1><?= esc(lang('App.yourSubjects')) ?></h1>
<p>Your subjects are for <?= esc($academicProgram['title']) ?>.
    <?php if ($academicProgram['curriculum_id'] !== null): ?>Choose subjects from its NVSU list.<?php else: ?>Type your subject names because an NVSU list is not available for this program.<?php endif ?>
</p>
<div class="actions">
    <a class="button" href="<?= esc(site_url('student/subjects/new')) ?>"><span class="solar-icon solar-icon-add" aria-hidden="true"></span> <?= esc(lang('App.addSubject')) ?></a>
    <?php if ($academicProgram['curriculum_id'] !== null): ?>
        <a class="button secondary" href="<?= esc(site_url('student/subjects/templates')) ?>">View my subject list</a>
    <?php endif ?>
</div>

<?php if ($errors !== []): ?>
    <ul class="errors" role="alert">
        <?php foreach ($errors as $error): ?>
            <li><?= esc($error) ?></li>
        <?php endforeach ?>
    </ul>
<?php endif ?>

<form method="post" action="<?= esc(site_url('student/subjects/search')) ?>">
    <?= csrf_field() ?>
    <label for="subject-query"><?= esc(lang('App.searchSubjects')) ?></label>
    <input id="subject-query" name="query" type="search" maxlength="120" value="<?= esc($query) ?>" autocomplete="off">
    <div class="actions">
        <button type="submit"><span class="solar-icon solar-icon-magnifier" aria-hidden="true"></span> <?= esc(lang('App.search')) ?></button>
        <a class="button secondary" href="<?= esc(site_url('student/subjects')) ?>"><?= esc(lang('App.clear')) ?></a>
    </div>
</form>

<p class="muted" role="status"><?= count($subjects) ?> subject<?= count($subjects) === 1 ? '' : 's' ?> found.</p>
<?php if ($subjects === []): ?>
    <section class="empty-state" aria-labelledby="subjects-empty-heading">
        <span class="empty-state-mark" aria-hidden="true"><span class="solar-icon solar-icon-book-2"></span></span>
        <h2 id="subjects-empty-heading"><?= $query === '' ? 'Add a subject to get started.' : 'No subjects match that search.' ?></h2>
        <p><?= $query === '' ? 'Subjects stay with your selected program. Each one can have its own topics and study history.' : 'Try another search, or clear it to see your subjects.' ?></p>
        <div class="actions">
            <?php if ($query !== ''): ?>
                <a class="button secondary" href="<?= esc(site_url('student/subjects')) ?>"><?= esc(lang('App.clearSearch')) ?></a>
            <?php else: ?>
                <a class="button" href="<?= esc(site_url('student/subjects/new')) ?>"><span class="solar-icon solar-icon-add" aria-hidden="true"></span><?= esc(lang('App.addFirstSubject')) ?></a>
            <?php endif ?>
        </div>
    </section>
<?php else: ?>
    <ul class="subject-catalog-list">
        <?php foreach ($subjects as $subjectIndex => $subject): ?>
            <li>
                <?php $subjectName = trim((string) $subject['name']); ?>
                <?php if ($subjectName === '' || preg_match('/[\p{L}\p{N}]/u', $subjectName) !== 1) { $subjectName = 'Untitled subject'; } ?>
                <a class="subject-catalog-entry" href="<?= esc(site_url('student/subjects/' . $subject['id'])) ?>">
                    <span class="subject-catalog-index" aria-hidden="true"><?= esc(str_pad((string) ($subjectIndex + 1), 2, '0', STR_PAD_LEFT)) ?></span>
                    <span class="subject-catalog-name"><?= esc($subjectName) ?></span>
                    <span class="subject-catalog-arrow" aria-hidden="true">&#8594;</span>
                </a>
            </li>
        <?php endforeach ?>
    </ul>
<?php endif ?>
<?= $this->endSection() ?>
