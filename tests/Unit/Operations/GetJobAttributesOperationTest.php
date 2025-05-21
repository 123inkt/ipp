<?php

declare(strict_types=1);

namespace DR\Ipp\Tests\Unit\Operations;

use DR\Ipp\Entity\IppServer;
use DR\Ipp\Entity\Response\CupsIppResponse;
use DR\Ipp\Enum\IppStatusCodeEnum;
use DR\Ipp\Enum\IppTypeEnum;
use DR\Ipp\Enum\JobStateEnum;
use DR\Ipp\Operations\GetJobAttributesOperation;
use DR\Ipp\Protocol\IppAttribute;
use DR\Ipp\Protocol\IppResponseParserInterface;
use Nyholm\Psr7\Request;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Psr\Http\Client\ClientInterface;
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

        $client          = $this->createMock(ClientInterface::class);
        $parser          = $this->createMock(IppResponseParserInterface::class);
        $print           = new GetJobAttributesOperation($server, $client, $parser);
        $responseContent = 'test';

        $body = $this->createMock(StreamInterface::class);
        $body->method('getContents')->willReturn($responseContent);
        $response = $this->createMock(ResponseInterface::class);
        $response->method('getBody')->willReturn($body);

        $attributes   = [
            'job-id'    => new IppAttribute(IppTypeEnum::Int, 'job-id', 1160),
            'job-uri'   => new IppAttribute(IppTypeEnum::Int, 'job-uri', $cups . '/jobs/1160'),
            'job-state' => new IppAttribute(IppTypeEnum::Enum, 'job-state', JobStateEnum::Completed->value)
        ];
        $cupsPrintJob = new CupsIppResponse(IppStatusCodeEnum::SuccessfulOk, $attributes);

        $client->expects($this->once())->method('sendRequest')->with(static::callback(static function (Request $request) {
            static::assertSame('POST', $request->getMethod());
            static::assertSame('https://cups', $request->getUri()->__toString());
            static::assertSame(['Host' => ['cups'], 'Content-Type' => ['application/ipp']], $request->getHeaders());

            return true;
        }))->willReturn($response);
        $parser->method('getResponse')->with($responseContent)->willReturn($cupsPrintJob);

        static::assertNotNull($cupsPrintJob->getJobUri());
        /** @var CupsIppResponse $job */
        $job = $print->getJob($cupsPrintJob->getJobUri());
        static::assertSame(1160, $job->getJobId());
        static::assertSame($cups . '/jobs/1160', $job->getJobUri());
        static::assertNull($job->getStatusMessage());
        static::assertSame(JobStateEnum::Completed, $job->getJobState());
    }
}
