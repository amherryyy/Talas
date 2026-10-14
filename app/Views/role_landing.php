<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= esc($pageTitle) ?> · Talas</title>
    <style {csp-style-nonce}>
        :root { color-scheme: light; font-family: system-ui, sans-serif; color: #18342d; background: #f4f7f4; }
        body { margin: 0; min-height: 100vh; display: grid; place-items: center; padding: 1.25rem; box-sizing: border-box; }
        main { width: min(100%, 42rem); box-sizing: border-box; padding: clamp(1.5rem, 5vw, 3rem); background: #fff; border: 1px solid #dce7df; border-radius: 1rem; }
        h1 { margin: 0 0 1rem; font-size: clamp(1.75rem, 5vw, 2.5rem); }
        p { max-width: 38rem; line-height: 1.65; }
        button { margin-top: 1rem; padding: .7rem 1rem; border: 0; border-radius: .5rem; color: #fff; background: #176b55; font: inherit; cursor: pointer; }
        button:focus-visible { outline: 3px solid #e4a83a; outline-offset: 3px; }
    </style>
</head>
<body>
<main>
    <p>Talas 2.0</p>
    <h1><?= esc($heading) ?></h1>
    <p><?= esc($message) ?></p>
    <form method="post" action="<?= esc(site_url('logout')) ?>">
        <?= csrf_field() ?>
        <button type="submit">Sign out</button>
    </form>
</main>
</body>
</html>
