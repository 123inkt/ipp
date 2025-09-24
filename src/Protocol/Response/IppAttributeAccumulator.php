<?php

declare(strict_types=1);

namespace DR\Ipp\Protocol\Response;

use DR\Ipp\Protocol\IppAttribute;

/**
 * @internal
 */
class IppAttributeAccumulator
{
    /** @var IppAttribute[][] */
    private array $attributeCollections = [];
    /** @var IppAttribute[] */
    private array $attributes = [];

    public function flush(): void
    {
        if (count($this->attributes) < 1) {
            return;
        }
        $this->attributeCollections[] = $this->attributes;
    }

    /**
     * @return IppAttribute[][]
     */
    public function getAttributes(): array
    {
        return $this->attributeCollections;
    }

    /**
     * Return a normalized list of attributes.
     * When an attribute has multiple values for the same name we return an array of values.
     * Modifies the internal state.
     * ex: attributes => [
     *      'job-id' => IppAttribute(IppTypeEnum::Int, 'job-id', 1),
     *      'supported-formats' => IppAttribute(IppTypeEnum::Keyword, 'supported-formats', ['png', 'pdf']),
     * ]
     * @return array<string, IppAttribute>
     */
    public function getNormalizedAttributes(): array
    {
        $attributes = [];
        foreach ($this->attributeCollections as $collection) {
            foreach ($collection as $attr) {
                if (array_key_exists($attr->getName(), $attributes)) {
                    $attributes[$attr->getName()]->appendValue($attr->getValue());
                } else {
                    $attributes[$attr->getName()] = $attr;
                }
            }
        }
        $this->attributeCollections = [];

        return $attributes;
    }

    public function addAttribute(?IppAttribute $attribute): void
    {
        if ($attribute === null) {
            return;
        }

        // If we encounter a new attribute with a name we've already seen, start a new collection of values
        if (array_key_exists($attribute->getName(), $this->attributes)) {
            $this->attributeCollections[] = $this->attributes;
            $this->attributes             = [];
        }
        $this->attributes[$attribute->getName()] = $attribute;
    }
}
