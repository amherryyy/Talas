<?= $this->extend('student/subjects/layout') ?>

<?= $this->section('content') ?>
<h1>Aralytics</h1>
<p class="muted">See where your saved study time went, spot your routines, and print a clear report.</p>

<nav class="no-print report-controls" aria-label="Report period and actions">
    <span class="report-controls-label">Choose a period</span>
    <div class="report-period-options">
    <?php foreach ($periods as $key => $label): ?>
        <a class="button<?= $summary['period'] === $key ? '' : ' secondary' ?>" href="<?= esc(site_url('student/aralytics?period=' . rawurlencode($key))) ?>"<?= $summary['period'] === $key ? ' aria-current="page"' : '' ?>><?= esc($label) ?></a>
    <?php endforeach ?>
    </div>
    <button type="button" data-print-report>Print report</button>
</nav>

<article class="aralytics-report" aria-labelledby="report-title">
    <h2 id="report-title">Study activity: <?= esc($summary['period_label']) ?></h2>
    <p class="muted"><?= $summary['start_date'] === null ? 'All recorded dates' : esc(date('M j, Y', strtotime($summary['start_date']))) . ' – ' . esc(date('M j, Y', strtotime($summary['end_date']))) ?></p>

    <section class="summary-grid aralytics-summary-grid" aria-label="Summary totals">
        <div class="summary-card summary-card-primary"><h3>Total study time</h3><p><?= esc((string) $summary['total_minutes']) ?><span>min</span></p><small>Saved in this period</small></div>
        <div class="summary-card"><h3>Sessions</h3><p><?= esc((string) $summary['session_count']) ?></p><small>Completed and saved</small></div>
        <div class="summary-card"><h3>Average session</h3><p><?= $summary['average_minutes'] === null ? '—' : esc(number_format($summary['average_minutes'], 1)) . ' min' ?></p></div>
    </section>

    <section class="aralytics-print-progression print-only" aria-labelledby="print-progression-heading">
        <h3 id="print-progression-heading">Study progress at print time</h3>
        <p class="muted">All-time account progress, separate from the selected activity period above.</p>
        <table class="report-table">
            <tbody>
                <tr><th scope="row">Current level</th><td>Level <?= esc((string) $progression['level']) ?> · <?= esc($progression['level_name']) ?></td></tr>
                <tr><th scope="row">All-time XP</th><td><?= esc(number_format((int) $progression['xp'])) ?> XP (<?= esc(number_format((int) $progression['study_xp'])) ?> study + <?= esc(number_format((int) $progression['streak_xp'])) ?> daily visit rewards)</td></tr>
                <tr><th scope="row">Current visit streak</th><td><?= esc((string) $progression['current_streak']) ?> <?= $progression['current_streak'] === 1 ? 'day' : 'days' ?></td></tr>
                <tr><th scope="row">Next level</th><td><?= $progression['next_level_name'] === null ? 'All current levels reached' : 'Level ' . esc((string) ($progression['level'] + 1)) . ' · ' . esc($progression['next_level_name']) . ' at ' . esc(number_format((int) $progression['next_level_xp'])) . ' XP' ?></td></tr>
            </tbody>
        </table>
        <h4>All-time milestones</h4>
        <table class="report-table">
            <thead><tr><th scope="col">Milestone</th><th scope="col">Status</th></tr></thead>
            <tbody>
                <?php foreach ($progression['badges'] as $badge): ?>
                    <tr><th scope="row"><?= esc($badge['name']) ?></th><td><?= $badge['unlocked'] ? 'Earned' : 'In progress' ?></td></tr>
                <?php endforeach ?>
            </tbody>
        </table>
    </section>

    <?php if ($summary['sessions'] === []): ?>
        <p class="notice" role="status">No sessions in this period.</p>
    <?php endif ?>

    <?php if ($summary['activity_series'] !== []): ?>
        <?php $activityMax = max(1, ...array_column($summary['activity_series'], 'minutes')); ?>
        <section class="report-section activity-trend-section" aria-labelledby="activity-trend-heading">
            <div class="report-section-heading"><div><p class="workspace-kicker">Study activity over time</p><h3 id="activity-trend-heading"><?= $summary['activity_granularity'] === 'month' ? 'Monthly study minutes' : 'Daily study minutes' ?></h3></div><p class="muted">Scaled to <?= esc((string) $activityMax) ?> min. Empty days and months are included<?= $summary['activity_granularity'] === 'month' ? '; scroll to see older months' : '.' ?></p></div>
            <div class="activity-chart-scroll no-print">
                <ol class="activity-chart" role="img" aria-label="Saved study minutes <?= esc(strtolower($summary['period_label'])) ?>. Exact minutes and session counts are listed in the table below.">
                    <?php foreach ($summary['activity_series'] as $index => $bucket): ?>
                        <?php $barHeight = $bucket['minutes'] === 0 ? 0 : max(2, (int) round(($bucket['minutes'] / $activityMax) * 100)); ?>
                        <li title="<?= esc($bucket['label'] . ': ' . $bucket['minutes'] . ' minutes, ' . $bucket['session_count'] . ' sessions') ?>" aria-hidden="true"><span class="activity-chart-bar" style="height: <?= esc((string) $barHeight) ?>%; --activity-delay: <?= esc((string) (min($index, 12) * 50)) ?>ms"></span><span class="activity-chart-label"><?= esc($bucket['label']) ?></span></li>
                    <?php endforeach ?>
                </ol>
            </div>
            <details class="insight-values activity-values">
                <summary>Exact activity values</summary>
                <table class="report-table"><thead><tr><th scope="col"><?= $summary['activity_granularity'] === 'month' ? 'Month' : 'Date' ?></th><th scope="col">Sessions</th><th scope="col">Minutes</th></tr></thead><tbody>
                    <?php foreach ($summary['activity_series'] as $bucket): ?>
                        <?php $bucketDate = new DateTimeImmutable($bucket['key'] . ($summary['activity_granularity'] === 'month' ? '-01' : ''), new DateTimeZone($appTimezone)); ?>
                        <tr><th scope="row"><?= esc($summary['activity_granularity'] === 'month' ? $bucketDate->format('F Y') : $bucketDate->format('M j, Y')) ?></th><td><?= esc((string) $bucket['session_count']) ?></td><td><?= esc((string) $bucket['minutes']) ?></td></tr>
                    <?php endforeach ?>
                </tbody></table>
            </details>
        </section>
    <?php endif ?>

    <details class="report-breakdown">
        <summary>Subject, topic, and session details</summary>
    <section class="report-section" aria-labelledby="subject-totals-heading">
            <div class="report-section-heading"><div><p class="workspace-kicker">Time by subject</p><h3 id="subject-totals-heading">Where your study time went</h3></div><p class="muted">Bars show each subject’s share of this period’s study time.</p></div>
        <?php if ($summary['subject_totals'] === []): ?>
            <p class="muted">No subject activity in this period.</p>
        <?php else: ?>
            <?php $subjectMinutesMax = max(1, $summary['total_minutes']); ?>
            <div class="analytics-bar-list no-print" role="group" aria-label="Study minutes by subject">
                <?php foreach ($summary['subject_totals'] as $row): ?>
                    <?php $subjectShare = round(($row['total_minutes'] / $subjectMinutesMax) * 100); ?>
                    <div class="analytics-bar-row"><span><?= esc($row['subject_name']) ?></span><progress value="<?= esc((string) $row['total_minutes']) ?>" max="<?= esc((string) $subjectMinutesMax) ?>" aria-label="<?= esc($row['subject_name'] . ': ' . $row['total_minutes'] . ' minutes, ' . $subjectShare . '% of study time') ?>"></progress><strong><?= esc((string) $row['total_minutes']) ?> min <small><?= esc((string) $subjectShare) ?>%</small></strong></div>
                <?php endforeach ?>
            </div>
            <table class="report-table"><thead><tr><th>Subject</th><th>Sessions</th><th>Minutes</th></tr></thead><tbody>
                <?php foreach ($summary['subject_totals'] as $row): ?>
                    <tr><th scope="row"><?= esc($row['subject_name']) ?></th><td><?= esc((string) $row['session_count']) ?></td><td><?= esc((string) $row['total_minutes']) ?></td></tr>
                <?php endforeach ?>
            </tbody></table>
        <?php endif ?>
    </section>

    <section class="report-section" aria-labelledby="topic-breakdown-heading">
        <div class="report-section-heading"><div><p class="workspace-kicker">More detail</p><h3 id="topic-breakdown-heading">Time by topic</h3></div><p class="muted">Includes sessions without a selected topic.</p></div>
        <?php if ($summary['topic_totals'] === []): ?>
            <p class="muted">No topic activity in this period.</p>
        <?php else: ?>
            <?php $topicMinutesMax = max(1, $summary['total_minutes']); ?>
            <div class="analytics-bar-list topic-analytics-bars no-print" role="group" aria-label="Study minutes by topic">
                <?php foreach ($summary['topic_totals'] as $row): ?>
                    <?php $topicLabel = $row['subject_name'] . ' / ' . $row['topic_title']; ?>
                    <?php $topicShare = round(($row['total_minutes'] / $topicMinutesMax) * 100); ?>
                    <div class="analytics-bar-row"><span><?= esc($topicLabel) ?></span><progress value="<?= esc((string) $row['total_minutes']) ?>" max="<?= esc((string) $topicMinutesMax) ?>" aria-label="<?= esc($topicLabel . ': ' . $row['total_minutes'] . ' minutes, ' . $topicShare . '% of study time') ?>"></progress><strong><?= esc((string) $row['total_minutes']) ?> min <small><?= esc((string) $topicShare) ?>%</small></strong></div>
                <?php endforeach ?>
            </div>
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
    </details>
    <details class="report-disclosure no-print">
        <summary>Private check-in trends</summary>
    <section class="report-section" aria-labelledby="check-in-pattern-heading">
        <h3 id="check-in-pattern-heading">Your private check-in pattern</h3>
        <p class="muted">Private to you; descriptive, not an assessment or prediction. Excluded from print.</p>
        <?php if ($checkInSummary['check_in_count'] === 0): ?>
            <p>No check-ins recorded in this period.</p>
        <?php else: ?>
            <p><?= esc((string) $checkInSummary['check_in_count']) ?> check-ins: high workload (4–5) <?= esc((string) $checkInSummary['high_workload_count']) ?>; low energy (1–2) <?= esc((string) $checkInSummary['low_energy_count']) ?>; both <?= esc((string) $checkInSummary['high_workload_low_energy_count']) ?>.</p>
            <h4>Rating distribution</h4>
            <?php $ratingChartMax = max(1, max($checkInSummary['workload_distribution']), max($checkInSummary['energy_distribution'])); ?>
            <?php
            $ratingChartBase = 238;
            $ratingChartHeight = 170;
            $ratingChartTicks = $ratingChartMax <= 4
                ? range($ratingChartMax, 0)
                : array_values(array_unique([$ratingChartMax, (int) ceil($ratingChartMax / 2), 0]));
            ?>
            <svg class="rating-chart" viewBox="0 0 620 292" role="img" aria-labelledby="rating-chart-title rating-chart-description">
                <title id="rating-chart-title">Workload and energy ratings for <?= esc($checkInSummary['period_label']) ?></title>
                <desc id="rating-chart-description">Paired bars compare workload and energy check-ins at ratings 1 to 5. Zero counts and exact values are listed in the table below.</desc>
                <defs>
                    <linearGradient id="workload-rating-gradient" x1="0" y1="0" x2="0" y2="1"><stop offset="0%" stop-color="#8a78ea" /><stop offset="100%" stop-color="#5544bd" /></linearGradient>
                    <linearGradient id="energy-rating-gradient" x1="0" y1="0" x2="0" y2="1"><stop offset="0%" stop-color="#81d1aa" /><stop offset="100%" stop-color="#42a978" /></linearGradient>
                </defs>
                <rect class="rating-chart-plot" x="52" y="62" width="518" height="176" rx="14" />
                <text class="rating-chart-axis-title" x="52" y="38">Number of check-ins</text>
                <?php foreach ($ratingChartTicks as $tick): ?>
                    <?php $tickY = $ratingChartBase - (int) round(($tick / $ratingChartMax) * $ratingChartHeight); ?>
                    <line class="grid-line<?= $tick === 0 ? ' axis-line' : '' ?>" x1="52" y1="<?= esc((string) $tickY) ?>" x2="570" y2="<?= esc((string) $tickY) ?>" />
                    <text class="rating-chart-tick" x="42" y="<?= esc((string) ($tickY + 4)) ?>" text-anchor="end"><?= esc((string) $tick) ?></text>
                <?php endforeach ?>
                <?php foreach ([1, 2, 3, 4, 5] as $rating): ?>
                    <?php
                    $center = 112 + (($rating - 1) * 100);
                    $workloadCount = $checkInSummary['workload_distribution'][$rating];
                    $energyCount = $checkInSummary['energy_distribution'][$rating];
                    $workloadHeight = (int) round(($workloadCount / $ratingChartMax) * $ratingChartHeight);
                    $energyHeight = (int) round(($energyCount / $ratingChartMax) * $ratingChartHeight);
                    ?>
                    <?php if ($workloadCount > 0): ?>
                        <rect class="workload-bar" x="<?= esc((string) ($center - 31)) ?>" y="<?= esc((string) ($ratingChartBase - $workloadHeight)) ?>" width="26" height="<?= esc((string) $workloadHeight) ?>" rx="7" />
                        <text class="bar-count" x="<?= esc((string) ($center - 18)) ?>" y="<?= esc((string) max(54, $ratingChartBase - $workloadHeight - 8)) ?>" text-anchor="middle"><?= esc((string) $workloadCount) ?></text>
                    <?php endif ?>
                    <?php if ($energyCount > 0): ?>
                        <rect class="energy-bar" x="<?= esc((string) ($center + 5)) ?>" y="<?= esc((string) ($ratingChartBase - $energyHeight)) ?>" width="26" height="<?= esc((string) $energyHeight) ?>" rx="7" />
                        <text class="bar-count" x="<?= esc((string) ($center + 18)) ?>" y="<?= esc((string) max(54, $ratingChartBase - $energyHeight - 8)) ?>" text-anchor="middle"><?= esc((string) $energyCount) ?></text>
                    <?php endif ?>
                    <rect class="rating-label-pill" x="<?= esc((string) ($center - 15)) ?>" y="248" width="30" height="24" rx="8" />
                    <text class="rating-chart-rating" x="<?= esc((string) $center) ?>" y="264" text-anchor="middle"><?= esc((string) $rating) ?></text>
                <?php endforeach ?>
                <text class="rating-chart-axis-title rating-chart-bottom-title" x="311" y="289" text-anchor="middle">Rating scale</text>
            </svg>
            <div class="chart-legend rating-chart-legend" aria-hidden="true">
                <span><i class="chart-swatch workload"></i>Workload</span>
                <span><i class="chart-swatch energy"></i>Energy</span>
            </div>
            <table class="report-table rating-distribution-table">
                <caption>Check-in counts by rating</caption>
                <thead><tr><th scope="col">Rating</th><?php foreach ([1, 2, 3, 4, 5] as $rating): ?><th scope="col"><?= esc((string) $rating) ?></th><?php endforeach ?></tr></thead>
                <tbody>
                    <tr><th scope="row">Workload</th><?php foreach ([1, 2, 3, 4, 5] as $rating): ?><td><?= esc((string) $checkInSummary['workload_distribution'][$rating]) ?></td><?php endforeach ?></tr>
                    <tr><th scope="row">Energy</th><?php foreach ([1, 2, 3, 4, 5] as $rating): ?><td><?= esc((string) $checkInSummary['energy_distribution'][$rating]) ?></td><?php endforeach ?></tr>
                </tbody>
            </table>
        <?php endif ?>
    </section>

    </details>
    <details class="report-disclosure no-print">
        <summary>Bonus study insights</summary>
    <section class="progression-insights" aria-labelledby="progression-insights-heading">
        <div class="dashboard-section-heading">
            <div><p class="workspace-kicker">Earned with study time and daily visits</p><h3 id="progression-insights-heading">Your unlocked insights</h3></div>
            <p class="muted">Core Aralytics stays open at every level.</p>
        </div>
        <div class="progression-insight-grid">
            <?php foreach ($progression['unlocks'] as $unlock): ?>
                <section class="progression-insight<?= $unlock['unlocked'] ? ' is-unlocked' : ' is-locked' ?>" aria-labelledby="insight-<?= esc($unlock['key']) ?>">
                    <p class="insight-unlock-level"><?= $unlock['unlocked'] ? 'Unlocked' : 'Level ' . esc((string) $unlock['level']) . ' · ' . esc(number_format((int) $unlock['xp'])) . ' XP' ?></p>
                    <h4 id="insight-<?= esc($unlock['key']) ?>"><?= esc($unlock['name']) ?></h4>
                    <p class="muted"><?= esc($unlock['description']) ?></p>
                    <?php if (! $unlock['unlocked']): ?>
                        <progress value="<?= esc((string) min($progression['xp'], $unlock['xp'])) ?>" max="<?= esc((string) $unlock['xp']) ?>" aria-label="Progress to unlock <?= esc($unlock['name']) ?>"></progress>
                        <p class="muted"><?= esc(number_format(max(0, (int) $unlock['xp'] - (int) $progression['xp']))) ?> XP to go</p>
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
    </details>

</article>
<?= $this->include('student/partials/progression_card', ['progression' => $progression]) ?>
<script src="<?= esc(duno_asset('assets/js/aralytics-print.js')) ?>" defer></script>
<?= $this->endSection() ?>
