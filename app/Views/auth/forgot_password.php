<?= $this->extend(config('Auth')->views['layout']) ?>
<?= $this->section('title') ?><?= esc(lang('Registration.forgotPassword')) ?><?= $this->endSection() ?>
<?= $this->section('main') ?>
<div class="container d-flex justify-content-center p-4 p-md-5">
    <section class="card col-12 col-md-6 col-lg-5 shadow-sm" aria-labelledby="forgot-password-title">
        <div class="card-body p-4 p-md-5">
            <h1 class="h3 mb-2" id="forgot-password-title"><?= esc(lang('Registration.forgotPassword')) ?></h1>
            <p class="text-body-secondary mb-4"><?= esc(lang('Registration.resetInstructions')) ?></p>
            <?php if (session('message') !== null) : ?>
                <div class="alert alert-success" role="status"><?= esc(session('message')) ?></div>
            <?php endif ?>
            <?php if (session('error') !== null) : ?>
                <div class="alert alert-danger" role="alert"><?= esc(session('error')) ?></div>
            <?php endif ?>
            <form action="<?= esc(url_to('password.forgot.submit')) ?>" method="post">
                <?= csrf_field() ?>
                <div class="form-field mb-3">
                    <label for="forgot-email"><?= esc(lang('Auth.email')) ?></label>
                    <input type="email" class="form-control" id="forgot-email" name="email" inputmode="email" autocomplete="email" required>
                </div>
                <div class="d-grid mb-3"><button type="submit" class="btn btn-primary"><?= esc(lang('Registration.sendResetCode')) ?></button></div>
                <p class="text-center mb-0"><a href="<?= esc(url_to('login')) ?>"><?= esc(lang('Registration.backToLogin')) ?></a></p>
            </form>
        </div>
    </section>
</div>
<?= $this->endSection() ?>
