<div class="app-tour-shade" data-tour-shade aria-hidden="true" hidden>
    <span data-shade-top></span>
    <span data-shade-left></span>
    <span data-shade-right></span>
    <span data-shade-bottom></span>
</div>
<aside
    class="app-tour"
    data-app-tour
    data-tour-user="<?= esc($appTourUserId) ?>"
    data-auto-start="<?= $appTourAutoStart ? 'true' : 'false' ?>"
    aria-labelledby="app-tour-title"
    aria-describedby="app-tour-copy"
    aria-modal="true"
    role="dialog"
    hidden
>
    <div class="app-tour-topline">
        <p class="workspace-kicker" data-tour-progress aria-live="polite"><?= esc(lang('App.tourProgress')) ?></p>
        <button class="app-tour-skip" type="button" data-tour-skip><?= esc(lang('App.skipTour')) ?></button>
    </div>
    <div class="app-tour-intro">
        <img class="app-tour-mascot" src="<?= esc(duno_asset('assets/images/duno-mascot-welcome.webp')) ?>" alt="" width="512" height="536" aria-hidden="true">
        <div>
            <h2 id="app-tour-title" data-tour-title tabindex="-1"><?= esc(lang('App.welcomeDuno')) ?></h2>
            <p id="app-tour-copy" class="app-tour-copy" data-tour-copy></p>
        </div>
    </div>
    <div class="app-tour-actions">
        <button class="button secondary" type="button" data-tour-back disabled><?= esc(lang('App.back')) ?></button>
        <button class="button" type="button" data-tour-next><?= esc(lang('App.next')) ?></button>
    </div>
</aside>
