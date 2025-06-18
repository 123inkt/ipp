<?php

declare(strict_types=1);

namespace DR\Ipp\Tests\Unit\Operations;

use DR\Ipp\Client\HttpClientInterface;
use DR\Ipp\Entity\IppPrinter;
use DR\Ipp\Entity\IppPrintFile;
use DR\Ipp\Entity\IppServer;
use DR\Ipp\Entity\Response\IppResponseInterface;
use DR\Ipp\Enum\FileTypeEnum;
use DR\Ipp\Enum\IppAttributeTypeEnum;
use DR\Ipp\Enum\IppOperationEnum;
use DR\Ipp\Enum\IppTypeEnum;
use DR\Ipp\Operations\PrintOperation;
use DR\Ipp\Protocol\IppAttribute;
use DR\Ipp\Protocol\IppOperation;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
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

        $client = $this->createMock(HttpClientInterface::class);
        $print  = new PrintOperation($server, $client);

        $fileData        = 'test';
        $responseContent = 'test';

        $body = $this->createMock(StreamInterface::class);
        $body->method('getContents')->willReturn($responseContent);
        $response = $this->createMock(IppResponseInterface::class);

        $client->expects($this->once())->method('sendRequest')->with(static::callback(static function (IppOperation $operation) {
            static::assertSame(IppOperationEnum::PrintJob, $operation->getOperation());
            static::assertCount(4, $operation->getJobAttributes(), 'Job attribute count incorrect');
            static::assertCount(0, $operation->getPrinterAttributes(), 'Printer attribute count incorrect');
            static::assertCount(5, $operation->getOperationAttributes(), 'Operation attribute count incorrect');
            static::assertNotEmpty($operation->getFileData());

            return true;
        }))->willReturn($response);

        $printer = new IppPrinter();
        $printer->setHostname('test');
        $printer->setDeviceUri('socket://10.10.10.10:9100');
        $printer->setLocation('location');
        $printer->setTrayName('tray1');

        $file = new IppPrintFile($fileData, FileTypeEnum::ZPL, 'test-print-file', 1);
        $file->addAttribute(IppAttributeTypeEnum::OperationAttribute, new IppAttribute(IppTypeEnum::Int, 'vendor-specific', 1));
        $file->addAttribute(IppAttributeTypeEnum::JobAttribute, new IppAttribute(IppTypeEnum::Int, 'vendor-specific', 2));

        $print->print($printer, $file);
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
        $client = $this->createMock(HttpClientInterface::class);
        $print  = new PrintOperation($server, $client);
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
