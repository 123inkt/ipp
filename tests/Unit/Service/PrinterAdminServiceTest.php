<?php

declare(strict_types=1);

namespace DR\Ipp\Tests\Unit\Service;

use DR\Ipp\Entity\IppPrinter;
use DR\Ipp\Operations\CreatePrinterInterface;
use DR\Ipp\Operations\DeletePrinterInterface;
use DR\Ipp\Operations\GetPrintersInterface;
use DR\Ipp\Service\PrinterAdminService;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Throwable;

#[CoversClass(PrinterAdminService::class)]
class PrinterAdminServiceTest extends TestCase
{
    private CreatePrinterInterface&MockObject $createPrinter;
    private DeletePrinterInterface&MockObject $deletePrinter;
    private GetPrintersInterface&MockObject $getPrinters;
    private PrinterAdminService $service;

    /**
     * @throws Throwable
     */
    protected function setUp(): void
    {
        $this->createPrinter = $this->createMock(CreatePrinterInterface::class);
        $this->deletePrinter = $this->createMock(DeletePrinterInterface::class);
        $this->getPrinters   = $this->createMock(GetPrintersInterface::class);
        $this->service       = new PrinterAdminService($this->createPrinter, $this->deletePrinter, $this->getPrinters);
    }

    /**
     * @throws Throwable
     */
    public function testCreate(): void
    {
        $this->createPrinter->expects($this->once())->method('create');
        $this->service->createPrinter($this->createMock(IppPrinter::class));
    }

    /**
     * @throws Throwable
     */
    public function testDelete(): void
    {
        $this->deletePrinter->expects($this->once())->method('delete');
        $this->service->deletePrinter($this->createMock(IppPrinter::class));
    }

    /**
     * @throws Throwable
     */
    public function testGetPrinters(): void
    {
        $this->getPrinters->expects($this->once())->method('get');
        $this->service->getPrinters();
    }
}
