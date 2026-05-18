<?php

declare(strict_types=1);

namespace Fromholdio\PHPStanNonNullableSinkRules\Sink;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\FuncCall;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ParameterReflection;
use PHPStan\Reflection\ParametersAcceptorSelector;
use PHPStan\Reflection\ReflectionProvider;

final class InternalFunctionArgumentSinkDetector implements SinkDetector
{
    public const IDENTIFIER = 'fio.nonNullableSink.internalFunctionArgument';

    public function __construct(
        private ReflectionProvider $reflectionProvider,
    ) {
    }

    public function detect(Node $node, Scope $scope): array
    {
        if (!$node instanceof FuncCall) {
            return [];
        }

        if (!$node->name instanceof Node\Name) {
            return [];
        }

        $functionName = strtolower($node->name->toString());
        if ($functionName === 'array_key_exists') {
            return [];
        }

        if (!$this->reflectionProvider->hasFunction($node->name, $scope)) {
            return [];
        }

        $function = $this->reflectionProvider->getFunction($node->name, $scope);
        if (!$function->isBuiltin()) {
            return [];
        }

        $variant = ParametersAcceptorSelector::selectFromArgs(
            $scope,
            $node->getArgs(),
            $function->getVariants(),
            $function->getNamedArgumentsVariants(),
        );

        $sinks = [];
        foreach ($node->getArgs() as $index => $arg) {
            $parameter = $this->parameterForArg($arg, $index, $variant->getParameters());
            if ($parameter === null) {
                continue;
            }

            $sinks[] = new Sink(
                $arg->value,
                $parameter->getType(),
                self::IDENTIFIER,
                sprintf(
                    'Argument #%d $%s passed to internal function %s() is not proven safe; expected %%s, %%s given.',
                    $index + 1,
                    $parameter->getName(),
                    $function->getName(),
                ),
                [
                    'sink' => 'internal-function-argument',
                    'function' => $function->getName(),
                    'argument' => $index + 1,
                    'parameter' => $parameter->getName(),
                ],
            );
        }

        return $sinks;
    }

    /**
     * @param list<ParameterReflection> $parameters
     */
    private function parameterForArg(Arg $arg, int $index, array $parameters): ?ParameterReflection
    {
        if ($arg->name !== null) {
            $name = $arg->name->toString();
            foreach ($parameters as $parameter) {
                if ($parameter->getName() === $name) {
                    return $parameter;
                }
            }

            return null;
        }

        if (isset($parameters[$index])) {
            return $parameters[$index];
        }

        if ($parameters === []) {
            return null;
        }

        $lastParameter = $parameters[count($parameters) - 1];
        if ($lastParameter->isVariadic()) {
            return $lastParameter;
        }

        return null;
    }
}
