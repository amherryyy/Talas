<section class="progression-card no-print" aria-labelledby="progression-heading">
    <div class="progression-overview">
        <div class="progression-level-mark" aria-label="Level <?= esc((string) $progression['level']) ?>">L<?= esc((string) $progression['level']) ?></div>
        <div class="progression-copy">
            <p class="workspace-kicker">Your Duno journey</p>
            <h2 id="progression-heading">Level <?= esc((string) $progression['level']) ?> · <?= esc($progression['level_name']) ?></h2>
            <p><strong><?= esc((string) $progression['xp']) ?> XP</strong> <span class="muted">(<?= esc((string) $progression['study_xp']) ?> study + <?= esc((string) $progression['streak_xp']) ?> streak)</span></p>
            <p class="progression-xp-rule">100 XP per saved study minute; +500 XP for your first app visit each day.</p>
        </div>
    </div>
    <div class="progression-track">
        <?php if ($progression['next_level_name'] !== null): ?>
            <progress value="<?= esc((string) $progression['level_progress_xp']) ?>" max="<?= esc((string) $progression['level_progress_max']) ?>" aria-label="Progress toward Level <?= esc((string) ($progression['level'] + 1)) ?>"></progress>
            <p class="muted"><?= esc((string) ($progression['next_level_xp'] - $progression['xp'])) ?> XP to Level <?= esc((string) ($progression['level'] + 1)) ?> · <?= esc($progression['next_level_name']) ?></p>
        <?php else: ?>
            <progress value="100" max="100" aria-label="All current levels completed"></progress>
            <p class="muted">All current levels reached. Your saved study time keeps adding to your record.</p>
        <?php endif ?>
        <?php if ($progression['next_unlock'] !== null): ?>
            <p class="progression-next-unlock"><strong>Next Aralytics unlock:</strong> <?= esc($progression['next_unlock']['name']) ?> at <?= esc((string) $progression['next_unlock']['xp']) ?> XP.</p>
        <?php else: ?>
            <p class="progression-next-unlock"><strong>Aralytics bonus:</strong> all current insights unlocked.</p>
        <?php endif ?>
    </div>
    <div class="progression-badges">
        <h3>Milestones</h3>
        <ul>
            <?php foreach ($progression['badges'] as $badge): ?>
                <li class="<?= $badge['unlocked'] ? 'badge-earned' : 'badge-locked' ?>"<?= $badge['unlocked'] ? '' : ' aria-label="' . esc($badge['name'] . ' locked: ' . $badge['description']) . '"' ?>>
                    <span><?= esc($badge['name']) ?></span>
                    <?php if ($badge['unlocked']): ?><span class="badge-status">Earned</span><?php else: ?><span class="badge-status">In progress</span><?php endif ?>
                </li>
            <?php endforeach ?>
        </ul>
    </div>
</section>
<div class="streak-chip no-print" aria-label="Current login streak: <?= esc((string) $progression['current_streak']) ?> <?= $progression['current_streak'] === 1 ? 'day' : 'days' ?>">
    <span class="streak-flame<?= session()->getFlashdata('daily_streak_reward') ? ' is-new' : '' ?>" aria-hidden="true">🔥</span>
    <span><strong><?= esc((string) $progression['current_streak']) ?>-day streak</strong><span class="streak-caption">Visit daily to keep it going</span></span>
</div>
