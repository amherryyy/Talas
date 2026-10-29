<!doctype html>
<?php $htmlLocale = match (service('request')->getLocale()) { 'fil' => 'fil-PH', 'taglish' => 'en-PH', default => 'en' }; ?>
<html lang="<?= esc($htmlLocale) ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#f7f6fc">
    <title><?= $this->renderSection('title') ?> · duno.</title>
    <link rel="stylesheet" href="<?= esc(duno_asset('assets/css/duno-ui.css')) ?>">
    <?= $this->renderSection('pageStyles') ?>
</head>
<body class="duno-auth">
    <a class="skip-link" href="#main-content"><?= esc(lang('App.skipMain')) ?></a>
    <?= view('partials/app_notifications', ['includeFlashNotifications' => false]) ?>
    <header class="auth-header">
    <a class="duno-brand" href="<?= esc(site_url()) ?>" aria-label="duno. — <?= esc(lang('App.brandTagline')) ?> Home">
        <span class="duno-wordmark">duno.</span>
        <span class="duno-tagline">Your Little Study Buddy.</span>
    </a>
    <?= view('partials/language_switcher') ?>
    </header>
    <main id="main-content" role="main" class="container">
        <aside class="auth-aside" aria-label="About Duno">
            <div class="auth-mascot-stage">
                <span class="auth-mascot-orbit auth-mascot-orbit-one" aria-hidden="true"></span>
                <span class="auth-mascot-orbit auth-mascot-orbit-two" aria-hidden="true"></span>
                <button class="auth-mascot-button" type="button" data-auth-mascot data-cheer-src="<?= esc(duno_asset('assets/images/duno-mascot-celebrating.webp')) ?>" aria-label="Tap Duno for a little encouragement">
                    <img src="<?= esc(duno_asset('assets/images/duno-mascot-welcome.webp')) ?>" alt="" width="512" height="536" aria-hidden="true" fetchpriority="high">
                </button>
                <p class="auth-mascot-message" data-auth-mascot-message aria-live="polite">One small step at a time.</p>
            </div>
            <div class="auth-aside-copy">
                <p class="workspace-kicker"><?= esc(lang('App.authKicker')) ?></p>
                <h1><?= esc(lang('App.authHeadline')) ?></h1>
                <p><?= esc(lang('App.authDescription')) ?></p>
            </div>
        </aside>
        <div class="auth-form-region"><?= $this->renderSection('main') ?></div>
    </main>
    <script src="<?= esc(duno_asset('assets/js/auth-mascot.js')) ?>" defer></script>
    <?= $this->renderSection('pageScripts') ?>
    <script src="<?= esc(duno_asset('assets/js/app-notifications.js')) ?>" defer></script>
</body>
</html>
