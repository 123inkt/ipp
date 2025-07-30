<?php

declare(strict_types=1);

namespace DR\Ipp\Protocol;

class IppResolution
{
    public function __construct(private readonly int $crossFeedResolution, private readonly int $feedResolution, private readonly int $unit)
    {
    }

    public function getCrossFeedResolution(): int
    {
        return $this->crossFeedResolution;
    }

    public function getFeedResolution(): int
    {
        return $this->feedResolution;
    }

    public function getUnit(): int
    {
        return $this->unit;
    }
}
