<?php
$languageReturnPath = trim(service('uri')->getPath(), '/');
$languageQuery = (string) service('request')->getServer('QUERY_STRING');
if ($languageQuery !== '') {
    $languageReturnPath .= '?' . $languageQuery;
}
?>
<form class="language-switcher" method="post" action="<?= esc(site_url('language')) ?>">
    <?= csrf_field() ?>
    <input type="hidden" name="return_to" value="<?= esc($languageReturnPath) ?>">
    <label for="duno-language"><?= esc(lang('App.language')) ?></label>
    <div class="language-switcher-controls">
        <select id="duno-language" name="language">
            <?php foreach (['en' => lang('App.english'), 'taglish' => lang('App.taglish'), 'fil' => lang('App.filipino')] as $languageCode => $languageName): ?>
                <option value="<?= esc($languageCode) ?>"<?= service('request')->getLocale() === $languageCode ? ' selected' : '' ?>><?= esc($languageName) ?></option>
            <?php endforeach ?>
        </select>
        <button type="submit"><?= esc(lang('App.apply')) ?></button>
    </div>
</form>
