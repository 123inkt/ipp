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
class CupsDeletePrinter
{
    public function __construct(
        private readonly IppServer $server,
        private readonly ClientInterface $client,
        private readonly IppResponseParserInterface $parser
    ) {
    }

    /**
     * @throws ClientExceptionInterface
     */
    public function delete(IppPrinter $printer): IppResponseInterface
    {
        $printerUri = $this->server->getUri() . '/printers/' . $printer->getHostname();

        $operation = new IppOperation(IppOperationEnum::CupsDeletePrinter);

        $operation->addOperationAttribute(new IppAttribute(IppTypeEnum::Charset, 'attributes-charset', 'utf-8'));
        $operation->addOperationAttribute(new IppAttribute(IppTypeEnum::NaturalLanguage, 'attributes-natural-language', 'en'));
        $operation->addOperationAttribute(new IppAttribute(IppTypeEnum::Uri, 'printer-uri', $printerUri));

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
