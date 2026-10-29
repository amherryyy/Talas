<?= $this->extend('student/subjects/layout') ?>

<?= $this->section('content') ?>
<?php $isEditing = $topic !== null; ?>
<h1><?= $isEditing ? 'Edit topic' : 'Add a topic' ?> in <?= esc($subject['name']) ?></h1>
<p class="back-navigation"><a class="button secondary back-link-button" href="<?= esc(site_url('student/subjects/' . $subject['id'])) ?>"><span aria-hidden="true">←</span> <?= esc(lang('App.backToSubject')) ?></a></p>

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

    <?php $importance = old('importance', $topic['importance'] ?? 'medium'); ?>
    <?= view('student/partials/segmented_choice', [
        'choiceId' => 'importance',
        'choiceName' => 'importance',
        'choiceLabel' => 'Importance',
        'choiceValue' => (string) $importance,
        'choiceClass' => 'topic-importance-choice',
        'choiceOptions' => ['low' => 'Low', 'medium' => 'Medium', 'high' => 'High'],
    ]) ?>

    <?php $progress = old('progress', $topic['progress'] ?? 'not_started'); ?>
    <?= view('student/partials/segmented_choice', [
        'choiceId' => 'progress',
        'choiceName' => 'progress',
        'choiceLabel' => 'Progress',
        'choiceValue' => (string) $progress,
        'choiceClass' => 'topic-progress-choice',
        'choiceOptions' => ['not_started' => 'Not started', 'in_progress' => 'In progress', 'done' => 'Done'],
    ]) ?>

    <div class="actions">
        <button type="submit"><?= $isEditing ? 'Save changes' : 'Create topic' ?></button>
    </div>
</form>
<?= $this->endSection() ?>
