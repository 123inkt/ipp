<?php

declare(strict_types=1);

namespace DR\Ipp\Tests\Unit\Protocol;

use DateTime;
use DR\Ipp\Entity\Response\CupsIppResponse;
use DR\Ipp\Enum\IppOperationEnum;
use DR\Ipp\Enum\IppStatusCodeEnum;
use DR\Ipp\Enum\IppTypeEnum;
use DR\Ipp\Enum\JobStateEnum;
use DR\Ipp\Protocol\IppAttribute;
use DR\Ipp\Protocol\IppOperation;
use DR\Ipp\Protocol\IppResponseParser;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use RuntimeException;

#[CoversClass(IppResponseParser::class)]
class IppResponseParserTest extends TestCase
{
    public function testGetPrintJob(): void
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

        $parser = new IppResponseParser();

        /** @var CupsIppResponse $job */
        $job = $parser->getResponse((string)$response);
        static::assertSame(1, $job->getJobId());
        static::assertSame(JobStateEnum::Completed, $job->getJobState());
        static::assertSame('ok', $job->getStatusMessage());
        static::assertSame('test', $job->getJobUri());
        static::assertSame(IppStatusCodeEnum::SuccessfulOkConflictingAttributes, $job->getStatusCode());

        $attr = $job->getAttributes();
        static::assertNotNull($attr);
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
            $attr['attributes-natural-language']
        );
        static::assertEquals(new IppAttribute(IppTypeEnum::Int, 'job-id', 1), $attr['job-id']);
        static::assertEquals(new IppAttribute(IppTypeEnum::Enum, 'job-state', 9), $attr['job-state']);
        static::assertEquals(new IppAttribute(IppTypeEnum::Uri, 'job-uri', 'test'), $attr['job-uri']);
        static::assertEquals(new IppAttribute(IppTypeEnum::Keyword, 'status-message', 'ok'), $attr['status-message']);
        static::assertEquals(
            new IppAttribute(IppTypeEnum::DateTime, 'date', DateTime::createFromFormat('Y-m-d H:i:sO', '2024-04-10 01:01:01+0000')),
            $attr['date']
        );
    }

    public function testParseError(): void
    {
        $this->expectException(RuntimeException::class);
        $parser = new IppResponseParser();
        $parser->getResponse("");
    }

    public function testExceptionOnInvalidDate(): void
    {
        $this->expectException(RuntimeException::class);

        $binaryDate = pack('c*', 0xFF, 0x00, 0x04, 0x0A, 0x01, 0x01, 0x01, 0x01, 0x2B, 0x00, 0x00);

        $response = new IppOperation(IppOperationEnum::PrintJob);
        $response->addJobAttribute(new IppAttribute(IppTypeEnum::DateTime, 'date', $binaryDate));

        $parser = new IppResponseParser();
        $parser->getResponse((string)$response);
    }

    public function testExceptionOnUnPackableDate(): void
    {
        $this->expectException(RuntimeException::class);

        $binaryDate = pack('c*', 0x00);

        $response = new IppOperation(IppOperationEnum::PrintJob);
        $response->addJobAttribute(new IppAttribute(IppTypeEnum::DateTime, 'date', $binaryDate));

        $parser = new IppResponseParser();
        $parser->getResponse((string)$response);
    }
}
