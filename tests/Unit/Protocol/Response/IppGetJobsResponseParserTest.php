<?php

declare(strict_types=1);

namespace DR\Ipp\Tests\Unit\Protocol\Response;

use DR\Ipp\Entity\IppJob;
use DR\Ipp\Enum\IppOperationEnum;
use DR\Ipp\Enum\IppStatusCodeEnum;
use DR\Ipp\Enum\IppTypeEnum;
use DR\Ipp\Factory\IppJobFactory;
use DR\Ipp\Protocol\IppAttribute;
use DR\Ipp\Protocol\IppOperation;
use DR\Ipp\Protocol\Response\IppGetJobsResponseParser;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

#[CoversClass(IppGetJobsResponseParser::class)]
class IppGetJobsResponseParserTest extends TestCase
{
    public function testGetResponse(): void
    {
        $response = new IppOperation(IppOperationEnum::PrintJob);
        $response->addOperationAttribute(new IppAttribute(IppTypeEnum::Charset, 'attributes-charset', 'utf-8'));
        $response->addOperationAttribute(new IppAttribute(IppTypeEnum::NaturalLanguage, 'attributes-natural-language', 'en'));
        $response->addJobAttribute(new IppAttribute(IppTypeEnum::Int, 'job-id', 1));
        $response->addJobAttribute(new IppAttribute(IppTypeEnum::Int, 'job-id', 2));
        $response->addJobAttribute(new IppAttribute(IppTypeEnum::Int, 'job-id', 3));

        $job        = new IppJob();
        $jobFactory = $this->createMock(IppJobFactory::class);
        $jobFactory->method('create')->willReturn($job);
        $parser = new IppGetJobsResponseParser($jobFactory);

        $body = $this->createMock(StreamInterface::class);
        $body->method('getContents')->willReturn((string)$response);
        $responseMock = $this->createMock(ResponseInterface::class);
        $responseMock->method('getBody')->willReturn($body);

        $ippResponse = $parser->getResponse($responseMock);
        static::assertSame(IppStatusCodeEnum::SuccessfulOkConflictingAttributes, $ippResponse->getStatusCode());
        static::assertSame([$job, $job, $job], $ippResponse->getJobs());
    }
}
