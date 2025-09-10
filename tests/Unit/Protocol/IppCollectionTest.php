<?php

declare(strict_types=1);

namespace DR\Ipp\Tests\Unit\Protocol;

use DR\Ipp\Protocol\IppCollection;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(IppCollection::class)]
class IppCollectionTest extends TestCase
{
    public function test(): void
    {
        $collection = new IppCollection();
        static::assertCount(0, $collection->getValues());

        $collection->add('foo', 'bar');
        static::assertCount(1, $collection->getValues());
        static::assertSame(['foo' => 'bar'], $collection->getValues());
    }
}
