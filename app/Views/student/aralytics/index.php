<?= $this->extend('student/subjects/layout') ?>

<?= $this->section('content') ?>
<style {csp-style-nonce}>
    .summary-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(min(100%, 10rem), 1fr)); gap: .75rem; }
    .summary-card, .report-section { padding: 1rem; border: 1px solid var(--duno-line); border-radius: .65rem; background: #fff; }
    .summary-card p { margin: .25rem 0 0; font-size: 1.25rem; font-weight: 700; }
    .report-section { margin-top: 1rem; }
    .report-table { width: 100%; border-collapse: collapse; }
    .report-table th, .report-table td { padding: .55rem; text-align: left; vertical-align: top; border-bottom: 1px solid var(--duno-line); }
    .report-table th:last-child, .report-table td:last-child { text-align: right; }
    .rating-chart { display: block; width: 100%; max-width: 42rem; height: auto; margin: 1rem auto; }
    .rating-chart .grid-line, .rating-chart .axis-line { stroke: var(--duno-line); stroke-width: 1; }
    .rating-chart text { fill: var(--duno-ink); font: 12px system-ui, sans-serif; }
    .rating-chart .workload-bar { fill: var(--duno-purple); }
    .rating-chart .energy-bar { fill: var(--duno-mint); stroke: #37634e; stroke-width: .5; }
    .rating-chart .bar-count { font-weight: 700; }
    .chart-legend { display: flex; flex-wrap: wrap; gap: .75rem 1rem; justify-content: center; }
    .chart-legend span { display: inline-flex; align-items: center; gap: .4rem; }
    .chart-swatch { display: inline-block; width: .8rem; height: .8rem; border-radius: .15rem; }
    .chart-swatch.workload { background: var(--duno-purple); }
    .chart-swatch.energy { background: var(--duno-mint); border: 1px solid #37634e; }
    @media (max-width: 36rem) {
        .report-table { font-size: .9rem; }
        .report-table th, .report-table td { padding: .4rem .25rem; }
    }
    @media print {
        body { padding: 0; color: #000; background: #fff; }
        .shell { width: 100%; }
        header, .no-print { display: none !important; }
        main, .summary-card, .report-section { padding: .4rem; border: 0; border-radius: 0; }
        h1, h2, h3 { page-break-after: avoid; }
        tr { page-break-inside: avoid; }
        a { color: #000; text-decoration: none; }
    }
</style>

<h1>Aralytics</h1>
<p class="muted">A descriptive summary of your saved study sessions.</p>

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
        <p class="muted">From your check-ins for <?= esc($checkInSummary['period_label']) ?>. This describes your ratings only; it is not an assessment or prediction. It is private to you and is not included in printed reports.</p>
        <?php if ($checkInSummary['check_in_count'] === 0): ?>
            <p>No check-ins recorded in this period.</p>
        <?php else: ?>
            <p>Across <?= esc((string) $checkInSummary['check_in_count']) ?> check-ins: workload was high (4–5) <?= esc((string) $checkInSummary['high_workload_count']) ?> times, energy was low (1–2) <?= esc((string) $checkInSummary['low_energy_count']) ?> times, and both ratings occurred together <?= esc((string) $checkInSummary['high_workload_low_energy_count']) ?> times.</p>
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
<script src="<?= esc(base_url('assets/js/aralytics-print.js')) ?>" defer></script>
<?= $this->endSection() ?>
