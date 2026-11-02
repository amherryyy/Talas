# Talas version-control workflow

The project owner reviews and accepts changes. Agents may edit and prepare reviewable changes, but must not stage, commit, push, merge, or publish unless the owner explicitly authorizes that action.

## Audited repository and release state

Checked 2026-11-02:

- GitHub `main` includes PR #23 merge `80e0a6a7366acb8d72a9b8c21b81fe715ca0cc84`, following PR #22. Local `main` still points at security remediation commit `88c5c19fdcadcbba898b2570ed73a9d880d6f6f0`; the current follow-up branch is `docs/r10-host-confirmation`.
- On 2026-11-02, accumulated product work was merged as PR #21, security remediations and r10 preparation as PR #22, and release-state records as PR #23. The owner reports r10 uploaded and supplied its marker; phpMyAdmin output confirms both new migration rows and schemas. Full hosted-file parity and post-upload smoke checks remain unverified.
- The hosted release and Git source are separate checkpoints. The owner supplied marker `duno-full-release-20261102-r10`; phpMyAdmin confirms the password-reset table migration in batch 16 and academic-school migration in batch 17. The r10 ZIP was built from source commit `80e0a6a7366acb8d72a9b8c21b81fe715ca0cc84`, contains 1,272 entries, and has SHA-256 `0A352798E8D6CB2B999DB9106771A403845CAD15F9628339A94720D29A48A33C`. The direct marker request from this audit was unavailable, so the marker is owner-provided evidence; complete uploaded-file parity remains unverified.
- The r8 package was built from clean source commit `120b76665ab062d5133f71ce03117093fb1f7a7f`. The local r9 ZIP (`build/duno-full-release-20261101-r9.zip`, 1,253 entries, SHA-256 `3B87F3EFEF361BA245DA15C1FDC3030993FC0FF5372C5941C04D70EE69CE4736`) has marker r9, but its source provenance was not found during this audit. The owner confirmed this artifact was uploaded; do not treat it as reproducible from `main` until provenance is established.
- The current branch `docs/r10-host-confirmation` starts at merged `main` commit `80e0a6a`; its intended changes record owner-provided r10 marker and phpMyAdmin evidence. The unrelated untracked logo file is preserved and excluded from Git and the release package. Do not use `reset --hard`, `clean`, force-push, or branch deletion to resolve uncertainty.
- No Git release tags are present. The release marker is not a Git tag, commit ID, or content hash. `release/duno-production-r7` is a historical branch name; it points at the r8 provenance-note commit `4525a5b`, not at `main` or an r9 source commit.
- Composer resolves CodeIgniter Framework `v4.7.5` and Shield `v1.4.1` in `composer.lock`. The r6 package's `vendor/composer/installed.json` was checked and contains those same versions. The owner-reported host PHP version is 8.4.27 (2026-10-25); it has not been independently checked in this session.
- `duno-full-release-20261101-r7.zip` has SHA-256 `B48283C634F98F9C3820C38AB5F9A035D380E97F28187456CEAE297DE87863C4`; it contains 1,239 entries (5,372,534 bytes), marker r7, CI4 4.7.5 and Shield 1.4.1, and was built from clean source commit `5c7463cc77f4b3a3e1e129da86623c4d02d02ef3`. The owner uploaded it and a fresh cache-busted Brave request returned r7. The archive hash identifies ZIP bytes; the full commit identifies source files. Complete host file parity is not verified.
- The r6 ZIP remains stale and must not be uploaded. Its SHA-256 is `F95CBD32FC661E515384A33EC5DDC912AFF4F118F10F248EAA81AB0C36AE92BC`; it predates the role-denial header fix and was built from dirty `main`.
- The response-hardening changes are committed at `120b76665ab062d5133f71ce03117093fb1f7a7f` on the historical `release/duno-production-r7` branch. The r8 package has 1,241 entries (5,373,531 bytes) and SHA-256 `703F22A55598BC4F831FEAF0A9C85AF0327B678A5BAB8686C21AF0DBDFFA0874`; it is built from that clean commit and the owner uploaded it. Brave confirmed r8 marker and tested header/status behavior. Documentation-only changes from earlier work remain in the worktree and were not included in the deployment ZIP.
- The owner reports r5 was used for live Aralytics acceptance. R9 is the owner-confirmed current upload, and the owner supplied the live marker text for r9. The owner now also reports selected r9 cross-account write-isolation and account-deletion checks; phpMyAdmin output confirms the progression migration. R9's source provenance, uploaded-file parity, and remaining live behavior remain unverified.

