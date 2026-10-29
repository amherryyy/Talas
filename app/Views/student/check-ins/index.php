<?= $this->extend('student/subjects/layout') ?>

<?= $this->section('content') ?>
<h1>Private check-ins</h1>
<p>Log workload and energy for your own reflection. Only you can see check-ins; Aralytics shows pattern counts. Admin views and printed reports exclude them.</p>
<p class="muted">Multiple check-ins per day are allowed. Entries stay until you delete them.</p>

<?php if ($errors !== []): ?>
    <ul class="errors" role="alert">
        <?php foreach ($errors as $message): ?><li><?= esc($message) ?></li><?php endforeach ?>
    </ul>
<?php endif ?>

<section aria-labelledby="new-check-in-heading">
    <h2 id="new-check-in-heading">Add a check-in</h2>
    <form class="check-in-entry-form" method="post" action="<?= esc(site_url('student/check-ins')) ?>">
        <?= csrf_field() ?>
        <?= view('student/partials/rating_scale', [
            'scaleName' => 'workload_rating',
            'scaleId' => 'workload-rating',
            'scaleLabel' => 'Workload',
            'scaleValue' => (string) old('workload_rating', ''),
            'scaleLowLabel' => 'Very light',
            'scaleHighLabel' => 'Very heavy',
        ]) ?>
        <?= view('student/partials/rating_scale', [
            'scaleName' => 'energy_rating',
            'scaleId' => 'energy-rating',
            'scaleLabel' => 'Energy',
            'scaleValue' => (string) old('energy_rating', ''),
            'scaleLowLabel' => 'Very low',
            'scaleHighLabel' => 'Very high',
        ]) ?>
        <p class="muted">Rate each from 1 to 5. This is optional, not an assessment.</p>
        <button type="submit">Save private check-in</button>
    </form>
</section>

<section aria-labelledby="check-in-summary-heading">
    <h2 id="check-in-summary-heading">Your last 7 days</h2>
    <p class="muted">Your ratings from <?= esc($summary['start_date']) ?> through <?= esc($summary['end_date']) ?>; descriptive only, not an assessment.</p>
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
