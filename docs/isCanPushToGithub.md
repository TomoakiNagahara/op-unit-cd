# `isCanPushToGithub()`

## Overview

`isCanPushToGithub()` controls whether a branch may be pushed to GitHub.

File:

`asset/unit/cd/function/isCanPushToGithub.php`

This function is part of the CD-side safety rules of the ONEPIECE Framework.

## Purpose

The purpose of this function is to prevent unintended code from being pushed to GitHub.

In particular, it is meant to prevent pushing:

- work-in-progress code
- experimental code
- research branches
- branches that are not intended to enter shared remote history

This helps reduce:

- history pollution
- accidental sharing of unfinished work
- unnecessary conflicts caused by unstable branches

## What the Function Checks

The function receives:

- the remote name
- the branch name

It then decides whether that branch may be pushed to GitHub.

## Allowed Cases

The function returns `true` in the following cases.

### 1. The branch is the application branch

If the branch name is equal to `_OP_APP_BRANCH_`, the push is allowed.

Example:

```php
define('_OP_APP_BRANCH_', 2030);
```

In that case, branch `2030` is allowed.

### 2. The branch name is year-based

If the branch name matches a year-like format such as `2025`, `2026`, `2030`, or `2031`, the push is allowed.

The current implementation uses this pattern:

```text
^20[2,3]\d$
```

### 3. The push target is not GitHub

If the remote URL is not a GitHub URL, this GitHub-specific restriction is not applied.

In that case, the function allows the push.

The intention behind this behavior is operational:

- if the repository is not on GitHub, it is treated as belonging to a private area
- if it is in a private area, accidental publication is considered less problematic than accidental publication to GitHub

This is a practical trust decision in the framework design.

### 4. The branch is explicitly allowed by CD configuration

The function loads CD configuration and checks whether the branch name is listed in the allowed branch list.

Default config source:

`asset/unit/cd/config.php`

Override location mentioned by the function:

`asset/config/cd.php`

In practical operation, if a branch is blocked and the developer wants to allow it, the normal pattern is:

1. copy `asset/unit/cd/config.php`
2. create `asset/config/cd.php`
3. add the desired branch name to the `branch` list

From the framework design point of view:

- `asset/unit/cd/config.php` should be treated as the default config
- `asset/config/cd.php` should be treated as the environment-specific config

Because of that, directly editing `asset/unit/cd/config.php` is not the recommended operational pattern.

The preferred pattern is to keep defaults in the unit-side config and place local or environment-specific changes in `asset/config/cd.php`.

## Confirmed Implementation Detail

This behavior is directly supported by the current implementation.

The actual path is:

1. `isCanPushToGithub()` calls `OP()->Config('cd')`
2. `Config.class.php` loads `asset/unit/cd/config.php`
3. `Config.class.php` then loads `asset/config/cd.php` if it exists
4. the later layer overwrites the earlier layer with `array_replace_recursive()`
5. `isCanPushToGithub()` checks `$_config['branch']`

So, if `asset/config/cd.php` adds a branch name to the `branch` list, that branch becomes allowed by the current implementation.

Strictly speaking, copying the whole default file is not technically required.

It is enough to provide the needed override structure in `asset/config/cd.php`.

## Blocked Case

If the push target is GitHub and the branch does not match any allowed rule, the function blocks the push.

When blocked, it prints:

- current working directory
- remote name
- branch name
- remote URL
- guidance about allowed branch configuration

## Design Intention

This function exists because not every branch should be treated as a delivery branch.

The ONEPIECE Framework separates:

- branches intended for shared release or integration flow
- branches used only for local development, experiments, or investigation

By restricting GitHub pushes by branch name, the framework tries to keep the shared history clean and predictable.

In that sense, the ONEPIECE Framework can also be seen as partially vendor-locked to GitHub, because the stricter branch publication control is applied specifically to GitHub pushes.

## Important Scope

This function does not enforce every push rule by itself.

It is only responsible for branch-based GitHub push restriction.

Other rules are handled elsewhere, such as:

- commit message prefix checks
- CI-passed commit checks
- hook-based local validation

## Checked Current Implementation Boundary

The current implementation boundary is explicit:

- `asset/unit/cd/function/isCanPushToGithub.php`
  handles branch-based GitHub push restriction
- `asset/init/hooks/pre-push-prefix.php`
  handles commit message prefix validation for pushed commits
- `asset/init/hooks/pre-push.sh`
  runs the CI script first, and then runs `pre-push-prefix.php`

That means the current As-Is implementation does **not** put the commit-message-prefix push block inside `op-unit-cd`.

Instead:

1. CI-related enforcement runs
2. prefix validation runs afterward
3. `op-unit-cd` remains responsible for branch-based publication control

So, in the current design, prefix blocking is part of the overall push policy, but not part of the internal responsibility of `isCanPushToGithub()`.

## [DOC-GAP] Scattered Prefix Enforcement

Historically, prefix-based push blocking was added in a situational and incremental way.

Because of that, the current implementation is scattered across multiple places instead of being concentrated cleanly in `op-unit-cd`.

This means the current layout should be understood as historical As-Is, not as the ideal final responsibility layout.

## [DOC-FUTURE] Planned Concentration in `op-unit-cd`

The intended long-term direction is to concentrate push-policy enforcement, including prefix-related blocking, into `op-unit-cd`.

In other words:

- the current scattered implementation is tolerated for compatibility and history
- the preferred future design is a more centralized CD-side responsibility model

More broadly, the ideal To-Be is that CD-related responsibilities should be concentrated in `op-unit-cd`.

That includes:

- branch-based publication control
- push-policy enforcement
- CD-side delivery decisions

## Summary

`isCanPushToGithub()` is a branch gate for GitHub pushes.

It allows pushes when:

- the branch matches `_OP_APP_BRANCH_`
- the branch is a year-based branch
- the branch is explicitly allowed by CD configuration
- the remote is not GitHub

It blocks other GitHub pushes in order to prevent unintended publication of unstable or incomplete work.
