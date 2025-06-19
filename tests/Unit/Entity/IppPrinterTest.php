<?php

declare(strict_types=1);

namespace DR\Ipp\Tests\Unit\Entity;

use DigitalRevolution\AccessorPairConstraint\AccessorPairAsserter;
use DR\Ipp\Entity\IppPrinter;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(IppPrinter::class)]
class IppPrinterTest extends TestCase
{
    use AccessorPairAsserter;

    public function test(): void
    {
        self::assertAccessorPairs(IppPrinter::class);
    }
}
