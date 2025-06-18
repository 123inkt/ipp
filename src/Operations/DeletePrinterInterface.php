<?php

declare(strict_types=1);

namespace DR\Ipp\Operations;

use DR\Ipp\Entity\IppPrinter;
use DR\Ipp\Entity\Response\IppResponseInterface;
use Psr\Http\Client\ClientExceptionInterface;

interface DeletePrinterInterface
{
    /**
     * @throws ClientExceptionInterface
     */
    public function delete(IppPrinter $printer): IppResponseInterface;
}
