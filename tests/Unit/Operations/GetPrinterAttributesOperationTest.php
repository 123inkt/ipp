<?php

declare(strict_types=1);

namespace DR\Ipp\Tests\Unit\Operations;

use DR\Ipp\Client\IppHttpClientInterface;
use DR\Ipp\Entity\IppPrinter;
use DR\Ipp\Entity\IppServer;
use DR\Ipp\Enum\IppOperationEnum;
use DR\Ipp\Factory\ResponseParserFactoryInterface;
use DR\Ipp\Operations\GetPrinterAttributesOperation;
use DR\Ipp\Protocol\IppOperation;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Throwable;

#[CoversClass(GetPrinterAttributesOperation::class)]
class GetPrinterAttributesOperationTest extends TestCase
{
    /**
     * @throws Throwable
     */
    public function testGetJobs(): void
    {
        $cups   = 'https://cups';
        $server = new IppServer();
        $server->setUri($cups);

        $client        = $this->createMock(IppHttpClientInterface::class);
        $parserFactory = $this->createMock(ResponseParserFactoryInterface::class);
        $parserFactory->expects($this->once())->method('responseParser');

        $operation       = new GetPrinterAttributesOperation($server, $client, $parserFactory);
        $responseContent = 'test';

        $body = $this->createMock(StreamInterface::class);
        $body->method('getContents')->willReturn($responseContent);
        $response = $this->createMock(ResponseInterface::class);

        $printer = new IppPrinter();
        $printer->setHostname('test');

        $client->expects($this->once())->method('sendRequest')->with(static::callback(static function (IppOperation $operation) {
            static::assertSame(IppOperationEnum::GetPrinterAttributes, $operation->getOperation());
            static::assertCount(0, $operation->getJobAttributes(), 'Job attribute count incorrect');
            static::assertCount(0, $operation->getPrinterAttributes(), 'Printer attribute count incorrect');
            static::assertCount(4, $operation->getOperationAttributes(), 'Operation attribute count incorrect');

            return true;
        }))->willReturn($response);

        $operation->getAttributes($printer);
    }
}
