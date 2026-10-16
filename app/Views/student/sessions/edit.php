<?= $this->extend('student/subjects/layout') ?>

<?= $this->section('content') ?>
<h1>Edit study session</h1>
<p><a href="<?= esc(site_url('student/sessions')) ?>">Back to study sessions</a></p>
<p class="muted">Completed <?= esc(date('M j, Y g:i a', strtotime($studySession['completed_at']))) ?>. The completion time stays unchanged when you edit this record.</p>

<?php if ($errors !== []): ?>
    <ul class="errors" role="alert">
        <?php foreach ($errors as $message): ?><li><?= esc($message) ?></li><?php endforeach ?>
    </ul>
<?php endif ?>

<?php $selectedSubject = old('subject_id', (string) $studySession['subject_id']); ?>
<?php $selectedTopic = old('topic_id', (string) ($studySession['topic_id'] ?? '')); ?>
<form method="post" action="<?= esc(site_url('student/sessions/' . $studySession['id'])) ?>" data-session-edit-form>
    <?= csrf_field() ?>
    <label for="session-subject">Subject</label>
    <select id="session-subject" name="subject_id" required data-subject-select>
        <option value="">Choose a subject</option>
        <?php foreach ($subjects as $subject): ?>
            <option value="<?= esc((string) $subject['id']) ?>"<?= (string) $subject['id'] === (string) $selectedSubject ? ' selected' : '' ?>><?= esc($subject['name']) ?></option>
        <?php endforeach ?>
    </select>
    <label for="session-topic">Topic (optional)</label>
    <select id="session-topic" name="topic_id" data-topic-select>
        <option value="">No topic</option>
        <?php foreach ($topics as $topic): ?>
            <option value="<?= esc((string) $topic['id']) ?>" data-subject-id="<?= esc((string) $topic['subject_id']) ?>"<?= (string) $topic['id'] === (string) $selectedTopic ? ' selected' : '' ?>><?= esc($topic['title'] . ' — ' . $topic['subject_name']) ?></option>
        <?php endforeach ?>
    </select>
    <label for="duration-minutes">Duration (whole minutes)</label>
    <input id="duration-minutes" name="duration_minutes" type="number" min="1" max="480" step="1" required value="<?= esc((string) old('duration_minutes', (string) $studySession['duration_minutes'])) ?>">
    <p class="muted">Choose 1 to 480 minutes. The server checks that the subject and optional topic belong to you.</p>
    <div class="actions">
        <button type="submit">Save changes</button>
        <a class="button secondary" href="<?= esc(site_url('student/sessions')) ?>">Cancel</a>
    </div>
</form>
<script src="<?= esc(base_url('assets/js/study-sessions.js')) ?>" defer></script>
<?= $this->endSection() ?>
