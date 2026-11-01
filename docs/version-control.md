# Talas version-control workflow

The project owner reviews and accepts changes. Agents may edit and prepare reviewable changes, but must not stage, commit, push, merge, or publish unless the owner explicitly authorizes that action.

## Audited repository and release state

Checked 2026-11-01:

- The repository has established Git history and a configured GitHub remote (`origin`). The checked-out branch is `main` at `310c5e520ed15509582a2f96af434e24e3065b14`, equal to `origin/main` (0 commits ahead or behind). The latest commit is `Merge InfinityFree .htaccess compatibility fix`.
- The working tree contains accumulated product, test, and documentation changes. There are no staged changes. Preserve all of them; do not use `reset --hard`, `clean`, force-push, or branch deletion to resolve uncertainty.
- No Git release tags are present. The release marker is not a Git tag, commit ID, or content hash.
- Composer resolves CodeIgniter Framework `v4.7.5` and Shield `v1.4.1` in `composer.lock`. The r6 package's `vendor/composer/installed.json` was checked and contains those same versions. The owner-reported host PHP version is 8.4.27 (2026-10-25); it has not been independently checked in this session.
- `duno-full-release-20261101-r6.zip` has SHA-256 `F95CBD32FC661E515384A33EC5DDC912AFF4F118F10F248EAA81AB0C36AE92BC`. Its public marker is `duno-full-release-20261101-r6`. It was built from the `main` commit above plus uncommitted worktree changes; it is not reproducible from that commit alone and has not been uploaded. A role-denial response-header fix was applied after assembly, so this ZIP is stale and must not be uploaded. The archive hash identifies the ZIP bytes, not the Git source revision.
- The owner reports r5 was used for live Aralytics acceptance and reports the r5 marker. Uploaded-file parity has not been independently verified. The stale r6 ZIP remains local; no replacement package has been built.

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
