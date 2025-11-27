<?php

declare(strict_types=1);

namespace DR\Ipp\Operations;

use DR\Ipp\Client\IppHttpClientInterface;
use DR\Ipp\Client\IppRequestException;
use DR\Ipp\Entity\IppJob;
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
class GetJobAttributesOperation
{
    public function __construct(private readonly IppHttpClientInterface $client, private readonly ResponseParserFactoryInterface $parserFactory)
    {
    }

    /**
     * @throws ClientExceptionInterface|IppRequestException
     */
    public function getJob(IppJob $job): IppResponseInterface
    {
        $operation = new IppOperation(IppOperationEnum::GetJobAttributes);
        $operation->addOperationAttribute(new IppAttribute(IppTypeEnum::Charset, 'attributes-charset', 'utf-8'));
        $operation->addOperationAttribute(new IppAttribute(IppTypeEnum::NaturalLanguage, 'attributes-natural-language', 'en'));
        $operation->addOperationAttribute(new IppAttribute(IppTypeEnum::Uri, 'job-uri', $job->getUri()));
        $operation->addOperationAttribute(new IppAttribute(IppTypeEnum::Keyword, 'which-jobs', 'all'));
        $operation->addOperationAttribute(new IppAttribute(IppTypeEnum::Keyword, 'requested-attributes', 'all'));

        return $this->parserFactory->jobResponseParser()->getResponse($this->client->sendRequest($operation));
    }
}
