<?php

$statusCode = (string) ($statusCode ?? '404');
$title = (string) ($title ?? lang('App.error404Title'));
$message = (string) ($message ?? lang('App.error404Message'));
$eyebrow = (string) ($eyebrow ?? 'Duno');
$mascot = (string) ($mascot ?? base_url('assets/images/duno-mascot-curious.webp'));
$pageTitle = $title . ' | Duno';
?>
<!doctype html>
<html lang="<?= esc(service('request')->getLocale()) ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title><?= esc($pageTitle) ?></title>
    <link rel="stylesheet" href="<?= esc(base_url('assets/css/duno-errors.css')) ?>">
</head>
<body class="duno-error-page">
    <a class="duno-error-brand" href="<?= esc(site_url('/')) ?>" aria-label="duno. - <?= esc(lang('App.home')) ?>">
        <span class="duno-error-wordmark">duno.</span>
        <span class="duno-error-tagline"><?= esc(lang('App.brandTagline')) ?></span>
    </a>

    <main class="duno-error-main" aria-labelledby="duno-error-title">
        <div class="duno-error-art" aria-hidden="true">
            <span class="duno-error-code"><?= esc($statusCode) ?></span>
            <img src="<?= esc($mascot) ?>" alt="">
        </div>

        <section class="duno-error-copy">
            <p class="duno-error-eyebrow"><?= esc($eyebrow) ?></p>
            <h1 id="duno-error-title"><?= esc($title) ?></h1>
            <p class="duno-error-message"><?= esc($message) ?></p>
            <nav class="duno-error-actions" aria-label="<?= esc(lang('App.errorRecovery')) ?>">
                <a class="duno-error-button duno-error-button-primary" href="<?= esc(site_url('/')) ?>"><?= esc(lang('App.home')) ?></a>
                <a class="duno-error-button duno-error-button-secondary" href="<?= esc(site_url('login')) ?>"><?= esc(lang('App.signIn')) ?></a>
            </nav>
        </section>
    </main>
</body>
</html>
