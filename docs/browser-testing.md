# Browser and responsive checks

Playwright provides repeatable browser checks for Talas layouts. It is a development-only tool: the application remains server-rendered PHP, and neither Node nor `node_modules` belongs in a hosting upload.

## One-time setup

1. Install Node.js and npm.
2. From the repository root, install the locked test dependency:

   ```powershell
   npm.cmd ci
   ```

3. Store Playwright's Chromium browser under the ignored project `node_modules` directory:

   ```powershell
   $env:PLAYWRIGHT_BROWSERS_PATH = '0'
   npx.cmd playwright install chromium
   ```

Set `PLAYWRIGHT_BROWSERS_PATH` to `0` in each terminal used to run Playwright when using this local browser cache.

If the runner fails at startup with `Error: spawn EPERM` in `WorkerHost.startRunner`, Windows has denied Node's child-process launch before a browser test ran. Run the suite in a local PowerShell terminal where Node can start child processes. The runner configuration itself does not need an application change for this error.

## Run

The synthetic browser account is stored in the isolated review database on port `3308`. Check whether it is running:

```powershell
Get-NetTCPConnection -LocalPort 3308 -State Listen -ErrorAction SilentlyContinue
```

If the command returns no listener, start the review database in the background:

```powershell
Start-Process -FilePath C:\xampp\mysql\bin\mysqld.exe -ArgumentList '--defaults-file=C:\xampp\htdocs\Talas\.tools\my-talas-review.ini' -WindowStyle Hidden
```

Start the web app from the repository root using the review router. It loads the private port-3308 connection override and PHP's `intl` extension. Do not use `spark serve` here: it launches a child PHP process that does not inherit the `-d extension=intl` flag, and the regular `public` rewrite router would use the default `.env` database on port `3307`.

```powershell
Set-Location C:\xampp\htdocs\Talas
C:\xampp\php\php.exe -d extension=intl -S localhost:8080 -t public .tools\talas-review-router.php
```

Leave the server running. The `localhost:8080` host matches `app/Config/App.php` and the Playwright default, keeping the browser session cookie on the same host. If another server is already using port 8080, stop it first. In a second terminal, run:

```powershell
$env:PLAYWRIGHT_BROWSERS_PATH = '0'
npm.cmd test
```

The test suite checks the login page at 320, 400, 768, and 1280 CSS pixels. With a configured synthetic student account it checks the student dashboard and study idea cards, subject list, topic search/filter, Priority, Sessions, daily plan, private Check-ins, and Aralytics pages at those sizes. It checks keyboard access to a suggestion source link, the daily-plan time input, and the Aralytics print controls; it also checks check-in create/edit/delete and that the private rating chart and numeric table render without horizontal overflow at 400px and 1280px, then disappear from print while the session report remains. These checks assert that page content fits the viewport without horizontal overflow and that primary controls remain visible. A person should still review the rendered screens for visual quality, keyboard flow, and browser-specific behavior.

For authenticated checks, the runner first reads the synthetic student credentials from the ignored local file `.tools/review-account.txt` when it exists. This file must contain `username=`, `email=`, and `password=` lines and must never be committed. The current review setup already has this local account file. Environment variables override it when another **synthetic student account** is needed:

```powershell
$env:TALAS_E2E_EMAIL = 'your-synthetic-student@example.test'
$env:TALAS_E2E_PASSWORD = 'your-local-test-password'
npm.cmd test
```

Do not commit credentials or add real student records to test fixtures. When the local file and both environment variables are absent, the login page check still runs and the authenticated page check is reported as skipped. The authenticated check uses a synthetic account and creates, edits, and deletes only the check-in it creates during the run; existing synthetic check-ins are left intact.

Useful commands:

```powershell
npm.cmd run test:headed
npm.cmd run test:report
```

Playwright can detect viewport overflow and capture failure screenshots/traces. A person still needs to review the rendered screens for visual quality, keyboard flow, and browser-specific behavior. PHPUnit remains the check for PHP rules and server behavior.
