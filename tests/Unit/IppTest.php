<?php

declare(strict_types=1);

namespace DR\Ipp\Tests\Unit;

use DR\Ipp\Entity\IppPrinter;
use DR\Ipp\Entity\IppPrintFile;
use DR\Ipp\Entity\IppServer;
use DR\Ipp\Entity\Response\IppResponseInterface;
use DR\Ipp\Ipp;
use DR\Ipp\Operations\CupsCreatePrinter;
use DR\Ipp\Operations\CupsDeletePrinter;
use DR\Ipp\Operations\GetJobAttributesOperation;
use DR\Ipp\Operations\PrintOperation;
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
    public function testCreatePrinter(): void
    {
        $printer = new IppPrinter();

        $mock = $this->createMock(CupsCreatePrinter::class);
        $this->setPrivateProperty($this->ipp, 'cupsCreatePrinter', $mock);

        $mock->expects($this->once())->method('create')->with($printer);
        $this->ipp->createPrinter($printer);
    }

    public function testDeletePrinter(): void
    {
        $printer = new IppPrinter();

        $mock = $this->createMock(CupsDeletePrinter::class);
        $this->setPrivateProperty($this->ipp, 'cupsDeletePrinter', $mock);

        $mock->expects($this->once())->method('delete')->with($printer);
        $this->ipp->deletePrinter($printer);
    }

    /**
     * @throws Throwable
     */
    public function testGetJobAttributes(): void
    {
        $job = $this->createMock(IppResponseInterface::class);

        $mock = $this->createMock(GetJobAttributesOperation::class);
        $this->setPrivateProperty($this->ipp, 'getJobAttributes', $mock);

        $mock->expects($this->once())->method('getJob')->with($job->getJobUri() ?? '');
        $this->ipp->getJobAttributes($job->getJobUri() ?? '');
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
