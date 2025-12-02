<?php

declare(strict_types=1);

namespace DR\Ipp\Operations;

use DR\Ipp\Client\IppHttpClientInterface;
use DR\Ipp\Client\IppRequestException;
use DR\Ipp\Entity\IppPrinter;
use DR\Ipp\Entity\IppServer;
use DR\Ipp\Entity\Response\IppResponseInterface;
use DR\Ipp\Enum\IppOperationEnum;
use DR\Ipp\Enum\IppTypeEnum;
use DR\Ipp\Factory\ResponseParserFactoryInterface;
use DR\Ipp\Protocol\IppAttribute;
use DR\Ipp\Protocol\IppOperation;
use Psr\Http\Client\ClientExceptionInterface;

/**
 * @internal
 */
class GetJobsOperation
{
    public function __construct(
        private readonly IppServer $server,
        private readonly IppHttpClientInterface $client,
        private readonly ResponseParserFactoryInterface $parserFactory,
    ) {
    }

    /**
     * @throws ClientExceptionInterface|IppRequestException
     */
    public function getJobList(IppPrinter $printer, bool $completed = false): IppResponseInterface
    {
        $printerUri = $this->server->getUri() . '/printers/' . $printer->getHostname();

        $operation = new IppOperation(IppOperationEnum::GetJobList);
        $operation->addOperationAttribute(new IppAttribute(IppTypeEnum::Charset, 'attributes-charset', 'utf-8'));
        $operation->addOperationAttribute(new IppAttribute(IppTypeEnum::NaturalLanguage, 'attributes-natural-language', 'en'));
        $operation->addOperationAttribute(new IppAttribute(IppTypeEnum::Uri, 'printer-uri', $printerUri));
        $operation->addOperationAttribute(new IppAttribute(IppTypeEnum::Keyword, 'requested-attributes', 'all'));
        $operation->addOperationAttribute(new IppAttribute(IppTypeEnum::Keyword, 'which-jobs', $completed ? 'completed' : 'not-completed'));

        return $this->parserFactory->jobResponseParser()->getResponse($this->client->sendRequest($operation));
    }
}
