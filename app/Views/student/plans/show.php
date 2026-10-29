<?= $this->extend('student/subjects/layout') ?>

<?= $this->section('content') ?>
<h1>Duno Plan</h1>
<p class="muted plan-desktop-intro">Plan for today · <?= esc($today) ?>. Blocks follow topic priority; adjust, move, or remove them.</p>

<section class="plan-mobile-agenda" aria-labelledby="agenda-heading" data-plan-agenda>
    <?php $selectedWeekIndex = array_search(true, array_column($weekDays, 'selected'), true); ?>
    <header class="plan-agenda-header">
        <div><p class="workspace-kicker">DUNO PLAN</p><h2 id="agenda-heading"><?= esc($selectedDateLabel) ?></h2></div>
        <button class="plan-agenda-add" type="button" data-agenda-open<?= $topicOptions === [] ? ' disabled' : '' ?> aria-haspopup="dialog" aria-controls="agenda-add-sheet"><span aria-hidden="true">+</span><span>Add block</span></button>
    </header>
    <div class="plan-agenda-week-heading"><a href="<?= esc(site_url('student/plan?date=' . $previousWeekDate)) ?>" aria-label="Previous week">‹</a><strong><?= esc($weekLabel) ?></strong><a href="<?= esc(site_url('student/plan?date=' . $nextWeekDate)) ?>" aria-label="Next week">›</a></div>
    <nav class="plan-agenda-week" aria-label="Days of the week">
        <span class="plan-agenda-selected-pill" style="--selected-day: <?= esc((string) ($selectedWeekIndex === false ? 0 : $selectedWeekIndex)) ?>" aria-hidden="true"></span>
        <?php foreach ($weekDays as $day): ?>
            <a class="plan-agenda-day<?= $day['selected'] ? ' is-selected' : '' ?><?= $day['today'] ? ' is-today' : '' ?>" href="<?= esc(site_url('student/plan?date=' . $day['date'])) ?>"<?= $day['today'] ? ' aria-current="date"' : '' ?><?= $day['selected'] ? ' aria-label="' . esc($day['weekday'] . ', ' . $day['date'] . ', selected') . '"' : ' aria-label="' . esc($day['weekday'] . ', ' . $day['date']) . '"' ?>>
                <span><?= esc($day['weekday']) ?></span><strong><?= esc($day['day']) ?></strong><i class="plan-agenda-dots" aria-hidden="true"><?php for ($dot = 0; $dot < min(3, $day['count']); $dot++): ?><b></b><?php endfor ?></i>
            </a>
        <?php endforeach ?>
    </nav>
    <div class="plan-agenda-day-summary"><h3><?= esc($selectedDateLabel) ?></h3><p><?= count($agendaPlan['items'] ?? []) ?> <?= count($agendaPlan['items'] ?? []) === 1 ? 'study block' : 'study blocks' ?><?= $agendaPlan !== null ? ' · ' . esc((string) $agendaPlan['available_minutes']) . ' min available' : '' ?></p></div>
    <ol class="plan-agenda-timeline" id="agenda-list" data-agenda-list>
        <?php
        $agendaItems = $agendaPlan['items'] ?? [];
        usort($agendaItems, static function (array $left, array $right): int {
            $leftTime = $left['start_time'] ?? null;
            $rightTime = $right['start_time'] ?? null;
            if ($leftTime === null && $rightTime !== null) return 1;
            if ($leftTime !== null && $rightTime === null) return -1;
            return ($leftTime <=> $rightTime) ?: ((int) $left['position'] <=> (int) $right['position']);
        });
        $nowInserted = false;
        $nowMinute = (int) substr($nowTime, 0, 2) * 60 + (int) substr($nowTime, 3, 2);
        foreach ($agendaItems as $agendaItem):
            $itemMinute = $agendaItem['start_time'] === null ? null : ((int) substr($agendaItem['start_time'], 0, 2) * 60 + (int) substr($agendaItem['start_time'], 3, 2));
            $itemEndMinute = $itemMinute === null ? null : $itemMinute + (int) $agendaItem['duration_minutes'];
            $showNow = $isSelectedToday && ! $nowInserted && ($itemMinute === null || $itemMinute > $nowMinute || ($itemMinute <= $nowMinute && $itemEndMinute > $nowMinute));
            if ($showNow): $nowInserted = true; ?>
                <li class="plan-agenda-now" aria-label="Current time"><span>NOW · <?= esc(DateTimeImmutable::createFromFormat('!H:i', $nowTime)->format('g:i A')) ?></span><i></i></li>
            <?php endif ?>
            <li class="plan-agenda-event<?= $agendaItem['start_time'] === null ? ' is-unscheduled' : '' ?><?= (int) ($newAgendaItemId ?? 0) === (int) $agendaItem['id'] ? ' is-entering' : '' ?>" data-agenda-item="<?= esc((string) $agendaItem['id']) ?>" tabindex="-1">
                <time class="plan-agenda-time"<?= $agendaItem['start_time'] === null ? '' : ' datetime="' . esc(substr($agendaItem['start_time'], 0, 5)) . '"' ?>><?= $agendaItem['start_time'] === null ? 'Time not set' : esc(DateTimeImmutable::createFromFormat('!H:i:s', $agendaItem['start_time'])->format('g:i A')) ?></time>
                <article class="plan-agenda-card">
                    <span class="plan-agenda-card-mark" aria-hidden="true"></span>
                    <div><h4><?= esc($agendaItem['topic_title']) ?></h4><p><?= esc($agendaItem['subject_name']) ?> · <?= esc((string) $agendaItem['duration_minutes']) ?> min</p></div>
                    <?php if ($agendaItem['topic_id'] !== null && in_array($agendaItem['topic_progress'] ?? null, ['not_started', 'in_progress', 'done'], true)): ?>
                        <form method="post" action="<?= esc(site_url('student/plan/items/' . $agendaItem['id'] . '/progress')) ?>" class="plan-agenda-progress-form" data-agenda-progress-form data-progress="<?= esc($agendaItem['topic_progress']) ?>">
                            <?= csrf_field() ?><input type="hidden" name="plan_date" value="<?= esc($selectedDate) ?>">
                            <label><input type="checkbox" name="done" value="1" data-agenda-progress-toggle<?= $agendaItem['topic_progress'] === 'done' ? ' checked' : '' ?>><span><?= $agendaItem['topic_progress'] === 'done' ? 'Done' : 'Mark done' ?></span></label>
                            <button type="submit" class="plan-agenda-progress-fallback">Save</button>
                        </form>
                    <?php elseif (($agendaItem['topic_progress'] ?? '') === 'done'): ?><span class="plan-agenda-status">Done</span><?php endif ?>
                </article>
            </li>
        <?php endforeach ?>
        <?php if ($isSelectedToday && ! $nowInserted): ?><li class="plan-agenda-now" aria-label="Current time"><span>NOW · <?= esc(DateTimeImmutable::createFromFormat('!H:i', $nowTime)->format('g:i A')) ?></span><i></i></li><?php endif ?>
        <?php if ($agendaItems === []): ?><li class="plan-agenda-empty"><span class="plan-agenda-empty-mark" aria-hidden="true">✦</span><strong>No study blocks yet</strong><p>Add a topic to start shaping this day.</p></li><?php endif ?>
    </ol>
    <?php if ($topicOptions === []): ?><p class="plan-agenda-hint">Add an unfinished topic to your subjects before scheduling another block.</p><?php endif ?>
