<?= $this->extend('student/subjects/layout') ?>

<?= $this->section('content') ?>
<h1>Duno Focus</h1>
<p class="muted">Your unfinished topics, ordered by what may need attention first.</p>

<?php if ($topics === []): ?>
    <section class="empty-state" aria-labelledby="focus-empty-heading">
        <span class="empty-state-mark" aria-hidden="true"><span class="solar-icon solar-icon-target"></span></span>
        <h2 id="focus-empty-heading">Your next step starts with a topic.</h2>
        <p>No unfinished topics are on your list right now. Topics marked done stay out of Duno Focus.</p>
        <div class="actions">
            <a class="button" href="<?= esc(site_url('student/topics')) ?>"><span class="solar-icon solar-icon-book-2" aria-hidden="true"></span>Browse topics</a>
            <a class="button secondary" href="<?= esc(site_url('student/subjects')) ?>">Manage subjects</a>
        </div>
    </section>
<?php else: ?>
    <?php
    $subjectCounts = [];
    foreach ($topics as $focusTopic) {
        $subjectCounts[(int) $focusTopic['subject_id']] = ($subjectCounts[(int) $focusTopic['subject_id']] ?? 0) + 1;
    }
    $selectedTopicId = (int) $topics[0]['id'];
    ?>
    <section class="focus-browser" data-focus-browser aria-label="Duno Focus topics">
        <label class="focus-mobile-subject-filter" for="focus-subject-mobile">
            <span>Subject</span>
            <select id="focus-subject-mobile" data-focus-subject-select>
                <option value="all">All subjects (<?= count($topics) ?>)</option>
                <?php foreach ($subjects as $subject): ?>
                    <option value="<?= esc((string) $subject['id']) ?>"><?= esc($subject['name']) ?> (<?= esc((string) ($subjectCounts[(int) $subject['id']] ?? 0)) ?>)</option>
                <?php endforeach ?>
            </select>
        </label>

        <div class="focus-browser-grid" data-focus-grid>
            <aside class="focus-folders" aria-label="Filter topics by subject">
                <p class="focus-pane-label">Subjects</p>
                <nav class="focus-folder-list" aria-label="Subjects">
                    <button class="focus-folder is-active" type="button" data-focus-folder="all" aria-pressed="true">
                        <span>All subjects</span><span class="focus-folder-count"><?= count($topics) ?></span>
                    </button>
                    <?php foreach ($subjects as $subject): ?>
                        <button class="focus-folder" type="button" data-focus-folder="<?= esc((string) $subject['id']) ?>" aria-pressed="false">
                            <span><?= esc($subject['name']) ?></span><span class="focus-folder-count"><?= esc((string) ($subjectCounts[(int) $subject['id']] ?? 0)) ?></span>
                        </button>
                    <?php endforeach ?>
                </nav>
                <p class="focus-folder-note">Counts include unfinished topics only.</p>
            </aside>

            <section class="focus-topic-list" aria-labelledby="focus-list-title">
                <div class="focus-list-header">
                    <label class="focus-search" for="focus-topic-search">
                        <span class="solar-icon solar-icon-magnifier" aria-hidden="true"></span>
                        <input id="focus-topic-search" type="search" placeholder="Search topics" autocomplete="off" aria-label="Search priority topics" data-focus-search>
                    </label>
                    <div class="focus-list-heading">
                        <h2 id="focus-list-title">Priority topics</h2>
                        <span data-focus-visible-count><?= count($topics) ?></span>
                    </div>
                    <p class="focus-list-hint">Choose a topic to see why it ranks here.</p>
                </div>
                <div class="focus-topic-scroll">
                    <ol class="focus-topic-rows" aria-label="Topics ranked by priority">
                        <?php foreach ($topics as $index => $topic): ?>
                            <?php $isSelected = (int) $topic['id'] === $selectedTopicId; ?>
                            <li data-focus-topic-row data-focus-subject="<?= esc((string) $topic['subject_id']) ?>">
                                <a class="focus-topic-row<?= $isSelected ? ' is-selected' : '' ?>" href="<?= esc(site_url('student/subjects/' . $topic['subject_id'] . '/topics/' . $topic['id'])) ?>" data-focus-topic="<?= esc((string) $topic['id']) ?>"<?= $isSelected ? ' aria-current="page"' : '' ?>>
                                    <span class="focus-topic-row-topline"><strong><?= esc($topic['title']) ?></strong><span class="focus-score"><?= esc((string) $topic['priority_score']) ?></span></span>
                                    <span class="focus-topic-subject"><?= esc($topic['subject_name']) ?></span>
                                    <span class="focus-topic-meta"><span><?= esc(ucfirst(str_replace('_', ' ', (string) $topic['progress']))) ?></span><?php if (! empty($topic['due_date'])): ?><time datetime="<?= esc($topic['due_date']) ?>">Due <?= esc(date('M j', strtotime($topic['due_date']))) ?></time><?php endif ?></span>
                                </a>
                            </li>
                        <?php endforeach ?>
                    </ol>
                    <p class="focus-filter-empty" data-focus-empty hidden>No topics match these filters. Try another subject or search term.</p>
                </div>
            </section>

            <section class="focus-detail-pane" data-focus-detail-pane aria-label="Selected topic details">
                <header class="focus-detail-header">
                    <button class="focus-detail-back" type="button" data-focus-back><span aria-hidden="true">←</span> Back to topics</button>
                    <span class="focus-detail-label">Priority details</span>
                </header>
                <div class="focus-detail-scroll" data-focus-details>
                    <?php foreach ($topics as $topic): ?>
                        <?php $isSelected = (int) $topic['id'] === $selectedTopicId; ?>
                        <article class="focus-detail-content<?= $isSelected ? ' is-current' : '' ?>" data-focus-detail="<?= esc((string) $topic['id']) ?>"<?= $isSelected ? '' : ' hidden' ?> aria-labelledby="focus-detail-title-<?= esc((string) $topic['id']) ?>">
                            <p class="focus-detail-eyebrow"><?= esc($topic['subject_name']) ?></p>
                            <div class="focus-detail-title-row">
                                <h2 id="focus-detail-title-<?= esc((string) $topic['id']) ?>"><?= esc($topic['title']) ?></h2>
                                <span class="focus-score focus-score-large" aria-label="Priority score <?= esc((string) $topic['priority_score']) ?>">Score <?= esc((string) $topic['priority_score']) ?></span>
                            </div>
                            <p class="focus-detail-intro">Duno ranks unfinished topics using importance, due date, and progress.</p>

                            <section class="focus-reason-card" aria-labelledby="focus-reasons-<?= esc((string) $topic['id']) ?>">
                                <h3 id="focus-reasons-<?= esc((string) $topic['id']) ?>">Why this score:</h3>
                                <ul>
                                    <?php foreach ($topic['priority_reasons'] as $reason): ?>
                                        <li><span class="focus-reason-check" aria-hidden="true">✓</span><?= esc($reason) ?></li>
                                    <?php endforeach ?>
                                </ul>
                            </section>

                            <dl class="focus-topic-facts">
                                <div><dt>Importance</dt><dd><?= esc(ucfirst((string) $topic['importance'])) ?></dd></div>
                                <div><dt>Progress</dt><dd><?= esc(ucfirst(str_replace('_', ' ', (string) $topic['progress']))) ?></dd></div>
                                <div><dt>Due date</dt><dd><?= ! empty($topic['due_date']) ? esc(date('M j, Y', strtotime($topic['due_date']))) : 'Not set' ?></dd></div>
                            </dl>
                            <a class="button focus-open-topic" href="<?= esc(site_url('student/subjects/' . $topic['subject_id'] . '/topics/' . $topic['id'])) ?>">Open topic <span aria-hidden="true">→</span></a>
                        </article>
                    <?php endforeach ?>
                    <div class="focus-detail-empty" data-focus-detail-empty hidden>
                        <span class="empty-state-mark" aria-hidden="true"><span class="solar-icon solar-icon-target"></span></span>
                        <h2>No topics in this view</h2>
                        <p>Try another subject or search term to find an unfinished topic.</p>
                    </div>
                </div>
            </section>
        </div>
    </section>
    <script src="<?= esc(duno_asset('assets/js/focus-browser.js')) ?>" defer></script>
<?php endif ?>
<?= $this->endSection() ?>
