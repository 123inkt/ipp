<?php

declare(strict_types=1);

namespace DR\Ipp\Entity;

use DR\Ipp\Enum\FileTypeEnum;
use DR\Ipp\Enum\IppAttributeTypeEnum;
use DR\Ipp\Protocol\IppAttribute;

class IppPrintFile
{
    /** @var array<int, IppAttribute[]> $ippAttributes */
    private array $ippAttributes = [
        IppAttributeTypeEnum::OperationAttribute->value => [],
        IppAttributeTypeEnum::JobAttribute->value       => [],
        IppAttributeTypeEnum::PrinterAttribute->value   => [],
    ];

    public function __construct(
        private string $data,
        private FileTypeEnum $fileType,
        private ?string $fileName = null,
        private int $numberOfCopies = 1
    ) {
    }

    public function getFileName(): ?string
    {
        return $this->fileName;
    }

    public function setFileName(?string $fileName): self
    {
        $this->fileName = $fileName;

        return $this;
    }

    public function getData(): string
    {
        return $this->data;
    }

    public function setData(string $data): self
    {
        $this->data = $data;

        return $this;
    }

    public function getFileType(): FileTypeEnum
    {
        return $this->fileType;
    }

    public function setFileType(FileTypeEnum $fileType): self
    {
        $this->fileType = $fileType;

        return $this;
    }

    public function getNumberOfCopies(): int
    {
        return $this->numberOfCopies;
    }

    public function setNumberOfCopies(int $numberOfCopies): self
    {
        $this->numberOfCopies = $numberOfCopies;

        return $this;
    }

    /**
     * @return IppAttribute[]
     */
    public function getIppAttributes(IppAttributeTypeEnum $type): array
    {
        return $this->ippAttributes[$type->value];
    }

    public function addAttribute(IppAttributeTypeEnum $type, IppAttribute $ippAttribute): self
    {
        $this->ippAttributes[$type->value][] = $ippAttribute;

        return $this;
    }
}
