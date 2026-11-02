<?= $this->extend('student/subjects/layout') ?>

<?= $this->section('content') ?>
<section class="profile-page" aria-labelledby="profile-heading">
    <header class="profile-page-heading">
        <p class="workspace-kicker"><?= esc(lang('App.studentAccount')) ?></p>
        <h1 id="profile-heading"><?= esc(lang('App.accountProfile')) ?></h1>
        <p class="profile-page-intro"><?= esc(lang('App.profileIntro')) ?></p>
    </header>

    <?php if ($errors !== []): ?>
        <ul class="errors" role="alert">
            <?php foreach ($errors as $message): ?><li><?= esc($message) ?></li><?php endforeach ?>
        </ul>
    <?php endif ?>

    <form id="student-profile-form" class="profile-form" method="post" action="<?= esc(site_url('student/profile')) ?>" data-profile-form>
        <?= csrf_field() ?>
        <div class="profile-identity">
            <span class="profile-avatar" aria-hidden="true"><?= esc(strtoupper(substr($username, 0, 1))) ?></span>
            <div class="profile-identity-copy">
                <strong><?= esc(old('username', $username)) ?></strong>
                <span><?= esc(lang('App.studentAccount')) ?></span>
            </div>
            <span class="profile-verified"><span aria-hidden="true">✓</span><?= esc(lang('App.verifiedEmail')) ?></span>
        </div>

        <div class="profile-fields">
            <div class="profile-field profile-field-editable">
                <label for="profile-username"><?= esc(lang('App.username')) ?></label>
                <input
                    id="profile-username"
                    name="username"
                    type="text"
                    minlength="3"
                    maxlength="30"
                    pattern="[A-Za-z0-9.]+"
                    required
                    autocomplete="username"
                    value="<?= esc(old('username', $username)) ?>"
                    aria-describedby="profile-username-help"
                    data-profile-username
                >
                <p id="profile-username-help" class="muted"><?= esc(lang('App.usernameHelp')) ?></p>
            </div>

            <div class="profile-field profile-field-readonly">
                <label for="profile-email"><?= esc(lang('App.emailAddress')) ?></label>
                <div class="profile-readonly-shell profile-readonly-shell-email">
                    <span class="profile-readonly-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="5" width="18" height="14" rx="2.5"></rect><path d="m4 7 8 6 8-6"></path></svg></span>
                    <input id="profile-email" class="profile-readonly-input profile-readonly-input-email" type="email" value="<?= esc($email) ?>" autocomplete="email" readonly aria-describedby="profile-email-help">
                    <span class="profile-readonly-lock" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="5" y="10" width="14" height="11" rx="2"></rect><path d="M8 10V7a4 4 0 1 1 8 0v3"></path></svg></span>
                </div>
                <p id="profile-email-help" class="muted"><?= esc(lang('App.emailReadOnly')) ?></p>
            </div>

            <div class="profile-field profile-field-readonly">
                <p class="profile-field-label" id="profile-academic-program-label">Current academic program</p>
                <div id="profile-academic-program" class="profile-readonly-shell profile-readonly-shell-program" role="group" aria-labelledby="profile-academic-program-label" aria-describedby="profile-academic-program-help">
                    <span class="profile-readonly-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="m3 9 9-5 9 5-9 5-9-5Z"></path><path d="M7 11.2v4.3c2.8 2.1 7.2 2.1 10 0v-4.3M21 9v6"></path></svg></span>
                    <span class="profile-readonly-program-copy"><span class="profile-readonly-program-text"><?= esc($academicProgramTitle) ?></span><small class="profile-readonly-school-text"><?= esc($academicProgramSchool) ?></small></span>
                </div>
                <p id="profile-academic-program-help" class="muted">Your school and program are saved once. Any available subject course list is managed separately.</p>
            </div>
        </div>

        <noscript><div class="profile-noscript-save"><button type="submit"><?= esc(lang('App.saveChanges')) ?></button></div></noscript>
    </form>

    <div class="profile-savebar" role="region" aria-label="<?= esc(lang('App.unsavedChanges')) ?>" aria-live="polite" data-profile-savebar hidden>
        <span class="profile-savebar-dot" aria-hidden="true"></span>
        <p><strong><?= esc(lang('App.unsavedChanges')) ?></strong><small><?= esc(lang('App.profileUnsaved')) ?></small></p>
        <button class="button secondary profile-discard" type="reset" form="student-profile-form" data-profile-discard><?= esc(lang('App.discardChanges')) ?></button>
        <button class="button profile-save" type="submit" form="student-profile-form" data-profile-submit>
            <span data-profile-ready><?= esc(lang('App.saveChanges')) ?></span>
            <span data-profile-saving hidden><?= esc(lang('App.savingChanges')) ?></span>
        </button>
    </div>

    <section class="profile-preferences" aria-labelledby="profile-preferences-heading">
        <p class="workspace-kicker"><?= esc(lang('App.menuAccount')) ?></p>
        <h2 id="profile-preferences-heading"><?= esc(lang('App.preferences')) ?></h2>
        <p class="profile-preferences-intro"><?= esc(lang('App.languagePreferenceHelp')) ?></p>
        <?= view('partials/language_switcher') ?>
    </section>

    <section class="profile-danger-zone" aria-labelledby="profile-delete-heading">
        <p class="workspace-kicker"><?= esc(lang('App.accountControl')) ?></p>
        <h2 id="profile-delete-heading"><?= esc(lang('App.deleteAccount')) ?></h2>
        <p><?= esc(lang('App.accountDeleteWarning')) ?></p>
        <?php if (($deleteErrors ?? []) !== []): ?>
            <ul class="errors profile-delete-errors" role="alert">
                <?php foreach ($deleteErrors as $message): ?><li><?= esc($message) ?></li><?php endforeach ?>
            </ul>
        <?php endif ?>
        <form method="post" action="<?= esc(site_url('student/profile/delete')) ?>" class="profile-delete-form">
            <?= csrf_field() ?>
            <div class="profile-field">
                <label for="delete-confirmation"><?= esc(lang('App.accountDeleteConfirmation')) ?></label>
                <input id="delete-confirmation" name="delete_confirmation" type="text" required autocomplete="off" autocapitalize="characters" spellcheck="false" aria-describedby="delete-confirmation-help">
                <p id="delete-confirmation-help" class="muted"><?= esc(lang('App.accountDeleteConfirmationHelp')) ?></p>
            </div>
            <div class="profile-field">
                <label for="delete-current-password"><?= esc(lang('App.currentPassword')) ?></label>
                <input id="delete-current-password" name="current_password" type="password" required autocomplete="current-password">
            </div>
            <button class="profile-delete-button" type="submit"><?= esc(lang('App.deleteAccountPermanently')) ?></button>
        </form>
    </section>
</section>
<script src="<?= esc(duno_asset('assets/js/profile-savebar.js')) ?>" defer></script>
<?= $this->endSection() ?>
