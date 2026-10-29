<?php
$includeFlashNotifications = $includeFlashNotifications ?? true;
$flashNotifications = [];

if ($includeFlashNotifications) {
    foreach (['success', 'info', 'warning', 'error', 'message'] as $flashType) {
        $flashMessage = session()->getFlashdata($flashType);
        if (is_string($flashMessage) && trim($flashMessage) !== '') {
            $flashNotifications[] = [
                'type' => $flashType === 'message' ? 'success' : $flashType,
                'message' => $flashMessage,
            ];
        }
    }

    if ($dailyStreakReward = session()->getFlashdata('daily_streak_reward')) {
        $flashNotifications[] = [
            'type' => 'success',
            'message' => lang('App.dailyReward', [
                $dailyStreakReward['xp'],
                $dailyStreakReward['streak_days'],
                lang($dailyStreakReward['streak_days'] === 1 ? 'App.day' : 'App.days'),
            ]),
            'reward' => true,
        ];
    }
}

$notificationTitles = [
    'success' => lang('App.notificationSuccess'),
    'info' => lang('App.notificationInfo'),
    'warning' => lang('App.notificationWarning'),
    'error' => lang('App.notificationError'),
];
$notificationEmotions = [
    'success' => '🥳',
    'info' => '🧐',
    'warning' => '😮',
    'error' => '😟',
];
?>
<div
    class="duno-notification-stack"
    data-notification-stack
    data-mascot-src="<?= esc(duno_asset('assets/images/duno-mascot.png')) ?>"
    data-dismiss-label="<?= esc(lang('App.notificationDismiss')) ?>"
    data-region-label="<?= esc(lang('App.notificationRegion')) ?>"
    data-title-success="<?= esc($notificationTitles['success']) ?>"
    data-title-info="<?= esc($notificationTitles['info']) ?>"
    data-title-warning="<?= esc($notificationTitles['warning']) ?>"
    data-title-error="<?= esc($notificationTitles['error']) ?>"
    role="region"
    aria-label="<?= esc(lang('App.notificationRegion')) ?>"
>
    <?php foreach ($flashNotifications as $notification): ?>
        <?php $type = in_array($notification['type'], ['success', 'info', 'warning', 'error'], true) ? $notification['type'] : 'info'; ?>
        <article class="duno-toast<?= ! empty($notification['reward']) ? ' streak-reward-notice' : '' ?>" data-mood="<?= esc($type) ?>" role="<?= $type === 'error' || $type === 'warning' ? 'alert' : 'status' ?>" aria-live="<?= $type === 'error' || $type === 'warning' ? 'assertive' : 'polite' ?>">
            <span class="duno-toast-mascot" aria-hidden="true">
                <img src="<?= esc(duno_asset('assets/images/duno-mascot.png')) ?>" alt="" width="1228" height="1285">
                <span class="duno-toast-emotion"><?= esc($notificationEmotions[$type]) ?></span>
            </span>
            <div class="duno-toast-copy">
                <strong class="duno-toast-title"><?= esc($notificationTitles[$type]) ?></strong>
                <p class="duno-toast-message"><?= esc($notification['message']) ?></p>
            </div>
            <a class="duno-toast-dismiss" href="<?= esc(current_url()) ?>" aria-label="<?= esc(lang('App.notificationDismiss')) ?>">×</a>
        </article>
    <?php endforeach ?>
</div>
