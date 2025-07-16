<?php

declare(strict_types=1);

namespace DR\Ipp\Client;

use Exception;
use Psr\Http\Client\RequestExceptionInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class IppRequestException extends Exception implements RequestExceptionInterface
{
    public function __construct(private readonly RequestInterface $request, private readonly ResponseInterface $response)
    {
        parent::__construct(
            $this->response->getStatusCode() . ' (' . $this->response->getReasonPhrase() . '): ' . $this->response->getBody(),
            $this->response->getStatusCode(),
        );
    }

    public function getRequest(): RequestInterface
    {
        return $this->request;
    }

    public function getResponse(): ResponseInterface
    {
        return $this->response;
    }
}
