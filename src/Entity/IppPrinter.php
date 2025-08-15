<?php

declare(strict_types=1);

namespace DR\Ipp\Entity;

class IppPrinter
{
    private string $hostname;
    private string $deviceUri;
    private string $location;
    private ?string $trayName = null;
    private ?string $ppdName = null;

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
}
