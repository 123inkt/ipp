<?php

declare(strict_types=1);

namespace DR\Ipp\Tests\Unit\Operations;

use DR\Ipp\Client\IppHttpClientInterface;
use DR\Ipp\Entity\IppServer;
use DR\Ipp\Entity\Response\IppResponseInterface;
use DR\Ipp\Enum\IppOperationEnum;
use DR\Ipp\Operations\GetJobAttributesOperation;
use DR\Ipp\Protocol\IppOperation;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
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
        $print           = new GetJobAttributesOperation($client);
        $responseContent = 'test';

        $body = $this->createMock(StreamInterface::class);
        $body->method('getContents')->willReturn($responseContent);
        $response = $this->createMock(IppResponseInterface::class);

        $printJob = $this->createMock(IppResponseInterface::class);
        $printJob->method('getJobUri')->willReturn($cups . '/jobs/1160');

        $client->expects($this->once())->method('sendRequest')->with(static::callback(static function (IppOperation $operation) {
            static::assertSame(IppOperationEnum::GetJobAttributes, $operation->getOperation());
            static::assertCount(0, $operation->getJobAttributes(), 'Job attribute count incorrect');
            static::assertCount(0, $operation->getPrinterAttributes(), 'Printer attribute count incorrect');
            static::assertCount(5, $operation->getOperationAttributes(), 'Operation attribute count incorrect');

            return true;
        }))->willReturn($response);

        static::assertIsString($printJob->getJobUri());
        $print->getJob($printJob->getJobUri());
    }
}
