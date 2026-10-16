<?= $this->extend('student/subjects/layout') ?>

<?= $this->section('content') ?>
<h1>Delete study session?</h1>
<p>This permanently removes the saved session from your history.</p>
<p><strong><?= esc($studySession['subject_name']) ?></strong><?= $studySession['topic_title'] !== null ? ' · ' . esc($studySession['topic_title']) : '' ?> — <?= esc((string) $studySession['duration_minutes']) ?> minutes</p>
<form method="post" action="<?= esc(site_url('student/sessions/' . $studySession['id'] . '/delete/confirm')) ?>">
    <?= csrf_field() ?>
    <input type="hidden" name="confirm_delete" value="1">
    <div class="actions">
        <button class="danger" type="submit">Confirm delete</button>
        <a class="button secondary" href="<?= esc(site_url('student/sessions')) ?>">Keep session</a>
    </div>
</form>
<?= $this->endSection() ?>
