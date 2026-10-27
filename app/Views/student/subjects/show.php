<?= $this->extend('student/subjects/layout') ?>

<?= $this->section('content') ?>
<h1><?= esc($subject['name']) ?></h1>
<p class="back-navigation"><a class="button secondary" href="<?= esc(site_url('student/subjects')) ?>"><span aria-hidden="true">←</span> <?= esc(lang('App.backToSubjects')) ?></a></p>
<p class="muted">Deleting a subject cannot be undone. Deletion is blocked while it has topics or saved study sessions.</p>
<section aria-labelledby="topics-heading">
    <div class="actions">
        <h2 id="topics-heading"><?= esc(lang('App.topics')) ?></h2>
        <a class="button secondary" href="<?= esc(site_url('student/subjects/' . $subject['id'] . '/topics')) ?>"><?= esc(lang('App.viewAll')) ?></a>
        <a class="button" href="<?= esc(site_url('student/subjects/' . $subject['id'] . '/topics/new')) ?>"><?= esc(lang('App.addTopic')) ?></a>
    </div>
    <?php if ($topics === []): ?>
        <p class="muted">No topics yet. Add one to track what you plan to study.</p>
    <?php else: ?>
        <ul class="subject-list">
            <?php foreach ($topics as $topic): ?>
                <li>
                    <a href="<?= esc(site_url('student/subjects/' . $subject['id'] . '/topics/' . $topic['id'])) ?>"><?= esc($topic['title']) ?></a>
                    <span class="muted"> · <?= esc(ucwords(str_replace('_', ' ', $topic['progress']))) ?></span>
                    <?php if ($topic['due_date'] !== null): ?>
                        <span class="muted"> · Due <?= esc($topic['due_date']) ?></span>
                    <?php endif ?>
                </li>
            <?php endforeach ?>
        </ul>
    <?php endif ?>
</section>
<div class="actions">
    <a class="button" href="<?= esc(site_url('student/subjects/' . $subject['id'] . '/edit')) ?>">Edit</a>
    <form method="post" action="<?= esc(site_url('student/subjects/' . $subject['id'] . '/delete')) ?>">
        <?= csrf_field() ?>
        <button class="danger" type="submit">Delete</button>
    </form>
</div>
<?= $this->endSection() ?>
