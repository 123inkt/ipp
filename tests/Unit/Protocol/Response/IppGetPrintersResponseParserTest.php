<?php

declare(strict_types=1);

namespace DR\Ipp\Tests\Unit\Protocol\Response;

use DR\Ipp\Entity\IppPrinter;
use DR\Ipp\Enum\IppOperationEnum;
use DR\Ipp\Enum\IppStatusCodeEnum;
use DR\Ipp\Enum\IppTypeEnum;
use DR\Ipp\Factory\IppPrinterFactory;
use DR\Ipp\Protocol\IppAttribute;
use DR\Ipp\Protocol\IppOperation;
use DR\Ipp\Protocol\Response\IppGetPrintersResponseParser;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

#[CoversClass(IppGetPrintersResponseParser::class)]
class IppGetPrintersResponseParserTest extends TestCase
{
    public function testGetResponse(): void
    {
        $response = new IppOperation(IppOperationEnum::PrintJob);
        $response->addOperationAttribute(new IppAttribute(IppTypeEnum::Charset, 'attributes-charset', 'utf-8'));
        $response->addOperationAttribute(new IppAttribute(IppTypeEnum::NaturalLanguage, 'attributes-natural-language', 'en'));
        $response->addJobAttribute(new IppAttribute(IppTypeEnum::Int, 'printer-name', '1'));
        $response->addJobAttribute(new IppAttribute(IppTypeEnum::Int, 'printer-name', '2'));
        $response->addJobAttribute(new IppAttribute(IppTypeEnum::Int, 'printer-name', '3'));

        $printer        = new IppPrinter();
        $printerFactory = $this->createMock(IppPrinterFactory::class);
        $printerFactory->method('create')->willReturn($printer);
        $parser = new IppGetPrintersResponseParser($printerFactory);

        $body = $this->createMock(StreamInterface::class);
        $body->method('getContents')->willReturn((string)$response);
        $responseMock = $this->createMock(ResponseInterface::class);
        $responseMock->method('getBody')->willReturn($body);

        $ippResponse = $parser->getResponse($responseMock);
        static::assertSame(IppStatusCodeEnum::SuccessfulOkConflictingAttributes, $ippResponse->getStatusCode());
        static::assertSame([$printer, $printer, $printer], $ippResponse->getPrinters());
    }
}
