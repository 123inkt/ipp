<?php

declare(strict_types=1);

namespace DR\Ipp\Entity\Response;

use DR\Ipp\Enum\JobStateEnum;

interface IppResponseInterface
{
    public function getJobUri(): ?string;

    public function getJobState(): ?JobStateEnum;

    public function getStatusMessage(): ?string;
}
