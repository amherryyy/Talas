<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= lang('Errors.pageNotFound') ?></title>

    <style>
        div.logo {
            height: 200px;
            width: 155px;
            display: inline-block;
            opacity: 0.08;
            position: absolute;
            top: 2rem;
            left: 50%;
            margin-left: -73px;
        }
        body {
            height: 100%;
            background: #fafafa;
            font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
            color: #777;
            font-weight: 300;
        }
        h1 {
            font-weight: lighter;
            letter-spacing: normal;
            font-size: 3rem;
            margin-top: 0;
            margin-bottom: 0;
            color: #222;
        }
        .wrap {
            max-width: 1024px;
            margin: 5rem auto;
            padding: 2rem;
            background: #fff;
            text-align: center;
            border: 1px solid #efefef;
            border-radius: 0.5rem;
            position: relative;
        }
        pre {
            white-space: normal;
            margin-top: 1.5rem;
        }
        code {
            background: #fafafa;
            border: 1px solid #efefef;
            padding: 0.5rem 1rem;
            border-radius: 5px;
            display: block;
        }
        p {
            margin-top: 1.5rem;
        }
        .footer {
            margin-top: 2rem;
            border-top: 1px solid #efefef;
            padding: 1em 2em 0 2em;
            font-size: 85%;
            color: #999;
        }
        a:active,
        a:link,
        a:visited {
            color: #dd4814;
        }
        .page-navigation { display:flex; flex-wrap:wrap; justify-content:center; gap:.65rem; margin-top:1.5rem; }
        .page-navigation a { display:inline-flex; min-width:6.5rem; min-height:2.75rem; align-items:center; justify-content:center; gap:.4rem; padding:.55rem .85rem; border:1px solid #ddd8f3; border-radius:.75rem; color:#332c5d; background:#fff; font:600 1rem system-ui,sans-serif; text-decoration:none; }
        .page-navigation a:focus-visible { outline:3px solid #7465d8; outline-offset:3px; }
    </style>
</head>
<body>
    <div class="wrap">
        <h1>404</h1>

        <p>
            <?php if (ENVIRONMENT !== 'production') : ?>
                <?= nl2br(esc($message)) ?>
            <?php else : ?>
                <?= lang('Errors.sorryCannotFind') ?>
            <?php endif; ?>
        </p>
        <nav class="page-navigation" aria-label="Page navigation">
            <a href="<?= esc(site_url()) ?>" data-page-back aria-label="Go back to the previous page"><span aria-hidden="true">←</span> Back</a>
            <a href="<?= esc(site_url()) ?>" aria-label="Go to home"><span aria-hidden="true">⌂</span> Home</a>
        </nav>
    </div>
    <script src="<?= esc(base_url('assets/js/page-navigation.js')) ?>" defer></script>
</body>
</html>
