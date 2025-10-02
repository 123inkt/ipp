<?php

declare(strict_types=1);

namespace DR\Ipp\Operations\Cups;

use DR\Ipp\Client\IppHttpClientInterface;
use DR\Ipp\Entity\IppPrinter;
use DR\Ipp\Entity\IppServer;
use DR\Ipp\Entity\Response\IppResponseInterface;
use DR\Ipp\Enum\IppOperationEnum;
use DR\Ipp\Enum\IppTypeEnum;
use DR\Ipp\Factory\ResponseParserFactoryInterface;
use DR\Ipp\Operations\CreatePrinterInterface;
use DR\Ipp\Protocol\IppAttribute;
use DR\Ipp\Protocol\IppOperation;
use Psr\Http\Client\ClientExceptionInterface;

/**
 * @internal
 */
class CupsCreatePrinter implements CreatePrinterInterface
{
    public const IDLE = 0x03;

    public function __construct(
        private readonly IppServer $server,
        private readonly IppHttpClientInterface $client,
        private readonly ResponseParserFactoryInterface $parserFactory,
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
        if ($printer->getPpdName() !== null) {
            $operation->addPrinterAttribute(new IppAttribute(IppTypeEnum::NameWithoutLang, 'ppd-name', $printer->getPpdName()));
        }

        return $this->parserFactory->responseParser()->getResponse($this->client->sendRequest($operation));
    }
}
