<?php

declare(strict_types=1);

namespace DR\Ipp\Entity;

use DateTimeInterface;
use DR\Ipp\Enum\JobStateEnum;
use DR\Ipp\Enum\JobStateReasonEnum;

class IppJob
{
    private int $id;
    private string $uri;
    private JobStateEnum $jobState;
    private JobStateReasonEnum $jobStateReason;
    private ?string $userName;
    private ?int $fileSize;
    private ?int $numberOfDocuments;
    private ?int $copies;
    private ?DateTimeInterface $creationDate;
    private ?DateTimeInterface $completionDate;
    private ?string $documentFormat;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getUri(): string
    {
        return $this->uri;
    }

    public function setUri(string $uri): self
    {
        $this->uri = $uri;

        return $this;
    }

    public function getJobStateReason(): JobStateReasonEnum
    {
        return $this->jobStateReason;
    }

    public function setJobStateReason(JobStateReasonEnum $jobStateReason): self
    {
        $this->jobStateReason = $jobStateReason;

        return $this;
    }

    public function getJobState(): JobStateEnum
    {
        return $this->jobState;
    }

    public function setJobState(JobStateEnum $jobState): self
    {
        $this->jobState = $jobState;

        return $this;
    }

    public function getUserName(): ?string
    {
        return $this->userName;
    }

    public function setUserName(?string $userName): self
    {
        $this->userName = $userName;

        return $this;
    }

    public function getFileSize(): ?int
    {
        return $this->fileSize;
    }

    public function setFileSize(?int $fileSize): self
    {
        $this->fileSize = $fileSize;

        return $this;
    }

    public function getNumberOfDocuments(): ?int
    {
        return $this->numberOfDocuments;
    }

    public function setNumberOfDocuments(?int $numberOfDocuments): self
    {
        $this->numberOfDocuments = $numberOfDocuments;

        return $this;
    }

    public function getCopies(): ?int
    {
        return $this->copies;
    }

    public function setCopies(?int $copies): self
    {
        $this->copies = $copies;

        return $this;
    }

    public function getCreationDate(): ?DateTimeInterface
    {
        return $this->creationDate;
    }

    public function setCreationDate(?DateTimeInterface $creationDate): self
    {
        $this->creationDate = $creationDate;

        return $this;
    }

    public function getCompletionDate(): ?DateTimeInterface
    {
        return $this->completionDate;
    }

    public function setCompletionDate(?DateTimeInterface $completionDate): self
    {
        $this->completionDate = $completionDate;

        return $this;
    }

    public function getDocumentFormat(): ?string
    {
        return $this->documentFormat;
    }

    public function setDocumentFormat(?string $documentFormat): self
    {
        $this->documentFormat = $documentFormat;

        return $this;
    }
}
