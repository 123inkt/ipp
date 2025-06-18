<?php

declare(strict_types=1);

namespace DR\Ipp\Client;

use DR\Ipp\Entity\Response\IppResponseInterface;
use DR\Ipp\Protocol\IppOperation;
use Psr\Http\Client\ClientExceptionInterface;

interface HttpClientInterface
{
    /**
     * @throws ClientExceptionInterface
     */
    public function sendRequest(IppOperation $operation): IppResponseInterface;
}
