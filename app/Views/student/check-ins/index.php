<?= $this->extend('student/subjects/layout') ?>

<?= $this->section('content') ?>
<h1>Private check-ins</h1>
<p>Record a quick snapshot of how heavy your workload feels and your current energy. Your entries are visible only to you. Aralytics may show a private count of rating patterns; individual entries and that summary are excluded from admin views and printed reports.</p>
<p class="muted">You may save more than one check-in a day. Entries stay here until you delete them.</p>

<?php if ($errors !== []): ?>
    <ul class="errors" role="alert">
        <?php foreach ($errors as $message): ?><li><?= esc($message) ?></li><?php endforeach ?>
    </ul>
<?php endif ?>

<section aria-labelledby="new-check-in-heading">
    <h2 id="new-check-in-heading">Add a check-in</h2>
    <form method="post" action="<?= esc(site_url('student/check-ins')) ?>">
        <?= csrf_field() ?>
        <label for="workload-rating">Workload</label>
        <select id="workload-rating" name="workload_rating" required>
            <option value="">Choose a rating</option>
            <?php foreach ([1 => '1 — Very light', 2 => '2', 3 => '3', 4 => '4', 5 => '5 — Very heavy'] as $rating => $label): ?>
                <option value="<?= esc((string) $rating) ?>"<?= (string) old('workload_rating', '') === (string) $rating ? ' selected' : '' ?>><?= esc($label) ?></option>
            <?php endforeach ?>
        </select>

        <label for="energy-rating">Energy</label>
        <select id="energy-rating" name="energy_rating" required>
            <option value="">Choose a rating</option>
            <?php foreach ([1 => '1 — Very low', 2 => '2', 3 => '3', 4 => '4', 5 => '5 — Very high'] as $rating => $label): ?>
                <option value="<?= esc((string) $rating) ?>"<?= (string) old('energy_rating', '') === (string) $rating ? ' selected' : '' ?>><?= esc($label) ?></option>
            <?php endforeach ?>
        </select>
        <p class="muted">Choose one rating from 1 to 5 for each. This is optional and is not an assessment.</p>
        <button type="submit">Save private check-in</button>
    </form>
</section>

<section aria-labelledby="check-in-summary-heading">
    <h2 id="check-in-summary-heading">Your last 7 days</h2>
    <p class="muted">This private summary counts your check-ins from <?= esc($summary['start_date']) ?> through <?= esc($summary['end_date']) ?>. It describes your ratings only; it is not an assessment.</p>
    <?php if ($summary['check_in_count'] === 0): ?>
        <p>You have no check-ins from the last 7 days yet.</p>
    <?php else: ?>
        <p>Of <?= esc((string) $summary['check_in_count']) ?> check-ins, <?= esc((string) $summary['high_workload_low_energy_count']) ?> had both a high workload rating (4–5) and a low energy rating (1–2).</p>
    <?php endif ?>
</section>

<section aria-labelledby="saved-check-ins-heading">
    <h2 id="saved-check-ins-heading">Your saved check-ins</h2>
    <?php if ($checkIns === []): ?>
        <p class="muted">You have no saved check-ins.</p>
    <?php else: ?>
        <ul class="subject-list">
            <?php foreach ($checkIns as $checkIn): ?>
                <?php $recordedAt = new DateTimeImmutable($checkIn['recorded_at'], new DateTimeZone(config('App')->appTimezone)); ?>
                <li>
                    <p><strong><time datetime="<?= esc($recordedAt->format(DateTimeInterface::ATOM)) ?>"><?= esc($recordedAt->format('M j, Y, g:i a')) ?></time></strong></p>
                    <p>Workload: <?= esc((string) $checkIn['workload_rating']) ?>/5 · Energy: <?= esc((string) $checkIn['energy_rating']) ?>/5</p>
                    <div class="actions">
                        <a class="button secondary" href="<?= esc(site_url('student/check-ins/' . $checkIn['id'] . '/edit')) ?>">Edit</a>
                        <a class="button danger" href="<?= esc(site_url('student/check-ins/' . $checkIn['id'] . '/delete')) ?>">Delete</a>
                    </div>
                </li>
            <?php endforeach ?>
        </ul>
    <?php endif ?>
</section>
<?= $this->endSection() ?>
