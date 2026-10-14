# Talas coding-agent instructions

CodeIgniter 4 is the agreed framework. The official CI4 starter 4.7.5 and Shield 1.4.1 are installed; the local authentication foundation is configured, while database-backed auth behavior and migrations remain unverified.

## Product and scope

Build the one-month Talas 2.0 course MVP described in `docs/requirements.md`. Keep the browser experience responsive and the rule-based priority, plan, and Aralytics outputs explainable. Preserve student ownership and private optional check-ins. Do not add AI recommendations, background workers, native apps, or features outside the approved backlog.

## Stack and deployment

Use CodeIgniter 4, PHP, MySQL/MariaDB, server-rendered views, CSS, and small plain JavaScript modules. Target InfinityFree free hosting. Never assume SSH, server Composer, cron, remote MySQL, or a changeable `htdocs` web root. Keep production assets and dependencies deployable by FTP. Protect all non-public application paths with tested deny rules.

## Before coding

Read `README.md` and `docs/development-blueprint.md`; inspect the current branch and working tree. Read `docs/requirements.md` and `docs/deployment.md` once they exist. For an unclear story, state the assumption and request a product decision before changing its behavior. Keep changes small enough to review in one pull request.

Follow `docs/ai-agent-workflow.md` for task briefs, repository-state handling, AI-use logging, verification reporting, review, and release flow. Follow `docs/version-control.md` for branching and staging. Do not stage, commit, push, merge, or publish unless the project owner explicitly authorizes the action. Record material LLM assistance in `docs/ai-use-log.md`; the project owner handles any course-specific disclosure requirements.

## Implementation rules

- Use framework validation, escaping, CSRF and session facilities. Check authorization and owner scope on every read and write.
- Put priority, plan, and summary calculations in small testable services; controllers stay thin.
- Use migrations for schema changes and safe demo seed data. Never commit `.env`, credentials, real student records, or hosting login details.
- Keep optional check-ins private. Avoid medical language and unsupported claims about grades or learning.
- Do not introduce a frontend framework or runtime build dependency without an explicit project decision.

## Done means

Run relevant unit/feature tests, PHP syntax/style checks, dependency audit, and a manual browser check for changed interactions. For auth, data-access, or deployment changes, check cross-user access and direct URL access to private files. Report what changed, what passed, and any unverified host behavior in the pull request. Update requirement traceability and deployment notes when behavior changes.
