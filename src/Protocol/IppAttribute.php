<?php

declare(strict_types=1);

namespace DR\Ipp\Protocol;

use DR\Ipp\Enum\IppTypeEnum;
use DR\Utils\Assert;

class IppAttribute
{
    public function __construct(private readonly IppTypeEnum $type, private readonly string $name, private readonly mixed $value)
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

    /**
     * @internal
     */
    public function __toString(): string
    {
        // 1 byte type
        $binary = pack('c', $this->type->value);
        // 2 bytes length of attribute name, followed by attribute name.
        $binary .= pack('n', strlen($this->name)) . $this->name;
        // 2 bytes value length, followed by the value.
        if ($this->type === IppTypeEnum::Int || $this->type === IppTypeEnum::Enum) {
            $binary .= pack('n', 4) . pack('N', $this->value);
        } elseif ($this->type === IppTypeEnum::Bool) {
            $binary .= pack('n', 1) . pack('c', (int)Assert::boolean($this->value));
        } else {
            $binary .= pack('n', strlen(Assert::string($this->value))) . Assert::string($this->value);
        }

        return $binary;
    }
}
