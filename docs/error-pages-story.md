# Duno error and host-denial fallback pages

**Status:** Implemented in the local working tree on 2026-11-01; host behavior and visual review remain unverified.

## Context

Owner-reported live checks on 2026-11-01 found:

- Signed-out `/student/priority` redirected to the Duno login page; using browser Back did not show the Focus topics.
- `/app/Config/Paths.php` displayed InfinityFree's generic 403 page at `https://errors.infinityfree.net/errors/403/`. No source contents were reported.
- `/.env` displayed Duno's PWA offline recovery page. The service worker is network-first for navigation and returns this page when the fetch fails, so the observed page does not establish a host 403/404 response. Per the existing owner-directed deployment note, do not repeat that probe.

The production package denies `app`, `vendor`, `system`, `writable`, dotfiles, and other private paths in its generated root `.htaccess` before requests can reach CodeIgniter. CodeIgniter's normal 403/404 views therefore cannot brand every denial. The existing offline page is a separate network-failure recovery state and should remain distinct from authorization and not-found errors.

## Proposed behavior

Keep every private-path denial in place. Where the hosting layer allows custom error documents, serve a small Duno-branded static 403 page from an ordinary public path such as `/status/403.html`; do not name the directory `errors`, and do not route denied requests through the application. Customize CodeIgniter-rendered 403 and 404 responses separately, preserving their status codes and providing the same visual language. Keep the current offline page for failed network navigations only.

The pages should use concise, non-technical copy, the Duno wordmark/palette, a Home link, and a sign-in link where useful. They must not echo the requested path, expose framework/file/configuration details, or suggest that data was loaded. Use a shared visual template with distinct access-denied and not-found messages. The static host 403 page should not rely on JavaScript, sessions, or private assets.

InfinityFree forum guidance describes `ErrorDocument` directives in an account's `htdocs` `.htaccess`, but provider-level error-document behavior can affect which page appears. Treat this as a host-specific capability to verify, not a guaranteed override. If InfinityFree continues to replace the response with its own page, keep the deny rules and record that the provider controls the outer fallback; do not weaken access controls to force Duno branding. See [InfinityFree custom error-page guidance](https://forum.infinityfree.com/t/soft-error-codes/41428) and its example of a provider-level error-document mapping in [this support thread](https://forum.infinityfree.com/t/the-link-to-index-html-does-not-work/107769).

## Acceptance criteria

- Private paths and dotfiles remain denied at the web-server layer; no request to them is routed into CodeIgniter or exposes file contents.
- On a disposable host/package probe, a denied private path returns 403/404. If supported by the host, it displays the Duno static 403 page while retaining a denial status and staying on the Duno host.
- CodeIgniter-generated 403 and 404 pages are Duno-branded, responsive, keyboard accessible, and preserve the original HTTP status. They provide a working safe recovery link and reveal no exception details.
- The PWA offline page appears only when a navigation cannot reach a network response. It is not treated as evidence that a path returned 403/404.
- The service worker does not cache error HTML, authenticated responses, or private records. The fallback assets are static and included in the reviewed FTP package.
- The deployment check records the exact requested path, final URL, visible page, and HTTP status from the Network panel when available. A provider-generated page without a captured response status is recorded as partial/unverified.

## Implementation and release plan

1. Review CI4 error rendering and current production root `.htaccess` generation in `scripts/Build-InfinityFreePackage.ps1`.
2. Add Duno error-view styling for application-level 403/404 without changing authorization decisions or status handling.
3. Add static host-level 403/404 files in a non-reserved public directory and package them from the build script. Add `ErrorDocument` directives only after a harmless denial fixture confirms that the host supports them.
4. Verify locally that normal 403/404 responses retain status, denial rules still block each private path, and the static page cannot leak or recurse.
5. Have the owner upload the reviewed package, then use a harmless temporary denial fixture or an already-denied app path to verify the provider outcome. Do not probe `/.env` again.
6. Update deployment and acceptance records with the observed final page and status. No database migration is expected.

## Current implementation

The proposed default is implemented locally: separate 403 and 404 messages share one responsive Duno template; the text uses the current English/Filipino/Taglish locale for CodeIgniter-rendered errors; and static host fallback files provide Home and Sign in links without JavaScript or session data. The role filter now renders the same branded 403 view instead of a bare `Forbidden` body, while keeping the same 403 decision. The package builder copies both static pages and adds local `ErrorDocument` mappings while retaining all private-path deny rules. The offline page and service-worker behavior are unchanged.

After the final role-denial header fix, PHPUnit passed 103 tests and 1,031 assertions; a focused test passed 1 test and 8 assertions for the no-store and security headers. Playwright passed 3/3 before that header-only change and confirmed local 403/404 status and branded copy. PHP syntax passed for all 1,036 packaged PHP files, the package-builder PowerShell script parsed without syntax errors, and Composer audit found no advisories. A human visual review and harmless host denial probe are pending. The r6 archive predates the header change and must not be uploaded. The host may continue to show its provider-generated page; do not weaken deny rules if it does.
