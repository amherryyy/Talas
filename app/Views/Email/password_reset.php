<!doctype html>
<html lang="en">
<body style="margin:0;padding:24px;background:#f6f4ff;color:#302e3b;font-family:Arial,sans-serif;">
    <main style="max-width:560px;margin:0 auto;padding:32px;background:#fff;border:1px solid #e6e1f5;border-radius:18px;">
        <p style="margin:0 0 8px;color:#6655d8;font-weight:700;">duno.</p>
        <h1 style="font-size:24px;margin:0 0 16px;">Reset your password</h1>
        <p style="line-height:1.6;">We received a request to reset the password for your Duno account. Enter this code in Duno:</p>
        <p style="margin:24px 0;padding:18px;border-radius:12px;background:#f1efff;color:#5142bd;text-align:center;font-size:32px;font-weight:700;letter-spacing:8px;"><?= esc($code) ?></p>
        <p style="line-height:1.6;">This code expires in <?= esc((string) $expiresMinutes) ?> minutes and can be used once. After five incorrect entries, request another code. If you did not request this, you can ignore this message; your password will not change.</p>
    </main>
</body>
</html>
