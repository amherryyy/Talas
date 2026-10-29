<?= $this->extend('student/subjects/layout') ?>

<?= $this->section('content') ?>
<h1><?= esc(lang('App.yourSubjects')) ?></h1>
<p><?= esc(lang('App.subjectsIntro')) ?></p>
<?php $programTitles = array_column($nvsuCurricula, 'title', 'id'); ?>
<div class="actions">
    <a class="button" href="<?= esc(site_url('student/subjects/new')) ?>"><span class="solar-icon solar-icon-add" aria-hidden="true"></span> <?= esc(lang('App.addSubject')) ?></a>
    <a class="button secondary" href="<?= esc(site_url('student/subjects/templates')) ?>"><?= esc(lang('App.programCatalog')) ?></a>
</div>

<form method="get" action="<?= esc(site_url('student/subjects')) ?>" class="subject-program-filter">
    <label for="subject-program-filter">Show subjects for an NVSU program</label>
    <select id="subject-program-filter" name="program">
        <option value="">All my subjects</option>
        <?php foreach ($nvsuCurricula as $program): ?>
            <option value="<?= esc($program['id']) ?>"<?= $programId === $program['id'] ? ' selected' : '' ?>><?= esc($program['title']) ?></option>
        <?php endforeach ?>
    </select>
    <div class="actions"><button type="submit">Show subjects</button></div>
</form>

<?php if ($errors !== []): ?>
    <ul class="errors" role="alert">
        <?php foreach ($errors as $error): ?>
            <li><?= esc($error) ?></li>
        <?php endforeach ?>
    </ul>
<?php endif ?>

<form method="post" action="<?= esc(site_url('student/subjects/search')) ?>">
    <?= csrf_field() ?>
    <input type="hidden" name="program_id" value="<?= esc($programId) ?>">
    <label for="subject-query"><?= esc(lang('App.searchSubjects')) ?></label>
    <input id="subject-query" name="query" type="search" maxlength="120" value="<?= esc($query) ?>" autocomplete="off">
    <div class="actions">
        <button type="submit"><span class="solar-icon solar-icon-magnifier" aria-hidden="true"></span> <?= esc(lang('App.search')) ?></button>
        <a class="button secondary" href="<?= esc(site_url('student/subjects' . ($programId !== '' ? '?program=' . rawurlencode($programId) : ''))) ?>"><?= esc(lang('App.clear')) ?></a>
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
                <a class="button secondary" href="<?= esc(site_url('student/subjects')) ?>"><?= esc(lang('App.clearSearch')) ?></a>
            <?php else: ?>
                <a class="button" href="<?= esc(site_url('student/subjects/new')) ?>"><span class="solar-icon solar-icon-add" aria-hidden="true"></span><?= esc(lang('App.addFirstSubject')) ?></a>
            <?php endif ?>
        </div>
    </section>
<?php else: ?>
    <ul class="subject-list">
        <?php foreach ($subjects as $subject): ?>
            <li>
                <a href="<?= esc(site_url('student/subjects/' . $subject['id'])) ?>"><?= esc($subject['name']) ?></a>
                <?php if (! empty($subject['program_id']) && isset($programTitles[$subject['program_id']])): ?>
                    <span class="muted subject-program-label"><?= esc($programTitles[$subject['program_id']]) ?></span>
                <?php endif ?>
            </li>
        <?php endforeach ?>
    </ul>
<?php endif ?>
<?= $this->endSection() ?>
