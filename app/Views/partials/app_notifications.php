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
$notificationDialogue = [
    'success' => lang('App.notificationDialogueSuccess'),
    'info' => lang('App.notificationDialogueInfo'),
    'warning' => lang('App.notificationDialogueWarning'),
    'error' => lang('App.notificationDialogueError'),
];
$notificationEmotions = [
    'success' => '🥳',
    'info' => '🧐',
    'warning' => '😮',
    'error' => '😟',
];
$notificationMascots = [
    'success' => 'duno-mascot-welcome.webp',
    'info' => 'duno-mascot-curious.webp',
    'warning' => 'duno-mascot-alert.webp',
    'error' => 'duno-mascot-support.webp',
];
?>
<div
    class="duno-notification-stack"
    data-notification-stack
    data-mascot-src="<?= esc(duno_asset('assets/images/duno-mascot-welcome.webp')) ?>"
    data-mascot-info-src="<?= esc(duno_asset('assets/images/duno-mascot-curious.webp')) ?>"
    data-mascot-warning-src="<?= esc(duno_asset('assets/images/duno-mascot-alert.webp')) ?>"
    data-mascot-error-src="<?= esc(duno_asset('assets/images/duno-mascot-support.webp')) ?>"
    data-mascot-reward-src="<?= esc(duno_asset('assets/images/duno-mascot-celebrating.webp')) ?>"
    data-dismiss-label="<?= esc(lang('App.notificationDismiss')) ?>"
    data-logged-out-short="<?= esc(lang('App.loggedOutShort')) ?>"
    data-region-label="<?= esc(lang('App.notificationRegion')) ?>"
    data-title-success="<?= esc($notificationTitles['success']) ?>"
    data-title-info="<?= esc($notificationTitles['info']) ?>"
    data-title-warning="<?= esc($notificationTitles['warning']) ?>"
    data-title-error="<?= esc($notificationTitles['error']) ?>"
    data-dialogue-success="<?= esc($notificationDialogue['success']) ?>"
    data-dialogue-info="<?= esc($notificationDialogue['info']) ?>"
    data-dialogue-warning="<?= esc($notificationDialogue['warning']) ?>"
    data-dialogue-error="<?= esc($notificationDialogue['error']) ?>"
    role="region"
    aria-label="<?= esc(lang('App.notificationRegion')) ?>"
>
    <?php foreach ($flashNotifications as $notification): ?>
        <?php $type = in_array($notification['type'], ['success', 'info', 'warning', 'error'], true) ? $notification['type'] : 'info'; ?>
        <?php $compact = $type === 'success' && preg_match('/logged out|log out|nakapag-log out/i', $notification['message']) === 1; ?>
        <?php $mascotFile = ! empty($notification['reward']) ? 'duno-mascot-celebrating.webp' : $notificationMascots[$type]; ?>
        <article class="duno-toast<?= ! empty($notification['reward']) ? ' streak-reward-notice' : '' ?><?= $compact ? ' duno-toast-compact' : '' ?>" data-mood="<?= esc($type) ?>" role="<?= $type === 'error' || $type === 'warning' ? 'alert' : 'status' ?>" aria-live="<?= $type === 'error' || $type === 'warning' ? 'assertive' : 'polite' ?>">
            <span class="duno-toast-mascot" aria-hidden="true">
                <img src="<?= esc(duno_asset('assets/images/' . $mascotFile)) ?>" alt="" width="512" height="536">
                <span class="duno-toast-emotion"><?= esc($notificationEmotions[$type]) ?></span>
            </span>
            <div class="duno-toast-copy">
                <?php if (! $compact): ?><strong class="duno-toast-title"><?= esc($notificationTitles[$type]) ?></strong><?php endif ?>
                <?php if (! $compact): ?><p class="duno-toast-dialogue"><?= esc($notificationDialogue[$type]) ?></p><?php endif ?>
                <p class="duno-toast-message"><?= esc($compact ? lang('App.loggedOutShort') : $notification['message']) ?></p>
            </div>
            <button class="duno-toast-dismiss" type="button" aria-label="<?= esc(lang('App.notificationDismiss')) ?>">×</button>
        </article>
    <?php endforeach ?>
</div>
