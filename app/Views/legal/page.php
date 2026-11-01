<!doctype html>
<?php $htmlLocale = match (service('request')->getLocale()) { 'fil' => 'fil-PH', 'taglish' => 'en-PH', default => 'en' }; ?>
<html lang="<?= esc($htmlLocale) ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#f7f6fc">
    <meta name="description" content="<?= esc($pageIntro) ?>">
    <?= view('partials/pwa_head') ?>
    <title><?= esc($pageTitle) ?> · duno.</title>
    <link rel="stylesheet" href="<?= esc(duno_asset('assets/css/student-navigation.css')) ?>">
    <link rel="stylesheet" href="<?= esc(duno_asset('assets/css/duno-ui.css')) ?>">
</head>
<body class="duno-landing duno-legal-page">
<a class="skip-link" href="#main-content"><?= esc(lang('App.skipMain')) ?></a>
<?= view('partials/pwa_network_status') ?>
<div class="legal-shell">
    <header class="legal-header">
        <a class="legal-brand" href="<?= esc(site_url('/')) ?>" aria-label="duno. — Your Little Study Buddy. Home">
            <span>duno.</span><small>Your Little Study Buddy.</small>
        </a>
        <a class="legal-home-link" href="<?= esc(site_url('/')) ?>">← <?= esc(lang('App.home')) ?></a>
    </header>
    <nav class="info-page-nav" aria-label="Duno information pages">
        <a href="<?= esc(site_url('about')) ?>"><?= esc(lang('App.about')) ?></a>
        <a href="<?= esc(site_url('terms')) ?>"<?= $slug === 'terms' ? ' aria-current="page"' : '' ?>><?= esc(lang('App.terms')) ?></a>
        <a href="<?= esc(site_url('privacy')) ?>"<?= $slug === 'privacy' ? ' aria-current="page"' : '' ?>><?= esc(lang('App.privacy')) ?></a>
        <a href="<?= esc(site_url('cookies')) ?>"<?= $slug === 'cookies' ? ' aria-current="page"' : '' ?>><?= esc(lang('App.cookies')) ?></a>
    </nav>
    <main id="main-content" class="legal-main">
        <p class="legal-kicker">Duno project notice <span>·</span> Review pending</p>
        <h1><?= esc($pageTitle) ?></h1>
        <p class="legal-intro"><?= esc($pageIntro) ?></p>
        <aside class="legal-review-note" aria-label="Review status">
            <strong>Preliminary notice</strong>
            <p>This page describes the current app, but it is not a final legal policy. The project owner and relevant school or institution still need to confirm the responsible publisher, privacy contact, request process, retention, and hosting details.</p>
        </aside>
        <article class="legal-content">
            <?php foreach ($sections as $index => $section): ?>
                <section aria-labelledby="legal-section-<?= esc((string) $index) ?>">
                    <h2 id="legal-section-<?= esc((string) $index) ?>"><?= esc($section['title']) ?></h2>
                    <?php foreach ($section['paragraphs'] as $paragraph): ?>
                        <p><?= $paragraph ?></p>
                    <?php endforeach ?>
                </section>
            <?php endforeach ?>
        </article>
        <p class="legal-updated">Prepared for review · 1 November 2026</p>
    </main>
</div>
<?= view('partials/app_footer', ['footerGroups' => $footerGroups]) ?>
<script src="<?= esc(duno_asset('assets/js/pwa.js')) ?>" defer></script>
</body>
</html>
