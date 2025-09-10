<?php

declare(strict_types=1);

namespace DR\Ipp\Tests\Unit\Operations;

use DR\Ipp\Client\IppHttpClientInterface;
use DR\Ipp\Entity\IppJob;
use DR\Ipp\Entity\IppServer;
use DR\Ipp\Enum\IppOperationEnum;
use DR\Ipp\Factory\ResponseParserFactoryInterface;
use DR\Ipp\Operations\GetJobAttributesOperation;
use DR\Ipp\Protocol\IppOperation;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Throwable;

#[CoversClass(GetJobAttributesOperation::class)]
class GetJobAttributesOperationTest extends TestCase
{
    /**
     * @throws Throwable
     */
    public function testPrint(): void
    {
        $cups   = 'https://cups';
        $server = new IppServer();
        $server->setUri($cups);

        $client          = $this->createMock(IppHttpClientInterface::class);
        $parseFactory    = $this->createMock(ResponseParserFactoryInterface::class);
        $print           = new GetJobAttributesOperation($client, $parseFactory);
        $responseContent = 'test';

        $body = $this->createMock(StreamInterface::class);
        $body->method('getContents')->willReturn($responseContent);
        $response = $this->createMock(ResponseInterface::class);

        $printJob = new IppJob();
        $printJob->setUri($cups . '/jobs/1160');

        $client->expects($this->once())->method('sendRequest')->with(static::callback(static function (IppOperation $operation) {
            static::assertSame(IppOperationEnum::GetJobAttributes, $operation->getOperation());
            static::assertCount(0, $operation->getJobAttributes(), 'Job attribute count incorrect');
            static::assertCount(0, $operation->getPrinterAttributes(), 'Printer attribute count incorrect');
            static::assertCount(5, $operation->getOperationAttributes(), 'Operation attribute count incorrect');

            return true;
        }))->willReturn($response);

        static::assertIsString($printJob->getUri());
        $print->getJob($printJob->getUri());
    }
}
