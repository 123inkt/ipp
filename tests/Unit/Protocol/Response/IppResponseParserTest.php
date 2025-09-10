<?php

declare(strict_types=1);

namespace DR\Ipp\Tests\Unit\Protocol\Response;

use DateTime;
use DR\Ipp\Entity\IppJob;
use DR\Ipp\Enum\IppOperationEnum;
use DR\Ipp\Enum\IppStatusCodeEnum;
use DR\Ipp\Enum\IppTypeEnum;
use DR\Ipp\Factory\IppJobFactory;
use DR\Ipp\Protocol\IppAttribute;
use DR\Ipp\Protocol\IppOperation;
use DR\Ipp\Protocol\Response\IppResponseParser;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use RuntimeException;

#[CoversClass(IppResponseParser::class)]
class IppResponseParserTest extends TestCase
{
    public function testGetResponse(): void
    {
        //                       year  year  month day   hour  min   sec  deci-sec  +  tzhour tzmin
        $binaryDate = pack('c*', 0x07, 0xE8, 0x04, 0x0A, 0x01, 0x01, 0x01, 0x01, 0x2B, 0x00, 0x00);

        $response = new IppOperation(IppOperationEnum::PrintJob);
        $response->addOperationAttribute(new IppAttribute(IppTypeEnum::Charset, 'attributes-charset', 'utf-8'));
        $response->addOperationAttribute(new IppAttribute(IppTypeEnum::NaturalLanguage, 'attributes-natural-language', 'en'));
        $response->addJobAttribute(new IppAttribute(IppTypeEnum::Int, 'job-id', 1));
        $response->addJobAttribute(new IppAttribute(IppTypeEnum::Enum, 'job-state', 9));
        $response->addJobAttribute(new IppAttribute(IppTypeEnum::Uri, 'job-uri', 'test'));
        $response->addJobAttribute(new IppAttribute(IppTypeEnum::Keyword, 'status-message', 'ok'));
        $response->addJobAttribute(new IppAttribute(IppTypeEnum::DateTime, 'date', $binaryDate));

        $job        = new IppJob();
        $jobFactory = $this->createMock(IppJobFactory::class);
        $jobFactory->method('create')->willReturn($job);
        $parser = new IppResponseParser($jobFactory);

        $body = $this->createMock(StreamInterface::class);
        $body->method('getContents')->willReturn((string)$response);
        $responseMock = $this->createMock(ResponseInterface::class);
        $responseMock->method('getBody')->willReturn($body);

        $ippResponse = $parser->getResponse($responseMock);
        static::assertSame('ok', $ippResponse->getStatusMessage());
        static::assertSame(IppStatusCodeEnum::SuccessfulOkConflictingAttributes, $ippResponse->getStatusCode());
        static::assertSame([$job], $ippResponse->getJobs());

        $attr = $ippResponse->getAttributes();
        static::assertCount(7, $attr);
        static::assertArrayHasKey('attributes-charset', $attr);
        static::assertArrayHasKey('attributes-natural-language', $attr);
        static::assertArrayHasKey('job-id', $attr);
        static::assertArrayHasKey('job-state', $attr);
        static::assertArrayHasKey('job-uri', $attr);
        static::assertArrayHasKey('status-message', $attr);
        static::assertArrayHasKey('date', $attr);
        static::assertEquals(new IppAttribute(IppTypeEnum::Charset, 'attributes-charset', 'utf-8'), $attr['attributes-charset']);
        static::assertEquals(
            new IppAttribute(IppTypeEnum::NaturalLanguage, 'attributes-natural-language', 'en'),
            $attr['attributes-natural-language'],
        );
        static::assertEquals(new IppAttribute(IppTypeEnum::Int, 'job-id', 1), $attr['job-id']);
        static::assertEquals(new IppAttribute(IppTypeEnum::Enum, 'job-state', 9), $attr['job-state']);
        static::assertEquals(new IppAttribute(IppTypeEnum::Uri, 'job-uri', 'test'), $attr['job-uri']);
        static::assertEquals(new IppAttribute(IppTypeEnum::Keyword, 'status-message', 'ok'), $attr['status-message']);
        static::assertEquals(
            new IppAttribute(IppTypeEnum::DateTime, 'date', DateTime::createFromFormat('Y-m-d H:i:sO', '2024-04-10 01:01:01+0000')),
            $attr['date'],
        );
    }