## Review and release procedure

Before any Git operation, inspect the actual repository state:

```powershell
git status --short --branch
git status --short --ignored
git diff --check
git diff --stat
git diff
git ls-files --others --exclude-standard
```

Review ignored files separately for `.env`, local Composer tooling, `vendor/`, runtime data, machine-specific `.vscode/`, and temporary host probes. The root `env` file is the framework template and may be tracked; the real `.env` must remain ignored. Never include credentials, real student data, local databases, logs, caches, or temporary probes in a commit or release package.

PRs #22 and #23 are merged. The r10 package provenance and owner-reported upload marker are recorded above. The marker and schema checks do not establish complete host-file parity or successful post-upload behavior. For future stories, create focused branches from an up-to-date `main` after the worktree is clean and the owner has authorized the required Git operations.

Before a future release is treated as tied to source control, build from an owner-reviewed clean commit and record that full commit hash alongside the package filename, release marker, locked dependency versions, and archive SHA-256. Do not label a dirty-worktree archive reproducible from its base commit. Keep the marker and Git provenance as separate fields.

## Per-story branch and staging flow

Once the owner has approved the Git operations and the worktree is clean, start each story from current `main`:

```powershell
git status --short --branch
git switch main
git pull --ff-only origin main
git switch -c feature/F-02-role-boundaries
```

Use `fix/` for a defect and `docs/` for a documentation-only change. Keep each branch focused. If an existing worktree is dirty or contains unexplained changes, stop before switching or pulling and preserve that work.

Stage only paths that belong to the reviewed story:

```powershell
git add -- app/Config/Routes.php app/Filters/ExclusiveRoleFilter.php docs/auth-foundation-story.md
git status --short
git diff --cached --check
git diff --cached --stat
git diff --cached --name-status
git diff --cached
```

Do not use `git add .` or `git add -A` for routine story commits. If a file is staged accidentally, unstage only that path with `git restore --staged -- <path>`, then inspect the index again. Run the required checks, review the complete staged diff, and commit only after owner authorization. Use a concise Conventional Commit message, for example:

```text
feat(auth): enforce student and admin route roles
fix(plan): reject sessions outside the selected day
docs(workflow): define staging and GitHub process
```

## Push, pull request, and merge

Push a story branch only when the owner has authorized publication:

```powershell
git push -u origin feature/F-02-role-boundaries
```

Never put tokens or passwords in URLs, commands, or repository files. Open a pull request targeting `main` with the project template and actual verification results, including checks not run and why. Review the GitHub diff and CI output; a green build does not replace authorization, privacy, browser, or host acceptance. Do not merge or deploy without owner authorization. After a merge is confirmed, update local `main` with `git pull --ff-only origin main` before beginning another story branch.

## Safe staging checklist

- [ ] The branch is the intended story branch and Git operations are authorized.
- [ ] The worktree has no unexplained changes.
- [ ] The staged file list contains only the story's intended files.
- [ ] The complete staged diff has been reviewed.
- [ ] No credentials, private data, local environment files, generated dependencies, or temporary probes are staged.
- [ ] `git diff --cached --check` reports no whitespace errors.
- [ ] Required checks and actual results are recorded.
- [ ] The commit message describes the change and story where useful.

When anything is uncertain, leave it unstaged and ask the project owner to review that path.
