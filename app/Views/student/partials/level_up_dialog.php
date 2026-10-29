<dialog class="level-up-dialog" data-level-up-dialog aria-labelledby="level-up-title" aria-describedby="level-up-description">
    <div class="level-up-dialog-content">
        <span class="mascot-stage" aria-hidden="true"><img class="level-up-mascot" src="<?= esc(duno_asset('assets/images/duno-mascot-celebrating.webp')) ?>" alt="" width="512" height="536"></span>
        <p class="workspace-kicker">A new milestone</p>
        <h2 id="level-up-title">Level <?= esc((string) $levelUp['level']) ?> · <?= esc($levelUp['name']) ?></h2>
        <p id="level-up-description">Your progression reached <?= esc((string) $levelUp['total_xp']) ?> XP.</p>
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
    <p class="notice" role="status">You reached Level <?= esc((string) $levelUp['level']) ?> · <?= esc($levelUp['name']) ?> with <?= esc((string) $levelUp['total_xp']) ?> XP.</p>
</noscript>
