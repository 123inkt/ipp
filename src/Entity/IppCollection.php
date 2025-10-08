<?php

declare(strict_types=1);

namespace DR\Ipp\Entity;

use DR\Ipp\Protocol\IppAttribute;

class IppCollection
{
    /** @var IppAttribute[] */
    private array $values = [];

    public function add(IppAttribute $attribute): void
    {
        $this->values[] = $attribute;
    }

    /**
     * @return IppAttribute[]
     */
    public function getValues(): array
    {
        return $this->values;
    }
}
