# PHPStan Non-Nullable Sink Rules

PHPStan rules for values that are not proven safe at non-nullable or narrowly typed PHP-owned sinks.

The guiding idea is intentionally narrow:

```text
Do not treat mixed as bad everywhere.
At known non-nullable/narrow PHP-owned sinks, mixed is not good enough.
```

This package is useful for dynamic PHP codebases where enabling strict mixed checks globally would produce too much noise, but where certain PHP-owned operations should still require a proven-safe value.

## Installation

Install the package as a dev dependency:

```bash
composer require --dev fromholdio/phpstan-non-nullable-sink-rules
```

If your project uses `phpstan/extension-installer`, the extension is loaded automatically:

```bash
composer require --dev phpstan/extension-installer
composer require --dev fromholdio/phpstan-non-nullable-sink-rules
```

Composer 2.2+ may ask whether `phpstan/extension-installer` is allowed to run as a plugin. Answer yes if you want automatic PHPStan extension registration.

Without `phpstan/extension-installer`, include the extension manually in `phpstan.neon`:

```neon
includes:
    - vendor/fromholdio/phpstan-non-nullable-sink-rules/extension.neon
```

Do not use both automatic installation and a manual include for this package in the same project; PHPStan will report that the extension file has been included more than once.

## Local Path Usage

Before the package is published on Packagist, or when testing local changes in another project, add a Composer path repository:

```json
{
    "repositories": [
        {
            "type": "path",
            "url": "../phpstan-non-nullable-sink-rules",
            "options": {
                "symlink": true
            }
        }
    ],
    "require-dev": {
        "fromholdio/phpstan-non-nullable-sink-rules": "*"
    }
}
```

Then run:

```bash
composer update fromholdio/phpstan-non-nullable-sink-rules
vendor/bin/phpstan analyse
```

## Rules

### `UnsafeValueForNonNullableSinkRule`

Reports values used at known non-nullable or narrowly typed PHP-owned sinks when PHPStan cannot prove the value satisfies the sink requirement.

The rule currently emits these identifiers:

```text
fio.nonNullableSink.arrayKey
fio.nonNullableSink.internalFunctionArgument
```

### Array Key Sinks

Identifier:

```text
fio.nonNullableSink.arrayKey
```

This sink family covers values used as array keys:

```php
$items[$key] = 'value';
$value = $items[$key];
isset($items[$key]);
array_key_exists($key, $items);
```

The required key type is `int|string`. The rule reports keys that are not proven to satisfy that requirement, including `mixed`, nullable values, `false`, `bool`, `float`, `array`, and `object`.

Example:

```php
function example(mixed $key, array $items): void
{
    $items[$key] = 'value';
}
```

PHPStan reports:

```text
Value used as an array key is not proven safe; expected int|string, mixed given.
```

### Internal Function Argument Sinks

Identifier:

```text
fio.nonNullableSink.internalFunctionArgument
```

This sink family covers calls to PHP built-in/internal functions where PHPStan knows the parameter contract and the argument is not proven compatible.

Example:

```php
function example(mixed $value, ?string $nullable): void
{
    trim($value);
    strlen($nullable);
}
```

PHPStan reports:

```text
Argument #1 $string passed to internal function trim() is not proven safe; expected string, mixed given.
Argument #1 $string passed to internal function strlen() is not proven safe; expected string, string|null given.
```

The rule does not treat arbitrary userland functions as sinks solely because they receive `mixed`.

## Scope Boundaries

This package models dangerous use-sites, not value sources.

It does not need special knowledge of where a value came from:

```php
filter_input(...)
get_option(...)
$_GET
json_decode(...)
```

If one of those values reaches a supported sink and PHPStan cannot prove it is safe, the rule reports the sink.

The package intentionally does not include:

- WordPress-aware source modelling.
- Special handling for `filter_input()`.
- Hook-aware analysis for `do_action()` or `apply_filters()`.
- Project-specific severity or release-gate policy.

Those decisions belong in consuming projects or higher-level workflows.

## Development

Install dependencies:

```bash
composer install
```

Run tests:

```bash
composer test
```

Run PHPStan against the package:

```bash
composer analyse
```

Run Composer validation:

```bash
composer validate --strict
```

## Requirements

- PHP `^8.1`
- PHPStan `^2.1.39`

## License

MIT.
