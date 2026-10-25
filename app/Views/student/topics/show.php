<?= $this->extend('student/subjects/layout') ?>

<?= $this->section('content') ?>
<h1><?= esc($topic['title']) ?></h1>
<p><a href="<?= esc(site_url('student/subjects/' . $subject['id'])) ?>">Back to <?= esc($subject['name']) ?></a></p>
<section class="topic-detail-card" aria-label="Topic details">
    <dl class="topic-detail-list">
        <div><dt>Subject</dt><dd><?= esc($subject['name']) ?></dd></div>
        <div><dt>Due date</dt><dd><?= $topic['due_date'] === null ? 'No due date' : esc($topic['due_date']) ?></dd></div>
        <div><dt>Importance</dt><dd><?= esc(ucfirst($topic['importance'])) ?></dd></div>
        <div><dt>Progress</dt><dd><?= esc(ucwords(str_replace('_', ' ', $topic['progress']))) ?></dd></div>
    </dl>
</section>
<p class="muted">A topic used by a saved study session cannot be deleted.</p>
<div class="actions">
    <a class="button" href="<?= esc(site_url('student/subjects/' . $subject['id'] . '/topics/' . $topic['id'] . '/edit')) ?>">Edit</a>
    <form method="post" action="<?= esc(site_url('student/subjects/' . $subject['id'] . '/topics/' . $topic['id'] . '/delete')) ?>">
        <?= csrf_field() ?>
        <button class="danger" type="submit">Delete topic</button>
    </form>
</div>
<?= $this->endSection() ?>
