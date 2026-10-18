<?= $this->extend('student/subjects/layout') ?>

<?= $this->section('content') ?>
<h1>Duno Plan</h1>
<p class="muted">Today’s plan · <?= esc($today) ?>. Blocks follow topic priority; adjust, move, or remove them.</p>

<?php if ($errors !== []): ?>
    <ul class="errors" role="alert">
        <?php foreach ($errors as $error): ?><li><?= esc($error) ?></li><?php endforeach ?>
    </ul>
<?php endif ?>

<?php if ($confirmReplacement): ?>
    <section class="notice" aria-labelledby="replace-heading">
        <h2 id="replace-heading">Replace today’s plan?</h2>
        <p>Generating a new plan will replace its current blocks and edits.</p>
        <form method="post" action="<?= esc(site_url('student/plan/generate')) ?>">
            <?= csrf_field() ?>
            <input type="hidden" name="available_minutes" value="<?= esc((string) $availableMinutes) ?>">
            <input type="hidden" name="confirm_replacement" value="1">
            <button type="submit">Replace plan</button>
            <a class="button secondary" href="<?= esc(site_url('student/plan')) ?>">Keep current plan</a>
        </form>
    </section>
<?php endif ?>

<section aria-labelledby="generate-heading">
    <h2 id="generate-heading">Generate or regenerate</h2>
    <form method="post" action="<?= esc(site_url('student/plan/generate')) ?>">
        <?= csrf_field() ?>
        <label for="available_minutes">Available study time today (minutes)</label>
        <input id="available_minutes" name="available_minutes" type="number" min="15" max="480" step="1" required value="<?= esc((string) ($availableMinutes !== '' ? $availableMinutes : ($plan['available_minutes'] ?? ''))) ?>">
        <p class="muted">Choose 15 to 480 minutes. Each unfinished topic gets a 25-minute block, shortened to fit.</p>
        <button type="submit">Build plan</button>
    </form>
</section>

<?php if ($plan === null): ?>
    <p class="muted">No plan yet for today. Generate one when you know how much time you have.</p>
<?php else: ?>
    <?php $plannedMinutes = array_sum(array_map(static fn (array $item): int => (int) $item['duration_minutes'], $plan['items'])); ?>
    <section aria-labelledby="plan-blocks-heading">
        <h2 id="plan-blocks-heading">Your blocks</h2>
        <p role="status">Planned <?= esc((string) $plannedMinutes) ?> of <?= esc((string) $plan['available_minutes']) ?> minutes; <?= esc((string) ((int) $plan['available_minutes'] - $plannedMinutes)) ?> unplanned.</p>
        <?php if ($plan['items'] === []): ?>
            <p class="muted">No unfinished topics are available to plan today.</p>
        <?php else: ?>
            <ol class="subject-list">
                <?php foreach ($plan['items'] as $index => $item): ?>
                    <li>
                        <h3><?= esc($item['topic_title']) ?></h3>
                        <p class="muted"><?= esc($item['subject_name']) ?></p>
                        <form method="post" action="<?= esc(site_url('student/plan/items/' . $item['id'] . '/duration')) ?>">
                            <?= csrf_field() ?>
                            <label for="duration-<?= esc((string) $item['id']) ?>">Block duration (minutes)</label>
                            <input id="duration-<?= esc((string) $item['id']) ?>" name="duration_minutes" type="number" min="1" max="480" step="1" required value="<?= esc((string) $item['duration_minutes']) ?>">
                            <button class="button secondary" type="submit">Save duration</button>
                        </form>
                        <div class="actions">
                            <form method="post" action="<?= esc(site_url('student/plan/items/' . $item['id'] . '/move/up')) ?>">
                                <?= csrf_field() ?><button class="button secondary" type="submit"<?= $index === 0 ? ' disabled' : '' ?>>Move up</button>
                            </form>
                            <form method="post" action="<?= esc(site_url('student/plan/items/' . $item['id'] . '/move/down')) ?>">
                                <?= csrf_field() ?><button class="button secondary" type="submit"<?= $index === count($plan['items']) - 1 ? ' disabled' : '' ?>>Move down</button>
                            </form>
                            <form method="post" action="<?= esc(site_url('student/plan/items/' . $item['id'] . '/remove')) ?>">
                                <?= csrf_field() ?><button class="danger" type="submit">Remove block</button>
                            </form>
                        </div>
                    </li>
                <?php endforeach ?>
            </ol>
        <?php endif ?>
    </section>
<?php endif ?>
<?= $this->endSection() ?>
