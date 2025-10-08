<?php

declare(strict_types=1);

namespace DR\Ipp\Tests\Unit\Protocol;

use DR\Ipp\Enum\IppOperationEnum;
use DR\Ipp\Enum\IppOperationTagEnum;
use DR\Ipp\Enum\IppTypeEnum;
use DR\Ipp\Protocol\IppAttribute;
use DR\Ipp\Protocol\IppEncoder;
use DR\Ipp\Protocol\IppOperation;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use RuntimeException;

#[CoversClass(IppEncoder::class)]
class IppEncoderTest extends TestCase
{
    public function testEncodeOperation(): void
    {
        $version   = 2;
        $value     = 1;
        $requestId = 1;

        $operation = new IppOperation(IppOperationEnum::PrintJob);
        $operation->addOperationAttribute(new IppAttribute(IppTypeEnum::Int, 'UT', $value));
        $operation->addOperationAttribute(new IppAttribute(IppTypeEnum::Int, 'UT', $value));
        $operation->addPrinterAttribute(new IppAttribute(IppTypeEnum::Int, 'UT', $value));
        $operation->addPrinterAttribute(new IppAttribute(IppTypeEnum::Int, 'UT', $value));
        $operation->addJobAttribute(new IppAttribute(IppTypeEnum::Int, 'UT', $value));
        $operation->addJobAttribute(new IppAttribute(IppTypeEnum::Int, 'UT', $value));
        $operation->setFileData('AAA');

        $attributeBinary = pack('c*', IppTypeEnum::Int->value, 0x00, 0x02) . 'UT' . pack('c*', 0x00, 0x04, 0x00, 0x00, 0x00, $value);

        $expected = pack('c*', $version, 0x00, 0x00, IppOperationEnum::PrintJob->value, 0x00, 0x00, 0x00, $requestId);
        $expected .= pack('c', IppOperationTagEnum::OperationAttributeStart->value) . $attributeBinary . $attributeBinary;
        $expected .= pack('c', IppOperationTagEnum::PrinterAttributeStart->value) . $attributeBinary . $attributeBinary;
        $expected .= pack('c', IppOperationTagEnum::JobAttributeStart->value) . $attributeBinary . $attributeBinary;
        $expected .= pack('c', IppOperationTagEnum::AttributeEnd->value);
        $expected .= 'AAA';

        static::assertSame($expected, IppEncoder::encodeOperation($operation));
    }

    public function testEncodeAttributeInt(): void
    {
        $attr     = new IppAttribute(IppTypeEnum::Int, 'unit', 4123);
        $expected = pack('c*', IppTypeEnum::Int->value, 0x00, 0x04) . 'unit' . pack('c*', 0x00, 0x04, 0x00, 0x00, 0x10, 0x1B);
        static::assertSame($expected, IppEncoder::encodeAttribute($attr));
    }

    public function testEncodeAttributeBool(): void
    {
        $attr     = new IppAttribute(IppTypeEnum::Bool, 'test', true);
        $expected = pack('c*', IppTypeEnum::Bool->value, 0x00, 0x04) . 'test' . pack('c*', 0x00, 0x01, 0x01);
        static::assertSame($expected, IppEncoder::encodeAttribute($attr));
    }

    public function testEncodeAttributeDefault(): void
    {
        $attr     = new IppAttribute(IppTypeEnum::Keyword, 'foo', 'bar');
        $expected = pack('c*', IppTypeEnum::Keyword->value, 0x00, 0x03) . 'foo' . pack('c*', 0x00, 0x03) . 'bar';
        static::assertSame($expected, IppEncoder::encodeAttribute($attr));
    }

    public function testEncodeMultiValue(): void
    {
        $attr     = new IppAttribute(IppTypeEnum::Int, 'test', [1, 2, 3]);
        $expected = pack('c*', IppTypeEnum::Int->value, 0x00, 0x04) . 'test' . pack('c*', 0x00, 0x04, 0x00, 0x00, 0x00, 0x01);
        $expected .= pack('c', IppTypeEnum::Int->value) . pack('n', 0) . pack('c*', 0x00, 0x04, 0x00, 0x00, 0x00, 0x02);
        $expected .= pack('c', IppTypeEnum::Int->value) . pack('n', 0) . pack('c*', 0x00, 0x04, 0x00, 0x00, 0x00, 0x03);
        static::assertSame($expected, IppEncoder::encodeAttribute($attr));
    }

    public function testExceptionOnBadBool(): void
    {
        $this->expectException(RuntimeException::class);

        $attr = new IppAttribute(IppTypeEnum::Bool, 'unit', 'foo');
        IppEncoder::encodeAttribute($attr);
    }

    public function testExceptionOnBadString(): void
    {
        $this->expectException(RuntimeException::class);

        $attr = new IppAttribute(IppTypeEnum::NameWithoutLang, 'unit', 123);
        IppEncoder::encodeAttribute($attr);
    }
}
