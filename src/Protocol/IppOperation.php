<?php

declare(strict_types=1);

namespace DR\Ipp\Protocol;

use DR\Ipp\Enum\IppOperationEnum;

/**
 * @see https://datatracker.ietf.org/doc/html/rfc8010/
 * @see https://datatracker.ietf.org/doc/html/rfc8011/
 * @see https://datatracker.ietf.org/doc/html/rfc2911/
 * @see https://github.com/OpenPrinting/libcups/blob/master/cups/ipp-support.c
 */
class IppOperation
{
    /** @var IppAttribute[] */
    private array $operationAttributes = [];
    /** @var IppAttribute[] */
    private array $printerAttributes = [];
    /** @var IppAttribute[] */
    private array $jobAttributes = [];

    private ?string $fileData = null;

    public function __construct(
        private readonly IppOperationEnum $operation,
        private readonly string $version = '2.0',
        private readonly int $requestId = 1,
    ) {
    }

    public function getVersion(): string
    {
        return $this->version;
    }

    public function getRequestId(): int
    {
        return $this->requestId;
    }

    public function getOperation(): IppOperationEnum
    {
        return $this->operation;
    }

    /**
     * @return IppAttribute[]
     */
    public function getOperationAttributes(): array
    {
        return $this->operationAttributes;
    }

    /**
     * @return IppAttribute[]
     */
    public function getPrinterAttributes(): array
    {
        return $this->printerAttributes;
    }

    /**
     * @return IppAttribute[]
     */
    public function getJobAttributes(): array
    {
        return $this->jobAttributes;
    }

    public function getFileData(): ?string
    {
        return $this->fileData;
    }

    public function addOperationAttribute(IppAttribute $attribute): self
    {
        $this->operationAttributes[] = $attribute;

        return $this;
    }

    public function addPrinterAttribute(IppAttribute $attribute): self
    {
        $this->printerAttributes[] = $attribute;

        return $this;
    }

    public function addJobAttribute(IppAttribute $attribute): self
    {
        $this->jobAttributes[] = $attribute;

        return $this;
    }

    public function setFileData(string $fileData): self
    {
        $this->fileData = $fileData;

        return $this;
    }

    /**
     * @internal
     */
    public function __toString(): string
    {
        return IppEncoder::encodeOperation($this);
    }
}
