<?php

declare(strict_types=1);

namespace DR\Ipp\Tests\Unit\Protocol;

use DigitalRevolution\AccessorPairConstraint\AccessorPairAsserter;
use DR\Ipp\Protocol\IppCollection;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(IppCollection::class)]
class IppCollectionTest extends TestCase
{
    use AccessorPairAsserter;

    public function test(): void
    {
        self::assertAccessorPairs(IppCollection::class);
    }
}

