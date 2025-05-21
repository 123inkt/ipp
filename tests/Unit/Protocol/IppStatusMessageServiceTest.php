<?php

declare(strict_types=1);

namespace DR\Ipp\Tests\Unit\Protocol;

use DR\Ipp\Enum\IppStatusCodeEnum;
use DR\Ipp\Protocol\IppStatusMessageService;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(IppStatusMessageService::class)]
class IppStatusMessageServiceTest extends TestCase
{
    public function testGetStatusMessage(): void
    {
        static::assertIsString(IppStatusMessageService::getStatusMessage(IppStatusCodeEnum::ClientErrorBadRequest));
        static::assertNull(IppStatusMessageService::getStatusMessage(IppStatusCodeEnum::SuccessfulOk));
    }
}
