# Installable Duno PWA

**Status:** Approved by the project owner on 2026-10-30 for implementation before the next-week deadline.

## Scope

Duno remains a responsive CodeIgniter web application. The PWA layer makes it installable where supported, provides app icons and standalone display metadata, and supplies an offline recovery screen. It caches only the static public shell assets needed to render that screen and shared visual shell.

Authenticated pages, account/session responses, study records, forms, and POST actions are never cached or queued. Saving and viewing account data require an internet connection. This is not offline study mode, background sync, a native app, or a reminder system. It needs no database migration or server-side account preference.

## Acceptance criteria

- The prepared site exposes a valid manifest with relative scope and Duno icons, including a maskable icon.
- On a supported secure-context browser, the service worker registers at the app root and the install action is available when the browser offers installation.
- Navigations always go to the network. If a navigation cannot reach the server, it displays the static offline page.
- The service worker only caches same-origin GET requests for public assets, the manifest, favicon, and offline page. It does not cache HTML navigations, form results, auth responses, or private records.
- No write is queued while offline; the app clearly says saved data and actions need a connection.
- If service workers are unsupported or blocked by the host, the normal online app remains usable.

## Verification

Static checks on 2026-10-31 confirmed valid manifest JSON, relative scope, a maskable icon, all 10 pre-cache files present, and valid JavaScript syntax. The worker now allows only exact reviewed static-shell paths, plus the manifest, favicon, and offline page; version query strings remain supported by matching allowlisted paths. The r4 package includes and passed static inspection of these files. Browser install/offline/cache inspection and InfinityFree behavior still need verification before release.

The offline recovery screen now presents connection state, retries on request, and reloads once when the browser reports a connection restored. It continues to state that saved account data and changes are unavailable offline. The service-worker cache version was advanced so installed clients can receive the updated fallback; its exact static allowlist now includes the local contour texture used by the shared app background. Manual reconnection and update checks remain to be repeated after deployment.
