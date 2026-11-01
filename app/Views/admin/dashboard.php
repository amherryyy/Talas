<!doctype html>
<?php $htmlLocale = match (service('request')->getLocale()) { 'fil' => 'fil-PH', 'taglish' => 'en-PH', default => 'en' }; ?>
<html lang="<?= esc($htmlLocale) ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#f7f6fc">
    <?= view('partials/pwa_head') ?>
    <title><?= esc($pageTitle) ?> · duno.</title>
    <link rel="stylesheet" href="<?= esc(duno_asset('assets/css/student-navigation.css')) ?>">
    <link rel="stylesheet" href="<?= esc(duno_asset('assets/css/duno-ui.css')) ?>">
</head>
<body class="duno-admin has-mobile-navigation">
<a class="skip-link" href="#main-content">Skip to main content</a>
<?= view('partials/pwa_network_status') ?>
<?= view('partials/app_notifications') ?>
<div class="shell">
    <header class="admin-header">
        <a class="admin-brand" href="<?= esc(site_url()) ?>" aria-label="duno. home"><span class="wordmark">duno.</span><span class="muted">Admin</span></a>
        <?= view('partials/language_switcher') ?>
        <form method="post" action="<?= esc(site_url('logout')) ?>">
            <?= csrf_field() ?>
            <button type="submit">Sign out</button>
        </form>
    </header>
    <main class="admin-main" id="main-content">
        <header class="admin-overview-heading">
            <div><p class="workspace-kicker">System overview</p><h1>Admin analytics</h1></div>
            <div class="admin-overview-tools">
                <div class="admin-overview-note" role="note">
                    <span class="admin-overview-chip">Aggregate view</span>
                    <p>Student names, individual records, and private check-ins stay out of this dashboard.</p>
                </div>
                <button class="admin-print-button no-print" type="button" data-admin-print>Print dashboard</button>
            </div>
        </header>

        <section class="admin-analytics-section" aria-labelledby="admin-totals-heading">
            <div class="admin-analytics-section-heading"><h2 id="admin-totals-heading">All-time totals</h2><span>All student accounts</span></div>
            <dl class="admin-metric-grid admin-core-metrics">
                <div class="admin-metric"><dt>Student accounts</dt><dd><?= esc(number_format($summary['student_count'])) ?></dd></div>
                <div class="admin-metric"><dt>Subjects</dt><dd><?= esc(number_format($summary['subject_count'])) ?></dd></div>
                <div class="admin-metric"><dt>Topics</dt><dd><?= esc(number_format($summary['topic_count'])) ?></dd></div>
                <div class="admin-metric"><dt>Saved study sessions</dt><dd><?= esc(number_format($summary['saved_session_count'])) ?></dd></div>
                <div class="admin-metric"><dt>Total study minutes</dt><dd><?= esc(number_format($summary['total_study_minutes'])) ?><small>min</small></dd></div>
            </dl>
        </section>

        <section class="admin-analytics-section" aria-labelledby="admin-engagement-heading">
            <div class="admin-analytics-section-heading"><h2 id="admin-engagement-heading">Study activity</h2><span>Saved records</span></div>
            <dl class="admin-metric-grid admin-secondary-metrics">
                <div class="admin-metric"><dt>Saved daily plans</dt><dd><?= esc(number_format($summary['saved_plan_count'])) ?></dd></div>
                <div class="admin-metric">
                    <dt>Recorded visits · 30 days</dt>
                    <dd><?= $summary['recorded_visits_30d'] === null ? '—' : esc(number_format($summary['recorded_visits_30d'])) ?></dd>
                    <?php if ($summary['recorded_visits_30d'] === null): ?><small class="admin-metric-note">Withheld below five students with a recorded visit.</small><?php endif ?>
                </div>
                <div class="admin-metric">
                    <dt>Program selection complete</dt>
                    <dd><?php if ($summary['onboarding_completion_rate'] === null): ?>—<?php else: ?><?= esc(number_format($summary['onboarding_completion_rate'], 1)) ?><small>%</small><?php endif ?></dd>
                    <?php if ($summary['onboarding_completion_rate'] === null): ?><small class="admin-metric-note">Shown when both completion groups contain at least five students.</small><?php endif ?>
                </div>
            </dl>
        </section>

        <section class="admin-analytics-section admin-patterns" aria-labelledby="admin-patterns-heading">
            <div class="admin-analytics-section-heading">
                <div><h2 id="admin-patterns-heading">Study patterns</h2><p>Descriptive snapshots from saved Duno records.</p></div>
            </div>
            <div class="admin-pattern-grid">
                <article class="admin-pattern-card" aria-labelledby="admin-session-pattern-heading">
                    <header class="admin-pattern-card-heading">
                        <h3 id="admin-session-pattern-heading">Saved session lengths</h3>
                        <p>Median and middle half of recorded session durations.</p>
                    </header>
                    <?php if ($summary['session_duration_stats'] === null): ?>
                        <p class="admin-pattern-empty">Average session length and distribution are withheld until at least five students have saved a session.</p>
                    <?php else: ?>
                        <?php
                        $sessionStats = $summary['session_duration_stats'];
                        $rangeScale = max(1, $sessionStats['q3']);
                        $q1Position = min(100, max(0, ($sessionStats['q1'] / $rangeScale) * 100));
                        $medianPosition = min(100, max(0, ($sessionStats['median'] / $rangeScale) * 100));
                        ?>
                        <div class="admin-session-summary">
                            <div class="admin-session-median">
                                <span>Median</span>
                                <strong><?= esc(number_format($sessionStats['median'], 1)) ?><small>min</small></strong>
                            </div>
                            <div class="admin-session-support">
                                <p>Average session length (mean) <b><?= esc(number_format($summary['average_session_minutes'], 1)) ?> min</b></p>
                                <p>Middle 50% from <b><?= esc(number_format($sessionStats['q1'], 1)) ?> to <?= esc(number_format($sessionStats['q3'], 1)) ?> min</b></p>
                            </div>
                        </div>
                        <div class="admin-session-range" role="img" aria-label="Median session length <?= esc(number_format($sessionStats['median'], 1)) ?> minutes. The middle half ranges from <?= esc(number_format($sessionStats['q1'], 1)) ?> to <?= esc(number_format($sessionStats['q3'], 1)) ?> minutes.">
                            <span class="admin-session-range-band" style="left: <?= esc(number_format($q1Position, 2, '.', '')) ?>%"></span>
                            <span class="admin-session-range-median" style="left: <?= esc(number_format($medianPosition, 2, '.', '')) ?>%"></span>
                        </div>
                        <div class="admin-session-range-labels"><span>0 min</span><span>Q1 <?= esc(number_format($sessionStats['q1'], 1)) ?></span><span>Median <?= esc(number_format($sessionStats['median'], 1)) ?></span><span>Q3 <?= esc(number_format($sessionStats['q3'], 1)) ?></span></div>
                        <p class="admin-pattern-footnote">Observed saved durations; this does not measure mastery or all time spent studying.</p>
                    <?php endif ?>
                </article>

                <article class="admin-pattern-card" aria-labelledby="admin-topic-status-heading">
                    <header class="admin-pattern-card-heading">
                        <h3 id="admin-topic-status-heading">Current topic status</h3>
                        <p>Share of students with topics who currently have at least one in each status.</p>
                    </header>
                    <div class="admin-topic-status-list">
                        <?php foreach ($summary['topic_status_snapshot'] as $status): ?>
                            <?php $statusWidth = $status['percent'] === null ? 0 : min(100, max(0, $status['percent'])); ?>
                            <div class="admin-topic-status-row">
                                <div class="admin-topic-status-heading"><span><?= esc($status['label']) ?></span><strong><?= $status['percent'] === null ? 'Withheld' : esc(number_format($status['percent'], 1)) . '%' ?></strong></div>
                                <div class="admin-topic-status-track" aria-hidden="true"><span class="admin-topic-status-fill is-<?= esc($status['key']) ?>" style="width: <?= esc(number_format($statusWidth, 1, '.', '')) ?>%"></span></div>
                            </div>
                        <?php endforeach ?>
                    </div>
                    <p class="admin-pattern-footnote">Groups with fewer than five students are withheld. Students can appear in multiple rows; this is a current self-reported snapshot, not a learning assessment.</p>
                </article>
            </div>
        </section>

        <section class="admin-analytics-section admin-coverage" aria-labelledby="admin-coverage-heading">
            <div class="admin-analytics-section-heading">
                <div>
                    <h2 id="admin-coverage-heading">Workspace setup coverage</h2>
                    <p>Student accounts with at least one saved record at each step.</p>
                </div>
                <span>Current records</span>
            </div>
            <div class="admin-coverage-grid">
                <?php foreach ($summary['workspace_setup_coverage'] as $milestone): ?>
                    <?php $coverageWidth = $milestone['percent'] === null ? 0 : min(100, max(0, $milestone['percent'])); ?>
                    <article class="admin-coverage-item">
                        <div class="admin-coverage-heading">
                            <h3><?= esc($milestone['label']) ?></h3>
                            <?php if ($milestone['percent'] === null): ?>
                                <strong>Withheld</strong>
                            <?php else: ?>
                                <strong><?= esc((string) $milestone['student_count']) ?> <small>students</small></strong>
                            <?php endif ?>
                        </div>
                        <div class="admin-coverage-track" aria-hidden="true"><span style="width: <?= esc(number_format($coverageWidth, 1, '.', '')) ?>%"></span></div>
                        <p><?= $milestone['percent'] === null ? 'Small group hidden' : esc(number_format($milestone['percent'], 1)) . '% of student accounts' ?></p>
                    </article>
                <?php endforeach ?>
            </div>
            <p class="admin-pattern-footnote">Coverage means at least one saved record exists; it does not count unsaved visits or measure learning.</p>
        </section>

        <section class="admin-analytics-section admin-planning" aria-labelledby="admin-planning-heading">
            <div class="admin-analytics-section-heading">
                <div>
                    <h2 id="admin-planning-heading">Planning patterns</h2>
                    <p>Typical plans saved during the last 30 calendar days.</p>
                </div>
                <span>Per-student medians</span>
            </div>
            <?php $planning = $summary['planning_patterns']; ?>
            <?php if ($planning['planner_count'] === null): ?>
                <p class="admin-pattern-empty">Planning patterns are withheld until at least five students have saved plans and five have not.</p>
            <?php else: ?>
                <dl class="admin-planning-grid">
                    <div><dt>Students with a saved plan</dt><dd><?= esc((string) $planning['planner_count']) ?><small><?= esc(number_format($planning['coverage_percent'], 1)) ?>%</small></dd></div>
                    <div><dt>Plan days per student</dt><dd><?= esc(number_format($planning['median_plan_days'], 1)) ?><small>median</small></dd></div>
                    <div><dt>Planned minutes per plan day</dt><dd><?= esc(number_format($planning['median_planned_minutes'], 1)) ?><small>min · median</small></dd></div>
                    <div><dt>Blocks per plan day</dt><dd><?= esc(number_format($planning['median_blocks_per_day'], 1)) ?><small>median</small></dd></div>
                    <div><dt>Available time allocated</dt><dd><?= $planning['median_capacity_percent'] === null ? '—' : esc(number_format($planning['median_capacity_percent'], 1)) . '<small>% · median</small>' ?></dd></div>
                </dl>
            <?php endif ?>
            <p class="admin-pattern-footnote">These are saved plans, not completed study time or evidence that a plan was followed. Each student contributes equally to the medians.</p>
        </section>

        <section class="admin-analytics-section admin-workload" aria-labelledby="admin-workload-heading">
            <div class="admin-analytics-section-heading">
                <div>
                    <h2 id="admin-workload-heading">Upcoming workload</h2>
                    <p>Share of students with unfinished topics who have at least one in each due-date group.</p>
                </div>
                <span>Current snapshot</span>
            </div>
            <div class="admin-topic-status-list">
                <?php foreach ($summary['upcoming_workload'] as $bucket): ?>
                    <?php $workloadWidth = $bucket['percent'] === null ? 0 : min(100, max(0, $bucket['percent'])); ?>
                    <div class="admin-topic-status-row">
                        <div class="admin-topic-status-heading"><span><?= esc($bucket['label']) ?></span><strong><?= $bucket['percent'] === null ? 'Withheld' : esc(number_format($bucket['percent'], 1)) . '%' ?></strong></div>
                        <div class="admin-topic-status-track" aria-hidden="true"><span class="admin-workload-fill is-<?= esc($bucket['key']) ?>" style="width: <?= esc(number_format($workloadWidth, 1, '.', '')) ?>%"></span></div>
                    </div>
                <?php endforeach ?>
            </div>
            <p class="admin-pattern-footnote">Students can have unfinished topics in several date groups, so percentages can overlap and do not need to add to 100%. Groups below five students are withheld.</p>
        </section>

        <section class="admin-analytics-section admin-trends" aria-labelledby="admin-trends-heading">
            <div class="admin-analytics-section-heading"><div><h2 id="admin-trends-heading">Trends</h2><p>System-wide counts; small groups are withheld.</p></div></div>
            <div class="admin-chart-grid">
                <?= view('admin/partials/bar_chart', [
                    'chartId' => 'weekly-visits',
                    'chartTitle' => 'Weekly visits and active students',
                    'chartDescription' => 'Recorded visit-days and distinct active students by week for the last eight complete weeks.',
                    'chartSeries' => $summary['weekly_activity']['visits'],
                    'chartSecondarySeries' => $summary['weekly_activity']['active_students'],
                    'chartPrimaryLabel' => 'Visit-days',
                    'chartSecondaryLabel' => 'Active students',
                ]) ?>
                <?= view('admin/partials/bar_chart', [
                    'chartId' => 'monthly-registrations',
                    'chartTitle' => 'New student accounts',
                    'chartDescription' => 'New student accounts by month for the last six months, including the current month to date.',
                    'chartSeries' => $summary['monthly_registrations'],
                ]) ?>
            </div>
        </section>

        <p class="privacy-note">This dashboard shows system-wide aggregates only. It never shows student names, individual records, program breakdowns, or private check-ins. Trend periods with fewer than five contributing students are withheld.</p>
        <p class="admin-print-meta">Duno admin analytics · Snapshot generated <?= esc(date('F j, Y, g:i A')) ?></p>
    </main>
</div>
<?= view('partials/app_footer', ['footerGroups' => [
    ['id' => 'footer-workspace', 'title' => lang('App.footerWorkspace'), 'links' => [
        ['label' => 'Admin dashboard', 'href' => site_url('admin')],
        ['label' => lang('App.home'), 'href' => site_url('/')],
    ]],
    ['id' => 'footer-explore', 'title' => lang('App.footerExplore'), 'links' => [
        ['label' => 'Duno tools', 'href' => site_url('/#tools')],
        ['label' => lang('App.guide'), 'href' => site_url('/#guide')],
    ]],
]]) ?>
<?= $this->include('partials/admin_bottom_nav') ?>
<script src="<?= esc(duno_asset('assets/js/app-notifications.js')) ?>" defer></script>
<script src="<?= esc(duno_asset('assets/js/pwa.js')) ?>" defer></script>
<script src="<?= esc(duno_asset('assets/js/admin-dashboard.js')) ?>" defer></script>
</body>
</html>
