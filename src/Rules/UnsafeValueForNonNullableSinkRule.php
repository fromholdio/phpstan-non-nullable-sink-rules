<?php

declare(strict_types=1);

namespace Fromholdio\PHPStanNonNullableSinkRules\Rules;

use Fromholdio\PHPStanNonNullableSinkRules\Error\RuleErrorFactory;
use Fromholdio\PHPStanNonNullableSinkRules\Sink\ArrayKeySinkDetector;
use Fromholdio\PHPStanNonNullableSinkRules\Sink\InternalFunctionArgumentSinkDetector;
use Fromholdio\PHPStanNonNullableSinkRules\Sink\SinkTypeChecker;
use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;

/**
 * @implements Rule<Node>
 */
final class UnsafeValueForNonNullableSinkRule implements Rule
{
    public function __construct(
        private ArrayKeySinkDetector $arrayKeySinkDetector,
        private InternalFunctionArgumentSinkDetector $internalFunctionArgumentSinkDetector,
        private SinkTypeChecker $sinkTypeChecker,
        private RuleErrorFactory $ruleErrorFactory,
    ) {
    }

    public function getNodeType(): string
    {
        return Node::class;
    }

    public function processNode(Node $node, Scope $scope): array
    {
        $errors = [];

        foreach ([
            $this->arrayKeySinkDetector,
            $this->internalFunctionArgumentSinkDetector,
        ] as $detector) {
            foreach ($detector->detect($node, $scope) as $sink) {
                if (!$this->sinkTypeChecker->isUsefulRequirement($sink->requiredType())) {
                    continue;
                }

                $actualType = $scope->getType($sink->expression());
                if ($this->sinkTypeChecker->isProvenSafe($actualType, $sink->requiredType())) {
                    continue;
                }

                $errors[] = $this->ruleErrorFactory->create($sink, $actualType);
            }
        }

        return $errors;
    }
}
