<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $this->renderSection('title') ?> · duno.</title>
    <style {csp-style-nonce}>
        :root { color-scheme: light; font-family: system-ui, sans-serif; color: #292641; background: #F7F6FC; }
        * { box-sizing: border-box; }
        body { min-height: 100vh; margin: 0; color: #292641; background: #F7F6FC; line-height: 1.5; }
        .duno-brand { width: min(100% - 2rem, 42rem); margin: 2rem auto 0; text-align: center; text-decoration: none; }
        .duno-wordmark { display: block; color: #7465D8; font-size: 2rem; font-weight: 800; letter-spacing: -.06em; line-height: 1; }
        .duno-tagline { display: block; width: max-content; margin: .35rem auto 0; padding-bottom: .2rem; border-bottom: 2px solid #FFDA92; color: #615D78; font-size: .95rem; }
        main.container { width: min(100%, 42rem); margin: 1rem auto 2rem; padding: 1rem; }
        .container.d-flex { display: flex; justify-content: center; width: 100%; padding: 1rem 0; }
        .card { width: 100%; max-width: 32rem; border: 1px solid #E5E2F0; border-radius: 1rem; background: #fff; box-shadow: 0 .6rem 1.8rem rgb(54 46 110 / 8%); }
        .card-body { padding: clamp(1.25rem, 5vw, 2rem); }
        .card-title { margin: 0 0 1.5rem; font-size: 1.6rem; line-height: 1.2; }
        .form-floating { margin-bottom: 1rem; }
        .form-floating label { display: block; margin: 0 0 .35rem; font-weight: 600; }
        .form-control { display: block; width: 100%; min-height: 2.8rem; padding: .65rem .75rem; border: 1px solid #8B87A3; border-radius: .5rem; color: #292641; background: #fff; font: inherit; }
        .form-control::placeholder { color: transparent; }
        .form-control:focus { border-color: #7465D8; box-shadow: 0 0 0 .2rem rgb(116 101 216 / 18%); }
        .form-check { margin: .8rem 0; }
        .form-check-input { margin-right: .4rem; accent-color: #7465D8; }
        .d-grid { display: grid; justify-items: center; margin: 1.25rem 0; }
        .btn { min-width: 10rem; padding: .7rem 1rem; border: 0; border-radius: .55rem; color: #fff; background: #7465D8; font: inherit; font-weight: 650; cursor: pointer; }
        .btn:hover { background: #5D50BF; }
        .alert { margin: 1rem 0; padding: .75rem 1rem; border: 1px solid #E5E2F0; border-radius: .6rem; }
        .alert-danger { color: #76231e; background: #fff0ee; }
        .alert-success { color: #214c3b; background: #B9E8D1; }
        .text-center { text-align: center; }
        a { color: #5849BC; }
        :focus-visible { outline: 3px solid #7465D8; outline-offset: 3px; }
        @media (max-width: 32rem) { .duno-brand { margin-top: 1.25rem; } main.container { padding: .75rem; } }
        @media (prefers-reduced-motion: reduce) { *, *::before, *::after { scroll-behavior: auto !important; transition-duration: .01ms !important; } }
    </style>
    <?= $this->renderSection('pageStyles') ?>
</head>
<body>
    <a class="duno-brand" href="<?= esc(site_url()) ?>" aria-label="duno. — Your Little Study Buddy. Home">
        <span class="duno-wordmark">duno.</span>
        <span class="duno-tagline">Your Little Study Buddy.</span>
    </a>
    <main role="main" class="container">
        <?= $this->renderSection('main') ?>
    </main>
    <?= $this->renderSection('pageScripts') ?>
</body>
</html>
