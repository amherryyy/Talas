<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= esc(lang('Registration.emailSubject')) ?></title>
</head>
<body style="margin:0;background:#f7f6fc;color:#292735;font-family:Arial,sans-serif;">
    <main style="max-width:520px;margin:24px auto;padding:28px;background:#fff;border:1px solid #e5e0f4;border-radius:18px;">
        <p style="margin:0;color:#7061d8;font-size:24px;font-weight:700;">duno.</p>
        <h1 style="font-size:24px;line-height:1.25;"><?= esc(lang('Registration.emailHeading')) ?></h1>
        <p><?= esc(lang('Registration.emailBody')) ?></p>
        <p style="margin:28px 0;padding:18px;border-radius:12px;background:#f3f0ff;text-align:center;font-size:34px;font-weight:700;letter-spacing:.22em;"><?= esc($code) ?></p>
        <p><?= esc(lang('Registration.emailExpiry')) ?></p>
        <p style="color:#686579;font-size:13px;"><?= esc(lang('Registration.emailIgnore')) ?></p>
    </main>
</body>
</html>
