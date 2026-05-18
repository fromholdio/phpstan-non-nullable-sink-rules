<?php

declare(strict_types=1);

namespace Fromholdio\PHPStanNonNullableSinkRules\Sink;

use PhpParser\Node\Expr;
use PHPStan\Type\Type;

final class Sink
{
    /**
     * @param array<string, mixed> $metadata
     */
    public function __construct(
        private Expr $expression,
        private Type $requiredType,
        private string $identifier,
        private string $message,
        private array $metadata = [],
    ) {
    }

    public function expression(): Expr
    {
        return $this->expression;
    }

    public function requiredType(): Type
    {
        return $this->requiredType;
    }

    public function identifier(): string
    {
        return $this->identifier;
    }

    public function message(): string
    {
        return $this->message;
    }

    /**
     * @return array<string, mixed>
     */
    public function metadata(): array
    {
        return $this->metadata;
    }
}
