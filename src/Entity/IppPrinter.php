<?php

declare(strict_types=1);

namespace DR\Ipp\Entity;

use DR\Ipp\Enum\PrinterStateEnum;
use DR\Ipp\Enum\PrinterStateReasonEnum;

class IppPrinter
{
    private string $hostname;
    private string $deviceUri;
    private string $location;
    private ?string $trayName = null;
    private ?string $ppdName = null;
    private ?PrinterStateEnum $printerState = null;
    private ?PrinterStateReasonEnum $printerStateReason = null;
    private ?string $printerType = null;

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

    public function getPrinterStateReason(): ?PrinterStateReasonEnum
    {
        return $this->printerStateReason;
    }

    public function setPrinterStateReason(?PrinterStateReasonEnum $printerStateReason): self
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
}
