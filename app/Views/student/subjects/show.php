<?= $this->extend('student/subjects/layout') ?>

<?= $this->section('content') ?>
<div class="subject-detail-topline">
    <a class="button secondary back-link-button" href="<?= esc(site_url('student/subjects')) ?>"><span aria-hidden="true">←</span> <?= esc(lang('App.backToSubjects')) ?></a>
    <?php if ($programTitle !== null): ?><p class="subject-program-label"><?= esc($programTitle) ?> <span class="muted">· NVSU course reference</span></p><?php endif ?>
</div>

<section class="subject-detail-hero" aria-labelledby="subject-title">
    <div>
        <p class="workspace-kicker">COURSE SUBJECT</p>
        <h1 id="subject-title"><?= esc($subject['name']) ?></h1>
        <p class="subject-detail-note">Keep this subject's topics and study sessions together.</p>
    </div>
    <div class="subject-detail-count" aria-label="<?= count($topics) ?> topics">
        <strong><?= count($topics) ?></strong>
        <span><?= esc(lang('App.topics')) ?></span>
    </div>
</section>

<section class="subject-detail-panel" aria-labelledby="topics-heading">
    <div class="subject-topics-heading">
        <div class="subject-topics-copy">
            <p class="workspace-kicker">STUDY MATERIAL</p>
            <h2 id="topics-heading"><?= esc(lang('App.topics')) ?></h2>
        </div>
        <div class="subject-topics-actions">
            <a class="button secondary" href="<?= esc(site_url('student/subjects/' . $subject['id'] . '/topics')) ?>"><?= esc(lang('App.viewAll')) ?></a>
            <a class="button" href="<?= esc(site_url('student/subjects/' . $subject['id'] . '/topics/new')) ?>"><?= esc(lang('App.addTopic')) ?></a>
        </div>
    </div>
    <?php if ($topics === []): ?>
        <div class="subject-topic-empty">
            <span aria-hidden="true">+</span>
            <div><strong>No topics yet</strong><p>Add a topic when you know what you want to work on.</p></div>
        </div>
    <?php else: ?>
        <?php $topicIndex = 0; ?>
        <ul class="subject-detail-topic-list">
            <?php foreach ($topics as $topic): ?>
                <?php $topicIndex++; ?>
                <?php $topicUrl = site_url('student/subjects/' . $subject['id'] . '/topics/' . $topic['id']); ?>
                <li>
                    <span class="subject-topic-index" aria-hidden="true"><?= esc(str_pad((string) $topicIndex, 2, '0', STR_PAD_LEFT)) ?></span>
                    <div class="subject-topic-copy">
                        <a href="<?= esc($topicUrl) ?>"><?= esc($topic['title']) ?></a>
                        <div class="subject-topic-meta">
                            <span class="subject-topic-status is-<?= esc(str_replace('_', '-', $topic['progress'])) ?>"><?= esc(ucwords(str_replace('_', ' ', $topic['progress']))) ?></span>
                            <?php if ($topic['due_date'] !== null): ?><span>Due <?= esc($topic['due_date']) ?></span><?php endif ?>
                        </div>
                    </div>
                    <a class="subject-topic-open" href="<?= esc($topicUrl) ?>" aria-label="Open <?= esc($topic['title']) ?>">↗</a>
                </li>
            <?php endforeach ?>
        </ul>
    <?php endif ?>
</section>

<div class="actions subject-detail-actions" aria-label="Subject actions">
    <a class="button" href="<?= esc(site_url('student/subjects/' . $subject['id'] . '/edit')) ?>">Edit</a>
    <form method="post" action="<?= esc(site_url('student/subjects/' . $subject['id'] . '/delete')) ?>">
        <?= csrf_field() ?>
        <button class="danger" type="submit">Delete</button>
    </form>
    <p class="muted">Subjects with topics or saved study sessions can't be deleted.</p>
</div>
<?= $this->endSection() ?>
