<?php

declare(strict_types=1);

namespace DR\Ipp\Tests\Unit;

use DR\Ipp\Entity\IppJob;
use DR\Ipp\Entity\IppPrinter;
use DR\Ipp\Entity\IppPrintFile;
use DR\Ipp\Entity\IppServer;
use DR\Ipp\Ipp;
use DR\Ipp\Operations\CancelJobOperation;
use DR\Ipp\Operations\GetJobAttributesOperation;
use DR\Ipp\Operations\GetJobsOperation;
use DR\Ipp\Operations\GetPrinterAttributesOperation;
use DR\Ipp\Operations\PrintOperation;
use DR\Ipp\Service\PrinterAdminService;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Client\ClientInterface;
use Psr\Log\LoggerInterface;
use ReflectionClass;
use Throwable;

#[CoversClass(Ipp::class)]
class IppTest extends TestCase
{
    private Ipp $ipp;
    private IppServer $ippServer;
    private ClientInterface&MockObject $client;

    protected function setUp(): void
    {
        $this->ippServer = new IppServer();
        $this->client    = $this->createMock(ClientInterface::class);
        $this->ipp       = new Ipp($this->ippServer, $this->client);
        $this->ipp->setLogger($this->createMock(LoggerInterface::class));
    }

    /**
     * @throws Throwable
     */
    public function testPrinterAdministration(): void
    {
        $mock = $this->createMock(PrinterAdminService::class);
        $this->setPrivateProperty($this->ipp, 'printerAdmin', $mock);

        static::assertSame($mock, $this->ipp->printerAdministration());
    }

    /**
     * @throws Throwable
     */
    public function testGetJobAttributes(): void
    {
        $job = new IppJob();
        $job->setUri('my-uri');

        $mock = $this->createMock(GetJobAttributesOperation::class);
        $this->setPrivateProperty($this->ipp, 'getJobAttributes', $mock);

        $mock->expects($this->once())->method('getJob')->with($job);
        $this->ipp->getJobAttributes($job);
    }

    /**
     * @throws Throwable
     */
    public function testPrint(): void
    {
        $printer = $this->createMock(IppPrinter::class);
        $file    = $this->createMock(IppPrintFile::class);

        $mock = $this->createMock(PrintOperation::class);
        $this->setPrivateProperty($this->ipp, 'printOperation', $mock);

        $mock->expects($this->once())->method('print')->with($printer, $file);
        $this->ipp->print($printer, $file);
    }

    public function testGetPrinterAttributes(): void
    {
        $printer = $this->createMock(IppPrinter::class);

        $mock = $this->createMock(GetPrinterAttributesOperation::class);
        $this->setPrivateProperty($this->ipp, 'getPrinterAttributes', $mock);

        $mock->expects($this->once())->method('getAttributes')->with($printer);
        $this->ipp->getPrinterAttributes($printer);
    }

    public function testGetJobs(): void
    {
        $printer = $this->createMock(IppPrinter::class);

        $mock = $this->createMock(GetJobsOperation::class);
        $this->setPrivateProperty($this->ipp, 'getJobs', $mock);

        $mock->expects($this->once())->method('getJobList')->with($printer);
        $this->ipp->getJobs($printer);
    }

    public function testCancelJob(): void
    {
        $job = new IppJob();
        $job->setUri('my-uri');

        $mock = $this->createMock(CancelJobOperation::class);
        $this->setPrivateProperty($this->ipp, 'cancelJob', $mock);

        $mock->expects($this->once())->method('cancel')->with($job);
        $this->ipp->cancelJob($job);
    }

    /**
     * @param class-string|object $obj
     *
     * @throws Throwable
     */
    private function setPrivateProperty(string|object $obj, string $property, mixed $value): void
    {
        $reflectionClass    = new ReflectionClass($obj);
        $reflectionProperty = $reflectionClass->getProperty($property);
        $reflectionProperty->setValue(is_object($obj) ? $obj : null, $value);
    }
}
