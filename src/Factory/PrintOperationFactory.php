<?php

declare(strict_types=1);

namespace DR\Ipp\Factory;

use DR\Ipp\Entity\IppServer;
use DR\Ipp\Operations\PrintOperation;
use DR\Ipp\Protocol\IppResponseParserInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Log\LoggerInterface;

/**
 * @internal
 */
class PrintOperationFactory
{
    public function create(IppServer $server, ClientInterface $client, IppResponseParserInterface $parser, ?LoggerInterface $logger): PrintOperation
    {
        $printOperation = new PrintOperation($server, $client, $parser);
        if ($logger !== null) {
            $printOperation->setLogger($logger);
        }

        return $printOperation;
    }
}
