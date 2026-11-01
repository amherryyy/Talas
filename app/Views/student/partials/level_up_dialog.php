<?php if (! empty($levelUp)): ?>
<dialog class="level-up-dialog" data-level-up-dialog aria-labelledby="level-up-title" aria-describedby="level-up-description">
    <div class="level-up-dialog-content">
        <div class="mascot-stage" data-duno-level-up><?= view('partials/duno_mascot', ['mascotClass' => 'level-up-mascot', 'mascotState' => 'idle', 'mascotInteractive' => false, 'mascotAlt' => '']) ?></div>
        <p class="workspace-kicker">A new milestone</p>
        <h2 id="level-up-title">Level <?= esc((string) $levelUp['level']) ?> · <?= esc($levelUp['name']) ?></h2>
        <p id="level-up-description">Your progression reached <?= esc(number_format((int) $levelUp['total_xp'])) ?> XP.</p>
        <?php if ($levelUp['unlocks'] !== []): ?>
            <div class="level-up-unlocks">
                <strong>New Aralytics unlocked</strong>
                <ul><?php foreach ($levelUp['unlocks'] as $unlock): ?><li><?= esc($unlock) ?></li><?php endforeach ?></ul>
            </div>
        <?php endif ?>
        <button type="button" data-level-up-close>Keep going</button>
    </div>
</dialog>
<noscript>
    <p class="notice" role="status">You reached Level <?= esc((string) $levelUp['level']) ?> · <?= esc($levelUp['name']) ?> with <?= esc(number_format((int) $levelUp['total_xp'])) ?> XP.</p>
</noscript>
<?php endif ?>
