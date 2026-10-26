<?php
$isStudent = $role === 'student';
$isAdmin = $role === 'admin';
$canOpenStudentTools = $isStudent;
$tools = [
    [
        'name' => 'Duno Focus',
        'eyebrow' => 'Choose a next step',
        'description' => 'See unfinished topics in a clear, explainable order.',
        'path' => 'student/priority',
        'icon' => 'target',
        'tone' => 'lavender',
        'tour' => 'focus',
    ],
    [
        'name' => 'Duno Plan',
        'eyebrow' => 'Shape your study time',
        'description' => 'Build and adjust a plan around the time you have.',
        'path' => 'student/plan',
        'icon' => 'calendar',
        'tone' => 'mint',
        'tour' => 'plan',
    ],
    [
        'name' => 'Duno Timer',
        'eyebrow' => 'Settle into a session',
        'description' => 'Time a study session and save it when you are done.',
        'path' => 'student/sessions',
        'icon' => 'stopwatch',
        'tone' => 'gold',
        'tour' => 'timer',
    ],
    [
        'name' => 'Aralytics',
        'eyebrow' => 'Look back at your work',
        'description' => 'Review simple summaries of your recorded study time.',
        'path' => 'student/aralytics',
        'icon' => 'chart-2',
        'tone' => 'blue',
        'tour' => 'aralytics',
    ],
];
$joinUrl = site_url('register');
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#f7f6fc">
    <meta name="description" content="Organize study topics, plan the time you have, and keep track of your sessions with Duno.">
    <title>duno. &mdash; Your Little Study Buddy.</title>
    <style {csp-style-nonce}>
        :root {
            color-scheme: light;
            --canvas: #f7f6fc;
            --surface: #fff;
            --ink: #2e2e3a;
            --muted: #64677b;
            --line: #e3e1eb;
            --purple: #7465d8;
            --purple-dark: #5746bd;
            --mint: #b9e8d1;
            --gold: #ffda92;
            --blue: #d7e8fa;
            --shadow: 0 12px 30px rgb(42 37 79 / 6%);
            --bottom-nav-line: var(--line);
            --bottom-nav-surface: rgb(255 255 255 / 96%);
            --bottom-nav-muted: var(--muted);
            --bottom-nav-active-surface: #f0edff;
            --bottom-nav-active-ink: var(--purple-dark);
        }
        * { box-sizing: border-box; }
        body {
            min-width: 320px;
            margin: 0;
            padding: 0 0 calc(2rem + env(safe-area-inset-bottom));
            background: var(--canvas);
            color: var(--ink);
            font-family: "Aptos", "Segoe UI", system-ui, sans-serif;
            line-height: 1.5;
        }
        a { color: inherit; }
        a:focus-visible, button:focus-visible, summary:focus-visible {
            outline: 3px solid var(--purple);
            outline-offset: 4px;
        }
        .shell { width: min(100% - 2rem, 68rem); margin-inline: auto; }
        .topbar {
            position: sticky;
            z-index: 5;
            top: 0;
            border-bottom: 1px solid rgb(227 225 235 / 80%);
            background: rgb(247 246 252 / 92%);
            backdrop-filter: blur(14px);
        }
        .topbar-inner { display: flex; min-height: 5rem; align-items: center; justify-content: space-between; gap: 1rem; }
        .brand { display: inline-flex; flex-direction: column; text-decoration: none; line-height: 1.1; }
        .wordmark { color: var(--purple); font-size: 1.65rem; font-weight: 850; letter-spacing: -.07em; }
        .tagline { margin-top: .2rem; color: var(--muted); font-size: .72rem; font-weight: 600; }
        .top-actions { display: flex; align-items: center; gap: .6rem; }
        .pill, .menu summary {
            display: inline-flex;
            min-height: 2.8rem;
            align-items: center;
            justify-content: center;
            border: 1px solid var(--line);
            border-radius: 999px;
            background: var(--surface);
            box-shadow: 0 3px 12px rgb(42 37 79 / 4%);
            color: var(--ink);
            font-size: .9rem;
            font-weight: 700;
            padding: .55rem 1rem;
            text-decoration: none;
            white-space: nowrap;
        }
        .pill-primary { border-color: var(--purple); background: var(--purple); color: #fff; }
        .menu { position: relative; }
        .menu summary { display: grid; width: 2.7rem; height: 2.7rem; place-items: center; cursor: pointer; list-style: none; border: 1px solid var(--line); border-radius: 50%; background: var(--surface); box-shadow: 0 2px 8px rgb(42 37 79 / 7%); }
        .menu summary .solar-icon { width: 1.2rem; height: 1.2rem; }
        .menu[open] summary { background: #f0edff; }
        .menu summary::-webkit-details-marker { display: none; }
        .menu-panel {
            position: absolute;
            top: calc(100% + .5rem);
            right: 0;
            display: grid;
            min-width: 12rem;
            gap: .2rem;
            padding: .5rem;
            border: 1px solid var(--line);
            border-radius: 1rem;
            background: var(--surface);
            box-shadow: var(--shadow);
        }
        .menu-panel a { border-radius: .6rem; padding: .65rem .75rem; text-decoration: none; }
        .menu-panel a:hover { background: var(--canvas); }
        .menu-panel form { margin: 0; }
        .menu-button { width: 100%; border: 0; border-radius: .6rem; background: transparent; color: inherit; cursor: pointer; font: inherit; padding: .65rem .75rem; text-align: left; }
        .menu-button:hover { background: var(--canvas); }
        .main { padding-top: clamp(1.25rem, 4vw, 3rem); }
        #home, #tools, #guide { scroll-margin-top: 6rem; }
        .welcome {
            position: relative;
            overflow: hidden;
            padding: clamp(1.4rem, 4vw, 2.8rem);
            border: 1px solid var(--line);
            border-radius: 2rem;
            background: var(--surface);
            box-shadow: var(--shadow);
        }
        .welcome-layout { display: grid; grid-template-columns: minmax(0, 1fr) minmax(9rem, 14rem); align-items: center; gap: 1rem; }
        .welcome-copy { min-width: 0; }
        .welcome-mascot { display: block; width: 100%; max-width: 14rem; height: auto; justify-self: center; filter: drop-shadow(0 .8rem 1rem rgb(75 61 134 / 14%)); }
        .welcome::after {
            position: absolute;
            right: -2.7rem;
            bottom: -4.8rem;
            width: 13rem;
            height: 13rem;
            border-radius: 50%;
            background: radial-gradient(circle at 35% 35%, #e8e4ff, #f3f0ff 64%, transparent 65%);
            content: "";
            pointer-events: none;
        }
        .welcome-layout { position: relative; z-index: 1; }
        .overline { margin: 0 0 .5rem; color: var(--purple-dark); font-size: .78rem; font-weight: 800; letter-spacing: .1em; text-transform: uppercase; }
        h1 { max-width: 13ch; margin: 0; font-size: clamp(2rem, 7vw, 4.1rem); font-weight: 850; letter-spacing: -.065em; line-height: 1.02; }
        .lead { max-width: 36rem; margin: 1rem 0 1.3rem; color: var(--muted); font-size: clamp(1rem, 2.5vw, 1.15rem); }
        .welcome-actions { display: flex; flex-wrap: wrap; gap: .65rem; }
        .section-heading { display: flex; align-items: end; justify-content: space-between; gap: 1rem; margin: 2rem 0 .9rem; }
        .section-heading h2 { margin: 0; font-size: clamp(1.25rem, 4vw, 1.7rem); letter-spacing: -.04em; }
        .section-heading p { margin: 0; color: var(--muted); font-size: .9rem; }
        .tool-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: .8rem; }
        .tool-card {
            display: flex;
            min-height: 11rem;
            flex-direction: column;
            align-items: flex-start;
            justify-content: space-between;
            gap: 1.25rem;
            padding: 1.15rem;
            border: 1px solid var(--line);
            border-radius: 1.45rem;
            background: var(--surface);
            box-shadow: 0 6px 18px rgb(42 37 79 / 4%);
            text-decoration: none;
            transition: transform .18s ease, box-shadow .18s ease;
        }
        a.tool-card:hover { transform: translateY(-2px); box-shadow: var(--shadow); }
        .tool-mark { display: grid; width: 2.7rem; height: 2.7rem; place-items: center; border-radius: 1rem; }
        .lavender .tool-mark { background: #ece9ff; color: #5949c2; }
        .mint .tool-mark { background: #e3f7ed; color: #326c52; }
        .gold .tool-mark { background: #fff3d9; color: #765000; }
        .blue .tool-mark { background: #e8f2fd; color: #365b83; }
        .tool-copy { display: block; }
        .tool-eyebrow { display: block; margin-bottom: .2rem; color: var(--muted); font-size: .77rem; font-weight: 650; }
        .tool-name { display: block; font-size: clamp(1.05rem, 3vw, 1.35rem); font-weight: 820; letter-spacing: -.035em; }
        .tool-description { display: block; margin-top: .35rem; color: var(--muted); font-size: .86rem; line-height: 1.4; }
        .guide-note {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            margin-top: .85rem;
            padding: 1rem 1.1rem;
            border: 1px solid #ddd8ff;
            border-radius: 1.2rem;
            background: #f0edff;
        }
        .guide-note p { display: flex; align-items: center; gap: .65rem; margin: 0; }
        .guide-note .solar-icon { width: 1.6rem; height: 1.6rem; color: var(--purple-dark); }
        .guide-note strong { display: block; }
        .guide-note span { color: #514d67; font-size: .87rem; }
        .guide-link { flex: none; color: var(--purple-dark); font-weight: 800; text-decoration-thickness: 2px; text-underline-offset: 3px; }
        .public-guide { margin: clamp(2rem, 5vw, 3.5rem) 0 1.5rem; scroll-margin-top: 6rem; }
        .public-guide h2 { margin: 0; font-size: clamp(1.5rem, 3vw, 2rem); letter-spacing: -.04em; }
        .public-guide-intro { margin: .4rem 0 1rem; color: var(--muted); }
        .public-guide-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: .8rem; margin: 0; padding: 0; list-style: none; }
        .public-guide-card { height: 100%; padding: 1rem; border: 1px solid var(--line); border-radius: 1rem; background: var(--surface); box-shadow: 0 8px 24px rgb(42 37 79 / 4%); }
        .public-guide-card h3 { margin: 0 0 .4rem; font-size: 1rem; }
        .public-guide-card p { margin: 0; color: var(--muted); font-size: .9rem; }
        .public-guide-card .guide-recommendation { margin-top: .65rem; font-size: .78rem; }
        .public-guide-source { margin: .8rem 0 0; color: var(--muted); font-size: .78rem; }
        .public-guide-source a { color: var(--purple-dark); font-weight: 700; }
        .privacy-note { max-width: 54rem; margin: 1.2rem auto 1.5rem; color: var(--muted); font-size: .84rem; text-align: center; }
        .footer { padding: .25rem 0 1.5rem; color: var(--muted); font-size: .78rem; text-align: center; }
        .footer small { display: block; margin-top: .35rem; }
        @media (min-width: 700px) {
            .tool-grid { gap: 1rem; }
            .tool-card { min-height: 14rem; padding: 1.4rem; }
        }
        @media (max-width: 520px) {
            body { padding-bottom: calc(6.2rem + env(safe-area-inset-bottom)); }
            .shell { width: min(100% - 1.25rem, 36rem); }
            .topbar-inner { min-height: 4.4rem; }
            .tagline { font-size: .67rem; }
            .top-actions { gap: .4rem; }
            .pill, .menu summary { min-height: 2.55rem; padding: .45rem .8rem; font-size: .82rem; }
            .welcome { border-radius: 1.55rem; }
            .welcome::after { right: -4rem; bottom: -5.5rem; }
            .welcome-layout { grid-template-columns: minmax(0, 1fr) 6.5rem; gap: .35rem; }
            .welcome-mascot { width: 6.5rem; }
            h1 { font-size: clamp(1.8rem, 8vw, 2.7rem); }
            .tool-grid { gap: .65rem; }
            .tool-card { min-height: 10.5rem; padding: .9rem; border-radius: 1.25rem; }
            .tool-mark { width: 2.4rem; height: 2.4rem; border-radius: .85rem; }
            .tool-description { font-size: .78rem; }
            .section-heading { align-items: flex-start; flex-direction: column; gap: .15rem; }
            .guide-note { align-items: flex-start; flex-direction: column; }
            .public-guide-grid { grid-template-columns: 1fr; }
            .student-bottom-nav { --bottom-nav-surface: rgb(255 255 255 / 96%); }
        }
        @media (max-width: 400px) {
            .top-actions > .pill:not(.pill-primary) { display: none; }
        }
        @media (max-width: 360px) {
            .top-actions > .pill-primary { display: none; }
        }
        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after { scroll-behavior: auto !important; transition-duration: .01ms !important; animation-duration: .01ms !important; }
        }
    </style>
    <link rel="stylesheet" href="<?= esc(duno_asset('assets/css/student-navigation.css')) ?>">
    <link rel="stylesheet" href="<?= esc(duno_asset('assets/css/duno-ui.css')) ?>">
</head>
<body class="duno-landing <?= ! $isLoggedIn || $isStudent || $isAdmin ? 'has-mobile-navigation' : '' ?>">
<a class="skip-link" href="#main-content">Skip to main content</a>
<header class="topbar">
    <div class="shell topbar-inner">
        <a class="brand" href="<?= esc(site_url('/')) ?>" aria-label="duno. home">
            <span class="wordmark">duno.</span>
            <span class="tagline">Your Little Study Buddy.</span>
        </a>
        <div class="top-actions">
            <?php if ($isLoggedIn && $workspaceUrl !== null): ?>
                <a class="pill pill-primary" href="<?= esc($workspaceUrl) ?>"><?= esc($workspaceLabel) ?></a>
            <?php elseif (! $isLoggedIn): ?>
                <a class="pill" href="<?= esc(site_url('login')) ?>">Sign in</a>
                <a class="pill pill-primary" href="<?= esc($joinUrl) ?>">Get started</a>
            <?php else: ?>
                <span class="pill">Signed in</span>
            <?php endif ?>
            <details class="menu">
                <summary aria-label="Toggle main menu" data-tour-target="more"><span class="solar-icon solar-icon-hamburger-menu" aria-hidden="true"></span></summary>
                <nav class="menu-panel" aria-label="Main menu">
                    <a href="<?= esc(site_url('')) ?>">Home</a>
                    <?php if ($isLoggedIn && $workspaceUrl !== null): ?>
                        <a href="<?= esc($workspaceUrl) ?>"><?= esc($workspaceLabel) ?></a>
                    <?php endif ?>
                    <?php if ($isStudent): ?>
                        <a href="<?= esc(site_url('student#app-tour')) ?>">Show app tour</a>
                    <?php endif ?>
                    <?php if (! $isLoggedIn): ?>
                        <a href="<?= esc(site_url('register')) ?>">Create account</a>
                        <a href="<?= esc(site_url('login')) ?>">Sign in</a>
                    <?php else: ?>
                        <form method="post" action="<?= esc(site_url('logout')) ?>">
                            <?= csrf_field() ?>
                            <button class="menu-button" type="submit">Sign out</button>
                        </form>
                    <?php endif ?>
                </nav>
            </details>
        </div>
    </div>
</header>

<main class="shell main" id="main-content">
    <section class="welcome" id="home" aria-labelledby="welcome-heading"<?= $isStudent ? ' data-tour-target="start"' : '' ?>>
        <div class="welcome-layout">
            <div class="welcome-copy">
                <p class="overline"><?= $isStudent ? 'Your study space' : 'A little more focus, one step at a time' ?></p>
                <h1 id="welcome-heading"><?= $isStudent ? 'What are we studying today?' : 'Make room for your next study step.' ?></h1>
                <p class="lead">
                    <?= $isStudent
                        ? 'Choose a tool, work at your pace, and keep your study activity in one place.'
                        : 'Organize topics, make a plan, and keep track of study sessions with Duno.' ?>
                </p>
                <div class="welcome-actions">
                    <?php if (! $isLoggedIn): ?>
                        <a class="pill pill-primary" href="<?= esc($joinUrl) ?>">Create your student account</a>
                        <a class="pill" href="<?= esc(site_url('login')) ?>">I already have an account</a>
                    <?php elseif ($workspaceUrl !== null): ?>
                        <a class="pill pill-primary" href="<?= esc($workspaceUrl) ?>"><?= esc($workspaceLabel) ?></a>
                    <?php endif ?>
                </div>
            </div>
            <img class="welcome-mascot" src="<?= esc(duno_asset('assets/images/duno-mascot.png')) ?>" alt="Duno, a cheerful lightbulb study buddy wearing a Filipino salakot" width="1228" height="1285" fetchpriority="high">
        </div>
    </section>

    <section id="tools" aria-labelledby="tools-heading">
        <div class="section-heading">
            <h2 id="tools-heading"><?= $isStudent ? 'Pick a place to begin' : 'A simple study toolkit' ?></h2>
            <p><?= $isStudent ? 'Your tools, ready when you are.' : 'Four small tools to support your study routine.' ?></p>
        </div>
        <div class="tool-grid">
            <?php foreach ($tools as $tool): ?>
                <?php $toolUrl = site_url($tool['path']); ?>
                <?php if ($canOpenStudentTools): ?>
                <a class="tool-card <?= esc($tool['tone']) ?>" href="<?= esc($toolUrl) ?>" data-tour-target="<?= esc($tool['tour']) ?>">
                <?php else: ?>
                <article class="tool-card <?= esc($tool['tone']) ?>">
                <?php endif ?>
                    <span class="tool-mark" aria-hidden="true"><span class="solar-icon solar-icon-<?= esc($tool['icon']) ?>"></span></span>
                    <span class="tool-copy">
                        <span class="tool-eyebrow"><?= esc($tool['eyebrow']) ?></span>
                        <span class="tool-name"><?= esc($tool['name']) ?></span>
                        <span class="tool-description"><?= esc($tool['description']) ?></span>
                    </span>
                <?php if ($canOpenStudentTools): ?>
                </a>
                <?php else: ?>
                </article>
                <?php endif ?>
            <?php endforeach ?>
        </div>
    </section>

    <?php if (! $isLoggedIn): ?>
    <section class="public-guide" id="guide" aria-labelledby="guide-heading">
        <h2 id="guide-heading">Duno Guide</h2>
        <p class="public-guide-intro">Short, general study ideas. Choose what fits your course.</p>
        <ul class="public-guide-grid">
            <?php foreach ($suggestions as $suggestion): ?>
            <li>
                <article class="public-guide-card" aria-labelledby="public-suggestion-<?= esc($suggestion['id']) ?>">
                    <h3 id="public-suggestion-<?= esc($suggestion['id']) ?>"><?= esc($suggestion['title']) ?></h3>
                    <p><?= esc($suggestion['body']) ?></p>
                    <p class="guide-recommendation"><?= esc($suggestion['recommendation']) ?></p>
                </article>
            </li>
            <?php endforeach ?>
        </ul>
        <p class="public-guide-source">Adapted from <a href="<?= esc($suggestionSourceUrl) ?>" rel="noopener noreferrer"><?= esc($suggestionSourceName) ?></a>.</p>
    </section>
    <?php else: ?>
    <aside class="guide-note" aria-label="Duno Guide"<?= $isStudent ? ' data-tour-target="guide"' : '' ?>>
        <p><span class="solar-icon solar-icon-book-2" aria-hidden="true"></span><span><strong>Duno Guide</strong><span>Short, curated study ideas you can try when they fit your course.</span></span></p>
        <?php if ($isStudent || $isAdmin || ! $isLoggedIn): ?>
        <a class="guide-link" href="<?= esc($isStudent ? site_url('student') : ($isAdmin ? $workspaceUrl : $joinUrl)) ?>">
            <?= $isStudent ? 'See study ideas' : 'Explore Duno' ?>
        </a>
        <?php endif ?>
    </aside>
    <?php endif ?>

    <p class="privacy-note">Workload and energy check-ins are optional and private to the student who records them. They are not shown in admin summaries or study reports.</p>
</main>

<?php if ($isStudent): ?>
<?= $this->include('partials/mobile_bottom_nav') ?>
<?php elseif ($isAdmin): ?>
<?= $this->include('partials/admin_bottom_nav') ?>
<?php elseif (! $isLoggedIn): ?>
<?= $this->include('partials/guest_bottom_nav') ?>
<?php endif ?>
<?php if ($isStudent): ?>
<?= view('partials/app_tour', [
    'appTourUserId' => $appTourUserId,
    'appTourAutoStart' => true,
]) ?>
<?php endif ?>

<footer class="shell footer">duno. &middot; Your Little Study Buddy.<small>Icons: <a href="https://solar-icons.vercel.app/" rel="noopener noreferrer">Solar Icons</a> icon set by 480 Design, licensed under <a href="https://creativecommons.org/licenses/by/4.0/" rel="noopener noreferrer">CC BY 4.0</a>.</small></footer>
<?php if (! $isLoggedIn): ?>
<script type="module" src="<?= esc(duno_asset('assets/js/public-bottom-nav.js')) ?>"></script>
<?php endif ?>
<?php if ($isStudent): ?><script src="<?= esc(duno_asset('assets/js/onboarding-tour.js')) ?>" defer></script><?php endif ?>
</body>
</html>
