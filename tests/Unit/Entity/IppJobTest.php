<?php

declare(strict_types=1);

namespace DR\Ipp\Tests\Unit\Entity;

use DigitalRevolution\AccessorPairConstraint\AccessorPairAsserter;
use DR\Ipp\Entity\IppJob;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(IppJob::class)]
class IppJobTest extends TestCase
{
    use AccessorPairAsserter;

    public function testAccessorPairs(): void
    {
        self::assertAccessorPairs(IppJob::class);
    }
}
