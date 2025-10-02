<?php

declare(strict_types=1);

namespace DR\Ipp\Tests\Unit\Protocol;

use DigitalRevolution\AccessorPairConstraint\AccessorPairAsserter;
use DR\Ipp\Protocol\IppResolution;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(IppResolution::class)]
class IppResolutionTest extends TestCase
{
    use AccessorPairAsserter;

    public function testAccessorPairs(): void
    {
        self::assertAccessorPairs(IppResolution::class);
    }
}
