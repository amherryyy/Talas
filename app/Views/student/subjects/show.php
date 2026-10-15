<?= $this->extend('student/subjects/layout') ?>

<?= $this->section('content') ?>
<h1><?= esc($subject['name']) ?></h1>
<p><a href="<?= esc(site_url('student/subjects')) ?>">Back to subjects</a></p>
<p class="muted">Deleting a subject cannot be undone. Deletion is blocked while it has topics.</p>
<div class="actions">
    <a class="button" href="<?= esc(site_url('student/subjects/' . $subject['id'] . '/edit')) ?>">Edit</a>
    <form method="post" action="<?= esc(site_url('student/subjects/' . $subject['id'] . '/delete')) ?>">
        <?= csrf_field() ?>
        <button class="danger" type="submit">Delete</button>
    </form>
</div>
<?= $this->endSection() ?>
