<?php

declare(strict_types=1);

namespace DR\Ipp\Operations;

use DR\Ipp\Entity\Response\IppResponseInterface;
use Psr\Http\Client\ClientExceptionInterface;

interface GetPrintersInterface
{
    /**
     * @throws ClientExceptionInterface
     */
    public function getPrinters(): IppResponseInterface;
}
