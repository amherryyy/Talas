<?= $this->extend('student/subjects/layout') ?>

<?= $this->section('content') ?>
<h1>Search topics</h1>
<p>Search your topic titles and subject names, then narrow the results with filters.</p>

<?php if ($errors !== []): ?>
    <ul class="errors" role="alert">
        <?php foreach ($errors as $error): ?>
            <li><?= esc($error) ?></li>
        <?php endforeach ?>
    </ul>
<?php endif ?>

<form method="post" action="<?= esc(site_url('student/topics')) ?>">
    <?= csrf_field() ?>
    <label for="topic-query">Search</label>
    <input id="topic-query" name="query" type="search" maxlength="120" value="<?= esc($filters['query']) ?>" autocomplete="off">

    <label for="filter-subject">Subject</label>
    <select id="filter-subject" name="subject_id">
        <option value="">All subjects</option>
        <?php foreach ($subjects as $subject): ?>
            <option value="<?= esc((string) $subject['id']) ?>" <?= $filters['subject_id'] === (int) $subject['id'] ? 'selected' : '' ?>><?= esc($subject['name']) ?></option>
        <?php endforeach ?>
    </select>

    <label for="filter-importance">Importance</label>
    <select id="filter-importance" name="importance">
        <option value="">Any importance</option>
        <?php foreach (['low' => 'Low', 'medium' => 'Medium', 'high' => 'High'] as $value => $label): ?>
            <option value="<?= esc($value) ?>" <?= $filters['importance'] === $value ? 'selected' : '' ?>><?= esc($label) ?></option>
        <?php endforeach ?>
    </select>

    <label for="filter-progress">Progress</label>
    <select id="filter-progress" name="progress">
        <option value="">Any progress</option>
        <?php foreach (['not_started' => 'Not started', 'in_progress' => 'In progress', 'done' => 'Done'] as $value => $label): ?>
            <option value="<?= esc($value) ?>" <?= $filters['progress'] === $value ? 'selected' : '' ?>><?= esc($label) ?></option>
        <?php endforeach ?>
    </select>

    <label for="filter-due-state">Due date</label>
    <select id="filter-due-state" name="due_state">
        <option value="">Any due date</option>
        <option value="undated" <?= $filters['due_state'] === 'undated' ? 'selected' : '' ?>>No due date</option>
        <option value="upcoming" <?= $filters['due_state'] === 'upcoming' ? 'selected' : '' ?>>Upcoming</option>
        <option value="overdue" <?= $filters['due_state'] === 'overdue' ? 'selected' : '' ?>>Overdue</option>
    </select>

    <div class="actions">
        <button type="submit">Apply filters</button>
        <a class="button secondary" href="<?= esc(site_url('student/topics')) ?>">Clear</a>
    </div>
</form>

<p class="muted" role="status"><?= count($topics) ?> topic<?= count($topics) === 1 ? '' : 's' ?> found.</p>
<?php if ($topics === []): ?>
    <p class="muted">No topics match those search and filter choices.</p>
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
