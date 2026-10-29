<?= $this->extend('student/subjects/layout') ?>

<?= $this->section('content') ?>
<h1>Find a topic</h1>
<p class="muted">Search your topics or narrow the list by subject, progress, or due date.</p>

<?php if ($errors !== []): ?>
    <ul class="errors" role="alert">
        <?php foreach ($errors as $error): ?>
            <li><?= esc($error) ?></li>
        <?php endforeach ?>
    </ul>
<?php endif ?>

<form class="topic-filter-form" method="post" action="<?= esc(site_url('student/topics')) ?>">
    <?= csrf_field() ?>
    <div class="topic-filter-search">
        <label for="topic-query">Search topics and subjects</label>
        <input id="topic-query" name="query" type="search" maxlength="120" value="<?= esc($filters['query']) ?>" autocomplete="off">
    </div>
    <div class="topic-filter-grid">
        <div>
            <label for="filter-subject">Subject</label>
            <select id="filter-subject" name="subject_id">
                <option value="">All subjects</option>
                <?php foreach ($subjects as $subject): ?>
                    <option value="<?= esc((string) $subject['id']) ?>" <?= $filters['subject_id'] === (int) $subject['id'] ? 'selected' : '' ?>><?= esc($subject['name']) ?></option>
                <?php endforeach ?>
            </select>
        </div>
        <?= view('student/partials/segmented_choice', [
            'choiceId' => 'filter-importance',
            'choiceName' => 'importance',
            'choiceLabel' => 'Importance',
            'choiceValue' => (string) $filters['importance'],
            'choiceClass' => 'topic-filter-choice',
            'choiceOptions' => ['' => 'Any', 'low' => 'Low', 'medium' => 'Medium', 'high' => 'High'],
        ]) ?>
        <?= view('student/partials/segmented_choice', [
            'choiceId' => 'filter-progress',
            'choiceName' => 'progress',
            'choiceLabel' => 'Progress',
            'choiceValue' => (string) $filters['progress'],
            'choiceClass' => 'topic-filter-choice',
            'choiceOptions' => ['' => 'Any', 'not_started' => 'Not started', 'in_progress' => 'In progress', 'done' => 'Done'],
        ]) ?>
        <div>
            <label for="filter-due-state">Due date</label>
            <select id="filter-due-state" name="due_state">
                <option value="">Any due date</option>
                <option value="undated" <?= $filters['due_state'] === 'undated' ? 'selected' : '' ?>>No due date</option>
                <option value="upcoming" <?= $filters['due_state'] === 'upcoming' ? 'selected' : '' ?>>Upcoming</option>
                <option value="overdue" <?= $filters['due_state'] === 'overdue' ? 'selected' : '' ?>>Overdue</option>
            </select>
        </div>
    </div>

    <div class="actions">
        <button type="submit"><span class="solar-icon solar-icon-filter" aria-hidden="true"></span> Apply filters</button>
        <a class="button secondary" href="<?= esc(site_url('student/topics')) ?>">Clear</a>
    </div>
</form>

<p class="muted" role="status"><?= count($topics) ?> topic<?= count($topics) === 1 ? '' : 's' ?> found.</p>
<?php if ($topics === []): ?>
    <?php
    $hasFilters = $filters['query'] !== '' || $filters['subject_id'] !== null || $filters['importance'] !== '' || $filters['progress'] !== '' || $filters['due_state'] !== '';
    ?>
    <section class="empty-state" aria-labelledby="topics-empty-heading">
        <span class="empty-state-mark" aria-hidden="true"><span class="solar-icon solar-icon-book-2"></span></span>
        <h2 id="topics-empty-heading"><?= $hasFilters ? 'No topics match these filters.' : 'Topics live inside your subjects.' ?></h2>
        <p><?= $hasFilters ? 'Clear the filters or try a broader search to see more topics.' : 'Choose a subject to add topics, set due dates, and track progress.' ?></p>
        <div class="actions">
            <?php if ($hasFilters): ?>
                <a class="button secondary" href="<?= esc(site_url('student/topics')) ?>">Clear filters</a>
            <?php endif ?>
            <?php if ($subjects === []): ?>
                <a class="button" href="<?= esc(site_url('student/subjects/new')) ?>"><span class="solar-icon solar-icon-add" aria-hidden="true"></span>Add your first subject</a>
            <?php else: ?>
                <a class="button" href="<?= esc(site_url('student/subjects')) ?>">Browse subjects</a>
            <?php endif ?>
        </div>
    </section>
<?php else: ?>
    <ul class="subject-list">
        <?php foreach ($topics as $topic): ?>
            <li>
                <a href="<?= esc(site_url('student/subjects/' . $topic['subject_id'] . '/topics/' . $topic['id'])) ?>"><?= esc($topic['title']) ?></a>
                <span class="muted"><?= esc($topic['subject_name']) ?> · <?= esc(ucwords(str_replace('_', ' ', $topic['progress']))) ?></span>
                <?php if ($topic['due_date'] !== null): ?>
                    <span class="muted"> · Due <?= esc($topic['due_date']) ?></span>
                <?php endif ?>
            </li>
        <?php endforeach ?>
    </ul>
<?php endif ?>
<?= $this->endSection() ?>
