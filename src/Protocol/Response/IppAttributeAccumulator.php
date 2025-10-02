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
        $this->attributes             = [];
    }

    /**
     * @return IppAttribute[][]
     */
    public function getAttributes(): array
    {
        return $this->attributeCollections;
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
