<?php

declare(strict_types=1);

namespace DR\Ipp\Tests\Unit\Client;

use DR\Ipp\Client\IppRequestException;
use Nyholm\Psr7\Request;
use Nyholm\Psr7\Response;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(IppRequestException::class)]
class IppRequestExceptionTest extends TestCase
{
    public function test(): void
    {
        $request  = new Request('GET', '');
        $response = new Response();

        $exception = new IppRequestException($request, $response);
        static::assertSame($request, $exception->getRequest());
        static::assertSame($response, $exception->getResponse());
    }
}
