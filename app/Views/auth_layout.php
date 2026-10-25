<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#f7f6fc">
    <title><?= $this->renderSection('title') ?> · duno.</title>
    <link rel="stylesheet" href="<?= esc(duno_asset('assets/css/duno-ui.css')) ?>">
    <?= $this->renderSection('pageStyles') ?>
</head>
<body class="duno-auth">
    <a class="skip-link" href="#main-content">Skip to main content</a>
    <a class="duno-brand" href="<?= esc(site_url()) ?>" aria-label="duno. — Your Little Study Buddy. Home">
        <span class="duno-wordmark">duno.</span>
        <span class="duno-tagline">Your Little Study Buddy.</span>
    </a>
    <main id="main-content" role="main" class="container">
        <div class="auth-form-region"><?= $this->renderSection('main') ?></div>
        <aside class="auth-aside" aria-label="About Duno">
            <div class="auth-aside-copy">
                <p class="workspace-kicker">One step at a time</p>
                <h1>Make room for a focused study session.</h1>
                <p>Keep your topics, study plan, and saved sessions together in one calm space.</p>
                <ul>
                    <li>Choose a topic to work on</li>
                    <li>Plan with the time you have</li>
                    <li>Look back at your study activity</li>
                </ul>
            </div>
            <img src="<?= esc(duno_asset('assets/images/duno-mascot.png')) ?>" alt="" width="1228" height="1285" aria-hidden="true">
        </aside>
    </main>
    <?= $this->renderSection('pageScripts') ?>
</body>
</html>
