<?php

declare(strict_types=1);

namespace DR\Ipp\Factory;

use DR\Ipp\Protocol\Response\IppGetJobsResponseParser;
use DR\Ipp\Protocol\Response\IppResponseParser;
use DR\Ipp\Protocol\Response\IppResponseParserInterface;

/**
 * @internal
 */
class ResponseParserFactory implements ResponseParserFactoryInterface
{
    public function responseParser(): IppResponseParserInterface
    {
        return new IppResponseParser(new IppJobFactory());
    }

    public function JobResponseParser(): IppResponseParserInterface
    {
        return new IppGetJobsResponseParser(new IppJobFactory());
    }
}
