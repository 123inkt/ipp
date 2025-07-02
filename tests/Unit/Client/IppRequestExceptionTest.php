<?php

declare(strict_types=1);

namespace DR\Ipp\Tests\Unit\Client;

use DigitalRevolution\AccessorPairConstraint\AccessorPairAsserter;
use DR\Ipp\Client\IppRequestException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(IppRequestException::class)]
class IppRequestExceptionTest extends TestCase
{
    use AccessorPairAsserter;

    public function test(): void
    {
        self::assertAccessorPairs(IppRequestException::class);
    }
}
