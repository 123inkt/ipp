<?php

declare(strict_types=1);

namespace DR\Ipp\Normalizer;

use DR\Ipp\Protocol\IppAttribute;

class IppAttributeCollectionNormalizer
{
    /**
     * Return a normalized list of attributes.
     * When an attribute has multiple values for the same name we return an array of values.
     * ex: attributes => [
     *      'job-id' => IppAttribute(IppTypeEnum::Int, 'job-id', 1),
     *      'supported-formats' => IppAttribute(IppTypeEnum::Keyword, 'supported-formats', ['png', 'pdf']),
     * ]
     *
     * @param IppAttribute[][] $attributeCollections
     *
     * @return array<string, IppAttribute>
     */
    public static function getNormalizedAttributes(array $attributeCollections): array
    {
        $attributes = [];
        foreach (self::clone($attributeCollections) as $collection) {
            foreach ($collection as $attr) {
                if (array_key_exists($attr->getName(), $attributes)) {
                    $attributes[$attr->getName()]->appendValue($attr->getValue());
                } else {
                    $attributes[$attr->getName()] = $attr;
                }
            }
        }

        return $attributes;
    }

    /**
     * @param IppAttribute[][] $attributeCollections
     *
     * @return IppAttribute[][]
     */
    private static function clone(array $attributeCollections): array
    {
        return array_map(
            static fn(array $collection) => array_map(static fn(IppAttribute $attr) => clone $attr, $collection),
            $attributeCollections,
        );
    }
}
