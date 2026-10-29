<!doctype html>
<?php $htmlLocale = match (service('request')->getLocale()) { 'fil' => 'fil-PH', 'taglish' => 'en-PH', default => 'en' }; ?>
<html lang="<?= esc($htmlLocale) ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#f7f6fc">
    <title><?= esc($pageTitle) ?> · duno.</title>
    <link rel="stylesheet" href="<?= esc(duno_asset('assets/css/student-navigation.css')) ?>">
    <link rel="stylesheet" href="<?= esc(duno_asset('assets/css/duno-ui.css')) ?>">
</head>
<body class="duno-admin has-mobile-navigation">
<a class="skip-link" href="#main-content">Skip to main content</a>
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
        <p class="workspace-kicker">Overview</p>
        <h1>Admin dashboard</h1>
        <p class="muted">Aggregate activity across student accounts.</p>

        <section class="summary-grid" aria-label="Student activity totals">
            <article class="summary-card"><h2>Student accounts</h2><p><?= esc(number_format($summary['student_count'])) ?></p></article>
            <article class="summary-card"><h2>Subjects</h2><p><?= esc(number_format($summary['subject_count'])) ?></p></article>
            <article class="summary-card"><h2>Topics</h2><p><?= esc(number_format($summary['topic_count'])) ?></p></article>
            <article class="summary-card"><h2>Saved study sessions</h2><p><?= esc(number_format($summary['saved_session_count'])) ?></p></article>
            <article class="summary-card"><h2>Total study minutes</h2><p><?= esc(number_format($summary['total_study_minutes'])) ?></p></article>
        </section>

        <p class="privacy-note">This dashboard shows totals only. It does not display account names, individual study records, or private check-in information.</p>
    </main>
</div>
<footer class="icon-credit app-footer">
    <span class="app-footer-brand">duno.</span>
    <span>Your Little Study Buddy.</span>
    <small>Icons: <a href="https://solar-icons.vercel.app/" rel="noopener noreferrer">Solar Icons</a> by 480 Design, CC BY 4.0.</small>
</footer>
<?= $this->include('partials/admin_bottom_nav') ?>
<script src="<?= esc(duno_asset('assets/js/app-notifications.js')) ?>" defer></script>
</body>
</html>