    public function testGetResponseDuplicateKeys(): void
    {
        $parser = new IppResponseParser($this->createMock(IppJobFactory::class));

        $response = new IppOperation(IppOperationEnum::PrintJob);
        $response->addJobAttribute(new IppAttribute(IppTypeEnum::Int, 'job-id', 1));
        $response->addJobAttribute(new IppAttribute(IppTypeEnum::Int, 'job-id', 2));
        $response->addJobAttribute(new IppAttribute(IppTypeEnum::Enum, 'job-state', 9));
        $response->addJobAttribute(new IppAttribute(IppTypeEnum::Int, 'job-id', 3));
        $response->addJobAttribute(new IppAttribute(IppTypeEnum::Enum, 'job-state', 9));

        $body = $this->createMock(StreamInterface::class);
        $body->method('getContents')->willReturn((string)$response);
        $responseMock = $this->createMock(ResponseInterface::class);
        $responseMock->method('getBody')->willReturn($body);

        $attributes = $parser->getResponse($responseMock)->getAttributes();
        static::assertArrayHasKey('job-id', $attributes);
        $value = $attributes['job-id']->getValue();
        static::assertIsArray($value);
        static::assertCount(3, $value);
        static::assertArrayHasKey(0, $value);
        static::assertSame(1, $value[0]);
        static::assertArrayHasKey(0, $value);
        static::assertSame(2, $value[1]);
        static::assertArrayHasKey(0, $value);
        static::assertSame(3, $value[2]);

        static::assertArrayHasKey('job-state', $attributes);
        $value = $attributes['job-state']->getValue();
        static::assertIsArray($value);
        static::assertArrayHasKey(0, $value);
        static::assertSame(9, $value[0]);
        static::assertArrayHasKey(1, $value);
        static::assertSame(9, $value[1]);
    }

    public function testParseError(): void
    {
        $body = $this->createMock(StreamInterface::class);
        $body->method('getContents')->willReturn("");
        $responseMock = $this->createMock(ResponseInterface::class);
        $responseMock->method('getBody')->willReturn($body);

        $this->expectException(RuntimeException::class);
        $parser = new IppResponseParser($this->createMock(IppJobFactory::class));
        $parser->getResponse($responseMock);
    }

    public function testExceptionOnInvalidDate(): void
    {
        $this->expectException(RuntimeException::class);

        $binaryDate = pack('c*', 0xFF, 0x00, 0x04, 0x0A, 0x01, 0x01, 0x01, 0x01, 0x2B, 0x00, 0x00);

        $response = new IppOperation(IppOperationEnum::PrintJob);
        $response->addJobAttribute(new IppAttribute(IppTypeEnum::DateTime, 'date', $binaryDate));

        $body = $this->createMock(StreamInterface::class);
        $body->method('getContents')->willReturn((string)$response);
        $responseMock = $this->createMock(ResponseInterface::class);
        $responseMock->method('getBody')->willReturn($body);

        $parser = new IppResponseParser($this->createMock(IppJobFactory::class));
        $parser->getResponse($responseMock);
    }

    public function testExceptionOnUnPackableDate(): void
    {
        $this->expectException(RuntimeException::class);

        $binaryDate = pack('c*', 0x00);

        $response = new IppOperation(IppOperationEnum::PrintJob);
        $response->addJobAttribute(new IppAttribute(IppTypeEnum::DateTime, 'date', $binaryDate));

        $body = $this->createMock(StreamInterface::class);
        $body->method('getContents')->willReturn((string)$response);
        $responseMock = $this->createMock(ResponseInterface::class);
        $responseMock->method('getBody')->willReturn($body);

        $parser = new IppResponseParser($this->createMock(IppJobFactory::class));
        $parser->getResponse($responseMock);
    }
}
