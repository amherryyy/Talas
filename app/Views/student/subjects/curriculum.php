<?= $this->extend('student/subjects/layout') ?>

<?= $this->section('content') ?>
<p class="back-navigation"><a class="button secondary back-link-button" href="<?= esc(site_url('student/subjects')) ?>"><span aria-hidden="true">â†</span> <?= esc(lang('App.backToSubjects')) ?></a></p>
<p class="workspace-kicker"><?= $curriculum === null ? 'Subject list' : 'NVSU subject list' ?></p>
<h1><?= esc($academicProgram['title']) ?></h1>

<?php if ($nvsuErrors !== []): ?>
    <ul class="errors" role="alert">
        <?php foreach ($nvsuErrors as $message): ?><li><?= esc($message) ?></li><?php endforeach ?>
    </ul>
<?php endif ?>

<?php if ($curriculum === null): ?>
    <p>Duno does not have a subject list for this program yet. Add a subject by typing its name.</p>
    <p><a class="button" href="<?= esc(site_url('student/subjects/new')) ?>">Type a subject name</a></p>
<?php else: ?>
    <p class="subject-curriculum-context">Choose subjects from the list for your account program. It is tied to <?= esc($academicProgram['campus']) ?>.</p>
    <p class="muted"><?= esc($curriculum['source_label']) ?> Â· <?= esc($curriculum['publication_date']) ?>. <?= esc($curriculum['note']) ?></p>
    <p><a href="<?= esc($curriculum['source']) ?>" target="_blank" rel="noopener noreferrer">Review the NVSU source (opens in a new tab)</a></p>

    <form method="post" action="<?= esc(site_url('student/subjects/nvsu/import')) ?>" class="curriculum-course-list">
        <?= csrf_field() ?>
        <input type="hidden" name="nvsu_program_id" value="<?= esc($curriculum['id']) ?>">
        <ul class="subject-list">
            <?php foreach ($curriculum['courses'] as $course): ?>
                <li><label><input type="checkbox" name="course_ids[]" value="<?= esc($course['id']) ?>" data-template-course> <?= esc($course['title']) ?></label></li>
            <?php endforeach ?>
        </ul>
        <p class="muted">Choose the subjects you take. Existing subjects and study history are kept; subjects already added are skipped.</p>
        <div class="actions">
            <button type="button" class="secondary" data-template-select-all>Select all</button>
            <button type="button" class="secondary" data-template-select-none>Clear selection</button>
            <button type="submit">Add selected subjects</button>
        </div>
    </form>
    <script src="<?= esc(duno_asset('assets/js/curriculum-template.js')) ?>" defer></script>
<?php endif ?>
<?= $this->endSection() ?>
