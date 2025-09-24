<?php

declare(strict_types=1);

namespace DR\Ipp\Entity\Response;

use DR\Ipp\Entity\IppJob;
use DR\Ipp\Entity\IppPrinter;
use DR\Ipp\Enum\IppStatusCodeEnum;
use DR\Ipp\Protocol\IppAttribute;
use DR\Ipp\Protocol\IppStatusMessageService;

class CupsIppResponse implements IppResponseInterface
{
    /**
     * @param array<string, IppAttribute> $attributes
     * @param IppJob[]                    $jobs
     */
    public function __construct(private readonly IppStatusCodeEnum $statusCode, private readonly array $attributes, private readonly array $jobs, private readonly array $printers)
    {
    }

    public function getStatusCode(): IppStatusCodeEnum
    {
        return $this->statusCode;
    }

    public function getStatusMessage(): ?string
    {
        $success = $this->statusCode->value < IppStatusCodeEnum::ClientErrorBadRequest->value;
        /** @var string|null $statusMessage */
        $statusMessage = $this->getAttribute('status-message')?->getValue();

        return $statusMessage ?? ($success ? null : IppStatusMessageService::getStatusMessage($this->statusCode));
    }

    /**
     * @return IppJob[]
     */
    public function getJobs(): array
    {
        return $this->jobs;
    }

    /**
     * @return IppPrinter[]
     */
    public function getPrinters(): array
    {
        return $this->printers;
    }

    /**
     * @inheritDoc
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    public function getAttribute(string $name): ?IppAttribute
    {
        return $this->attributes[$name] ?? null;
    }
}
