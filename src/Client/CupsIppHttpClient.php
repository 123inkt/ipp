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

class CupsIppHttpClient implements IppHttpClientInterface
{
    private const int HTTP_STATUS_SERVER_ERROR = 500;

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
            $request = new Request(
                'POST',
                $this->server->getUri() . '/admin',
                [
                    'Content-Type'  => 'application/ipp',
                    'Authorization' => 'Basic ' . base64_encode($this->server->getUsername() . ":" . $this->server->getPassword())
                ],
                (string)$operation
            );
        } else {
            $request = new Request(
                'POST',
                $this->server->getUri(),
                ['Content-Type' => 'application/ipp'],
                (string)$operation
            );
        }

        $response = $this->client->sendRequest($request);

        // a server error won't be an ipp response we can parse.
        if ($response->getStatusCode() >= self::HTTP_STATUS_SERVER_ERROR) {
            throw new IppRequestException($request, $response);
        }

        return $this->parser->getResponse($response->getBody()->getContents());
    }
}
