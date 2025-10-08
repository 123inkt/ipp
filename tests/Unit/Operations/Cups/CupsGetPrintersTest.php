<?php

declare(strict_types=1);

namespace DR\Ipp\Tests\Unit\Operations\Cups;

use DR\Ipp\Client\IppHttpClientInterface;
use DR\Ipp\Entity\IppServer;
use DR\Ipp\Enum\IppOperationEnum;
use DR\Ipp\Factory\ResponseParserFactoryInterface;
use DR\Ipp\Operations\Cups\CupsGetPrinters;
use DR\Ipp\Protocol\IppOperation;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Throwable;

#[CoversClass(CupsGetPrinters::class)]
class CupsGetPrintersTest extends TestCase
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

        $client        = $this->createMock(IppHttpClientInterface::class);
        $parserFactory = $this->createMock(ResponseParserFactoryInterface::class);
        $parserFactory->expects($this->once())->method('printerResponseParser');
        $printerCreator   = new CupsGetPrinters($client, $parserFactory);
        $responseContents = 'test';

        $body = $this->createMock(StreamInterface::class);
        $body->method('getContents')->willReturn($responseContents);
        $response = $this->createMock(ResponseInterface::class);

        $client->expects($this->once())->method('sendRequest')->with(static::callback(static function (IppOperation $operation) {
            static::assertSame(IppOperationEnum::CupsGetPrinters, $operation->getOperation());
            static::assertCount(0, $operation->getJobAttributes(), 'Job attribute count incorrect');
            static::assertCount(0, $operation->getPrinterAttributes(), 'Printer attribute count incorrect');
            static::assertCount(2, $operation->getOperationAttributes(), 'Operation attribute count incorrect');

            return true;
        }))->willReturn($response);

        $printerCreator->get();
    }
}
