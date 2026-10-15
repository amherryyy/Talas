<?= $this->extend('student/subjects/layout') ?>

<?= $this->section('content') ?>
<h1><?= esc($topic['title']) ?></h1>
<p><a href="<?= esc(site_url('student/subjects/' . $subject['id'])) ?>">Back to <?= esc($subject['name']) ?></a></p>
<dl>
    <dt>Subject</dt><dd><?= esc($subject['name']) ?></dd>
    <dt>Due date</dt><dd><?= $topic['due_date'] === null ? 'No due date' : esc($topic['due_date']) ?></dd>
    <dt>Importance</dt><dd><?= esc(ucfirst($topic['importance'])) ?></dd>
    <dt>Progress</dt><dd><?= esc(ucwords(str_replace('_', ' ', $topic['progress']))) ?></dd>
</dl>
<p class="muted">A topic used by a saved study session cannot be deleted.</p>
<div class="actions">
    <a class="button" href="<?= esc(site_url('student/subjects/' . $subject['id'] . '/topics/' . $topic['id'] . '/edit')) ?>">Edit</a>
    <form method="post" action="<?= esc(site_url('student/subjects/' . $subject['id'] . '/topics/' . $topic['id'] . '/delete')) ?>">
        <?= csrf_field() ?>
        <button class="danger" type="submit">Delete</button>
    </form>
</div>
<?= $this->endSection() ?>
