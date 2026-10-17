<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= esc($pageTitle ?? 'Subjects') ?> · duno.</title>
    <style {csp-style-nonce}>
        :root { color-scheme: light; font-family: system-ui, sans-serif; color: var(--duno-ink); background: var(--duno-canvas); --duno-purple: #7465D8; --duno-mint: #B9E8D1; --duno-gold: #FFDA92; --duno-canvas: #F7F6FC; --duno-ink: #292641; --duno-muted: #615D78; --duno-line: #E5E2F0; }
        * { box-sizing: border-box; }
        body { margin: 0; min-height: 100vh; padding: 1rem; }
        .shell { width: min(100%, 56rem); margin: 0 auto; }
        header, main { padding: clamp(1rem, 4vw, 2rem); background: #fff; border: 1px solid var(--duno-line); border-radius: 1rem; }
        header { margin: 0 auto 1rem; display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 1rem; }
        .brand-lockup { display: grid; gap: .1rem; text-decoration: none; }
        .brand-wordmark { margin: 0; color: var(--duno-purple); font-size: 1.45rem; font-weight: 800; letter-spacing: -.05em; line-height: 1; }
        .brand-tagline { width: max-content; margin: 0; padding-bottom: .12rem; border-bottom: 2px solid var(--duno-gold); color: var(--duno-muted); font-size: .78rem; }
        nav, .actions { display: flex; flex-wrap: wrap; gap: .75rem; align-items: center; }
        .student-menu { flex: 1 1 auto; }
        .student-menu > summary { display: none; }
        .student-menu nav { justify-content: flex-end; }
        main { line-height: 1.55; }
        h1 { margin-top: 0; font-size: clamp(1.7rem, 5vw, 2.3rem); }
        a { color: #5849BC; }
        a:focus-visible, button:focus-visible, input:focus-visible, select:focus-visible, summary:focus-visible { outline: 3px solid var(--duno-purple); outline-offset: 3px; }
        .button, button { display: inline-block; padding: .65rem .9rem; border: 0; border-radius: .5rem; color: #fff; background: var(--duno-purple); font: inherit; text-decoration: none; cursor: pointer; transition: background-color 160ms ease, transform 160ms ease; }
        .button:hover, button:hover { background: #5D50BF; }
        .button:active, button:active { transform: translateY(1px); }
        .button.secondary { color: var(--duno-ink); background: #EFEDFA; }
        .button.danger, button.danger { background: #9b302a; }
        .subject-list { list-style: none; padding: 0; margin: .75rem 0; }
        .subject-list li { border-top: 1px solid var(--duno-line); padding: .7rem 0; overflow-wrap: anywhere; }
        .timer-display { margin: 1rem 0 .25rem; font-variant-numeric: tabular-nums; font-size: clamp(2rem, 10vw, 3.5rem); font-weight: 750; }
        fieldset { margin: 1rem 0; padding: 1rem; border: 1px solid var(--duno-line); border-radius: .6rem; }
        label { display: block; font-weight: 650; margin: 1rem 0 .35rem; }
        input[type=text], input[type=date], input[type=search], input[type=number], select { display: block; width: 100%; max-width: 36rem; padding: .7rem; border: 1px solid #71847a; border-radius: .4rem; font: inherit; background: #fff; }
        .notice { padding: .75rem 1rem; border: 1px solid var(--duno-line); border-radius: .5rem; background: var(--duno-mint); }
        .notice.error, .errors { color: #76231e; background: #fff0ee; }
        .errors { padding: .75rem 1rem .75rem 2.25rem; }
        .muted { color: var(--duno-muted); }
        form.inline { display: inline; }
        @media (max-width: 32rem) {
            body { padding: .4rem; }
            header, main { padding: .8rem; border-radius: .7rem; }
            header { align-items: flex-start; gap: .6rem; margin-bottom: .6rem; }
            nav, .actions { gap: .45rem .6rem; }
            .student-menu { flex: 0 1 auto; }
            .student-menu > summary { display: flex; align-items: center; width: max-content; min-height: 44px; padding: .45rem .7rem; border-radius: .4rem; background: #EFEDFA; color: var(--duno-ink); cursor: pointer; }
            .student-menu > summary:hover { background: var(--duno-mint); }
            .student-menu[open] { flex-basis: 100%; }
            .student-menu nav { justify-content: flex-start; margin-top: .5rem; font-size: .94rem; }
            .brand-tagline { max-width: 12rem; }
            .subject-list { margin: .5rem 0; }
            .subject-list li { padding: .55rem 0; }
        }
        @media (prefers-reduced-motion: reduce) { *, *::before, *::after { scroll-behavior: auto !important; transition-duration: .01ms !important; } }
    </style>
</head>
<body>
<div class="shell">
    <header>
        <a class="brand-lockup" href="<?= esc(site_url('student')) ?>" aria-label="duno. — Your Little Study Buddy. Home">
            <span class="brand-wordmark">duno.</span>
            <span class="brand-tagline">Your Little Study Buddy.</span>
        </a>
        <details class="student-menu" data-responsive-menu open>
            <summary>Menu</summary>
            <nav aria-label="Student navigation">
                <a href="<?= esc(site_url('student/priority')) ?>">Duno Focus</a>
                <a href="<?= esc(site_url('student/subjects')) ?>">Subjects</a>
                <a href="<?= esc(site_url('student/topics')) ?>">Topics</a>
                <a href="<?= esc(site_url('student/plan')) ?>">Duno Plan</a>
                <a href="<?= esc(site_url('student#study-ideas-heading')) ?>">Duno Guide</a>
                <a href="<?= esc(site_url('student/sessions')) ?>">Duno Timer</a>
                <a href="<?= esc(site_url('student/check-ins')) ?>">Check-ins</a>
                <a href="<?= esc(site_url('student/aralytics')) ?>">Aralytics</a>
                <form class="inline" method="post" action="<?= esc(site_url('logout')) ?>">
                    <?= csrf_field() ?>
                    <button class="button secondary" type="submit">Sign out</button>
                </form>
            </nav>
        </details>
    </header>
    <main>
        <?php if ($success = session()->getFlashdata('success')): ?>
            <p class="notice" role="status"><?= esc($success) ?></p>
        <?php endif ?>
        <?php if ($error = session()->getFlashdata('error')): ?>
            <p class="notice error" role="alert"><?= esc($error) ?></p>
        <?php endif ?>
        <?= $this->renderSection('content') ?>
    </main>
</div>
<script src="<?= esc(base_url('assets/js/student-navigation.js')) ?>" defer></script>
</body>
</html>
