<?= $this->extend('student/subjects/layout') ?>

<?= $this->section('content') ?>
<?php $isEditing = $topic !== null; ?>
<h1><?= $isEditing ? 'Edit topic' : 'Add a topic' ?> in <?= esc($subject['name']) ?></h1>
<p class="back-navigation"><a class="button secondary" href="<?= esc(site_url('student/subjects/' . $subject['id'])) ?>"><span aria-hidden="true">←</span> <?= esc(lang('App.backToSubject')) ?></a></p>

<?php if ($errors !== []): ?>
    <ul class="errors" role="alert">
        <?php foreach ($errors as $message): ?>
            <li><?= esc($message) ?></li>
        <?php endforeach ?>
    </ul>
<?php endif ?>

<?php $action = $isEditing
    ? site_url('student/subjects/' . $subject['id'] . '/topics/' . $topic['id'])
    : site_url('student/subjects/' . $subject['id'] . '/topics'); ?>
<form class="topic-form" method="post" action="<?= esc($action) ?>">
    <?= csrf_field() ?>
    <label for="title">Topic title</label>
    <input id="title" name="title" type="text" maxlength="120" required autocomplete="off" value="<?= esc(old('title', $topic['title'] ?? '')) ?>" aria-describedby="title-help">
    <p id="title-help" class="muted">Use a title up to 120 characters. Duplicate titles are allowed.</p>

    <label for="due_date">Due date <span class="muted">(optional)</span></label>
    <input id="due_date" name="due_date" type="date" value="<?= esc(old('due_date', $topic['due_date'] ?? '')) ?>">

    <label for="importance">Importance</label>
    <?php $importance = old('importance', $topic['importance'] ?? 'medium'); ?>
    <select id="importance" name="importance" required>
        <option value="low" <?= $importance === 'low' ? 'selected' : '' ?>>Low</option>
        <option value="medium" <?= $importance === 'medium' ? 'selected' : '' ?>>Medium</option>
        <option value="high" <?= $importance === 'high' ? 'selected' : '' ?>>High</option>
    </select>

    <label for="progress">Progress</label>
    <?php $progress = old('progress', $topic['progress'] ?? 'not_started'); ?>
    <select id="progress" name="progress" required>
        <option value="not_started" <?= $progress === 'not_started' ? 'selected' : '' ?>>Not started</option>
        <option value="in_progress" <?= $progress === 'in_progress' ? 'selected' : '' ?>>In progress</option>
        <option value="done" <?= $progress === 'done' ? 'selected' : '' ?>>Done</option>
    </select>

    <div class="actions">
        <button type="submit"><?= $isEditing ? 'Save changes' : 'Create topic' ?></button>
    </div>
</form>
<?= $this->endSection() ?>