</section>

<div class="plan-desktop-content">

<?php if ($errors !== []): ?>
    <ul class="errors" role="alert">
        <?php foreach ($errors as $error): ?><li><?= esc($error) ?></li><?php endforeach ?>
    </ul>
<?php endif ?>

<?php if ($confirmReplacement): ?>
    <section class="notice" aria-labelledby="replace-heading">
        <h2 id="replace-heading">Replace today’s plan?</h2>
        <p>Generating a new plan for today will replace its current blocks and edits.</p>
        <form method="post" action="<?= esc(site_url('student/plan/generate')) ?>">
            <?= csrf_field() ?>
            <input type="hidden" name="available_minutes" value="<?= esc((string) $availableMinutes) ?>">
            <input type="hidden" name="plan_date" value="<?= esc($desktopPlanDate) ?>">
            <input type="hidden" name="start_time" value="<?= esc((string) $startTime) ?>">
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
        <input type="hidden" name="plan_date" value="<?= esc($desktopPlanDate) ?>">
        <label for="available_minutes">Available study time for today (minutes)</label>
        <input id="available_minutes" name="available_minutes" type="number" min="15" max="480" step="1" required value="<?= esc((string) ($availableMinutes !== '' ? $availableMinutes : ($plan['available_minutes'] ?? ''))) ?>">
        <p class="muted">Choose 15 to 480 minutes. Each unfinished topic gets a 25-minute block, shortened to fit.</p>
        <label for="plan_start_time">Start these blocks at (optional)</label>
        <input id="plan_start_time" name="start_time" type="time" value="<?= esc((string) $startTime) ?>">
        <p class="muted">If you leave the start time blank, blocks stay unscheduled.</p>
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
                    <div class="plan-progress-heading"><p class="workspace-kicker">STUDY PLAN · <?= esc($desktopPlanDate) ?></p><h3 id="plan-progress-heading">Track your progress</h3><p>Mark a topic done when you finish it. Unchecking moves it back to in progress.</p></div>
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
                                <input type="hidden" name="plan_date" value="<?= esc($desktopPlanDate) ?>">
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
                            <input type="hidden" name="plan_date" value="<?= esc($desktopPlanDate) ?>">
                            <label for="duration-<?= esc((string) $item['id']) ?>">Block duration (minutes)</label>
                            <input id="duration-<?= esc((string) $item['id']) ?>" name="duration_minutes" type="number" min="1" max="480" step="1" required value="<?= esc((string) $item['duration_minutes']) ?>">
                            <button class="button secondary" type="submit">Save duration</button>
                        </form>
                        <div class="actions">
                            <form method="post" action="<?= esc(site_url('student/plan/items/' . $item['id'] . '/move/up')) ?>">
                                <?= csrf_field() ?><input type="hidden" name="plan_date" value="<?= esc($desktopPlanDate) ?>"><button class="button secondary" type="submit"<?= $index === 0 ? ' disabled' : '' ?>>Move up</button>
                            </form>
                            <form method="post" action="<?= esc(site_url('student/plan/items/' . $item['id'] . '/move/down')) ?>">
                                <?= csrf_field() ?><input type="hidden" name="plan_date" value="<?= esc($desktopPlanDate) ?>"><button class="button secondary" type="submit"<?= $index === count($plan['items']) - 1 ? ' disabled' : '' ?>>Move down</button>
                            </form>
                            <form method="post" action="<?= esc(site_url('student/plan/items/' . $item['id'] . '/remove')) ?>">
                                <?= csrf_field() ?><input type="hidden" name="plan_date" value="<?= esc($desktopPlanDate) ?>"><button class="danger" type="submit">Remove block</button>
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
</div>

