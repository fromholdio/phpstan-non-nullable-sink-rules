<?php

declare(strict_types=1);

namespace Fromholdio\PHPStanNonNullableSinkRules\Sink;

use PhpParser\Node;
use PhpParser\Node\Expr\ArrayDimFetch;
use PhpParser\Node\Expr\FuncCall;
use PHPStan\Analyser\Scope;

final class ArrayKeySinkDetector implements SinkDetector
{
    public const IDENTIFIER = 'fio.nonNullableSink.arrayKey';

    public function detect(Node $node, Scope $scope): array
    {
        if ($node instanceof ArrayDimFetch) {
            if ($node->dim === null) {
                return [];
            }

            return [
                new Sink(
                    $node->dim,
                    SinkRequirement::arrayKey(),
                    self::IDENTIFIER,
                    'Value used as an array key is not proven safe; expected %s, %s given.',
                    [
                        'sink' => 'array-key',
                        'introducedIn' => '8.5',
                    ],
                ),
            ];
        }

        if (!$node instanceof FuncCall) {
            return [];
        }

        if (!$node->name instanceof Node\Name) {
            return [];
        }

        if (strtolower($node->name->toString()) !== 'array_key_exists') {
            return [];
        }

        if (!isset($node->getArgs()[0])) {
            return [];
        }

        return [
            new Sink(
                $node->getArgs()[0]->value,
                SinkRequirement::arrayKey(),
                self::IDENTIFIER,
                'Value used as an array key is not proven safe; expected %s, %s given.',
                [
                    'sink' => 'array-key',
                    'introducedIn' => '8.5',
                    'function' => 'array_key_exists',
                    'argument' => 1,
                ],
            ),
        ];
    }
}
