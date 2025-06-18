<?php

declare(strict_types=1);

namespace DR\Ipp\Client;

use DR\Ipp\Entity\IppServer;
use DR\Ipp\Entity\Response\IppResponseInterface;
use DR\Ipp\Enum\IppOperationEnum;
use DR\Ipp\Protocol\IppOperation;
use DR\Ipp\Protocol\IppResponseParserInterface;
use Nyholm\Psr7\Request;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;

class CupsHttpClient implements HttpClientInterface
{
    public function __construct(
        private readonly IppServer $server,
        private readonly ClientInterface $client,
        private readonly IppResponseParserInterface $parser
    ) {
    }

    /**
     * @param IppOperation $operation
     *
     * @return IppResponseInterface
     * @throws ClientExceptionInterface
     */
    public function sendRequest(IppOperation $operation): IppResponseInterface
    {
        if ($operation->getOperation()->value >= IppOperationEnum::CupsGetDefault->value) {
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
        } else {
            $response = $this->client->sendRequest(
                new Request(
                    'POST',
                    $this->server->getUri(),
                    ['Content-Type' => 'application/ipp'],
                    (string)$operation
                )
            );
        }

        return $this->parser->getResponse($response->getBody()->getContents());
    }
}
