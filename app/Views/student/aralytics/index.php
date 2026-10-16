<?= $this->extend('student/subjects/layout') ?>

<?= $this->section('content') ?>
<style {csp-style-nonce}>
    .summary-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(min(100%, 10rem), 1fr)); gap: .75rem; }
    .summary-card, .report-section { padding: 1rem; border: 1px solid #dce7df; border-radius: .65rem; }
    .summary-card p { margin: .25rem 0 0; font-size: 1.25rem; font-weight: 700; }
    .report-section { margin-top: 1rem; }
    .report-table { width: 100%; border-collapse: collapse; }
    .report-table th, .report-table td { padding: .55rem; text-align: left; vertical-align: top; border-bottom: 1px solid #dce7df; }
    .report-table th:last-child, .report-table td:last-child { text-align: right; }
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
