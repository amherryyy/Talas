<?= $this->extend('student/subjects/layout') ?>

<?= $this->section('content') ?>
<?php $isEditing = $subject !== null; ?>
<h1><?= $isEditing ? 'Edit subject' : 'Add a subject' ?></h1>
<p class="back-navigation"><a class="button secondary" href="<?= esc(site_url('student/subjects')) ?>"><span aria-hidden="true">←</span> <?= esc(lang('App.backToSubjects')) ?></a></p>

<?php if ($errors !== []): ?>
    <ul class="errors" role="alert">
        <?php foreach ($errors as $message): ?>
            <li><?= esc($message) ?></li>
        <?php endforeach ?>
    </ul>
<?php endif ?>

<form method="post" action="<?= esc($isEditing ? site_url('student/subjects/' . $subject['id']) : site_url('student/subjects')) ?>">
    <?= csrf_field() ?>
    <label for="name">Subject name</label>
    <input id="name" name="name" type="text" maxlength="120" required autocomplete="off" value="<?= esc(old('name', $subject['name'] ?? '')) ?>" aria-describedby="name-help">
    <p id="name-help" class="muted">Use a name up to 120 characters.</p>
    <div class="actions">
        <button type="submit"><?= $isEditing ? 'Save changes' : 'Create subject' ?></button>
    </div>
</form>
<?= $this->endSection() ?>
