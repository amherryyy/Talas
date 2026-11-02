<?= $this->extend(config('Auth')->views['layout']) ?>

<?= $this->section('title') ?><?= lang('Auth.login') ?><?= $this->endSection() ?>

<?= $this->section('main') ?>
<div class="container d-flex justify-content-center p-4 p-md-5">
    <section class="card col-12 col-md-5 shadow-sm" aria-labelledby="login-title">
        <div class="card-body p-4 p-md-5">
            <h2 class="card-title mb-4" id="login-title"><?= esc(lang('Auth.login')) ?></h2>

            <?php foreach (['error' => 'danger', 'message' => 'success'] as $key => $style) : ?>
                <?php if (session($key) !== null) : ?>
                    <div class="alert alert-<?= esc($style) ?>" role="status"><?= esc(session($key)) ?></div>
                <?php endif ?>
            <?php endforeach ?>
            <?php if (session('errors') !== null) : ?>
                <div class="alert alert-danger" role="alert">
                    <?php foreach ((array) session('errors') as $error) : ?>
                        <?= esc($error) ?><br>
                    <?php endforeach ?>
                </div>
            <?php endif ?>

            <form action="<?= esc(url_to('login')) ?>" method="post">
                <?= csrf_field() ?>
                <div class="form-field mb-3">
                    <label for="login-email"><?= esc(lang('Auth.email')) ?></label>
                    <input type="email" class="form-control" id="login-email" name="email" inputmode="email" autocomplete="email" value="<?= esc(old('email')) ?>" required>
                </div>
                <div class="form-field mb-3">
                    <label for="login-password"><?= esc(lang('Auth.password')) ?></label>
                    <input type="password" class="form-control" id="login-password" name="password" autocomplete="current-password" required>
                </div>
                <?php if (setting('Auth.sessionConfig')['allowRemembering']) : ?>
                    <div class="form-check mb-3">
                        <input type="checkbox" name="remember" id="login-remember" class="form-check-input" value="1"<?= old('remember') ? ' checked' : '' ?>>
                        <label class="form-check-label" for="login-remember"><?= esc(lang('Auth.rememberMe')) ?></label>
                    </div>
                <?php endif ?>
                <div class="d-grid col-12 col-md-8 mx-auto mb-3">
                    <button type="submit" class="btn btn-primary"><?= esc(lang('Auth.login')) ?></button>
                </div>
                <p class="text-center mb-2"><a href="<?= esc(url_to('password.forgot')) ?>"><?= esc(lang('Registration.forgotPassword')) ?></a></p>
                <?php if (setting('Auth.allowRegistration')) : ?>
                    <p class="text-center mb-0"><?= esc(lang('Auth.needAccount')) ?> <a href="<?= esc(url_to('register')) ?>"><?= esc(lang('Auth.register')) ?></a></p>
                <?php endif ?>
            </form>
        </div>
    </section>
</div>
<?= $this->endSection() ?>
