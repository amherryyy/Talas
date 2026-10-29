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
        <div class="plan-time-summary"><h2 id="plan-blocks-heading">Your blocks</h2><p role="status">Planned <?= esc((string) $plannedMinutes) ?> of <?= esc((string) $plan['available_minutes']) ?> minutes; <?= esc((string) ((int) $plan['available_minutes'] - $plannedMinutes)) ?> unplanned.</p></div>
        <?php if ($plan['items'] === []): ?>
            <p class="muted">No unfinished topics are available to plan today.</p>
        <?php else: ?>
            <?php
            $trackableItems = array_values(array_filter(
                $plan['items'],
                static fn (array $item): bool => $item['topic_id'] !== null && in_array($item['topic_progress'] ?? null, ['not_started', 'in_progress', 'done'], true),
            ));
            $doneItems = count(array_filter($trackableItems, static fn (array $item): bool => $item['topic_progress'] === 'done'));
            $trackableCount = count($trackableItems);
            $progressPercent = $trackableCount === 0 ? 0 : (int) round(($doneItems / $trackableCount) * 100);
            ?>
            <section class="plan-progress-card" data-plan-progress-card aria-labelledby="plan-progress-heading">
                <header class="plan-progress-header">
                    <span class="plan-progress-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M4 5.5A2.5 2.5 0 0 1 6.5 3H20v16H6.5A2.5 2.5 0 0 0 4 21.5z"/><path d="M4 5.5v16M8 7h8M8 11h6"/></svg></span>
                    <div class="plan-progress-heading"><p class="workspace-kicker">TODAY’S STUDY PLAN</p><h3 id="plan-progress-heading">Track your progress</h3><p>Mark a topic done when you finish it. Unchecking moves it back to in progress.</p></div>
                    <em class="plan-progress-done" data-plan-progress-done<?= $trackableCount > 0 && $doneItems === $trackableCount ? '' : ' hidden' ?>>Done</em>
                </header>
                <div class="plan-progress-meter">
                    <div class="plan-progress-track" data-plan-progress-bar role="progressbar" aria-label="Completed study plan topics" aria-valuemin="0" aria-valuemax="<?= esc((string) max(1, $trackableCount)) ?>" aria-valuenow="<?= esc((string) $doneItems) ?>" aria-valuetext="<?= esc($trackableCount === 0 ? 'No linked topics available to track' : $doneItems . ' of ' . $trackableCount . ' topics done') ?>">
                        <span class="plan-progress-fill" data-plan-progress-fill style="transform: scaleX(<?= esc((string) ($progressPercent / 100)) ?>)"></span>
                    </div>
                    <strong class="plan-progress-percent" data-plan-progress-percent><?= esc((string) $progressPercent) ?>%</strong>
                </div>
                <p class="plan-progress-summary" data-plan-progress-summary><?= esc($trackableCount === 0 ? 'No linked topics available to track.' : $doneItems . ' of ' . $trackableCount . ' topics done') ?></p>
                <p class="plan-progress-status" data-plan-progress-status role="status" aria-live="polite"></p>
                <ol class="plan-progress-list">
                <?php foreach ($plan['items'] as $index => $item): ?>
                    <?php $itemTrackable = $item['topic_id'] !== null && in_array($item['topic_progress'] ?? null, ['not_started', 'in_progress', 'done'], true); ?>
                    <li data-plan-progress-item class="<?= $itemTrackable && $item['topic_progress'] === 'done' ? 'is-done' : '' ?><?= $itemTrackable ? '' : ' is-unavailable' ?>">
                        <?php if ($itemTrackable): ?>
                            <form method="post" action="<?= esc(site_url('student/plan/items/' . $item['id'] . '/progress')) ?>" class="plan-progress-form" data-plan-progress-form data-progress="<?= esc($item['topic_progress']) ?>">
                                <?= csrf_field() ?>
                                <label class="plan-progress-task">
                                    <input class="plan-progress-input" type="checkbox" name="done" value="1" data-plan-progress-toggle<?= $item['topic_progress'] === 'done' ? ' checked' : '' ?>>
                                    <span class="plan-progress-check" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.8" stroke-linecap="round" stroke-linejoin="round"><path d="m5 12 5 5L20 7"/></svg></span>
                                    <span class="plan-progress-copy"><strong><?= esc($item['topic_title']) ?></strong><small><?= esc($item['subject_name']) ?> · <?= esc((string) $item['duration_minutes']) ?> min</small></span>
                                </label>
                                <button class="plan-progress-submit button secondary" type="submit">Save status</button>
                            </form>
                        <?php else: ?>
                            <div class="plan-progress-task plan-progress-task-unavailable"><span class="plan-progress-check" aria-hidden="true">—</span><span class="plan-progress-copy"><strong><?= esc($item['topic_title']) ?></strong><small><?= esc($item['subject_name']) ?> · Topic no longer available</small></span></div>
                        <?php endif ?>
                        <div class="plan-block-editor">
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
                        </div>
                    </li>
                <?php endforeach ?>
                </ol>
            </section>
            <script src="<?= esc(duno_asset('assets/js/plan-progress-card.js')) ?>" defer></script>
        <?php endif ?>
    </section>
<?php endif ?>
<?= $this->endSection() ?>
