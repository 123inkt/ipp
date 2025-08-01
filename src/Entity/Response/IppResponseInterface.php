<?php

declare(strict_types=1);

namespace DR\Ipp\Entity\Response;

use DR\Ipp\Enum\JobStateEnum;
use DR\Ipp\Protocol\IppAttribute;

interface IppResponseInterface
{
    public function getJobUri(): ?string;

    public function getJobState(): ?JobStateEnum;

    public function getStatusMessage(): ?string;

    /**
     * @return IppAttribute[]
     */
    public function getAttributes(): array;
}
