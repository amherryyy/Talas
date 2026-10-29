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

            <label for="profile-email"><?= esc(lang('App.emailAddress')) ?></label>
            <input id="profile-email" type="email" value="<?= esc($email) ?>" autocomplete="email" readonly aria-describedby="profile-email-help">
            <p id="profile-email-help" class="muted"><?= esc(lang('App.emailReadOnly')) ?></p>
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
</section>
<script src="<?= esc(duno_asset('assets/js/profile-savebar.js')) ?>" defer></script>
<?= $this->endSection() ?>
