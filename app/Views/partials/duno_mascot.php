<?php
$mascotClass = trim((string) ($mascotClass ?? ''));
$mascotState = (string) ($mascotState ?? 'idle');
$mascotState = in_array($mascotState, ['idle', 'greeting', 'thinking', 'celebrating', 'solo'], true) ? $mascotState : 'idle';
$mascotInteractive = (bool) ($mascotInteractive ?? false);
$mascotLabel = (string) ($mascotLabel ?? 'Tap Duno for a playful hello');
$mascotAlt = (string) ($mascotAlt ?? '');
$mascotTag = $mascotInteractive ? 'button' : 'span';
?>
<<?= $mascotTag ?> class="duno-mascot <?= esc($mascotClass) ?>" data-duno-mascot data-duno-state="<?= esc($mascotState) ?>"<?= $mascotInteractive ? ' type="button" data-duno-interactive aria-label="' . esc($mascotLabel) . '"' : ' aria-hidden="true"' ?>>
    <picture>
        <source srcset="<?= esc(duno_asset('assets/images/duno-mascot-animation.webp')) ?>" type="image/webp">
        <img src="<?= esc(duno_asset('assets/images/duno-mascot.png')) ?>" alt="<?= esc($mascotAlt) ?>" width="1224" height="1285"<?= $mascotAlt === '' ? ' aria-hidden="true"' : '' ?> decoding="async">
    </picture>
</<?= $mascotTag ?>>
