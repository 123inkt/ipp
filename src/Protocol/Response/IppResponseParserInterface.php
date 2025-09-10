<?php

declare(strict_types=1);

namespace DR\Ipp\Protocol\Response;

use DR\Ipp\Entity\Response\IppResponseInterface;
use Psr\Http\Message\ResponseInterface;

interface IppResponseParserInterface
{
    public function getResponse(ResponseInterface $response): IppResponseInterface;
}
