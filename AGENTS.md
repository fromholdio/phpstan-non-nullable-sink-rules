# Agent Instructions

This repository is a standalone PHPStan extension package. Keep changes focused on the extension itself; do not add consuming-project integrations or project-specific release policy here.

## Package Conventions

- Composer package: `fromholdio/phpstan-non-nullable-sink-rules`
- Namespace: `Fromholdio\PHPStanNonNullableSinkRules`
- License: `BSD-3-Clause`, copyright `Luke Fromhold`
- PHPStan extension registration lives in `extension.neon`.
- Keep `composer.json` configured as a PHPStan extension with `"type": "phpstan-extension"` and `extra.phpstan.includes`.
- Use stable PHPStan identifiers under the `fio` prefix, for example:
  - `fio.nonNullableSink.arrayKey`
  - `fio.nonNullableSink.internalFunctionArgument`

## Development

- Use Composer for dependency changes.
- Write behavior tests with PHPStan's `RuleTestCase` under `tests/Rules/`.
- Keep fixture code under `tests/Rules/data/`.
- Use `RuleErrorBuilder` with identifiers for reported errors.
- Avoid source-specific modelling such as WordPress helpers, `filter_input()`, or project-specific APIs unless the package scope is deliberately expanded.

## Verification

Run these before claiming completion:

```bash
composer validate --strict
composer test
composer analyse
vendor/bin/phpstan analyse --error-format=raw
```

For packaging or installation changes, also verify path-repository usage from a separate throwaway Composer project.
