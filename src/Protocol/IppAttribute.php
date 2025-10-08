<?php

declare(strict_types=1);

namespace DR\Ipp\Protocol;

use DR\Ipp\Enum\IppTypeEnum;

class IppAttribute
{
    public function __construct(private readonly IppTypeEnum $type, private readonly string $name, private mixed $value)
    {
    }

    public function getType(): IppTypeEnum
    {
        return $this->type;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getValue(): mixed
    {
        return $this->value;
    }

    public function appendValue(mixed $additionalValue): void
    {
        if (is_array($this->value) === false) {
            $this->value = [$this->value];
        }
        if (is_array($additionalValue)) {
            $this->value = array_merge($this->value, $additionalValue);

            return;
        }

        $this->value[] = $additionalValue;
    }

    /**
     * @internal
     */
    public function __toString(): string
    {
        return IppEncoder::encodeAttribute($this);
    }
}
