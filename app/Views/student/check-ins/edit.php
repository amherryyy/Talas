<?= $this->extend('student/subjects/layout') ?>

<?= $this->section('content') ?>
<h1>Edit private check-in</h1>
<p>The saved time stays the same when you edit ratings.</p>

<?php if ($errors !== []): ?>
    <ul class="errors" role="alert">
        <?php foreach ($errors as $message): ?><li><?= esc($message) ?></li><?php endforeach ?>
    </ul>
<?php endif ?>

<?php $workloadRating = old('workload_rating', (string) $checkIn['workload_rating']); ?>
<?php $energyRating = old('energy_rating', (string) $checkIn['energy_rating']); ?>
<form class="check-in-entry-form" method="post" action="<?= esc(site_url('student/check-ins/' . $checkIn['id'])) ?>">
    <?= csrf_field() ?>
    <?= view('student/partials/rating_scale', [
        'scaleName' => 'workload_rating',
        'scaleId' => 'workload-rating',
        'scaleLabel' => 'Workload',
        'scaleValue' => (string) $workloadRating,
        'scaleLowLabel' => 'Very light',
        'scaleHighLabel' => 'Very heavy',
    ]) ?>
    <?= view('student/partials/rating_scale', [
        'scaleName' => 'energy_rating',
        'scaleId' => 'energy-rating',
        'scaleLabel' => 'Energy',
        'scaleValue' => (string) $energyRating,
        'scaleLowLabel' => 'Very low',
        'scaleHighLabel' => 'Very high',
    ]) ?>
    <p class="muted">Choose one rating from 1 to 5 for each.</p>
    <div class="actions">
        <button type="submit">Save changes</button>
        <a class="button secondary" href="<?= esc(site_url('student/check-ins')) ?>">Cancel</a>
    </div>
</form>
<?= $this->endSection() ?>
