# Story: responsive and keyboard-ready experience (F-13)

**Status:** F-13 and its compact-content follow-up were accepted by the owner on 2026-10-17 and integrated on `main` at `c83f664` as of 2026-10-25. The owner approved the signed-out public navigation mapping on 2026-10-25 and confirmed the guest experience is all good after review.

## Story

As a student, I can use the main Talas experience on a narrow phone or desktop, move through its controls by keyboard, and print a readable Aralytics report.

## Acceptance criteria

- The login page, student workspace, subjects, topic search, priority, sessions, daily plan, and Aralytics pages fit viewport widths of 320, 400, 768, and 1280 CSS pixels without horizontal overflow.
- Primary links and form controls have accessible names and visible keyboard focus; the daily-plan available-time field and Aralytics period and print controls can be reached by keyboard.
- Aralytics print output hides navigation and interactive controls while retaining its report content.
- A manual visual review checks narrow phone and desktop layouts, keyboard navigation, and print output in a browser.

## Verification record

- Existing Playwright coverage checks the login page and, when local synthetic student credentials are configured, the workspace, subjects, topic search, priority, sessions, and Aralytics pages at 320, 400, 768, and 1280 pixels. It reads the ignored local `.tools/review-account.txt` by default, with environment variables available to override it.
- This task adds the daily plan to that responsive loop and checks keyboard access to its available-time field.
- The owner accepted F-13, then requested less scrolling on narrow screens. The follow-up collapses the wrapped student navigation behind a keyboard-accessible Menu control on phone widths, removes a duplicate navigation link, tightens history and report spacing, and shortens repeated explanatory copy without removing details or controls.
- The owner reviewed and accepted the compact-content follow-up for now.
- Existing Aralytics browser coverage checks period links, keyboard access to the print button, and print media behavior.
- The owner previously reviewed responsive and keyboard behavior for the subject, topic, priority, study-session, Aralytics, and suggestion screens; see the linked story records and `docs/ai-use-log.md`.
- After the compact-content follow-up, `PLAYWRIGHT_BROWSERS_PATH=0 npm.cmd test` passed both Playwright tests (2 passed, 53.9 seconds) on 2026-10-17 using the ignored local synthetic account file, without shell credential variables. The authenticated journey covered the responsive page matrix, keyboard reachability, Aralytics print behavior, and check-in create/edit/delete; cleanup verified only the newly created check-in was deleted, leaving existing synthetic entries intact. The restricted runner initially returned `spawn EPERM`; rerunning with permission to start the Playwright worker completed successfully.
- The owner's Playwright run reached a CodeIgniter error page because `spark serve` spawned PHP without the CLI `-d extension=intl` flag. A direct server run then used the default `.env` database on port 3307, which refused the connection. Browser-testing instructions now use the local review router, which loads the synthetic review database on port 3308, and start PHP's built-in server directly with `intl` loaded.
- The owner reviewed and accepted the compact-content follow-up. Afterward, the full PHPUnit suite passed (65 tests, 582 assertions); scoped AuthenticationFlow and AdminDashboard tests passed (6 tests, 43 assertions). PHP syntax, JavaScript syntax, and `git diff --check` passed.

## Remaining verification

- The owner manually reviewed the signed-in student and admin experiences and reported them all good on 2026-10-25. The public page desktop and phone-like layout, menu click/keyboard operation, and visible focus were also reviewed locally. After the guest bar and public Guide section were implemented, the owner confirmed the guest experience is all good. The bar links Home, Tools, and Guide to public sections, with Join linking to student registration and Sign in remaining in the top menu. Hosted behavior remains unverified.
