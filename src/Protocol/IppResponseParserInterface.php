<?php

declare(strict_types=1);

namespace DR\Ipp\Protocol;

use DR\Ipp\Entity\Response\IppResponseInterface;

interface IppResponseParserInterface
{
    public function getResponse(string $response): IppResponseInterface;
}
