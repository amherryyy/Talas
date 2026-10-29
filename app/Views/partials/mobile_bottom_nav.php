<?php
$currentPath = trim(service('uri')->getPath(), '/');
$currentPath = preg_replace('#^index\.php/?#', '', $currentPath);
$active = match (true) {
    $currentPath === 'student' => 'home',
    str_contains($currentPath, '/priority') => 'focus',
    str_contains($currentPath, '/plan') => 'plan',
    str_contains($currentPath, '/sessions') => 'timer',
    str_contains($currentPath, '/aralytics') => 'aralytics',
    default => null,
};
$items = [
    ['label' => lang('App.home'), 'href' => site_url('student'), 'icon' => 'home', 'key' => 'home'],
    ['label' => lang('App.focus'), 'href' => site_url('student/priority'), 'icon' => 'target', 'key' => 'focus'],
    ['label' => lang('App.plan'), 'href' => site_url('student/plan'), 'icon' => 'calendar', 'key' => 'plan'],
    ['label' => lang('App.timer'), 'href' => site_url('student/sessions'), 'icon' => 'stopwatch', 'key' => 'timer'],
    ['label' => lang('App.guide'), 'href' => site_url('student#guide-heading'), 'icon' => 'book-2', 'key' => 'guide'],
    ['label' => lang('App.aralytics'), 'href' => site_url('student/aralytics'), 'icon' => 'chart-2', 'key' => 'aralytics'],
];
?>
<nav class="student-bottom-nav" aria-label="<?= esc(lang('App.primaryNav')) ?>">
    <?php foreach ($items as $item): ?>
        <a href="<?= esc($item['href']) ?>"<?= $active === $item['key'] ? ' aria-current="page"' : '' ?><?= $item['key'] === 'guide' ? ' data-guide-nav' : '' ?><?= $item['key'] === 'home' ? ' data-home-nav' : '' ?> data-tour-target="<?= $item['key'] === 'home' ? 'start' : esc($item['key']) ?>">
            <span class="student-bottom-nav-mark solar-icon solar-icon-<?= esc($item['icon']) ?>" aria-hidden="true"></span>
            <span><?= esc($item['label']) ?></span>
        </a>
    <?php endforeach ?>
</nav>
