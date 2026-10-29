<!DOCTYPE html>
<?php $htmlLocale = match (service('request')->getLocale()) { 'fil' => 'fil-PH', 'taglish' => 'en-PH', default => 'en' }; ?>
<html lang="<?= esc($htmlLocale) ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= esc($pageTitle) ?> · duno.</title>
    <style {csp-style-nonce}>
        :root { color-scheme: light; font-family: system-ui, sans-serif; color: #292641; background: #F7F6FC; }
        body { margin: 0; min-height: 100vh; display: grid; place-items: center; padding: 1.25rem; box-sizing: border-box; }
        main { width: min(100%, 42rem); box-sizing: border-box; padding: clamp(1.5rem, 5vw, 3rem); background: #fff; border: 1px solid #E5E2F0; border-radius: 1rem; }
        h1 { margin: 0 0 1rem; font-size: clamp(1.75rem, 5vw, 2.5rem); }
        p { max-width: 38rem; line-height: 1.65; }
        button { margin-top: 1rem; padding: .7rem 1rem; border: 0; border-radius: .5rem; color: #fff; background: #7465D8; font: inherit; cursor: pointer; }
        button:hover, .action:hover { background: #5D50BF; }
        button:focus-visible, a:focus-visible { outline: 3px solid #7465D8; outline-offset: 3px; }
        .brand:focus-visible { border-radius: .2rem; }
        .action { display: inline-block; margin: .5rem 0; padding: .7rem 1rem; border-radius: .5rem; color: #fff; background: #7465D8; text-decoration: none; }
        .brand { display: inline-block; margin: 0; color: #7465D8; font-size: 1.4rem; font-weight: 750; letter-spacing: -.04em; text-decoration: none; }
        .tagline { width: max-content; margin: .15rem 0 1.5rem; padding-bottom: .2rem; border-bottom: 2px solid #FFDA92; color: #615D78; font-size: .92rem; }
    </style>
    <link rel="stylesheet" href="<?= esc(duno_asset('assets/css/duno-ui.css')) ?>">
</head>
<body class="duno-role">
<a class="skip-link" href="#main-content">Skip to main content</a>
<?= view('partials/app_notifications') ?>
<main id="main-content">
    <?= view('partials/language_switcher') ?>
    <a class="brand" href="<?= esc(site_url()) ?>" aria-label="duno. home">duno.</a>
    <p class="tagline">Your Little Study Buddy.</p>
    <h1><?= esc($heading) ?></h1>
    <p><?= esc($message) ?></p>
    <?php if (isset($actionHref, $actionLabel)): ?>
        <p><a class="action" href="<?= esc($actionHref) ?>"><?= esc($actionLabel) ?></a></p>
    <?php endif ?>
    <form method="post" action="<?= esc(site_url('logout')) ?>">
        <?= csrf_field() ?>
        <button type="submit">Sign out</button>
    </form>
</main>
<script src="<?= esc(duno_asset('assets/js/app-notifications.js')) ?>" defer></script>
</body>
</html>
