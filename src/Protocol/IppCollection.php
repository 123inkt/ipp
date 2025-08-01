<?php

declare(strict_types=1);

namespace DR\Ipp\Protocol;

class IppCollection
{
    /**
     * @var array<string, mixed>
     */
    private array $values = [];

    public function add(string $name, mixed $value): void
    {
        $this->values[$name] = $value;
    }

    /**
     * @return array<string, mixed>
     */
    public function getValues(): array
    {
        return $this->values;
    }
}
