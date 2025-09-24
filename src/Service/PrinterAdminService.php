<?php

declare(strict_types=1);

namespace DR\Ipp\Service;

use DR\Ipp\Entity\IppPrinter;
use DR\Ipp\Entity\Response\IppResponseInterface;
use DR\Ipp\Operations\CreatePrinterInterface;
use DR\Ipp\Operations\DeletePrinterInterface;
use DR\Ipp\Operations\GetPrintersInterface;
use Psr\Http\Client\ClientExceptionInterface;

class PrinterAdminService
{
    public function __construct(
        private readonly CreatePrinterInterface $createPrinter,
        private readonly DeletePrinterInterface $deletePrinter,
        private readonly GetPrintersInterface $getPrinters,
    ) {
    }

    /**
     * @throws ClientExceptionInterface
     */
    public function createPrinter(IppPrinter $printer): IppResponseInterface
    {
        return $this->createPrinter->create($printer);
    }

    /**
     * @throws ClientExceptionInterface
     */
    public function deletePrinter(IppPrinter $printer): IppResponseInterface
    {
        return $this->deletePrinter->delete($printer);
    }

    /**
     * @throws ClientExceptionInterface
     */
    public function getPrinters(): IppResponseInterface
    {
        return $this->getPrinters->getPrinters();
    }
}
