<?php

declare(strict_types=1);

namespace DR\Ipp\Factory;

use DR\Ipp\Client\IppHttpClientInterface;
use DR\Ipp\Entity\IppServer;
use DR\Ipp\Operations\PrintOperation;
use Psr\Log\LoggerInterface;

/**
 * @internal
 */
class PrintOperationFactory
{
    public function create(IppServer $server, IppHttpClientInterface $httpClient, ?LoggerInterface $logger): PrintOperation
    {
        $printOperation = new PrintOperation($server, $httpClient);
        if ($logger !== null) {
            $printOperation->setLogger($logger);
        }

        return $printOperation;
    }
}
