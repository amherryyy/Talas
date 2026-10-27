<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">

    <title><?= lang('Errors.whoops') ?></title>

    <style>
        <?= preg_replace('#[\r\n\t ]+#', ' ', file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'debug.css')) ?>
        .page-navigation { display:flex; flex-wrap:wrap; justify-content:center; gap:.65rem; margin-top:1.5rem; }
        .page-navigation a { display:inline-flex; min-width:6.5rem; min-height:2.75rem; align-items:center; justify-content:center; gap:.4rem; padding:.55rem .85rem; border:1px solid #ddd8f3; border-radius:.75rem; color:#332c5d; background:#fff; font:600 1rem system-ui,sans-serif; text-decoration:none; }
        .page-navigation a:focus-visible { outline:3px solid #7465d8; outline-offset:3px; }
    </style>
</head>
<body>

    <div class="container text-center">

        <h1 class="headline"><?= lang('Errors.whoops') ?></h1>

        <p class="lead"><?= lang('Errors.weHitASnag') ?></p>
        <nav class="page-navigation" aria-label="Page navigation">
            <a href="<?= esc(site_url()) ?>" data-page-back aria-label="Go back to the previous page"><span aria-hidden="true">←</span> Back</a>
            <a href="<?= esc(site_url()) ?>" aria-label="Go to home"><span aria-hidden="true">⌂</span> Home</a>
        </nav>

    </div>

    <script src="<?= esc(base_url('assets/js/page-navigation.js')) ?>" defer></script>
</body>

</html>
