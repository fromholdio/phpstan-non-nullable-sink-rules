<?php

declare(strict_types=1);

namespace Fromholdio\PHPStanNonNullableSinkRules\Sink;

use PHPStan\Type\MixedType;
use PHPStan\Type\NullType;
use PHPStan\Type\Type;

final class SinkTypeChecker
{
    public function isProvenSafe(Type $actualType, Type $requiredType): bool
    {
        return $requiredType->isSuperTypeOf($actualType)->yes();
    }

    public function isUsefulRequirement(Type $requiredType): bool
    {
        if ($requiredType instanceof MixedType) {
            return false;
        }

        return !$requiredType->isSuperTypeOf(new NullType())->yes();
    }
}
