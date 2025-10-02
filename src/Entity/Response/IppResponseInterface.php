<?php

declare(strict_types=1);

namespace DR\Ipp\Entity\Response;

use DR\Ipp\Entity\IppJob;
use DR\Ipp\Enum\IppStatusCodeEnum;
use DR\Ipp\Protocol\IppAttribute;

interface IppResponseInterface
{
    public function getStatusCode(): IppStatusCodeEnum;

    public function getStatusMessage(): ?string;

    /**
     * @return IppJob[]
     */
    public function getJobs(): array;

    /**
     * @return array<string, IppAttribute>
     */
    public function getAttributes(): array;

    public function getAttribute(string $name): ?IppAttribute;
}
