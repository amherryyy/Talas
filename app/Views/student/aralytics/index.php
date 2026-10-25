<?= $this->extend('student/subjects/layout') ?>

<?= $this->section('content') ?>
<h1>Aralytics</h1>
<p class="muted">Study-session totals for the selected period.</p>

<?= $this->include('student/partials/progression_card', ['progression' => $progression]) ?>

<div class="actions no-print report-controls" aria-label="Report controls">
    <span>Period:</span>
    <?php foreach ($periods as $key => $label): ?>
        <a class="button<?= $summary['period'] === $key ? '' : ' secondary' ?>" href="<?= esc(site_url('student/aralytics?period=' . rawurlencode($key))) ?>"<?= $summary['period'] === $key ? ' aria-current="page"' : '' ?>><?= esc($label) ?></a>
    <?php endforeach ?>
    <button type="button" data-print-report>Print report</button>
</div>

<article aria-labelledby="report-title">
    <h2 id="report-title">Study activity: <?= esc($summary['period_label']) ?></h2>
    <p class="muted"><?= $summary['start_date'] === null ? 'All recorded dates' : esc(date('M j, Y', strtotime($summary['start_date']))) . ' – ' . esc(date('M j, Y', strtotime($summary['end_date']))) ?></p>

    <section class="report-section no-print" aria-labelledby="check-in-pattern-heading">
        <h3 id="check-in-pattern-heading">Your private check-in pattern</h3>
        <p class="muted">Private to you; descriptive, not an assessment or prediction. Excluded from print.</p>
        <?php if ($checkInSummary['check_in_count'] === 0): ?>
            <p>No check-ins recorded in this period.</p>
        <?php else: ?>
            <p><?= esc((string) $checkInSummary['check_in_count']) ?> check-ins: high workload (4–5) <?= esc((string) $checkInSummary['high_workload_count']) ?>; low energy (1–2) <?= esc((string) $checkInSummary['low_energy_count']) ?>; both <?= esc((string) $checkInSummary['high_workload_low_energy_count']) ?>.</p>
            <h4>Rating distribution</h4>
            <?php $ratingChartMax = max(1, max($checkInSummary['workload_distribution']), max($checkInSummary['energy_distribution'])); ?>
            <?php $ratingChartBase = 220; $ratingChartHeight = 168; ?>
            <svg class="rating-chart" viewBox="0 0 620 260" role="img" aria-labelledby="rating-chart-title rating-chart-description">
                <title id="rating-chart-title">Workload and energy ratings for <?= esc($checkInSummary['period_label']) ?></title>
                <desc id="rating-chart-description">Paired bars show the count at each rating from 1 to 5. Exact counts are also listed in the table below.</desc>
                <text x="52" y="38">Number of check-ins</text>
                <line class="grid-line" x1="52" y1="52" x2="570" y2="52" />
                <line class="grid-line" x1="52" y1="136" x2="570" y2="136" />
                <line class="axis-line" x1="52" y1="220" x2="570" y2="220" />
                <text x="42" y="56" text-anchor="end"><?= esc((string) $ratingChartMax) ?></text>
                <text x="42" y="224" text-anchor="end">0</text>
                <?php foreach ([1, 2, 3, 4, 5] as $rating): ?>
                    <?php
                    $center = 112 + (($rating - 1) * 100);
                    $workloadCount = $checkInSummary['workload_distribution'][$rating];
                    $energyCount = $checkInSummary['energy_distribution'][$rating];
                    $workloadHeight = (int) round(($workloadCount / $ratingChartMax) * $ratingChartHeight);
                    $energyHeight = (int) round(($energyCount / $ratingChartMax) * $ratingChartHeight);
                    ?>
                    <rect class="workload-bar" x="<?= esc((string) ($center - 27)) ?>" y="<?= esc((string) ($ratingChartBase - $workloadHeight)) ?>" width="24" height="<?= esc((string) $workloadHeight) ?>" rx="2" />
                    <rect class="energy-bar" x="<?= esc((string) ($center + 3)) ?>" y="<?= esc((string) ($ratingChartBase - $energyHeight)) ?>" width="24" height="<?= esc((string) $energyHeight) ?>" rx="2" />
                    <text class="bar-count" x="<?= esc((string) ($center - 15)) ?>" y="<?= esc((string) max(44, $ratingChartBase - $workloadHeight - 6)) ?>" text-anchor="middle"><?= esc((string) $workloadCount) ?></text>
                    <text class="bar-count" x="<?= esc((string) ($center + 15)) ?>" y="<?= esc((string) max(44, $ratingChartBase - $energyHeight - 6)) ?>" text-anchor="middle"><?= esc((string) $energyCount) ?></text>
                    <text x="<?= esc((string) $center) ?>" y="240" text-anchor="middle"><?= esc((string) $rating) ?></text>
                <?php endforeach ?>
                <text x="311" y="258" text-anchor="middle">Rating (1–5)</text>
            </svg>
            <div class="chart-legend" aria-hidden="true">
                <span><i class="chart-swatch workload"></i>Workload</span>
                <span><i class="chart-swatch energy"></i>Energy</span>
            </div>
            <table class="report-table">
                <caption>Check-in counts by rating</caption>
                <thead><tr><th scope="col">Rating</th><?php foreach ([1, 2, 3, 4, 5] as $rating): ?><th scope="col"><?= esc((string) $rating) ?></th><?php endforeach ?></tr></thead>
                <tbody>
                    <tr><th scope="row">Workload</th><?php foreach ([1, 2, 3, 4, 5] as $rating): ?><td><?= esc((string) $checkInSummary['workload_distribution'][$rating]) ?></td><?php endforeach ?></tr>
                    <tr><th scope="row">Energy</th><?php foreach ([1, 2, 3, 4, 5] as $rating): ?><td><?= esc((string) $checkInSummary['energy_distribution'][$rating]) ?></td><?php endforeach ?></tr>
                </tbody>
            </table>
        <?php endif ?>
    </section>

    <section class="progression-insights no-print" aria-labelledby="progression-insights-heading">
        <div class="dashboard-section-heading">
            <div><p class="workspace-kicker">Earned with your saved study time</p><h3 id="progression-insights-heading">Your unlocked insights</h3></div>
            <p class="muted">Core Aralytics stays open at every level.</p>
        </div>
        <div class="progression-insight-grid">
            <?php foreach ($progression['unlocks'] as $unlock): ?>
                <section class="progression-insight<?= $unlock['unlocked'] ? ' is-unlocked' : ' is-locked' ?>" aria-labelledby="insight-<?= esc($unlock['key']) ?>">
                    <p class="insight-unlock-level"><?= $unlock['unlocked'] ? 'Unlocked' : 'Level ' . esc((string) $unlock['level']) . ' · ' . esc((string) $unlock['xp']) . ' XP' ?></p>
                    <h4 id="insight-<?= esc($unlock['key']) ?>"><?= esc($unlock['name']) ?></h4>
                    <p class="muted"><?= esc($unlock['description']) ?></p>
                    <?php if (! $unlock['unlocked']): ?>
                        <progress value="<?= esc((string) min($progression['xp'], $unlock['xp'])) ?>" max="<?= esc((string) $unlock['xp']) ?>" aria-label="Progress to unlock <?= esc($unlock['name']) ?>"></progress>
                        <p class="muted"><?= esc((string) max(0, $unlock['xp'] - $progression['xp'])) ?> XP to go</p>
                    <?php elseif ($unlock['key'] === 'rhythm'): ?>
                        <p><strong><?= esc((string) $progression['study_days_last_30']) ?> of 30 days</strong> had saved study time.</p>
                        <div class="rhythm-map" role="img" aria-label="Daily saved study time over the past 30 days. A table of exact minutes is available below.">
                            <?php foreach ($progression['daily_minutes_last_30'] as $day): ?>
                                <?php $intensity = $day['minutes'] === 0 ? 0 : ($day['minutes'] < 25 ? 1 : ($day['minutes'] < 50 ? 2 : 3)); ?>
                                <span class="rhythm-day intensity-<?= $intensity ?>" aria-hidden="true"><?= esc(date('j', strtotime($day['date']))) ?></span>
                            <?php endforeach ?>
                        </div>
                        <details class="insight-values">
                            <summary>Day-by-day minutes</summary>
                            <table class="report-table"><thead><tr><th scope="col">Date</th><th scope="col">Saved minutes</th></tr></thead><tbody>
                                <?php foreach ($progression['daily_minutes_last_30'] as $day): ?>
                                    <tr><th scope="row"><?= esc(date('M j', strtotime($day['date']))) ?></th><td><?= esc((string) $day['minutes']) ?></td></tr>
                                <?php endforeach ?>
                            </tbody></table>
                        </details>
                    <?php elseif ($unlock['key'] === 'weekdays'): ?>
                        <?php $weekdayMax = max(1, ...array_column($progression['weekday_minutes_last_30'], 'minutes')); ?>
                        <div class="weekday-pattern-list">
                            <?php foreach ($progression['weekday_minutes_last_30'] as $weekday): ?>
                                <div class="weekday-pattern-row">
                                    <span><?= esc($weekday['day']) ?></span>
                                    <progress value="<?= esc((string) $weekday['minutes']) ?>" max="<?= esc((string) $weekdayMax) ?>" aria-label="<?= esc($weekday['day'] . ': ' . $weekday['minutes'] . ' saved minutes in the past 30 days') ?>"></progress>
                                    <strong><?= esc((string) $weekday['minutes']) ?> min</strong>
                                </div>
                            <?php endforeach ?>
                        </div>
                    <?php elseif ($unlock['key'] === 'momentum'): ?>
                        <div class="momentum-comparison">
                            <div><span><?= esc(date('M j', strtotime($progression['previous_30_day_start']))) ?>–<?= esc(date('M j', strtotime($progression['previous_30_day_end']))) ?></span><strong><?= esc((string) $progression['previous_30_day_minutes']) ?> min</strong></div>
                            <div><span><?= esc(date('M j', strtotime($progression['current_30_day_start']))) ?>–<?= esc(date('M j', strtotime($progression['current_30_day_end']))) ?></span><strong><?= esc((string) $progression['current_30_day_minutes']) ?> min</strong></div>
                        </div>
                        <?php if ($progression['30_day_minutes_change'] > 0): ?>
                            <p>That’s <?= esc((string) $progression['30_day_minutes_change']) ?> more saved minutes than the previous 30-day period.</p>
                        <?php elseif ($progression['30_day_minutes_change'] < 0): ?>
                            <p>That’s <?= esc((string) abs($progression['30_day_minutes_change'])) ?> fewer saved minutes than the previous 30-day period.</p>
                        <?php else: ?>
                            <p>Saved study minutes match the previous 30-day period.</p>
                        <?php endif ?>
                    <?php elseif ($unlock['key'] === 'six_months'): ?>
                        <?php $monthMax = max(1, ...array_column($progression['monthly_minutes_last_6'], 'minutes')); ?>
                        <div class="month-trend-list">
                            <?php foreach ($progression['monthly_minutes_last_6'] as $month): ?>
                                <div class="month-trend-row">
                                    <span><?= esc(date('M Y', strtotime($month['month'] . '-01'))) ?></span>
                                    <progress value="<?= esc((string) $month['minutes']) ?>" max="<?= esc((string) $monthMax) ?>" aria-label="<?= esc(date('F Y', strtotime($month['month'] . '-01')) . ': ' . $month['minutes'] . ' saved minutes') ?>"></progress>
                                    <strong><?= esc((string) $month['minutes']) ?> min</strong>
                                </div>
                            <?php endforeach ?>
                        </div>
                        <p class="muted">Current month includes activity through today.</p>
                    <?php endif ?>
                </section>
            <?php endforeach ?>
        </div>
    </section>

    <section class="summary-grid" aria-label="Summary totals">
        <div class="summary-card"><h3>Total minutes</h3><p><?= esc((string) $summary['total_minutes']) ?></p></div>
        <div class="summary-card"><h3>Sessions</h3><p><?= esc((string) $summary['session_count']) ?></p></div>
        <div class="summary-card"><h3>Average session</h3><p><?= $summary['average_minutes'] === null ? '—' : esc(number_format($summary['average_minutes'], 1)) . ' min' ?></p></div>
    </section>

    <?php if ($summary['sessions'] === []): ?>
        <p class="notice" role="status">No sessions in this period.</p>
    <?php endif ?>

    <section class="report-section" aria-labelledby="subject-totals-heading">
        <h3 id="subject-totals-heading">Subject totals</h3>
        <?php if ($summary['subject_totals'] === []): ?>
            <p class="muted">No subject activity in this period.</p>
        <?php else: ?>
            <table class="report-table"><thead><tr><th>Subject</th><th>Sessions</th><th>Minutes</th></tr></thead><tbody>
                <?php foreach ($summary['subject_totals'] as $row): ?>
                    <tr><th scope="row"><?= esc($row['subject_name']) ?></th><td><?= esc((string) $row['session_count']) ?></td><td><?= esc((string) $row['total_minutes']) ?></td></tr>
                <?php endforeach ?>
            </tbody></table>
        <?php endif ?>
    </section>

    <section class="report-section" aria-labelledby="topic-breakdown-heading">
        <h3 id="topic-breakdown-heading">Topic breakdown</h3>
        <?php if ($summary['topic_totals'] === []): ?>
            <p class="muted">No topic activity in this period.</p>
        <?php else: ?>
            <table class="report-table"><thead><tr><th>Subject and topic</th><th>Sessions</th><th>Minutes</th></tr></thead><tbody>
                <?php foreach ($summary['topic_totals'] as $row): ?>
                    <tr><th scope="row"><?= esc($row['subject_name']) ?> · <?= esc($row['topic_title']) ?></th><td><?= esc((string) $row['session_count']) ?></td><td><?= esc((string) $row['total_minutes']) ?></td></tr>
                <?php endforeach ?>
            </tbody></table>
        <?php endif ?>
    </section>

    <section class="report-section" aria-labelledby="sessions-heading">
        <h3 id="sessions-heading">Dated sessions</h3>
        <?php if ($summary['sessions'] === []): ?>
            <p class="muted">No dated sessions to list.</p>
        <?php else: ?>
            <table class="report-table"><thead><tr><th>Date</th><th>Subject and topic</th><th>Minutes</th></tr></thead><tbody>
                <?php foreach ($summary['sessions'] as $session): ?>
                    <?php $completedAt = new DateTimeImmutable((string) $session['completed_at'], new DateTimeZone($appTimezone)); ?>
                    <tr>
                        <td><?= esc($completedAt->format('M j, Y g:i a')) ?></td>
                        <td><?= esc($session['subject_name']) ?> · <?= esc($session['topic_title'] ?? 'No topic') ?></td>
                        <td><?= esc((string) $session['duration_minutes']) ?></td>
                    </tr>
                <?php endforeach ?>
            </tbody></table>
        <?php endif ?>
    </section>
</article>
<script src="<?= esc(duno_asset('assets/js/aralytics-print.js')) ?>" defer></script>
<?= $this->endSection() ?>
