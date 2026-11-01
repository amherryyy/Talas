# Talas version-control workflow

The project owner reviews and accepts changes. Agents may edit and prepare reviewable changes, but must not stage, commit, push, merge, or publish unless the owner explicitly authorizes that action.

## Audited repository and release state

Checked 2026-11-01:

- The repository has established Git history and a configured GitHub remote (`origin`). Base `main` and `origin/main` remain at `310c5e520ed15509582a2f96af434e24e3065b14`. The reviewed release branch `release/duno-production-r7` has source commit `5c7463cc77f4b3a3e1e129da86623c4d02d02ef3`.
- The r7 source baseline groups the reviewed accumulated product, test, and documentation changes into one release-readiness commit. The worktree has subsequent release-record documentation edits; inspect actual Git status before further operations. Do not use `reset --hard`, `clean`, force-push, or branch deletion to resolve uncertainty.
- No Git release tags are present. The release marker is not a Git tag, commit ID, or content hash.
- Composer resolves CodeIgniter Framework `v4.7.5` and Shield `v1.4.1` in `composer.lock`. The r6 package's `vendor/composer/installed.json` was checked and contains those same versions. The owner-reported host PHP version is 8.4.27 (2026-10-25); it has not been independently checked in this session.
- `duno-full-release-20261101-r7.zip` has SHA-256 `B48283C634F98F9C3820C38AB5F9A035D380E97F28187456CEAE297DE87863C4`; it contains 1,239 entries (5,372,534 bytes), marker r7, CI4 4.7.5 and Shield 1.4.1, and was built from clean source commit `5c7463cc77f4b3a3e1e129da86623c4d02d02ef3`. The owner uploaded it and a fresh cache-busted Brave request returned r7. The archive hash identifies ZIP bytes; the full commit identifies source files. Complete host file parity is not verified.
- The r6 ZIP remains stale and must not be uploaded. Its SHA-256 is `F95CBD32FC661E515384A33EC5DDC912AFF4F118F10F248EAA81AB0C36AE92BC`; it predates the role-denial header fix and was built from dirty `main`.
- An r8 response-hardening preview ZIP was prepared from r7 provenance commit `d4a3354e2c476aefaa9cfbd340364e88b9d25500` plus uncommitted source changes; SHA-256 `9C3345FF486869072F0C70DCFE04A2A553F20895F14235C312988E688B60D8FC`. It is not tied to a clean commit and must not be treated as production-ready. The current `.git` directory is read-only in this workspace, so no staging or commit was performed.
- The owner reports r5 was used for live Aralytics acceptance. R7 is now the uploaded release by owner report, and its marker was independently read in Brave. An old open tab still showed r3, so stale response/cache behavior requires follow-up. Uploaded-file parity and full live behavior remain unverified.

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

The current worktree is already dirty. Do not switch branches, pull, reset, or stage it as a routine cleanup step. First review and classify every existing change, preserve unrelated work, and get owner direction for how to divide the accumulated stories. For a future reviewed story, create a focused branch from an up-to-date `main` only after the worktree is clean and the owner has authorized the required Git operations.

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
