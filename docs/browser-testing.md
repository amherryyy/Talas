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

## Run

Start the local Talas app at `http://localhost:8080` (or set `TALAS_BASE_URL` to its base URL). The default matches `app/Config/App.php`, so the browser session cookie stays on the same host. Then run:

```powershell
$env:PLAYWRIGHT_BROWSERS_PATH = '0'
npm.cmd test
```

The test suite checks the login page at 320, 400, 768, and 1280 CSS pixels. With test credentials it checks the student dashboard and study idea cards, subject list, topic search/filter, Priority, Sessions, private Check-ins, and Aralytics pages at those sizes. It checks keyboard access to a suggestion source link and Print report control, check-in create/edit/delete, and that Aralytics print media hides navigation and controls while retaining report content. These checks assert that page content fits the viewport without horizontal overflow and that primary controls remain visible. The rating-distribution chart and its numeric table still need a manual review at mobile and desktop widths, including confirmation that print output hides the private chart.

For authenticated checks, use a **synthetic student account** and set its credentials only in the current shell:

```powershell
$env:TALAS_E2E_EMAIL = 'your-synthetic-student@example.test'
$env:TALAS_E2E_PASSWORD = 'your-local-test-password'
npm.cmd test
```

Do not commit credentials or add real student records to test fixtures. When these variables are absent, the login page check still runs and the authenticated page check is reported as skipped. The authenticated check uses a synthetic account and creates, edits, and deletes one synthetic private check-in during the run.

Useful commands:

```powershell
npm.cmd run test:headed
npm.cmd run test:report
```

Playwright can detect viewport overflow and capture failure screenshots/traces. A person still needs to review the rendered screens for visual quality, keyboard flow, and browser-specific behavior. PHPUnit remains the check for PHP rules and server behavior.
