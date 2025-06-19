<?php

declare(strict_types=1);

namespace DR\Ipp\Entity\Response;

use DR\Ipp\Enum\IppStatusCodeEnum;
use DR\Ipp\Enum\JobStateEnum;
use DR\Ipp\Protocol\IppAttribute;
use DR\Ipp\Protocol\IppStatusMessageService;
use DR\Utils\Assert;

class CupsIppResponse implements IppResponseInterface
{
    /**
     * @param IppAttribute[] $attributes
     */
    public function __construct(private readonly IppStatusCodeEnum $statusCode, private readonly array $attributes)
    {
    }

    public function getJobState(): ?JobStateEnum
    {
        $success = $this->statusCode->value < IppStatusCodeEnum::ClientErrorBadRequest->value;
        if ($success === false) {
            return JobStateEnum::Failed;
        }

        /** @var int|null $jobState */
        $jobState = isset($this->attributes['job-state']) ? $this->attributes['job-state']->getValue() : null;

        return $jobState === null ? null : JobStateEnum::tryFrom($jobState);
    }

    public function getStatusCode(): IppStatusCodeEnum
    {
        return $this->statusCode;
    }

    public function getJobId(): ?int
    {
        return isset($this->attributes['job-id']) ? Assert::integer($this->attributes['job-id']->getValue()) : null;
    }

    public function getJobUri(): ?string
    {
        return isset($this->attributes['job-uri']) ? Assert::string($this->attributes['job-uri']->getValue()) : null;
    }

    public function getStatusMessage(): ?string
    {
        $success = $this->statusCode->value < IppStatusCodeEnum::ClientErrorBadRequest->value;
        /** @var string|null $statusMessage */
        $statusMessage = isset($this->attributes['status-message']) ? $this->attributes['status-message']->getValue() : null;

        return $statusMessage ?? ($success ? null : IppStatusMessageService::getStatusMessage($this->statusCode));
    }

    /**
     * @return IppAttribute[]
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }
}
