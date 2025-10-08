<?php

declare(strict_types=1);

namespace DR\Ipp\Factory;

use DR\Ipp\Entity\IppPrinter;
use DR\Ipp\Enum\PrinterStateEnum;
use DR\Ipp\Enum\PrinterStateReasonEnum;
use DR\Ipp\Protocol\IppAttribute;
use DR\Utils\Assert;

class IppPrinterFactory
{
    /**
     * @param IppAttribute[] $attributes
     */
    public function create(array $attributes): ?IppPrinter
    {
        $name = $this->getAttribute($attributes, 'printer-name');
        if ($name === null) {
            return null;
        }

        $printer = new IppPrinter();
        $printer->setHostname(Assert::string($name->getValue()));
        $printer->setLocation(Assert::string($this->getAttribute($attributes, 'printer-location')?->getValue()));
        $printer->setDeviceUri(Assert::string($this->getAttribute($attributes, 'device-uri')?->getValue()));
        $printer->setPrinterState(PrinterStateEnum::from(Assert::integer($this->getAttribute($attributes, 'printer-state')?->getValue())));
        $stateReason = PrinterStateReasonEnum::tryFrom(Assert::string($this->getAttribute($attributes, 'printer-state-reasons')?->getValue()));
        $printer->setPrinterStateReason($stateReason ?? PrinterStateReasonEnum::Unknown);
        $printerType = $this->getAttribute($attributes, 'printer-make-and-model')?->getValue();
        $printer->setPrinterType($printerType === null ? null : Assert::string($printerType));

        return $printer;
    }

    /**
     * @param IppAttribute[] $attributes
     */
    private function getAttribute(array $attributes, string $name): ?IppAttribute
    {
        return $attributes[$name] ?? null;
    }
}
