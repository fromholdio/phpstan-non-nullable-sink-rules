<?php

declare(strict_types=1);

function array_key_sinks(
    int $safeInt,
    string $safeString,
    mixed $mixedKey,
    ?string $nullableString,
    string|false $stringOrFalse,
    object $objectKey,
): void {
    $array = [];
    $array[$safeInt] = true;
    $array[$safeString] = true;
    $array[] = true;

    $array[$mixedKey] = true;
    $array[$nullableString] = true;
    $array[$stringOrFalse] = true;
    $array[null] = true;
    $array[false] = true;
    $array[true] = true;
    $array[4.5] = true;
    $array[[]] = true;
    $array[$objectKey] = true;

    isset($array[$mixedKey]);
    array_key_exists($mixedKey, $array);
    array_key_exists($safeString, $array);
}
