# Draft story: authentication and role foundation

**Status:** Local student/admin route boundaries and a one-time development-only first-admin command are implemented. End-to-end acceptance and hosted database/import verification remain pending.

## Story

As a Talas user, I can use the approved account flow to register or receive an account, sign in, and sign out. Student and admin access is enforced by the server so each role can reach only its authorized routes.

## Requirements

- `F-01`: student registration, session-based sign-in and sign-out using CodeIgniter Shield.
- `F-02`: distinct student/admin access; deny by default and authorize every protected request server-side.
- `S-01`: framework authentication and server-side role checks.
- `S-04`: CSRF-protected state changes and secure session settings appropriate to HTTPS.

## Approved acceptance criteria

1. Any visitor can register a student account; the server assigns the student group, and the registration form cannot select or submit a group/role.
2. A registered student can sign in and sign out.
3. The initial admin demo account is created through a controlled setup path; no public registration route can grant the admin group.
4. A signed-out request cannot access protected student or admin routes.
5. An authenticated student is denied admin routes; an admin is denied student-only routes.
6. Role checks occur server-side and do not rely on hidden navigation links.
7. State-changing forms use framework CSRF protection; sessions use secure settings suitable for the HTTPS host.
8. Login throttling and password handling use Shield/framework facilities; no plaintext password storage is introduced.
9. Tests cover signed-out, student, and admin access for allowed and denied routes.

## Boundaries

- Use CodeIgniter Shield; do not hand-build password storage or session authentication.
- The owner approved a private demo account for the initial admin and a separate synthetic student account for any public demo. Never put admin credentials in public demo instructions, source control, or a publicly readable file.
- Do not provide a public admin-registration path.
- Keep the MVP independent of outbound email; do not add email verification or magic-link reset unless the mail path is separately verified and approved.
- Do not add OAuth, multi-factor authentication, or other unapproved sign-in methods.
- Use synthetic accounts only. Never put hosting credentials or real student information in the repository, prompts, or test output.

## Approved route split

Owner approved this split on 2026-10-14:

- **Public:** landing/help pages, login, and open student registration. Apply Shield's authentication rate-limiting filter to login and registration.
- **Shared authenticated:** logout and self-service profile routes, protected by the session filter and scoped to the current user.
- **Student-only:** `/student/*`, protected by the session filter and an exclusive student-role filter that rejects admin-group members. Every data operation remains owner-scoped.
- **Admin-only:** `/admin/*`, protected by the session filter, admin-role filter, and `admin.access` permission. Start with only the approved admin view; default to aggregate-only data and exclude check-ins and student study details.
- Do not register a user-supplied role field. Shield's configured default group should be `student`; assign `admin` only through the reviewed bootstrap path.

Routes use Shield's session and permission filters plus an app role filter. The app filter rejects admin-group members from student routes even if an account is accidentally assigned both groups; the student/admin boundary therefore does not rely on groups being mutually exclusive by convention. This follows Shield's [controller filters](https://shield.codeigniter.com/latest/references/controller_filters/) and [authorization guidance](https://shield.codeigniter.com/1.4/references/authorization/).

## Remaining setup work

- Hosted admin provisioning and the hosting database/import path remain deferred. Do not use the local CLI bootstrap against a hosted database.

## Readiness constraints

- The owner deferred creation of a database, so the host phpMyAdmin import path is unverified.
- Shield config, public auth routes, CSRF, rate limiting, role-protected dashboard routes, and data-free landing pages are configured locally. The bootstrap command is implemented; local database availability, hosted database/import, and end-to-end auth-flow verification remain pending.

## Proposed implementation sequence after Ready

1. Install the official CodeIgniter app starter and Shield using reviewed, locked Composer versions. **Complete:** CI4 4.7.5 and Shield 1.4.1 are recorded in `composer.lock`.
2. Configure session, CSRF, authentication routes, and role authorization through framework facilities. **In progress:** Shield config is installed; registration/login are rate-limited, CSRF is enabled globally for state-changing requests, logout is POST-only, public registration defaults to `student`, remember-me and magic links are disabled, and last-active tracking is off. `/student` requires a session and student role while rejecting admin members; `/admin` requires a session, admin role, and `admin.access` permission. The student route remains a placeholder; the admin route now shows aggregate dashboard cards.
3. Add focused tests for signed-out, student, and admin allowed/denied paths after the database and bootstrap paths are ready.
4. Review the diff, security behavior, and local checks before the owner accepts the slice.

This story records the approved authentication scope and route split. The local first-admin setup is documented below; hosted provisioning remains outside that command.

## Local first-admin setup

After applying all local migrations, run C:\xampp\php\php.exe -d extension=intl spark admin:create-local from the repository root. The command runs only when CI_ENVIRONMENT=development, requires the configured database host to be loopback, and refuses to create a second admin. Enter a unique username and email when prompted. It creates an admin-only account with a generated password and prints that password once. Copy it directly into a password manager; it is not written to a file or application log.

This command is for the local development database only. It cannot create a hosted admin account and must not be pointed at a remote database.
