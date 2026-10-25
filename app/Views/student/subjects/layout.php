<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#f7f6fc">
    <title><?= esc($pageTitle ?? 'Your study space') ?> · duno.</title>
    <link rel="stylesheet" href="<?= esc(base_url('assets/css/student-navigation.css?v=20261025-ui1')) ?>">
    <link rel="stylesheet" href="<?= esc(base_url('assets/css/duno-ui.css?v=20261025-ui7')) ?>">
</head>
<body class="duno-workspace has-mobile-navigation">
<a class="skip-link" href="#main-content">Skip to main content</a>
<div class="shell">
    <header class="app-header">
        <a class="brand-lockup" href="<?= esc(site_url('student')) ?>" aria-label="duno. — Your Little Study Buddy. Home">
            <span class="brand-wordmark">duno.</span>
            <span class="brand-tagline">Your Little Study Buddy.</span>
        </a>
        <?php
        $currentPath = trim(service('uri')->getPath(), '/');
        $currentPath = preg_replace('#^index\.php/?#', '', $currentPath);
        $primaryLinks = [
            ['label' => 'Focus', 'path' => 'student/priority', 'icon' => 'target'],
            ['label' => 'Plan', 'path' => 'student/plan', 'icon' => 'calendar'],
            ['label' => 'Timer', 'path' => 'student/sessions', 'icon' => 'stopwatch'],
            ['label' => 'Aralytics', 'path' => 'student/aralytics', 'icon' => 'chart-2'],
        ];
        ?>
        <nav class="workspace-nav" aria-label="Primary student navigation">
            <?php foreach ($primaryLinks as $link): ?>
                <?php $isCurrent = $currentPath === $link['path'] || str_starts_with($currentPath, $link['path'] . '/'); ?>
                <a href="<?= esc(site_url($link['path'])) ?>"<?= $isCurrent ? ' aria-current="page"' : '' ?>>
                    <span class="solar-icon solar-icon-<?= esc($link['icon']) ?>" aria-hidden="true"></span><?= esc($link['label']) ?>
                </a>
            <?php endforeach ?>
        </nav>
        <details class="student-menu">
            <summary aria-label="Open more student pages"><span class="solar-icon solar-icon-hamburger-menu" aria-hidden="true"></span><span>More</span></summary>
            <nav aria-label="More student pages">
                <a href="<?= esc(site_url('student')) ?>"<?= $currentPath === 'student' ? ' aria-current="page"' : '' ?>>Home</a>
                <a href="<?= esc(site_url('student/subjects')) ?>"<?= str_starts_with($currentPath, 'student/subjects') ? ' aria-current="page"' : '' ?>>Subjects</a>
                <a href="<?= esc(site_url('student/topics')) ?>"<?= str_contains($currentPath, 'topics') ? ' aria-current="page"' : '' ?>>Topics</a>
                <a href="<?= esc(site_url('student#study-ideas-heading')) ?>">Duno Guide</a>
                <a href="<?= esc(site_url('student/check-ins')) ?>"<?= str_contains($currentPath, 'check-ins') ? ' aria-current="page"' : '' ?>>Private check-ins</a>
                <form method="post" action="<?= esc(site_url('logout')) ?>">
                    <?= csrf_field() ?>
                    <button type="submit">Sign out</button>
                </form>
            </nav>
        </details>
    </header>
    <main id="main-content">
        <?php if ($success = session()->getFlashdata('success')): ?>
            <p class="notice" role="status"><?= esc($success) ?></p>
        <?php endif ?>
        <?php if ($error = session()->getFlashdata('error')): ?>
            <p class="notice error" role="alert"><?= esc($error) ?></p>
        <?php endif ?>
        <?= $this->renderSection('content') ?>
    </main>
</div>
<footer class="icon-credit"><small>Icons: <a href="https://solar-icons.vercel.app/" rel="noopener noreferrer">Solar Icons</a> by 480 Design, CC BY 4.0.</small></footer>
<?= $this->include('partials/mobile_bottom_nav') ?>
</body>
</html>
