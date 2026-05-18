<?php

declare(strict_types=1);

namespace Fromholdio\PHPStanNonNullableSinkRules\Sink;

use PHPStan\Type\IntegerType;
use PHPStan\Type\StringType;
use PHPStan\Type\Type;
use PHPStan\Type\TypeCombinator;

final class SinkRequirement
{
    public static function arrayKey(): Type
    {
        return TypeCombinator::union(new IntegerType(), new StringType());
    }
}
