<?php

declare(strict_types=1);

namespace DR\Ipp\Operations;

use DR\Ipp\Entity\IppPrinter;
use DR\Ipp\Entity\IppServer;
use DR\Ipp\Entity\Response\IppResponseInterface;
use DR\Ipp\Enum\IppOperationEnum;
use DR\Ipp\Enum\IppTypeEnum;
use DR\Ipp\Protocol\IppAttribute;
use DR\Ipp\Protocol\IppOperation;
use DR\Ipp\Protocol\IppResponseParserInterface;
use Nyholm\Psr7\Request;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;

/**
 * @internal
 */
class CupsCreatePrinter
{
    public const IDLE = 0x03;

    public function __construct(
        private readonly IppServer $server,
        private readonly ClientInterface $client,
        private readonly IppResponseParserInterface $parser
    ) {
    }

    /**
     * @throws ClientExceptionInterface
     */
    public function create(IppPrinter $printer): IppResponseInterface
    {
        $printerUri = $this->server->getUri() . '/printers/' . $printer->getHostname();

        $operation = new IppOperation(IppOperationEnum::CupsAddModifyPrinter);

        $operation->addOperationAttribute(new IppAttribute(IppTypeEnum::Charset, 'attributes-charset', 'utf-8'));
        $operation->addOperationAttribute(new IppAttribute(IppTypeEnum::NaturalLanguage, 'attributes-natural-language', 'en'));
        $operation->addOperationAttribute(new IppAttribute(IppTypeEnum::Uri, 'printer-uri', $printerUri));
        $operation->addOperationAttribute(new IppAttribute(IppTypeEnum::NameWithoutLang, 'requesting-user-name', $this->server->getUsername()));

        $operation->addPrinterAttribute(new IppAttribute(IppTypeEnum::Enum, 'printer-state', self::IDLE));
        $operation->addPrinterAttribute(new IppAttribute(IppTypeEnum::Bool, 'printer-is-accepting-jobs', true));
        $operation->addPrinterAttribute(new IppAttribute(IppTypeEnum::Uri, 'device-uri', $printer->getDeviceUri()));
        $operation->addPrinterAttribute(new IppAttribute(IppTypeEnum::TextWithoutLang, 'printer-location', $printer->getLocation()));

        $response = $this->client->sendRequest(
            new Request(
                'POST',
                $this->server->getUri() . '/admin',
                [
                    'Content-Type'  => 'application/ipp',
                    'Authorization' => 'Basic ' . base64_encode($this->server->getUsername() . ":" . $this->server->getPassword())
                ],
                (string)$operation
            )
        );

        return $this->parser->getResponse($response->getBody()->getContents());
    }
}
