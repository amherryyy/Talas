<?= $this->extend(config('Auth')->views['layout']) ?>

<?= $this->section('title') ?><?= lang('Auth.register') ?><?= $this->endSection() ?>

<?= $this->section('main') ?>
<div class="container d-flex justify-content-center p-4 p-md-5">
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

                <div class="form-floating mb-3">
                    <input type="email" class="form-control" id="register-email" name="email" inputmode="email" autocomplete="email" placeholder="<?= esc(lang('Auth.email')) ?>" value="<?= esc(old('email')) ?>" required>
                    <label for="register-email"><?= esc(lang('Auth.email')) ?></label>
                </div>

                <div class="form-floating mb-3">
                    <input type="text" class="form-control" id="register-username" name="username" inputmode="text" autocomplete="username" placeholder="<?= esc(lang('Auth.username')) ?>" value="<?= esc(old('username')) ?>" required>
                    <label for="register-username"><?= esc(lang('Auth.username')) ?></label>
                </div>

                <div class="form-floating mb-3">
                    <input type="password" class="form-control" id="register-password" name="password" autocomplete="new-password" placeholder="<?= esc(lang('Auth.password')) ?>" required>
                    <label for="register-password"><?= esc(lang('Auth.password')) ?></label>
                </div>

                <div class="form-floating mb-4">
                    <input type="password" class="form-control" id="register-password-confirm" name="password_confirm" autocomplete="new-password" placeholder="<?= esc(lang('Auth.passwordConfirm')) ?>" required>
                    <label for="register-password-confirm"><?= esc(lang('Auth.passwordConfirm')) ?></label>
                </div>

                <div class="form-check mb-4">
                    <input class="form-check-input" type="checkbox" id="terms-privacy-consent" name="terms_privacy_consent" value="1" required<?= old('terms_privacy_consent') === '1' ? ' checked' : '' ?>>
                    <label class="form-check-label" for="terms-privacy-consent">
                        <?= esc(lang('App.registrationConsentLead')) ?>
                        <a href="<?= esc(url_to('legal.terms')) ?>" target="_blank" rel="noopener noreferrer"><?= esc(lang('App.termsAndConditions')) ?></a>
                        <?= esc(lang('App.registrationConsentJoin')) ?>
                        <a href="<?= esc(url_to('legal.privacy')) ?>" target="_blank" rel="noopener noreferrer"><?= esc(lang('App.privacyNotice')) ?></a>.
                        <?= esc(lang('App.registrationAdultConfirmation')) ?>
                    </label>
                </div>

                <div class="d-grid col-12 col-md-9 mx-auto mb-3">
                    <button type="submit" class="btn btn-primary"><?= lang('Auth.register') ?></button>
                </div>
                <p class="text-center mb-0"><?= lang('Auth.haveAccount') ?> <a href="<?= esc(url_to('login')) ?>"><?= lang('Auth.login') ?></a></p>
            </form>
        </div>
    </section>
</div>
<?= $this->endSection() ?>
