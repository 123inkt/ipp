<?php

declare(strict_types=1);

namespace DR\Ipp\Tests\Unit\Protocol;

use DigitalRevolution\AccessorPairConstraint\AccessorPairAsserter;
use DR\Ipp\Enum\IppOperationEnum;
use DR\Ipp\Enum\IppOperationTagEnum;
use DR\Ipp\Protocol\IppOperation;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(IppOperation::class)]
class IppOperationTest extends TestCase
{
    use AccessorPairAsserter;

    public function testAccessorPairs(): void
    {
        self::assertAccessorPairs(IppOperation::class);
    }

    public function testToString(): void
    {
        $operation = new IppOperation(IppOperationEnum::PrintJob);
        $expected  = pack('c*', 0x02, 0x00, 0x00, 0x02, 0x00, 0x00, 0x00, 0x01);
        $expected  .= pack('c', IppOperationTagEnum::AttributeEnd->value);

        static::assertSame((string)$operation, $expected);
    }
}
