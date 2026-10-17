<?= $this->extend('student/subjects/layout') ?>

<?= $this->section('content') ?>
<?php $recordedAt = new DateTimeImmutable($checkIn['recorded_at'], new DateTimeZone(config('App')->appTimezone)); ?>
<h1>Delete private check-in?</h1>
<p>This permanently deletes the check-in saved on <time datetime="<?= esc($recordedAt->format(DateTimeInterface::ATOM)) ?>"><?= esc($recordedAt->format('M j, Y, g:i a')) ?></time>.</p>
<p>Workload: <?= esc((string) $checkIn['workload_rating']) ?>/5 · Energy: <?= esc((string) $checkIn['energy_rating']) ?>/5</p>
<form method="post" action="<?= esc(site_url('student/check-ins/' . $checkIn['id'] . '/delete/confirm')) ?>">
    <?= csrf_field() ?>
    <input type="hidden" name="confirm_delete" value="1">
    <div class="actions">
        <button class="danger" type="submit">Delete check-in</button>
        <a class="button secondary" href="<?= esc(site_url('student/check-ins')) ?>">Cancel</a>
    </div>
</form>
<?= $this->endSection() ?>
