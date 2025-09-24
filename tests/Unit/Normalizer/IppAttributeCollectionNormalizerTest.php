<?php

declare(strict_types=1);

namespace DR\Ipp\Tests\Unit\Normalizer;

use DR\Ipp\Enum\IppTypeEnum;
use DR\Ipp\Normalizer\IppAttributeCollectionNormalizer;
use DR\Ipp\Protocol\IppAttribute;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(IppAttributeCollectionNormalizer::class)]
class IppAttributeCollectionNormalizerTest extends TestCase
{
    public function testGetNormalizedAttributes(): void
    {
        $attr = new IppAttribute(IppTypeEnum::Int, 'foo', 0);

        static::assertCount(1, IppAttributeCollectionNormalizer::getNormalizedAttributes([[$attr, $attr]]));
    }

    public function testGetNormalizedAttributesDuplicateArray(): void
    {
        $attr = new IppAttribute(IppTypeEnum::Keyword, 'foo', ['unit', 'test']);

        $normalized = IppAttributeCollectionNormalizer::getNormalizedAttributes([[$attr, $attr]]);
        static::assertSame(['unit', 'test', 'unit', 'test'], $normalized['foo']->getValue());
    }

    public function testAttributeCollectionNotChanged(): void
    {
        $attr = new IppAttribute(IppTypeEnum::Int, 'foo', 0);
        IppAttributeCollectionNormalizer::getNormalizedAttributes([[$attr, $attr]]);

        static::assertEquals(new IppAttribute(IppTypeEnum::Int, 'foo', 0), $attr);
    }
}
