<?php

declare(strict_types=1);

namespace DR\Ipp\Operations;

use DR\Ipp\Client\IppRequestException;
use DR\Ipp\Entity\Response\IppResponseInterface;
use Psr\Http\Client\ClientExceptionInterface;

interface GetPrintersInterface
{
    /**
     * @throws ClientExceptionInterface|IppRequestException
     */
    public function get(): IppResponseInterface;
}
