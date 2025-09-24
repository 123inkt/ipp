<?php

declare(strict_types=1);

namespace DR\Ipp\Tests\Unit\Protocol;

use DR\Ipp\Enum\IppTypeEnum;
use DR\Ipp\Protocol\IppAttribute;
use DR\Ipp\Protocol\IppCollection;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(IppCollection::class)]
class IppCollectionTest extends TestCase
{
    public function testGetValues(): void
    {
        $collection = new IppCollection();
        static::assertCount(0, $collection->getValues());

        $attribute = new IppAttribute(IppTypeEnum::Keyword, 'foo', 'bar');
        $collection->add($attribute);
        static::assertCount(1, $collection->getValues());
        static::assertSame([$attribute], $collection->getValues());
    }
}
