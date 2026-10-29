<?= $this->extend(config('Auth')->views['layout']) ?>

<?= $this->section('title') ?><?= esc(lang('Registration.title')) ?><?= $this->endSection() ?>

<?= $this->section('main') ?>
<section class="container d-flex justify-content-center p-5">
    <div class="card col-12 col-md-5 shadow-sm">
        <div class="card-body">
            <h1 class="card-title"><?= esc(lang('Registration.title')) ?></h1>
            <?php if (session('error') !== null) : ?>
                <div class="alert alert-danger" role="alert"><?= esc(session('error')) ?></div>
            <?php endif ?>

            <p><?= esc(lang('Registration.sent')) ?></p>
            <?php if (isset($user) && $user->email !== null) : ?>
                <p><strong><?= esc($user->email) ?></strong></p>
            <?php endif ?>

            <form action="<?= esc(url_to('auth-action-verify')) ?>" method="post">
                <?= csrf_field() ?>
                <div class="form-floating mb-2">
                    <label for="verification-code"><?= esc(lang('Registration.code')) ?></label>
                    <input
                        class="form-control"
                        id="verification-code"
                        name="token"
                        type="text"
                        inputmode="numeric"
                        autocomplete="one-time-code"
                        pattern="[0-9]{6}"
                        minlength="6"
                        maxlength="6"
                        aria-describedby="verification-expiry"
                        required
                        autofocus
                    >
                </div>
                <p id="verification-expiry"><?= esc(lang('Registration.expiry')) ?></p>
                <div class="d-grid col-12 col-md-8 mx-auto m-3">
                    <button class="btn btn-primary" type="submit"><?= esc(lang('Registration.verify')) ?></button>
                </div>
            </form>

            <form class="text-center" action="<?= esc(url_to('auth-registration-resend')) ?>" method="post">
                <?= csrf_field() ?>
                <button class="btn btn-secondary" type="submit"><?= esc(lang('Registration.resend')) ?></button>
            </form>
            <div class="registration-recovery">
                <p><?= esc(lang('Registration.wrongEmail')) ?></p>
                <form action="<?= esc(url_to('auth-registration-cancel')) ?>" method="post">
                    <?= csrf_field() ?>
                    <button class="btn btn-secondary" type="submit"><?= esc(lang('Registration.startOver')) ?></button>
                </form>
            </div>
        </div>
    </div>
</section>
<?= $this->endSection() ?>
