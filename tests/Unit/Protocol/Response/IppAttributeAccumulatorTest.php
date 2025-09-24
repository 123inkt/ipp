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

        $store = new IppAttributeAccumulator();
        static::assertCount(0, $store->getAttributes());

        $store->addAttribute($attr);
        $store->flush();

        static::assertCount(1, $store->getAttributes());
    }

    public function testStoreAttributeDuplicate(): void
    {
        $attr = new IppAttribute(IppTypeEnum::Int, 'foo', 0);

        $store = new IppAttributeAccumulator();
        static::assertCount(0, $store->getAttributes());

        $store->addAttribute($attr);
        $store->addAttribute($attr);
        $store->flush();

        static::assertCount(2, $store->getAttributes());
        static::assertCount(1, $store->getNormalizedAttributes());
    }

    public function testStoreAttributeDuplicateArray(): void
    {
        $attr = new IppAttribute(IppTypeEnum::Keyword, 'foo', ['unit', 'test']);

        $store = new IppAttributeAccumulator();
        static::assertCount(0, $store->getAttributes());

        $store->addAttribute($attr);
        $store->addAttribute($attr);
        $store->flush();

        static::assertCount(2, $store->getAttributes());
        $normalized = $store->getNormalizedAttributes();
        static::assertCount(1, $normalized);
        static::assertSame(['unit', 'test', 'unit', 'test'], $normalized['foo']->getValue());
    }

    public function testStoreAttributeNull(): void
    {
        $store = new IppAttributeAccumulator();
        static::assertCount(0, $store->getAttributes());

        $store->addAttribute(null);
        $store->flush();

        static::assertCount(0, $store->getAttributes());
    }
}
