<?php

declare(strict_types=1);

namespace DR\Ipp\Entity;

use DateTimeInterface;
use DR\Ipp\Enum\AuthenticationSupportEnum;
use DR\Ipp\Enum\PrinterStateEnum;
use DR\Ipp\Enum\PrintQualityEnum;
use DR\Ipp\Enum\SecuritySupportEnum;

class IppPrinter
{
    private string $hostname;
    private string $deviceUri;
    private string $location;
    private ?string $printerUri;
    private ?string $trayName = null;
    private ?string $ppdName = null;
    private ?PrinterStateEnum $printerState = null;
    private ?string $printerStateReason = null;
    private ?string $printerType = null;
    /** @var AuthenticationSupportEnum[] */
    private array $uriAuthSupported = [];
    /** @var SecuritySupportEnum[] */
    private array $uriSecuritySupported = [];
    private ?string $defaultDocumentFormat = null;
    /** @var string[] */
    private array $documentFormatsSupported = [];
    private ?bool $acceptingJobs = null;
    private ?int $queuedJobsCount = null;
    private ?DateTimeInterface $upSince = null;
    private ?int $fileSizeLimit = null;
    private ?int $pageLimit = null;
    private ?string $defaultMedia = null;
    /** @var string[] */
    private array $supportedMedia = [];
    private ?PrintQualityEnum $defaultQuality = null;
    /** @var PrintQualityEnum[] */
    private array $printQualitiesSupported = [];

    public function getHostname(): string
    {
        return $this->hostname;
    }

    public function setHostname(string $hostname): self
    {
        $this->hostname = $hostname;

        return $this;
    }

    public function getDeviceUri(): string
    {
        return $this->deviceUri;
    }

    public function setDeviceUri(string $deviceUri): self
    {
        $this->deviceUri = $deviceUri;

        return $this;
    }

    public function getLocation(): string
    {
        return $this->location;
    }

    public function setLocation(string $location): self
    {
        $this->location = $location;

        return $this;
    }

    public function getPrinterUri(): ?string
    {
        return $this->printerUri;
    }

    public function setPrinterUri(?string $printerUri): self
    {
        $this->printerUri = $printerUri;

        return $this;
    }

    public function getTrayName(): ?string
    {
        return $this->trayName;
    }

    public function setTrayName(?string $trayName): self
    {
        $this->trayName = $trayName;

        return $this;
    }

    public function getPpdName(): ?string
    {
        return $this->ppdName;
    }

    public function setPpdName(?string $ppdName): self
    {
        $this->ppdName = $ppdName;

        return $this;
    }

    public function getPrinterState(): ?PrinterStateEnum
    {
        return $this->printerState;
    }

    public function setPrinterState(?PrinterStateEnum $printerState): self
    {
        $this->printerState = $printerState;

        return $this;
    }

    public function getPrinterStateReason(): ?string
    {
        return $this->printerStateReason;
    }

    public function setPrinterStateReason(?string $printerStateReason): self
    {
        $this->printerStateReason = $printerStateReason;

        return $this;
    }

    public function getPrinterType(): ?string
    {
        return $this->printerType;
    }

    public function setPrinterType(?string $printerType): self
    {
        $this->printerType = $printerType;

        return $this;
    }

    public function getUpSince(): ?DateTimeInterface
    {
        return $this->upSince;
    }

    public function setUpSince(?DateTimeInterface $upSince): self
    {
        $this->upSince = $upSince;

        return $this;
    }

    public function getQueuedJobsCount(): ?int
    {
        return $this->queuedJobsCount;
    }

    public function setQueuedJobsCount(?int $queuedJobsCount): self
    {
        $this->queuedJobsCount = $queuedJobsCount;

        return $this;
    }

    public function getAcceptingJobs(): ?bool
    {
        return $this->acceptingJobs;
    }

    public function setAcceptingJobs(?bool $acceptingJobs): self
    {
        $this->acceptingJobs = $acceptingJobs;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getDocumentFormatsSupported(): array
    {
        return $this->documentFormatsSupported;
    }

    /**
     * @param string[] $documentFormatsSupported
     */
    public function setDocumentFormatsSupported(array $documentFormatsSupported): self
    {
        $this->documentFormatsSupported = $documentFormatsSupported;

        return $this;
    }

    public function getDefaultDocumentFormat(): ?string
    {
        return $this->defaultDocumentFormat;
    }

    public function setDefaultDocumentFormat(?string $defaultDocumentFormat): self
    {
        $this->defaultDocumentFormat = $defaultDocumentFormat;

        return $this;
    }

    /**
     * @return AuthenticationSupportEnum[]|null
     */
    public function getUriAuthSupported(): ?array
    {
        return $this->uriAuthSupported;
    }

    /**
     * @param AuthenticationSupportEnum[] $uriAuthSupported
     */
    public function setUriAuthSupported(array $uriAuthSupported): self
    {
        $this->uriAuthSupported = $uriAuthSupported;

        return $this;
    }

    /**
     * @return SecuritySupportEnum[]
     */
    public function getUriSecuritySupported(): array
    {
        return $this->uriSecuritySupported;
    }

    /**
     * @param SecuritySupportEnum[] $uriSecuritySupported
     */
    public function setUriSecuritySupported(array $uriSecuritySupported): self
    {
        $this->uriSecuritySupported = $uriSecuritySupported;

        return $this;
    }

    public function getFileSizeLimit(): ?int
    {
        return $this->fileSizeLimit;
    }

    public function setFileSizeLimit(?int $fileSizeLimit): self
    {
        $this->fileSizeLimit = $fileSizeLimit;

        return $this;
    }

    public function getPageLimit(): ?int
    {
        return $this->pageLimit;
    }

    public function setPageLimit(?int $pageLimit): self
    {
        $this->pageLimit = $pageLimit;

        return $this;
    }

    public function getDefaultMedia(): ?string
    {
        return $this->defaultMedia;
    }

    public function setDefaultMedia(?string $defaultMedia): self
    {
        $this->defaultMedia = $defaultMedia;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getSupportedMedia(): array
    {
        return $this->supportedMedia;
    }

    /**
     * @param string[] $supportedMedia
     */
    public function setSupportedMedia(array $supportedMedia): self
    {
        $this->supportedMedia = $supportedMedia;

        return $this;
    }

    public function getDefaultQuality(): ?PrintQualityEnum
    {
        return $this->defaultQuality;
    }

    public function setDefaultQuality(?PrintQualityEnum $defaultQuality): self
    {
        $this->defaultQuality = $defaultQuality;

        return $this;
    }

    /**
     * @return PrintQualityEnum[]
     */
    public function getPrintQualitiesSupported(): array
    {
        return $this->printQualitiesSupported;
    }

    /**
     * @param PrintQualityEnum[] $printQualitiesSupported
     */
    public function setPrintQualitiesSupported(array $printQualitiesSupported): self
    {
        $this->printQualitiesSupported = $printQualitiesSupported;

        return $this;
    }
}
