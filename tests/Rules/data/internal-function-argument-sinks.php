<?php

declare(strict_types=1);

function userland_sink_test(mixed $value): void
{
}

function nullable_userland_sink_test(?string $value): void
{
}

function internal_function_argument_sinks(
    mixed $mixedValue,
    ?string $nullableString,
    string $safeString,
    array $safeArray,
): void {
    trim($safeString);
    trim($mixedValue);
    trim($nullableString);
    strlen($mixedValue);
    preg_match('/needle/', $mixedValue);
    array_keys($safeArray);
    array_keys($mixedValue);
    userland_sink_test($mixedValue);
    nullable_userland_sink_test($nullableString);
}
