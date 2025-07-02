<?php

declare(strict_types=1);

namespace DR\Ipp\Tests\Unit\Client;

use DR\Ipp\Client\CupsIppHttpClient;
use DR\Ipp\Client\IppRequestException;
use DR\Ipp\Entity\IppServer;
use DR\Ipp\Enum\IppOperationEnum;
use DR\Ipp\Protocol\IppOperation;
use DR\Ipp\Protocol\IppResponseParser;
use Nyholm\Psr7\Request;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Throwable;

#[CoversClass(CupsIppHttpClient::class)]
class CupsIppHttpClientTest extends TestCase
{
    /**
     * @throws Throwable
     */
    public function testCupsRequest(): void
    {
        $server = $this->createMock(IppServer::class);
        $server->expects($this->once())->method('getUri')->willReturn('https://cups');
        $server->expects($this->once())->method('getUsername')->willReturn('unit');
        $server->expects($this->once())->method('getPassword')->willReturn('test');

        $responseStream = $this->createMock(StreamInterface::class);
        $responseStream->method('getContents')->willReturn('');
        $response = $this->createMock(ResponseInterface::class);
        $response->expects($this->once())->method('getBody')->willReturn($responseStream);

        $clientMock = $this->createMock(ClientInterface::class);
        $clientMock->expects($this->once())->method('sendRequest')->with(static::callback(static function (Request $request) {
            static::assertSame('POST', $request->getMethod());
            static::assertSame('cups', $request->getUri()->getHost());
            static::assertSame('/admin', $request->getUri()->getPath());
            static::assertSame(
                ['Host' => ['cups'], 'Content-Type' => ['application/ipp'], 'Authorization' => ['Basic dW5pdDp0ZXN0']],
                $request->getHeaders()
            );
            static::assertSame('unittest', $request->getBody()->getContents());

            return true;
        }))->willReturn($response);

        $parser = $this->createMock(IppResponseParser::class);
        $parser->expects($this->once())->method('getResponse');
        $client = new CupsIppHttpClient($server, $clientMock, $parser);

        $operation = $this->createMock(IppOperation::class);
        $operation->method('getOperation')->willReturn(IppOperationEnum::CupsAddModifyPrinter);
        $operation->method('__toString')->willReturn('unittest');

        $client->sendRequest($operation);
    }

    /**
     * @throws Throwable
     */
    public function testNormalRequest(): void
    {
        $server = $this->createMock(IppServer::class);
        $server->expects($this->once())->method('getUri')->willReturn('https://cups');

        $responseStream = $this->createMock(StreamInterface::class);
        $responseStream->method('getContents')->willReturn('');
        $response = $this->createMock(ResponseInterface::class);
        $response->expects($this->once())->method('getBody')->willReturn($responseStream);

        $clientMock = $this->createMock(ClientInterface::class);
        $clientMock->expects($this->once())->method('sendRequest')->with(static::callback(static function (Request $request) {
            static::assertSame('POST', $request->getMethod());
            static::assertSame('cups', $request->getUri()->getHost());
            static::assertSame('', $request->getUri()->getPath());
            static::assertSame(
                ['Host' => ['cups'], 'Content-Type' => ['application/ipp']],
                $request->getHeaders()
            );
            static::assertSame('unittest', $request->getBody()->getContents());

            return true;
        }))->willReturn($response);

        $parser = $this->createMock(IppResponseParser::class);
        $parser->expects($this->once())->method('getResponse');
        $client = new CupsIppHttpClient($server, $clientMock, $parser);

        $operation = $this->createMock(IppOperation::class);
        $operation->method('getOperation')->willReturn(IppOperationEnum::PrintJob);
        $operation->method('__toString')->willReturn('unittest');

        $client->sendRequest($operation);
    }

    /**
     * @throws Throwable
     */
    public function testException(): void
    {
        $this->expectException(IppRequestException::class);
        $this->expectExceptionMessage('500 (Internal server error): ');

        $server = $this->createMock(IppServer::class);
        $server->expects($this->once())->method('getUri')->willReturn('https://cups');

        $response = $this->createMock(ResponseInterface::class);
        $response->expects($this->exactly(3))->method('getStatusCode')->willReturn(500);
        $response->expects($this->once())->method('getReasonPhrase')->willReturn('Internal server error');
        $response->expects($this->once())->method('getBody')->willReturn($this->createMock(StreamInterface::class));

        $clientMock = $this->createMock(ClientInterface::class);
        $clientMock->expects($this->once())->method('sendRequest')->willReturn($response);
        $parser = $this->createMock(IppResponseParser::class);
        $client = new CupsIppHttpClient($server, $clientMock, $parser);

        $operation = $this->createMock(IppOperation::class);
        $operation->method('getOperation')->willReturn(IppOperationEnum::PrintJob);
        $operation->method('__toString')->willReturn('unittest');

        $client->sendRequest($operation);
    }
}
