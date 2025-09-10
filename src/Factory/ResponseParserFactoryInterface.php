<?php

declare(strict_types=1);

namespace DR\Ipp\Factory;

use DR\Ipp\Protocol\Response\IppResponseParserInterface;

interface ResponseParserFactoryInterface
{
    public function responseParser(): IppResponseParserInterface;

    public function JobResponseParser(): IppResponseParserInterface;
}
