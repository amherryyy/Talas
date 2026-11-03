<?= $this->extend(config('Auth')->views['layout']) ?>

<?= $this->section('title') ?><?= lang('Auth.register') ?><?= $this->endSection() ?>

<?= $this->section('main') ?>
<div class="container d-flex justify-content-center p-4 p-md-5">
    <div class="auth-card-stack">
    <section class="card col-12 col-md-7 col-lg-6 shadow-sm" aria-labelledby="register-title">
        <div class="card-body p-4 p-md-5">
            <h2 class="card-title mb-4" id="register-title"><?= lang('Auth.register') ?></h2>

            <?php if (session('error') !== null) : ?>
                <div class="alert alert-danger" role="alert"><?= esc(session('error')) ?></div>
            <?php elseif (session('errors') !== null) : ?>
                <div class="alert alert-danger" role="alert">
                    <?php foreach ((array) session('errors') as $error) : ?>
                        <?= esc($error) ?><br>
                    <?php endforeach ?>
                </div>
            <?php endif ?>

            <form action="<?= esc(url_to('register')) ?>" method="post">
                <?= csrf_field() ?>

                <div class="form-field mb-3">
                    <label for="register-email"><?= esc(lang('Auth.email')) ?></label>
                    <input type="email" class="form-control" id="register-email" name="email" inputmode="email" autocomplete="email" value="<?= esc(old('email')) ?>" required>
                </div>

                <div class="form-field mb-3">
                    <label for="register-username"><?= esc(lang('Auth.username')) ?></label>
                    <input type="text" class="form-control" id="register-username" name="username" inputmode="text" autocomplete="username" value="<?= esc(old('username')) ?>" required>
                </div>

                <div class="form-field mb-3">
                    <label for="register-password"><?= esc(lang('Auth.password')) ?></label>
                    <input type="password" class="form-control" id="register-password" name="password" autocomplete="new-password" required>
                </div>

                <div class="form-field mb-4">
                    <label for="register-password-confirm"><?= esc(lang('Auth.passwordConfirm')) ?></label>
                    <input type="password" class="form-control" id="register-password-confirm" name="password_confirm" autocomplete="new-password" required>
                </div>

                <label class="registration-consent mb-4" for="terms-privacy-consent">
                    <input class="registration-consent-checkbox" type="checkbox" id="terms-privacy-consent" name="terms_privacy_consent" value="1" required<?= old('terms_privacy_consent') === '1' ? ' checked' : '' ?>>
                    <span class="registration-consent-copy">
                        <span class="registration-consent-label">
                            <?= esc(lang('App.registrationConsentLead')) ?>
                            <a href="<?= esc(url_to('legal.terms')) ?>" target="_blank" rel="noopener noreferrer"><?= esc(lang('App.termsAndConditions')) ?></a>
                            <?= esc(lang('App.registrationConsentJoin')) ?>
                            <a href="<?= esc(url_to('legal.privacy')) ?>" target="_blank" rel="noopener noreferrer"><?= esc(lang('App.privacyNotice')) ?></a>.
                            <span class="registration-adult-confirmation"><?= esc(lang('App.registrationAdultConfirmation')) ?></span>
                        </span>
                    </span>
                </label>

                <div class="d-grid col-12 col-md-9 mx-auto mb-3">
                    <button type="submit" class="btn btn-primary"><?= lang('Auth.register') ?></button>
                </div>
                <p class="text-center mb-0"><?= lang('Auth.haveAccount') ?> <a href="<?= esc(url_to('login')) ?>"><?= lang('Auth.login') ?></a></p>
            </form>
        </div>
    </section>
    <div class="auth-role-entry">
        <a class="auth-role-link" href="<?= esc(url_to('admin.login')) ?>"><?= esc(lang('App.adminSignIn')) ?></a>
    </div>
    </div>
</div>
<?= $this->endSection() ?>
