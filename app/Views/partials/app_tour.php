<aside
    class="app-tour"
    data-app-tour
    data-tour-user="<?= esc($appTourUserId) ?>"
    data-auto-start="<?= $appTourAutoStart ? 'true' : 'false' ?>"
    aria-labelledby="app-tour-title"
    aria-describedby="app-tour-copy"
    aria-modal="false"
    role="dialog"
    hidden
>
    <div class="app-tour-topline">
        <p class="workspace-kicker" data-tour-progress aria-live="polite">Getting started · Step 1 of 7</p>
        <button class="app-tour-skip" type="button" data-tour-skip>Skip tour</button>
    </div>
    <h2 id="app-tour-title" data-tour-title tabindex="-1">Welcome to Duno</h2>
    <p id="app-tour-copy" class="app-tour-copy" data-tour-copy></p>
    <div class="app-tour-actions">
        <button class="button secondary" type="button" data-tour-back disabled>Back</button>
        <button class="button" type="button" data-tour-next>Next</button>
    </div>
</aside>
