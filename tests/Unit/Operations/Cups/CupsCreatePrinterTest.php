<?php

declare(strict_types=1);

namespace DR\Ipp\Tests\Unit\Operations\Cups;

use DR\Ipp\Client\IppHttpClientInterface;
use DR\Ipp\Entity\IppPrinter;
use DR\Ipp\Entity\IppServer;
use DR\Ipp\Entity\Response\IppResponseInterface;
use DR\Ipp\Enum\IppOperationEnum;
use DR\Ipp\Operations\Cups\CupsCreatePrinter;
use DR\Ipp\Protocol\IppOperation;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\StreamInterface;
use Throwable;

#[CoversClass(CupsCreatePrinter::class)]
class CupsCreatePrinterTest extends TestCase
{
    /**
     * @throws Throwable
     */
    public function testCreate(): void
    {
        $cups   = 'https://cups';
        $server = new IppServer();
        $server->setUri($cups);
        $server->setUsername('admin');
        $server->setPassword('admin');

        $client           = $this->createMock(IppHttpClientInterface::class);
        $printerCreator   = new CupsCreatePrinter($server, $client);
        $responseContents = 'test';

        $body = $this->createMock(StreamInterface::class);
        $body->method('getContents')->willReturn($responseContents);
        $response = $this->createMock(IppResponseInterface::class);

        $client->expects($this->once())->method('sendRequest')->with(static::callback(static function (IppOperation $operation) {
            static::assertSame(IppOperationEnum::CupsAddModifyPrinter, $operation->getOperation());
            static::assertCount(0, $operation->getJobAttributes(), 'Job attribute count incorrect');
            static::assertCount(5, $operation->getPrinterAttributes(), 'Printer attribute count incorrect');
            static::assertCount(4, $operation->getOperationAttributes(), 'Operation attribute count incorrect');

            return true;
        }))->willReturn($response);

        $printer = new IppPrinter();
        $printer->setHostname('test');
        $printer->setDeviceUri('socket://10.10.10.10:9100');
        $printer->setLocation('location');
        $printer->setPpdName('file.ppd');

        $printerCreator->create($printer);
    }
}
