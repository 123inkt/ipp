<?php

declare(strict_types=1);

namespace DR\Ipp\Tests\Unit\Protocol;

use DigitalRevolution\AccessorPairConstraint\AccessorPairAsserter;
use DR\Ipp\Enum\IppTypeEnum;
use DR\Ipp\Protocol\IppAttribute;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(IppAttribute::class)]
class IppAttributeTest extends TestCase
{
    use AccessorPairAsserter;

    public function testAccessorPairs(): void
    {
        self::assertAccessorPairs(IppAttribute::class);
    }

    public function testToString(): void
    {
        $attr   = new IppAttribute(IppTypeEnum::Int, 'unit', 4123);

        static::assertSame(pack('c*', 0x21, 0x00, 0x04) . 'unit' . pack('c*', 0x00, 0x04, 0x00, 0x00, 0x10, 0x1B), (string)$attr);
    }

    public function testAppendValue(): void
    {
        $attr = new IppAttribute(IppTypeEnum::Keyword, 'test', 'foo');
        $attr->appendValue('bar');

        static::assertSame(['foo', 'bar'], $attr->getValue());
    }

    public function testAppendValueArray(): void
    {
        $attr = new IppAttribute(IppTypeEnum::Keyword, 'test', ['foo']);
        $attr->appendValue(['bar']);

        static::assertSame(['foo', 'bar'], $attr->getValue());
    }
}
