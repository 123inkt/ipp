<?php

declare(strict_types=1);

namespace DR\Ipp\Operations;

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
class GetJobAttributesOperation
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
    public function getJob(string $jobUri): IppResponseInterface
    {
        $operation = new IppOperation(IppOperationEnum::GetJobAttributes);
        $operation->addOperationAttribute(new IppAttribute(IppTypeEnum::Charset, 'attributes-charset', 'utf-8'));
        $operation->addOperationAttribute(new IppAttribute(IppTypeEnum::NaturalLanguage, 'attributes-natural-language', 'en'));
        $operation->addOperationAttribute(new IppAttribute(IppTypeEnum::Uri, 'job-uri', $jobUri));
        $operation->addOperationAttribute(new IppAttribute(IppTypeEnum::Keyword, 'which-jobs', 'all'));
        $operation->addOperationAttribute(new IppAttribute(IppTypeEnum::Keyword, 'requested-attributes', 'all'));

        $response = $this->client->sendRequest(
            new Request(
                'POST',
                $this->server->getUri(),
                ['Content-Type' => 'application/ipp'],
                (string)$operation
            )
        );

        return $this->parser->getResponse($response->getBody()->getContents());
    }
}
