<?php

declare(strict_types=1);

namespace DR\Ipp\Tests\Unit\Protocol\Response;

use DR\Ipp\Protocol\IppAttribute;
use DR\Ipp\Protocol\Response\IppAttributeAccumulator;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(IppAttributeAccumulator::class)]
class IppAttributeAccumulatorTest extends TestCase
{
    public function testAddAttribute(): void
    {
        $attr = $this->createMock(IppAttribute::class);

        $accumulator = new IppAttributeAccumulator();
        static::assertCount(0, $accumulator->getAttributes());

        $accumulator->addAttribute($attr);
        $accumulator->flush();

        static::assertCount(1, $accumulator->getAttributes());
    }

    public function testAddAttributeNull(): void
    {
        $accumulator = new IppAttributeAccumulator();
        static::assertCount(0, $accumulator->getAttributes());

        $accumulator->addAttribute(null);
        $accumulator->flush();

        static::assertCount(0, $accumulator->getAttributes());
    }

    public function testStartNewCollection(): void
    {
        $attr = $this->createMock(IppAttribute::class);
        $attr->method('getName')->willReturn('unit');

        $accumulator = new IppAttributeAccumulator();

        $accumulator->addAttribute($attr);
        $accumulator->addAttribute($attr);
        $accumulator->flush();

        static::assertCount(2, $accumulator->getAttributes());
    }
}
