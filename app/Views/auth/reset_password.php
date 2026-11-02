<?= $this->extend(config('Auth')->views['layout']) ?>
<?= $this->section('title') ?><?= esc(lang('Registration.resetPassword')) ?><?= $this->endSection() ?>
<?= $this->section('main') ?>
<div class="container d-flex justify-content-center p-4 p-md-5">
    <section class="card col-12 col-md-6 col-lg-5 shadow-sm" aria-labelledby="reset-password-title">
        <div class="card-body p-4 p-md-5">
            <h1 class="h3 mb-2" id="reset-password-title"><?= esc(lang('Registration.resetPassword')) ?></h1>
            <p class="text-body-secondary mb-4"><?= esc(lang('Registration.resetNewPasswordInstructions')) ?></p>
            <?php if (session('message') !== null) : ?>
                <div class="alert alert-success" role="status"><?= esc(session('message')) ?></div>
            <?php endif ?>
            <?php if (session('error') !== null) : ?>
                <div class="alert alert-warning" role="status"><?= esc(session('error')) ?></div>
            <?php endif ?>
            <?php if (session('errors') !== null) : ?>
                <div class="alert alert-danger" role="alert">
                    <?php foreach ((array) session('errors') as $error) : ?>
                        <?= esc($error) ?><br>
                    <?php endforeach ?>
                </div>
            <?php endif ?>
            <form action="<?= esc(url_to('password.reset.submit')) ?>" method="post">
                <?= csrf_field() ?>
                <div class="form-field mb-3">
                    <label for="reset-email"><?= esc(lang('Auth.email')) ?></label>
                    <input type="email" class="form-control" id="reset-email" name="email" inputmode="email" autocomplete="email" required>
                </div>
                <div class="form-field mb-3">
                    <label for="reset-code"><?= esc(lang('Registration.resetCode')) ?></label>
                    <input type="text" class="form-control" id="reset-code" name="code" inputmode="numeric" autocomplete="one-time-code" pattern="[0-9]{6}" maxlength="6" minlength="6" required>
                </div>
                <div class="form-field mb-3">
                    <label for="reset-password"><?= esc(lang('Auth.password')) ?></label>
                    <input type="password" class="form-control" id="reset-password" name="password" autocomplete="new-password" required>
                </div>
                <div class="form-field mb-3">
                    <label for="reset-password-confirm"><?= esc(lang('Auth.passwordConfirm')) ?></label>
                    <input type="password" class="form-control" id="reset-password-confirm" name="password_confirm" autocomplete="new-password" required>
                </div>
                <div class="d-grid mb-3"><button type="submit" class="btn btn-primary"><?= esc(lang('Registration.saveNewPassword')) ?></button></div>
                <p class="text-center mb-0"><a href="<?= esc(url_to('password.forgot')) ?>"><?= esc(lang('Registration.requestAnotherCode')) ?></a></p>
            </form>
        </div>
    </section>
</div>
<?= $this->endSection() ?>
