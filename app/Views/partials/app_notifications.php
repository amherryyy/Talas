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
        <p class="duno-toast-source notice<?= $type === 'error' ? ' error' : '' ?><?= ! empty($notification['reward']) ? ' streak-reward-notice' : '' ?>" role="<?= $type === 'error' ? 'alert' : 'status' ?>" data-notification-type="<?= esc($type) ?>">
            <?= esc($notification['message']) ?>
        </p>
    <?php endforeach ?>
</div>
