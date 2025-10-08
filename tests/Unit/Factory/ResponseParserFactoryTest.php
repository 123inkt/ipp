<?php

declare(strict_types=1);

namespace DR\Ipp\Tests\Unit\Factory;

use DR\Ipp\Factory\ResponseParserFactory;
use DR\Ipp\Protocol\Response\IppGetJobsResponseParser;
use DR\Ipp\Protocol\Response\IppResponseParser;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(ResponseParserFactory::class)]
class ResponseParserFactoryTest extends TestCase
{
    public function testClass(): void
    {
        $factory = new ResponseParserFactory();
        static::assertInstanceOf(IppResponseParser::class, $factory->responseParser());
        static::assertInstanceOf(IppGetJobsResponseParser::class, $factory->jobResponseParser());
        static::assertInstanceOf(IppGetJobsResponseParser::class, $factory->printerResponseParser());
    }
}
