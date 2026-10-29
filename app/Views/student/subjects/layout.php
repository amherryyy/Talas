<!doctype html>
<?php $htmlLocale = match (service('request')->getLocale()) { 'fil' => 'fil-PH', 'taglish' => 'en-PH', default => 'en' }; ?>
<html lang="<?= esc($htmlLocale) ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#f7f6fc">
    <title><?= esc($pageTitle ?? 'Your study space') ?> · duno.</title>
    <link rel="stylesheet" href="<?= esc(duno_asset('assets/css/student-navigation.css')) ?>">
    <link rel="stylesheet" href="<?= esc(duno_asset('assets/css/duno-ui.css')) ?>">
</head>
<body class="duno-workspace has-mobile-navigation">
<a class="skip-link" href="#main-content"><?= esc(lang('App.skipMain')) ?></a>
<div class="shell">
    <?php
    $currentPath = trim(service('uri')->getPath(), '/');
    $currentPath = preg_replace('#^index\.php/?#', '', $currentPath);
    $isTopicPage = str_contains($currentPath, '/topics');
    $isMorePage = str_starts_with($currentPath, 'student/subjects')
        || str_starts_with($currentPath, 'student/topics')
        || str_starts_with($currentPath, 'student/check-ins');
    $primaryLinks = [
        ['label' => lang('App.focus'), 'path' => 'student/priority', 'icon' => 'target', 'tour' => 'focus'],
        ['label' => lang('App.plan'), 'path' => 'student/plan', 'icon' => 'calendar', 'tour' => 'plan'],
        ['label' => lang('App.timer'), 'path' => 'student/sessions', 'icon' => 'stopwatch', 'tour' => 'timer'],
        ['label' => lang('App.guide'), 'path' => 'student#guide-heading', 'icon' => 'book-2', 'tour' => 'guide'],
        ['label' => lang('App.aralytics'), 'path' => 'student/aralytics', 'icon' => 'chart-2', 'tour' => 'aralytics'],
    ];
    ?>
    <header class="app-header">
        <a class="brand-lockup" href="<?= esc(site_url('student')) ?>"<?= $currentPath === 'student' ? ' aria-current="page"' : '' ?> data-home-nav aria-label="duno. — Your Little Study Buddy. Home">
            <span class="brand-wordmark" data-tour-target="start">duno.</span>
            <span class="brand-tagline">Your Little Study Buddy.</span>
        </a>
        <nav class="workspace-nav" aria-label="<?= esc(lang('App.primaryNav')) ?>">
            <?php foreach ($primaryLinks as $link): ?>
                <?php $isGuide = $link['label'] === 'Guide'; $isCurrent = ! $isGuide && ($currentPath === $link['path'] || str_starts_with($currentPath, $link['path'] . '/')); ?>
                <a href="<?= esc(site_url($link['path'])) ?>"<?= $isCurrent ? ' aria-current="page"' : '' ?><?= $isGuide ? ' data-guide-nav' : '' ?> data-tour-target="<?= esc($link['tour']) ?>">
                    <span class="solar-icon solar-icon-<?= esc($link['icon']) ?>" aria-hidden="true"></span><?= esc($link['label']) ?>
                </a>
            <?php endforeach ?>
        </nav>
        <details class="student-menu" data-student-menu>
            <summary aria-label="<?= esc(lang('App.openMore')) ?>"<?= $isMorePage ? ' aria-current="page"' : '' ?> data-tour-target="more"><span class="solar-icon solar-icon-hamburger-menu" aria-hidden="true"></span><span><?= esc(lang('App.more')) ?></span></summary>
            <nav aria-label="<?= esc(lang('App.moreNav')) ?>">
                <a href="<?= esc(site_url('student')) ?>"<?= $currentPath === 'student' ? ' aria-current="page"' : '' ?> data-home-nav><?= esc(lang('App.home')) ?></a>
                <a href="<?= esc(site_url('student')) ?>#app-tour"><?= esc(lang('App.showTour')) ?></a>
                <a href="<?= esc(site_url('student/subjects')) ?>"<?= str_starts_with($currentPath, 'student/subjects') && ! $isTopicPage ? ' aria-current="page"' : '' ?>><?= esc(lang('App.subjects')) ?></a>
                <a href="<?= esc(site_url('student/topics')) ?>"<?= str_starts_with($currentPath, 'student/topics') || $isTopicPage ? ' aria-current="page"' : '' ?>><?= esc(lang('App.topics')) ?></a>
                <a href="<?= esc(site_url('student/check-ins')) ?>"<?= str_contains($currentPath, 'check-ins') ? ' aria-current="page"' : '' ?>><?= esc(lang('App.privateCheckins')) ?></a>
                <?= view('partials/language_switcher') ?>
                <form method="post" action="<?= esc(site_url('logout')) ?>">
                    <?= csrf_field() ?>
                    <button type="submit"><?= esc(lang('App.signOut')) ?></button>
                </form>
            </nav>
        </details>
    </header>
    <main id="main-content">
        <?= view('partials/app_notifications') ?>
        <?= $this->renderSection('content') ?>
    </main>
</div>
<script src="<?= esc(duno_asset('assets/js/onboarding-tour.js')) ?>" defer></script>
<footer class="icon-credit app-footer">
    <span class="app-footer-brand">duno.</span>
    <span>Your Little Study Buddy.</span>
    <small>Icons: <a href="https://solar-icons.vercel.app/" rel="noopener noreferrer">Solar Icons</a> by 480 Design, CC BY 4.0.</small>
</footer>
<?= $this->include('partials/mobile_bottom_nav') ?>
<?= view('partials/app_tour', [
    'appTourUserId' => (string) auth()->id(),
    'appTourAutoStart' => true,
]) ?>
<?php if ($levelUp = session()->getFlashdata('level_up')): ?>
    <?= $this->include('student/partials/level_up_dialog', ['levelUp' => $levelUp]) ?>
<?php endif ?>
<script src="<?= esc(duno_asset('assets/js/app-notifications.js')) ?>" defer></script>
<script src="<?= esc(duno_asset('assets/js/student-navigation.js')) ?>" defer></script>
<?php if ($levelUp): ?><script src="<?= esc(duno_asset('assets/js/level-up.js')) ?>" defer></script><?php endif ?>
</body>
</html>
