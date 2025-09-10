<?php

declare(strict_types=1);

namespace DR\Ipp\Client;

use DR\Ipp\Protocol\IppOperation;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Message\ResponseInterface;

interface IppHttpClientInterface
{
    /**
     * @throws ClientExceptionInterface
     */
    public function sendRequest(IppOperation $operation): ResponseInterface;
}
