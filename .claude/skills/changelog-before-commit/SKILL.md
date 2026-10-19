---
name: changelog-before-commit
description: "Before any git commit in this project: add the CHANGELOG.md [Unreleased] entry for user-visible changes, decide whether to release it with simplysmart/tools (vendor/bin/version-release), and confirm docs-after-tests already ran on the diff. Use whenever a commit is imminent."
license: MIT
---

# Changelog before commit

Documentation (code comments, `docs/`, README, CONTRIBUTING) is already handled by `docs-after-tests`. This step only adds what belongs to the commit itself.

1. **Read the diff being committed** — `git diff --staged`, or the working diff if nothing is staged.
2. **Docs check.** If the diff changes code but no doc comment or document that `docs/README.md` maps to the change, run `docs-after-tests` first. Don't redo it when it already ran.
3. **CHANGELOG entry** — only for user-visible changes (feature, requirement, setup step, behavior). Skip refactors, renames, test-only changes, formatting. Add it under `## [Unreleased]` in `### Added` / `Changed` / `Deprecated` / `Removed` / `Fixed` / `Security`, in Polish, describing what the project now does, not which files changed. Prefix a backwards-incompatible entry with `**BREAKING**` — that marker or a `### Removed` entry forces a MAJOR bump.
4. **Release point?** If `[Unreleased]` is non-empty, decide whether this commit is a release. If yes, run `vendor/bin/version-release --root=packages/simplysmart/mainboard` from the main project root (the package has no release script of its own — `simplysmart/tools` is the one shared implementation for the main project and every package) and commit the updated `VERSION` and `CHANGELOG.md` together with the code. A MAJOR bump stops and asks for `--major-confirm` — confirm the change really breaks compatibility before passing it. If several commits will form one release, leave `[Unreleased]` open and say so explicitly.

This skill does not run `git commit`. Stage and commit only when asked, after showing `git status` / `git diff`.
