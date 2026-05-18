<?php

declare(strict_types=1);

namespace Fromholdio\PHPStanNonNullableSinkRules\Error;

use Fromholdio\PHPStanNonNullableSinkRules\Sink\Sink;
use PHPStan\Rules\IdentifierRuleError;
use PHPStan\Rules\RuleErrorBuilder;
use PHPStan\Type\Type;
use PHPStan\Type\VerbosityLevel;

final class RuleErrorFactory
{
    public function create(Sink $sink, Type $actualType): IdentifierRuleError
    {
        return RuleErrorBuilder::message(sprintf(
            $sink->message(),
            $sink->requiredType()->describe(VerbosityLevel::typeOnly()),
            $actualType->describe(VerbosityLevel::typeOnly()),
        ))
            ->identifier($sink->identifier())
            ->metadata($sink->metadata())
            ->build();
    }
}
