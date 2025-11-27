<?php

declare(strict_types=1);

namespace DR\Ipp\Operations\Cups;

use DR\Ipp\Client\IppHttpClientInterface;
use DR\Ipp\Client\IppRequestException;
use DR\Ipp\Entity\Response\IppResponseInterface;
use DR\Ipp\Enum\IppOperationEnum;
use DR\Ipp\Enum\IppTypeEnum;
use DR\Ipp\Factory\ResponseParserFactoryInterface;
use DR\Ipp\Operations\GetPrintersInterface;
use DR\Ipp\Protocol\IppAttribute;
use DR\Ipp\Protocol\IppOperation;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;

/**
 * @internal
 */
class CupsGetPrinters implements LoggerAwareInterface, GetPrintersInterface
{
    use LoggerAwareTrait;

    public function __construct(private readonly IppHttpClientInterface $client, private readonly ResponseParserFactoryInterface $parserFactory)
    {
    }

    /**
     * @throws ClientExceptionInterface|IppRequestException
     */
    public function get(): IppResponseInterface
    {
        $operation = new IppOperation(IppOperationEnum::CupsGetPrinters);
        $operation->addOperationAttribute(new IppAttribute(IppTypeEnum::Charset, 'attributes-charset', 'utf-8'));
        $operation->addOperationAttribute(new IppAttribute(IppTypeEnum::NaturalLanguage, 'attributes-natural-language', 'en'));

        return $this->parserFactory->printerResponseParser()->getResponse($this->client->sendRequest($operation));
    }
}
