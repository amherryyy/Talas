<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= esc($pageTitle ?? 'Subjects') ?> · Talas</title>
    <style {csp-style-nonce}>
        :root { color-scheme: light; font-family: system-ui, sans-serif; color: #18342d; background: #f4f7f4; }
        * { box-sizing: border-box; }
        body { margin: 0; min-height: 100vh; padding: 1rem; }
        .shell { width: min(100%, 56rem); margin: 0 auto; }
        header, main { padding: clamp(1rem, 4vw, 2rem); background: #fff; border: 1px solid #dce7df; border-radius: 1rem; }
        header { margin: 0 auto 1rem; display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 1rem; }
        header p { margin: 0; font-weight: 700; }
        nav, .actions { display: flex; flex-wrap: wrap; gap: .75rem; align-items: center; }
        main { line-height: 1.55; }
        h1 { margin-top: 0; font-size: clamp(1.7rem, 5vw, 2.3rem); }
        a { color: #125c49; }
        a:focus-visible, button:focus-visible, input:focus-visible, select:focus-visible { outline: 3px solid #e4a83a; outline-offset: 3px; }
        .button, button { display: inline-block; padding: .65rem .9rem; border: 0; border-radius: .5rem; color: #fff; background: #176b55; font: inherit; text-decoration: none; cursor: pointer; }
        .button.secondary { color: #18342d; background: #e9f1ec; }
        .button.danger, button.danger { background: #9b302a; }
        .subject-list { list-style: none; padding: 0; margin: 1rem 0; }
        .subject-list li { border-top: 1px solid #dce7df; padding: .9rem 0; overflow-wrap: anywhere; }
        .timer-display { margin: 1rem 0 .25rem; font-variant-numeric: tabular-nums; font-size: clamp(2rem, 10vw, 3.5rem); font-weight: 750; }
        fieldset { margin: 1rem 0; padding: 1rem; border: 1px solid #dce7df; border-radius: .6rem; }
        label { display: block; font-weight: 650; margin: 1rem 0 .35rem; }
        input[type=text], input[type=date], input[type=search], input[type=number], select { display: block; width: 100%; max-width: 36rem; padding: .7rem; border: 1px solid #71847a; border-radius: .4rem; font: inherit; background: #fff; }
        .notice { padding: .75rem 1rem; border-radius: .5rem; background: #e9f1ec; }
        .notice.error, .errors { color: #76231e; background: #fff0ee; }
        .errors { padding: .75rem 1rem .75rem 2.25rem; }
        .muted { color: #52655c; }
        form.inline { display: inline; }
        @media (max-width: 32rem) { body { padding: .5rem; } header { align-items: flex-start; } }
    </style>
</head>
<body>
<div class="shell">
    <header>
        <p>Talas 2.0</p>
        <nav aria-label="Student navigation">
            <a href="<?= esc(site_url('student')) ?>">Workspace</a>
            <a href="<?= esc(site_url('student/subjects')) ?>">Subjects</a>
            <a href="<?= esc(site_url('student/topics')) ?>">Topics</a>
            <a href="<?= esc(site_url('student/priority')) ?>">Priority</a>
            <a href="<?= esc(site_url('student/plan')) ?>">Plan</a>
            <a href="<?= esc(site_url('student/sessions')) ?>">Sessions</a>
            <form class="inline" method="post" action="<?= esc(site_url('logout')) ?>">
                <?= csrf_field() ?>
                <button class="button secondary" type="submit">Sign out</button>
            </form>
        </nav>
    </header>
    <main>
        <?php if ($success = session()->getFlashdata('success')): ?>
            <p class="notice" role="status"><?= esc($success) ?></p>
        <?php endif ?>
        <?php if ($error = session()->getFlashdata('error')): ?>
            <p class="notice error" role="alert"><?= esc($error) ?></p>
        <?php endif ?>
        <?= $this->renderSection('content') ?>
    </main>
</div>
</body>
</html>
