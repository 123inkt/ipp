<?php

declare(strict_types=1);

namespace DR\Ipp\Tests\Unit\Protocol\Response;

use DR\Ipp\Enum\IppTypeEnum;
use DR\Ipp\Protocol\IppAttribute;
use DR\Ipp\Protocol\Response\IppAttributeAccumulator;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(IppAttributeAccumulator::class)]
class IppAttributeAccumulatorTest extends TestCase
{
    public function testStoreAttribute(): void
    {
        $attr = $this->createMock(IppAttribute::class);

        $accumulator = new IppAttributeAccumulator();
        static::assertCount(0, $accumulator->getAttributes());

        $accumulator->addAttribute($attr);
        $accumulator->flush();

        static::assertCount(1, $accumulator->getAttributes());
    }

    public function testStoreAttributeDuplicate(): void
    {
        $attr = new IppAttribute(IppTypeEnum::Int, 'foo', 0);

        $accumulator = new IppAttributeAccumulator();
        static::assertCount(0, $accumulator->getAttributes());

        $accumulator->addAttribute($attr);
        $accumulator->addAttribute($attr);
        $accumulator->flush();

        static::assertCount(2, $accumulator->getAttributes());
        static::assertCount(1, $accumulator->getNormalizedAttributes());
    }

    public function testStoreAttributeDuplicateArray(): void
    {
        $attr = new IppAttribute(IppTypeEnum::Keyword, 'foo', ['unit', 'test']);

        $accumulator = new IppAttributeAccumulator();
        static::assertCount(0, $accumulator->getAttributes());

        $accumulator->addAttribute($attr);
        $accumulator->addAttribute($attr);
        $accumulator->flush();

        static::assertCount(2, $accumulator->getAttributes());
        $normalized = $accumulator->getNormalizedAttributes();
        static::assertCount(1, $normalized);
        static::assertSame(['unit', 'test', 'unit', 'test'], $normalized['foo']->getValue());
    }

    public function testStoreAttributeNull(): void
    {
        $accumulator = new IppAttributeAccumulator();
        static::assertCount(0, $accumulator->getAttributes());

        $accumulator->addAttribute(null);
        $accumulator->flush();

        static::assertCount(0, $accumulator->getAttributes());
    }
}
