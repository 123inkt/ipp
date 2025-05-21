<?php

declare(strict_types=1);

namespace DR\Ipp\Tests\Unit\Protocol;

use DigitalRevolution\AccessorPairConstraint\AccessorPairAsserter;
use DR\Ipp\Enum\IppOperationEnum;
use DR\Ipp\Enum\IppOperationTagEnum;
use DR\Ipp\Enum\IppTypeEnum;
use DR\Ipp\Protocol\IppAttribute;
use DR\Ipp\Protocol\IppOperation;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(IppOperation::class)]
class IppOperationTest extends TestCase
{
    use AccessorPairAsserter;

    public function test(): void
    {
        self::assertAccessorPairs(IppOperation::class);
    }

    public function testToString(): void
    {
        $operation = new IppOperation(IppOperationEnum::PrintJob);
        $operation->addOperationAttribute(new IppAttribute(IppTypeEnum::Int, 'UT', 1));
        $operation->addOperationAttribute(new IppAttribute(IppTypeEnum::Int, 'UT', 1));
        $operation->addPrinterAttribute(new IppAttribute(IppTypeEnum::Int, 'UT', 1));
        $operation->addPrinterAttribute(new IppAttribute(IppTypeEnum::Int, 'UT', 1));
        $operation->addJobAttribute(new IppAttribute(IppTypeEnum::Int, 'UT', 1));
        $operation->addJobAttribute(new IppAttribute(IppTypeEnum::Int, 'UT', 1));
        $operation->setFileData('AAA');

        $attributeBinary = pack('c*', 0x21, 0x00, 0x02) . 'UT' . pack('c*', 0x00, 0x04, 0x00, 0x00, 0x00, 0x01);

        $expected = pack('c*', 0x02, 0x00, 0x00, 0x02, 0x00, 0x00, 0x00, 0x01);
        $expected .= pack('c', IppOperationTagEnum::OperationAttributeStart->value) . $attributeBinary . $attributeBinary;
        $expected .= pack('c', IppOperationTagEnum::PrinterAttributeStart->value) . $attributeBinary . $attributeBinary;
        $expected .= pack('c', IppOperationTagEnum::JobAttributeStart->value) . $attributeBinary . $attributeBinary;
        $expected .= pack('c', IppOperationTagEnum::AttributeEnd->value);
        $expected .= 'AAA';
        static::assertSame((string)$operation, $expected);
    }
}
