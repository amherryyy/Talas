# Talas version-control workflow

This is the operating procedure for local Git, staging, commits, GitHub, and pull requests. The project owner reviews and accepts changes. Agents may edit and prepare a reviewable change, but must not stage, commit, push, merge, or publish unless the owner explicitly authorizes that action.

Replace example repository URLs, branch names, and file paths with the actual values before running commands.

## Current repository state

As checked on 2026-10-17, F-10 was merged to `main` through [PR #5](https://github.com/amherryyy/Talas/pull/5) (merge commit `ce44d12`). Local `main` was fast-forwarded to `ce44d12`; F-11, F-12, and F-13 are integrated locally on `feature/F-13-responsive-accessibility`. The latest F-11 verification follow-up is committed as `84ab7f5`. The `origin` remote is configured. A pre-existing untracked `.github/` path remains outside the committed work and must remain untouched. Never merge or deploy without separate owner authorization. Recheck actual branch and worktree state before each Git action. Do not use `reset --hard`, `clean`, force-push, or branch deletion to resolve uncertainty.

## One-time GitHub setup

### 1. Review the local snapshot

From PowerShell in the project root:

```powershell
git status --short --branch
git status --short --ignored
git check-ignore -v .env .tools/composer.phar vendor writable/logs writable/cache
```

Confirm that `.env`, local Composer tooling, `vendor/`, runtime data, machine-specific `.vscode/`, and temporary host-probe upload fixtures are ignored. The root `env` file is the framework's configuration template and is expected to be versioned; the real `.env` is not. `.vscode/settings.json` currently contains an absolute XAMPP path, so keep it local. Review all source, docs, tests, and deployment files for credentials, real student data, local paths, or temporary probes.

### 2. Create the first local commit

This repository has no prior history, so the first commit establishes the reviewed project baseline on `main`. After reviewing the complete snapshot, stage the intended project files and inspect the index before committing:

```powershell
git add -A
git status --short
git diff --cached --check
git diff --cached --stat
git diff --cached --name-status
git diff --cached
```

If any local, generated, secret, or unrelated file appears, unstage only that path. On the unborn branch, use `git rm --cached -- <path>`; after the first commit, use `git restore --staged -- <path>`. Correct `.gitignore` if appropriate, then recheck the full staged diff. Do not commit until the index contains only the reviewed baseline.

Use a clear initial message, for example:

```powershell
git commit -m "chore: establish Talas CI4 foundation"
git status --short --branch
git log -1 --oneline
```

The initial commit records the current scaffold, including work already present locally. It does not mean the authentication story has passed its database-backed acceptance checks.

### 3. Connect and publish to GitHub

Create the GitHub repository without initializing it with a README, license, or `.gitignore` when publishing this existing local history. Then add the actual repository URL and verify it before the first push:

```powershell
git remote add origin https://github.com/OWNER/REPOSITORY.git
git remote -v
git push -u origin main
```

Use the account's approved HTTPS or SSH authentication; never put a token or password in the URL, a command, or repository files. If the GitHub repository already has commits, stop and inspect both histories. Preserve and reconcile the remote history; never force-push to make them match.

After the first push, enable GitHub branch protection for `main` when available: require pull requests, prevent force-pushes and deletion, and require configured CI checks once CI exists. Until checks are configured, say so in the review; do not invent a passing status gate.

## Per-story branch and staging flow

Start each ready story from an up-to-date `main` and use one focused branch:

```powershell
git status --short --branch
git switch main
git pull --ff-only origin main
git switch -c feature/F-02-role-boundaries
```

Use `fix/` for a defect and `docs/` for documentation-only work. Do not start if the working tree has unexplained changes; identify and preserve them first. Do not pull or switch branches in the current unborn repository until its initial commit and remote setup are complete.

While working, keep changes scoped to the story. Before staging, inspect both tracked and untracked files:

```powershell
git status --short
git diff
```

Stage only the paths that belong to the story:

```powershell
git add -- app/Config/Routes.php app/Filters/ExclusiveRoleFilter.php docs/auth-foundation-story.md
git status --short
git diff --cached --check
git diff --cached --stat
git diff --cached --name-status
git diff --cached
```

Replace the example paths with the actual story files. Do not use `git add .` or `git add -A` for routine story commits. Do not stage unrelated user changes, secrets, `.env`, local databases, uploads, logs, caches, `vendor/`, `.tools/`, or temporary hosting probes. If a file is already staged accidentally, unstage that specific path and review again.

Run the story's required checks, then recheck the staged diff. Commit only the reviewed index. Use a concise Conventional Commit message, such as:

```text
feat(auth): enforce student and admin route roles
fix(plan): reject sessions outside the selected day
docs(workflow): define staging and GitHub process
```

One branch should represent one story. Use additional commits only for meaningful, reviewable steps; do not amend or rewrite a commit that has already been pushed unless the owner explicitly directs it and collaborators are not relying on it.

## Push and review

Push a story branch only when the owner has authorized publication:

```powershell
git push -u origin feature/F-02-role-boundaries
```

Open a pull request targeting `main` with a complete, self-contained description. Use `.github/PULL_REQUEST_TEMPLATE.md` and fill in every applicable section: story/requirement IDs and motivation, user-visible changes, security/privacy impact, verification with actual results (including checks not run and why), AI-use log status, and known limitations or deployment impact. Link related docs when relevant. Keep the title concise and specific; write the body so a reviewer can understand the change without reconstructing it from commit messages. Review the concrete GitHub diff and CI results; a green build does not replace authorization, privacy, or browser checks.

After owner review and acceptance, merge using the repository's selected GitHub review process. Do not merge or deploy on the owner's behalf. Then update local `main` with `git pull --ff-only origin main` before beginning the next story branch. Keep the branch until the merge is confirmed; delete it only through the normal GitHub cleanup after confirmation.

## Safe staging checklist

Before every commit, confirm:

- [ ] The branch is the intended story branch (or this is the reviewed initial baseline commit).
- [ ] `git status --short` contains no unexplained changes.
- [ ] The staged file list contains only this story's intended files.
- [ ] The staged diff has been read, not just summarized.
- [ ] No credentials, private data, local environment files, generated dependencies, or temporary probes are staged.
- [ ] `git diff --cached --check` reports no whitespace errors.
- [ ] Required checks and their actual results are recorded.
- [ ] The commit message describes the change and requirement/story where useful.

When anything is uncertain, leave it unstaged and ask the project owner to review that path. A clean, narrow index is the boundary of what the next commit will contain.
