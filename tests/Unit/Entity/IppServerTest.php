<?php

declare(strict_types=1);

namespace DR\Ipp\Tests\Unit\Entity;

use DigitalRevolution\AccessorPairConstraint\AccessorPairAsserter;
use DR\Ipp\Entity\IppServer;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(IppServer::class)]
class IppServerTest extends TestCase
{
    use AccessorPairAsserter;

    public function test(): void
    {
        self::assertAccessorPairs(IppServer::class);
    }
}
