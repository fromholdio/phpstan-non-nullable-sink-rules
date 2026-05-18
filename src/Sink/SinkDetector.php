<?php

declare(strict_types=1);

namespace Fromholdio\PHPStanNonNullableSinkRules\Sink;

use PhpParser\Node;
use PHPStan\Analyser\Scope;

interface SinkDetector
{
    /**
     * @return list<Sink>
     */
    public function detect(Node $node, Scope $scope): array;
}
