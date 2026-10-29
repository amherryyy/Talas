<?= $this->extend('student/subjects/layout') ?>

<?= $this->section('content') ?>
<?php
$isEditing = $subject !== null;
$selectedProgramId = old('program_id', $subject['program_id'] ?? '');
$selectedCourseId = old('course_id', $selectedCourseId ?? '');
?>
<h1><?= $isEditing ? 'Edit subject' : 'Add a subject' ?></h1>
<p class="back-navigation"><a class="button secondary back-link-button" href="<?= esc(site_url('student/subjects')) ?>"><span aria-hidden="true">←</span> <?= esc(lang('App.backToSubjects')) ?></a></p>

<?php if ($errors !== []): ?>
    <ul class="errors" role="alert">
        <?php foreach ($errors as $message): ?>
            <li><?= esc($message) ?></li>
        <?php endforeach ?>
    </ul>
<?php endif ?>

<form method="post" action="<?= esc($isEditing ? site_url('student/subjects/' . $subject['id']) : site_url('student/subjects')) ?>">
    <?= csrf_field() ?>
    <label for="program-id">NVSU course list <span class="muted">(optional)</span></label>
    <select id="program-id" name="program_id" aria-describedby="program-help">
        <option value="">No program selected</option>
        <?php foreach ($nvsuCurricula as $program): ?>
            <option value="<?= esc($program['id']) ?>"<?= $selectedProgramId === $program['id'] ? ' selected' : '' ?>><?= esc($program['title']) ?></option>
        <?php endforeach ?>
    </select>
    <p id="program-help" class="muted">Choose a specific NVSU curriculum reference. Your selection filters the course choices below; the chosen course becomes this subject. Leave it blank to enter a subject manually.</p>

    <label for="course-id">Choose a course from that list <span class="muted">(optional)</span></label>
    <select id="course-id" name="course_id" aria-describedby="course-help">
        <option value="">Type a subject name below</option>
        <?php foreach ($nvsuCurricula as $program): ?>
            <optgroup label="<?= esc($program['title']) ?>" data-program-options="<?= esc($program['id']) ?>">
                <?php foreach ($program['courses'] as $course): ?>
                    <option value="<?= esc($course['id']) ?>" data-program-id="<?= esc($program['id']) ?>" data-course-name="<?= esc($course['title']) ?>"<?= $selectedCourseId === $course['id'] ? ' selected' : '' ?>><?= esc($course['title']) ?></option>
                <?php endforeach ?>
            </optgroup>
        <?php endforeach ?>
    </select>
    <p id="course-help" class="muted">Course lists are historical NVSU references. Confirm current course titles with your campus.</p>

    <label for="name">Subject name</label>
    <input id="name" name="name" type="text" maxlength="120" required autocomplete="off" value="<?= esc(old('name', $subject['name'] ?? '')) ?>" aria-describedby="name-help">
    <p id="name-help" class="muted">Choosing a course fills this in. You can also enter a subject manually. Use up to 120 characters.</p>
    <div class="actions">
        <button type="submit"><?= $isEditing ? 'Save changes' : 'Create subject' ?></button>
    </div>
</form>
<script src="<?= esc(duno_asset('assets/js/nvsu-subject-picker.js')) ?>" defer></script>
<?= $this->endSection() ?>
