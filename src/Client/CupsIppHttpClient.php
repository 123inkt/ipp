<?php

declare(strict_types=1);

namespace DR\Ipp\Client;

use DR\Ipp\Entity\IppServer;
use DR\Ipp\Enum\IppOperationEnum;
use DR\Ipp\Protocol\IppOperation;
use Nyholm\Psr7\Request;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\ResponseInterface;

class CupsIppHttpClient implements IppHttpClientInterface
{
    private const int HTTP_STATUS_BAD_REQUEST = 400;

    public function __construct(private readonly IppServer $server, private readonly ClientInterface $client)
    {
    }

    /**
     * @param IppOperation $operation
     *
     * @throws ClientExceptionInterface
     */
    public function sendRequest(IppOperation $operation): ResponseInterface
    {
        if ($operation->getOperation()->value >= IppOperationEnum::CupsGetDefault->value) {
            $url = $this->server->getUri() . '/admin';
        } else {
            $url = $this->server->getUri();
        }

        $headers = ['Content-Type' => 'application/ipp'];
        if ($this->server->hasCredentials()) {
            $headers['Authorization'] = 'Basic ' . base64_encode($this->server->getUsername() . ":" . $this->server->getPassword());
        }
        $request  = new Request('POST', $url, $headers, (string)$operation);
        $response = $this->client->sendRequest($request);

        // a server error won't be an ipp response we can parse.
        if ($response->getStatusCode() >= self::HTTP_STATUS_BAD_REQUEST) {
            throw new IppRequestException($request, $response);
        }

        return $response;
    }
}
