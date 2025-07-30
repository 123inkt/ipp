<?php

declare(strict_types=1);

namespace DR\Ipp\Protocol;

class IppCollection
{
    private array $values = [];

    public function add(string $name, mixed $value): void
    {
        $this->values[$name] = $value;
    }

    public function getValues(): array
    {
        return $this->values;
    }
}
