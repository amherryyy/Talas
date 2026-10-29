<?php
/** @var string $choiceId */
/** @var string $choiceName */
/** @var string $choiceLabel */
/** @var string $choiceValue */
/** @var array<string, string> $choiceOptions */
/** @var string $choiceClass */
?>
<fieldset class="segmented-choice-group <?= esc($choiceClass ?? '') ?>">
    <legend><?= esc($choiceLabel) ?></legend>
    <div class="segmented-choice-options">
        <?php foreach ($choiceOptions as $value => $label): ?>
            <?php $optionId = $choiceId . '-' . ($value !== '' ? $value : 'all'); ?>
            <label class="segmented-choice" for="<?= esc($optionId) ?>">
                <input id="<?= esc($optionId) ?>" type="radio" name="<?= esc($choiceName) ?>" value="<?= esc($value) ?>" required<?= $choiceValue === (string) $value ? ' checked' : '' ?>>
                <span><?= esc($label) ?></span>
            </label>
        <?php endforeach ?>
    </div>
</fieldset>
