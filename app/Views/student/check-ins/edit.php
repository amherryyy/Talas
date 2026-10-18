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
<form method="post" action="<?= esc(site_url('student/check-ins/' . $checkIn['id'])) ?>">
    <?= csrf_field() ?>
    <label for="workload-rating">Workload</label>
    <select id="workload-rating" name="workload_rating" required>
        <?php foreach ([1 => '1 — Very light', 2 => '2', 3 => '3', 4 => '4', 5 => '5 — Very heavy'] as $rating => $label): ?>
            <option value="<?= esc((string) $rating) ?>"<?= (string) $workloadRating === (string) $rating ? ' selected' : '' ?>><?= esc($label) ?></option>
        <?php endforeach ?>
    </select>

    <label for="energy-rating">Energy</label>
    <select id="energy-rating" name="energy_rating" required>
        <?php foreach ([1 => '1 — Very low', 2 => '2', 3 => '3', 4 => '4', 5 => '5 — Very high'] as $rating => $label): ?>
            <option value="<?= esc((string) $rating) ?>"<?= (string) $energyRating === (string) $rating ? ' selected' : '' ?>><?= esc($label) ?></option>
        <?php endforeach ?>
    </select>
    <p class="muted">Choose one rating from 1 to 5 for each.</p>
    <div class="actions">
        <button type="submit">Save changes</button>
        <a class="button secondary" href="<?= esc(site_url('student/check-ins')) ?>">Cancel</a>
    </div>
</form>
<?= $this->endSection() ?>
