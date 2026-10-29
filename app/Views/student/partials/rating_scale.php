<?php
/** @var string $scaleName */
/** @var string $scaleId */
/** @var string $scaleLabel */
/** @var string $scaleValue */
/** @var string $scaleLowLabel */
/** @var string $scaleHighLabel */
?>
<fieldset class="check-in-scale check-in-rating-scale">
    <legend><?= esc($scaleLabel) ?></legend>
    <div class="check-in-scale-options">
        <?php foreach ([1, 2, 3, 4, 5] as $rating): ?>
            <label class="check-in-scale-choice">
                <input id="<?= esc($scaleId . '-' . $rating) ?>" type="radio" name="<?= esc($scaleName) ?>" value="<?= $rating ?>" aria-label="<?= $rating ?> out of 5" required<?= $scaleValue === (string) $rating ? ' checked' : '' ?>>
                <span><?= $rating ?></span>
            </label>
        <?php endforeach ?>
    </div>
    <div class="check-in-scale-endpoints"><span><?= esc($scaleLowLabel) ?></span><span><?= esc($scaleHighLabel) ?></span></div>
</fieldset>