<dialog class="plan-agenda-sheet" id="agenda-add-sheet" data-agenda-sheet aria-labelledby="agenda-sheet-heading" aria-describedby="agenda-sheet-copy">
    <form method="post" action="<?= esc(site_url('student/plan/items')) ?>" data-agenda-form>
        <?= csrf_field() ?>
        <input type="hidden" name="plan_date" value="<?= esc($selectedDate) ?>">
        <div class="plan-agenda-sheet-grab" aria-hidden="true"></div>
        <header><div><p class="workspace-kicker">DUNO PLAN</p><h2 id="agenda-sheet-heading">Add a study block</h2></div><button type="button" class="plan-agenda-sheet-close" data-agenda-close aria-label="Close">×</button></header>
        <p id="agenda-sheet-copy">Add an unfinished topic or set a time for a block already in your plan.</p>
        <label for="agenda-topic">Topic</label>
        <select id="agenda-topic" name="agenda_item" data-agenda-choice required>
            <option value="">Choose a topic</option>
            <?php foreach ($topicOptions as $topic): ?><option value="<?= esc($topic['value']) ?>" data-duration="<?= esc((string) $topic['duration']) ?>"><?= esc($topic['label']) ?></option><?php endforeach ?>
        </select>
        <div class="plan-agenda-sheet-fields">
            <div><label for="agenda-start-time">Start time</label><input id="agenda-start-time" name="start_time" type="time" required value="<?= esc((string) $agendaDefaultTime) ?>"></div>
            <div><label for="agenda-duration">Duration</label><div class="plan-agenda-duration-field"><input id="agenda-duration" name="duration_minutes" type="number" min="1" max="480" value="25" required><span>min</span></div></div>
        </div>
        <?php if ($agendaPlan === null): ?>
            <label for="agenda-available">Available study time for <?= esc($selectedDateLabel) ?> (minutes)</label>
            <input id="agenda-available" name="available_minutes" type="number" min="15" max="480" step="1" required>
            <p class="plan-agenda-field-hint">Set a daily limit between 15 and 480 minutes. Added blocks must fit within it.</p>
        <?php else: ?>
            <input type="hidden" name="available_minutes" value="<?= esc((string) $agendaPlan['available_minutes']) ?>">
            <p class="plan-agenda-field-hint"><?= esc((string) $agendaPlan['available_minutes']) ?> minutes available for this day.</p>
        <?php endif ?>
        <div class="plan-agenda-sheet-actions"><button type="button" class="button secondary" data-agenda-close>Cancel</button><button class="button" type="submit"<?= $topicOptions === [] ? ' disabled' : '' ?>>Add block</button></div>
    </form>
</dialog>
<script src="<?= esc(duno_asset('assets/js/plan-agenda.js')) ?>" defer></script>
<?= $this->endSection() ?>
