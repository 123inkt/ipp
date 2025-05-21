<?php

declare(strict_types=1);

namespace DR\Ipp\Tests\Unit\Protocol;

use DigitalRevolution\AccessorPairConstraint\AccessorPairAsserter;
use DR\Ipp\Enum\IppTypeEnum;
use DR\Ipp\Protocol\IppAttribute;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use RuntimeException;

#[CoversClass(IppAttribute::class)]
class IppAttributeTest extends TestCase
{
    use AccessorPairAsserter;

    public function test(): void
    {
        self::assertAccessorPairs(IppAttribute::class);
    }

    public function testExceptionOnBadBool(): void
    {
        $this->expectException(RuntimeException::class);

        $attr = new IppAttribute(IppTypeEnum::Bool, 'unit', 'foo');
        $attr->__toString();
    }

    public function testExceptionOnBadString(): void
    {
        $this->expectException(RuntimeException::class);

        $attr = new IppAttribute(IppTypeEnum::NameWithoutLang, 'unit', 123);
        $attr->__toString();
    }

    public function testToString(): void
    {
        $attr   = new IppAttribute(IppTypeEnum::Int, 'unit', 4123);
        $binary = (string)$attr;

        static::assertSame($binary, pack('c*', 0x21, 0x00, 0x04) . 'unit' . pack('c*', 0x00, 0x04, 0x00, 0x00, 0x10, 0x1B));

        $attr   = new IppAttribute(IppTypeEnum::Bool, 'test', true);
        $binary = (string)$attr;

        static::assertSame($binary, pack('c*', 0x22, 0x00, 0x04) . 'test' . pack('c*', 0x00, 0x01, 0x01));

        $attr   = new IppAttribute(IppTypeEnum::Keyword, 'foo', 'bar');
        $binary = (string)$attr;

        static::assertSame($binary, pack('c*', 0x44, 0x00, 0x03) . 'foo' . pack('c*', 0x00, 0x03) . 'bar');
    }
}
