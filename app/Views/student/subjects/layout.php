<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= esc($pageTitle ?? 'Subjects') ?> · duno.</title>
    <link rel="stylesheet" href="<?= esc(base_url('assets/css/student-navigation.css')) ?>">
    <style {csp-style-nonce}>
        :root { color-scheme: light; font-family: system-ui, sans-serif; color: var(--duno-ink); background: var(--duno-canvas); --duno-purple: #7465D8; --duno-mint: #B9E8D1; --duno-gold: #FFDA92; --duno-lavender: #EDE9FE; --duno-canvas: #F7F6FC; --duno-ink: #2E2E3A; --duno-muted: #615D78; --duno-line: #E5E2F0; }
        * { box-sizing: border-box; }
        body { margin: 0; min-height: 100vh; padding: 1rem; }
        .shell { width: min(100%, 56rem); margin: 0 auto; }
        header, main { padding: clamp(1rem, 4vw, 2rem); background: #fff; border: 1px solid var(--duno-line); border-radius: 1rem; }
        header { margin: 0 auto 1rem; display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 1rem; }
        .brand-lockup { display: grid; gap: .1rem; text-decoration: none; }
        .brand-wordmark { margin: 0; color: var(--duno-purple); font-size: 1.45rem; font-weight: 800; letter-spacing: -.05em; line-height: 1; }
        .brand-tagline { width: max-content; margin: 0; padding-bottom: .12rem; border-bottom: 2px solid var(--duno-gold); color: var(--duno-muted); font-size: .78rem; }
        nav, .actions { display: flex; flex-wrap: wrap; gap: .75rem; align-items: center; }
        .student-menu { position: relative; flex: 1 1 auto; }
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
            .student-menu { flex: 0 0 auto; }
            .student-menu > summary { display: grid; width: 44px; height: 44px; place-items: center; border: 1px solid var(--duno-line); border-radius: 50%; background: #fff; color: var(--duno-ink); cursor: pointer; box-shadow: 0 2px 8px rgb(42 37 79 / 7%); }
            .student-menu > summary:hover, .student-menu[open] > summary { background: #EFEDFA; }
        .student-menu > summary .solar-icon { width: 21px; height: 21px; }
            .student-menu nav { position: absolute; z-index: 20; top: calc(100% + .45rem); right: 0; display: grid; width: min(17rem, calc(100vw - 1.5rem)); max-height: min(70vh, 30rem); align-content: start; gap: .2rem; overflow-y: auto; margin: 0; padding: .5rem; border: 1px solid var(--duno-line); border-radius: .9rem; background: #fff; box-shadow: 0 12px 32px rgb(34 30 60 / 18%); font-size: .92rem; }
            .student-menu nav a { padding: .62rem .7rem; border-radius: .55rem; text-decoration: none; }
            .student-menu nav a:hover, .student-menu nav a:focus-visible { background: #EFEDFA; }
            .student-menu nav form { padding: .25rem; }
            .student-menu nav button { width: 100%; text-align: left; }
            .brand-tagline { max-width: 12rem; }
            .subject-list { margin: .5rem 0; }
            .subject-list li { padding: .55rem 0; }
        }
        @media (prefers-reduced-motion: reduce) { *, *::before, *::after { scroll-behavior: auto !important; transition-duration: .01ms !important; } }
    </style>
</head>
<body class="has-mobile-navigation">
<div class="shell">
    <header>
        <a class="brand-lockup" href="<?= esc(site_url('student')) ?>" aria-label="duno. — Your Little Study Buddy. Home">
            <span class="brand-wordmark">duno.</span>
            <span class="brand-tagline">Your Little Study Buddy.</span>
        </a>
        <details class="student-menu" data-responsive-menu>
            <summary aria-label="Toggle student menu"><span class="solar-icon solar-icon-hamburger-menu" aria-hidden="true"></span></summary>
            <nav aria-label="More student pages">
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
<footer class="icon-credit"><small>Icons: <a href="https://solar-icons.vercel.app/" rel="noopener noreferrer">Solar Icons</a> icon set by 480 Design, licensed under <a href="https://creativecommons.org/licenses/by/4.0/" rel="noopener noreferrer">CC BY 4.0</a>.</small></footer>
<?= $this->include('partials/mobile_bottom_nav') ?>
<script src="<?= esc(base_url('assets/js/student-navigation.js')) ?>" defer></script>
</body>
</html>
