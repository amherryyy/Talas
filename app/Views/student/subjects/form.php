<?= $this->extend('student/subjects/layout') ?>

<?= $this->section('content') ?>
<?php
$isEditing = $subject !== null;
$curriculum = $nvsuCurricula[0] ?? null;
?>
<h1><?= $isEditing ? 'Edit subject' : 'Add a subject' ?></h1>
<p class="back-navigation"><a class="button secondary back-link-button" href="<?= esc(site_url('student/subjects')) ?>"><span aria-hidden="true">â†</span> <?= esc(lang('App.backToSubjects')) ?></a></p>

<?php if ($curriculum !== null): ?>
    <p class="subject-curriculum-context"><?= esc($academicProgram['title']) ?> has an NVSU subject list. Choose the subject you take from that list.</p>
<?php else: ?>
    <p class="subject-curriculum-context">Duno has no subject list for <?= esc($academicProgram['title']) ?> at <?= esc($academicProgram['school']) ?> yet. Type the subject name from your curriculum.</p>
<?php endif ?>

<?php if ($errors !== []): ?>
    <ul class="errors" role="alert">
        <?php foreach ($errors as $message): ?><li><?= esc($message) ?></li><?php endforeach ?>
    </ul>
<?php endif ?>

<form class="subject-form" method="post" action="<?= esc($isEditing ? site_url('student/subjects/' . $subject['id']) : site_url('student/subjects')) ?>">
    <?= csrf_field() ?>
    <?php if ($curriculum !== null): ?>
        <label for="course-id">Subject in your program</label>
        <select id="course-id" name="course_id" required aria-describedby="course-help">
            <option value="">Choose a subject</option>
            <?php foreach ($curriculum['courses'] as $course): ?>
                <option value="<?= esc($course['id']) ?>"<?= $selectedCourseId === $course['id'] || old('course_id') === $course['id'] ? ' selected' : '' ?>><?= esc($course['title']) ?></option>
            <?php endforeach ?>
        </select>
        <p id="course-help" class="muted">This list follows the program saved to your account. Subjects from other programs are not available here.</p>
    <?php else: ?>
        <label for="subject-name">Subject name</label>
        <input id="subject-name" name="name" type="text" maxlength="120" required value="<?= esc(old('name', $subject['name'] ?? '')) ?>" autocomplete="off">
        <p class="muted">Enter a subject from <?= esc($academicProgram['title']) ?>.</p>
    <?php endif ?>
    <div class="actions"><button type="submit"><?= $isEditing ? 'Save subject' : 'Add subject' ?></button></div>
</form>
<?= $this->endSection() ?>
