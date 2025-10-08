<?php

declare(strict_types=1);

namespace DR\Ipp\Tests\Unit\Entity\Response;

use DR\Ipp\Entity\IppJob;
use DR\Ipp\Entity\IppPrinter;
use DR\Ipp\Entity\Response\CupsIppResponse;
use DR\Ipp\Enum\IppStatusCodeEnum;
use DR\Ipp\Enum\IppTypeEnum;
use DR\Ipp\Protocol\IppAttribute;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(CupsIppResponse::class)]
class CupsIppResponseTest extends TestCase
{
    public function testGetAttributes(): void
    {
        $attribute = new IppAttribute(IppTypeEnum::Int, '', 0);
        $response  = new CupsIppResponse(IppStatusCodeEnum::SuccessfulOk, ['status-code' => $attribute], [], []);
        static::assertSame(['status-code' => $attribute], $response->getAttributes());
    }

    public function testGetStatusCode(): void
    {
        $response = new CupsIppResponse(IppStatusCodeEnum::SuccessfulOk, [], [], []);
        static::assertSame(IppStatusCodeEnum::SuccessfulOk, $response->getStatusCode());
    }

    public function testGetStatusMessage(): void
    {
        $attributes = ['status-message' => new IppAttribute(IppTypeEnum::Int, 'status-message', 'test')];
        $response   = new CupsIppResponse(IppStatusCodeEnum::SuccessfulOk, $attributes, [], []);
        static::assertSame('test', $response->getStatusMessage());

        $response = new CupsIppResponse(IppStatusCodeEnum::ClientErrorGone, [], [], []);
        static::assertSame('Gone', $response->getStatusMessage());

        $response = new CupsIppResponse(IppStatusCodeEnum::SuccessfulOk, [], [], []);
        static::assertNull($response->getStatusMessage());
    }

    public function testGetJobs(): void
    {
        $job      = new IppJob();
        $response = new CupsIppResponse(IppStatusCodeEnum::SuccessfulOk, [], [$job], []);
        static::assertSame([$job], $response->getJobs());
    }

    public function testGetPrinters(): void
    {
        $printer  = new IppPrinter();
        $response = new CupsIppResponse(IppStatusCodeEnum::SuccessfulOk, [], [], [$printer]);
        static::assertSame([$printer], $response->getPrinters());
    }
}
