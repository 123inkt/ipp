<?php

declare(strict_types=1);

namespace DR\Ipp\Tests\Unit\Operations;

use DR\Ipp\Entity\IppPrinter;
use DR\Ipp\Entity\IppPrintFile;
use DR\Ipp\Entity\IppServer;
use DR\Ipp\Entity\Response\CupsIppResponse;
use DR\Ipp\Enum\FileTypeEnum;
use DR\Ipp\Enum\IppAttributeTypeEnum;
use DR\Ipp\Enum\IppStatusCodeEnum;
use DR\Ipp\Enum\IppTypeEnum;
use DR\Ipp\Enum\JobStateEnum;
use DR\Ipp\Operations\PrintOperation;
use DR\Ipp\Protocol\IppAttribute;
use DR\Ipp\Protocol\IppResponseParserInterface;
use Nyholm\Psr7\Request;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Throwable;

#[CoversClass(PrintOperation::class)]
class PrintOperationTest extends TestCase
{
    /**
     * @throws Throwable
     */
    public function testPrint(): void
    {
        $cups   = 'https://cups';
        $server = new IppServer();
        $server->setUri($cups);

        $client = $this->createMock(ClientInterface::class);
        $parser = $this->createMock(IppResponseParserInterface::class);
        $print  = new PrintOperation($server, $client, $parser);

        $fileData        = 'test';
        $responseContent = 'test';

        $body = $this->createMock(StreamInterface::class);
        $body->method('getContents')->willReturn($responseContent);
        $response = $this->createMock(ResponseInterface::class);
        $response->method('getBody')->willReturn($body);

        $client->expects($this->once())->method('sendRequest')->with(static::callback(static function (Request $request) {
            static::assertSame('POST', $request->getMethod());
            static::assertSame('https://cups', $request->getUri()->__toString());
            static::assertSame(
                ['Host' => ['cups'], 'Content-Type' => ['application/ipp']],
                $request->getHeaders()
            );

            return true;
        }))->willReturn($response);

        $attributes = [
            'job-id'         => new IppAttribute(IppTypeEnum::Int, 'job-id', 12),
            'job-state'      => new IppAttribute(IppTypeEnum::Enum, 'job-state', JobStateEnum::Failed->value),
            'status-message' => new IppAttribute(IppTypeEnum::Keyword, 'status-message', 'test')
        ];
        $parser->method('getResponse')->with($responseContent)->willReturn(
            new CupsIppResponse(IppStatusCodeEnum::Unknown, $attributes)
        );

        $printer = new IppPrinter();
        $printer->setHostname('test');
        $printer->setDeviceUri('socket://10.10.10.10:9100');
        $printer->setLocation('location');
        $printer->setTrayName('tray1');

        $file = new IppPrintFile($fileData, FileTypeEnum::ZPL, 'test-print-file', 1);
        $file->addAttribute(IppAttributeTypeEnum::OperationAttribute, new IppAttribute(IppTypeEnum::Int, 'vendor-specific', 1));
        $file->addAttribute(IppAttributeTypeEnum::JobAttribute, new IppAttribute(IppTypeEnum::Int, 'vendor-specific', 2));

        /** @var CupsIppResponse $job */
        $job = $print->print($printer, $file);
        static::assertSame(12, $job->getJobId());
        static::assertSame('test', $job->getStatusMessage());
        static::assertSame(JobStateEnum::Failed, $job->getJobState());
    }

    /**
     * @throws Throwable
     */
    #[DataProvider('fileTypeProvider')]
    public function testFileTypeLookup(FileTypeEnum $fileType, string $expected): void
    {
        $cups   = 'https://cups';
        $server = new IppServer();
        $server->setUri($cups);
        $client = $this->createMock(ClientInterface::class);
        $parser = $this->createMock(IppResponseParserInterface::class);
        $print  = new PrintOperation($server, $client, $parser);
        static::assertSame($expected, $print->fileTypeLookup($fileType));
    }

    /**
     * @return array<int|string, mixed>
     */
    public static function fileTypeProvider(): array
    {
        return [
            [FileTypeEnum::PDF, 'application/pdf'],
            [FileTypeEnum::PCL, 'application/vnd.hp-pcl'],
            [FileTypeEnum::ZPL, 'application/octet-stream'],
            [FileTypeEnum::PS, 'application/postscript'],
            [FileTypeEnum::JPG, 'image/jpeg'],
            [FileTypeEnum::PNG, 'image/png']
        ];
    }
}
