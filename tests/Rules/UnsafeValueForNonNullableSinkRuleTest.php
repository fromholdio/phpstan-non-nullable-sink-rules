<?php

declare(strict_types=1);

namespace Fromholdio\PHPStanNonNullableSinkRules\Tests\Rules;

use Fromholdio\PHPStanNonNullableSinkRules\Error\RuleErrorFactory;
use Fromholdio\PHPStanNonNullableSinkRules\Rules\UnsafeValueForNonNullableSinkRule;
use Fromholdio\PHPStanNonNullableSinkRules\Sink\ArrayKeySinkDetector;
use Fromholdio\PHPStanNonNullableSinkRules\Sink\InternalFunctionArgumentSinkDetector;
use Fromholdio\PHPStanNonNullableSinkRules\Sink\SinkTypeChecker;
use PHPStan\Analyser\Error;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

/**
 * @extends RuleTestCase<UnsafeValueForNonNullableSinkRule>
 */
final class UnsafeValueForNonNullableSinkRuleTest extends RuleTestCase
{
    protected function getRule(): Rule
    {
        return new UnsafeValueForNonNullableSinkRule(
            new ArrayKeySinkDetector(),
            new InternalFunctionArgumentSinkDetector($this->createReflectionProvider()),
            new SinkTypeChecker(),
            new RuleErrorFactory(),
        );
    }

    public function testArrayKeySinks(): void
    {
        $errors = $this->analyseForIdentifiers(__DIR__ . '/data/array-key-sinks.php');

        self::assertSame([
            ['fio.nonNullableSink.arrayKey', 18, 'Value used as an array key is not proven safe; expected int|string, mixed given.'],
            ['fio.nonNullableSink.arrayKey', 19, 'Value used as an array key is not proven safe; expected int|string, string|null given.'],
            ['fio.nonNullableSink.arrayKey', 20, 'Value used as an array key is not proven safe; expected int|string, string|false given.'],
            ['fio.nonNullableSink.arrayKey', 21, 'Value used as an array key is not proven safe; expected int|string, null given.'],
            ['fio.nonNullableSink.arrayKey', 22, 'Value used as an array key is not proven safe; expected int|string, false given.'],
            ['fio.nonNullableSink.arrayKey', 23, 'Value used as an array key is not proven safe; expected int|string, true given.'],
            ['fio.nonNullableSink.arrayKey', 24, 'Value used as an array key is not proven safe; expected int|string, float given.'],
            ['fio.nonNullableSink.arrayKey', 25, 'Value used as an array key is not proven safe; expected int|string, array given.'],
            ['fio.nonNullableSink.arrayKey', 26, 'Value used as an array key is not proven safe; expected int|string, object given.'],
            ['fio.nonNullableSink.arrayKey', 28, 'Value used as an array key is not proven safe; expected int|string, mixed given.'],
            ['fio.nonNullableSink.arrayKey', 29, 'Value used as an array key is not proven safe; expected int|string, mixed given.'],
        ], $errors);
    }

    public function testInternalFunctionArgumentSinks(): void
    {
        $errors = $this->analyseForIdentifiers(__DIR__ . '/data/internal-function-argument-sinks.php');

        self::assertSame([
            ['fio.nonNullableSink.internalFunctionArgument', 20, 'Argument #1 $string passed to internal function trim() is not proven safe; expected string, mixed given.'],
            ['fio.nonNullableSink.internalFunctionArgument', 21, 'Argument #1 $string passed to internal function trim() is not proven safe; expected string, string|null given.'],
            ['fio.nonNullableSink.internalFunctionArgument', 22, 'Argument #1 $string passed to internal function strlen() is not proven safe; expected string, mixed given.'],
            ['fio.nonNullableSink.internalFunctionArgument', 23, 'Argument #2 $subject passed to internal function preg_match() is not proven safe; expected string, mixed given.'],
            ['fio.nonNullableSink.internalFunctionArgument', 25, 'Argument #1 $array passed to internal function array_keys() is not proven safe; expected array, mixed given.'],
        ], $errors);
    }

    /**
     * @return list<array{string|null, int|null, string}>
     */
    private function analyseForIdentifiers(string $file): array
    {
        $errors = $this->gatherAnalyserErrors([$file]);
        usort($errors, static function (Error $left, Error $right): int {
            return [$left->getLine(), $left->getIdentifier(), $left->getMessage()]
                <=> [$right->getLine(), $right->getIdentifier(), $right->getMessage()];
        });

        return array_map(
            static fn (Error $error): array => [
                $error->getIdentifier(),
                $error->getLine(),
                $error->getMessage(),
            ],
            $errors,
        );
    }
}
