<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= esc($pageTitle) ?> · Duno</title>
    <link rel="stylesheet" href="<?= esc(base_url('assets/css/student-navigation.css')) ?>">
    <style {csp-style-nonce}>
        :root { color-scheme: light; font-family: system-ui, sans-serif; color: #282444; background: #F7F6FC; }
        * { box-sizing: border-box; }
        body { margin: 0; min-height: 100vh; padding: 1rem; }
        .shell { width: min(100%, 68rem); margin: 0 auto; }
        header, main { padding: clamp(1rem, 4vw, 2rem); background: #fff; border: 1px solid #e4e0f1; border-radius: 1rem; }
        header { margin-bottom: 1rem; display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 1rem; }
        header p { margin: 0; }
        .wordmark { color: #7465D8; font-size: 1.35rem; font-weight: 800; }
        main { line-height: 1.55; }
        h1 { margin: 0; font-size: clamp(1.7rem, 5vw, 2.3rem); }
        .muted { color: #57536b; }
        .summary-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(min(100%, 12rem), 1fr)); gap: 1rem; margin-top: 1.5rem; }
        .summary-card { min-height: 9rem; padding: 1.1rem; border: 1px solid #e4e0f1; border-radius: .8rem; background: #fff; }
        .summary-card:nth-child(2n) { background: #f5f2ff; }
        .summary-card:nth-child(3n) { background: #effaf4; }
        .summary-card h2 { margin: 0; color: #57536b; font-size: 1rem; font-weight: 650; }
        .summary-card p { margin: .65rem 0 0; color: #282444; font-size: clamp(1.8rem, 6vw, 2.4rem); font-variant-numeric: tabular-nums; font-weight: 800; line-height: 1.1; overflow-wrap: anywhere; }
        .privacy-note { margin-top: 1.5rem; padding: 1rem; border-radius: .7rem; background: #fff3d9; }
        button { padding: .65rem .9rem; border: 0; border-radius: .5rem; color: #fff; background: #7465D8; font: inherit; cursor: pointer; }
        button:focus-visible { outline: 3px solid #c18b17; outline-offset: 3px; }
        @media (max-width: 32rem) {
            body { padding: .5rem; }
            header { align-items: flex-start; }
            .summary-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); gap: .65rem; margin-top: 1rem; }
            .summary-card { min-height: 6.5rem; padding: .75rem; }
            .summary-card h2 { font-size: .85rem; line-height: 1.25; }
            .summary-card p { margin-top: .5rem; font-size: clamp(1.35rem, 7vw, 1.8rem); }
        }
    </style>
</head>
<body class="has-mobile-navigation">
<div class="shell">
    <header>
        <p><span class="wordmark">duno.</span><br><span class="muted">Admin</span></p>
        <form method="post" action="<?= esc(site_url('logout')) ?>">
            <?= csrf_field() ?>
            <button type="submit">Sign out</button>
        </form>
    </header>
    <main>
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
<footer class="icon-credit"><small>Icons: <a href="https://solar-icons.vercel.app/" rel="noopener noreferrer">Solar Icons</a> icon set by 480 Design, licensed under <a href="https://creativecommons.org/licenses/by/4.0/" rel="noopener noreferrer">CC BY 4.0</a>.</small></footer>
<?= $this->include('partials/admin_bottom_nav') ?>
</body>
</html>
