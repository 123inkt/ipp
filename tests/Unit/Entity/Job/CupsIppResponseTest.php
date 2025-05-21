<?php

declare(strict_types=1);

namespace DR\Ipp\Tests\Unit\Entity\Job;

use DigitalRevolution\AccessorPairConstraint\AccessorPairAsserter;
use DR\Ipp\Entity\Response\CupsIppResponse;
use DR\Ipp\Enum\IppStatusCodeEnum;
use DR\Ipp\Enum\IppTypeEnum;
use DR\Ipp\Enum\JobStateEnum;
use DR\Ipp\Protocol\IppAttribute;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Throwable;

#[CoversClass(CupsIppResponse::class)]
class CupsIppResponseTest extends TestCase
{
    use AccessorPairAsserter;

    public function test(): void
    {
        self::assertAccessorPairs(CupsIppResponse::class);
    }

    /**
     * @throws Throwable
     */
    public function testGetJobState(): void
    {
        $job = new CupsIppResponse(IppStatusCodeEnum::Unknown, []);
        static::assertSame(JobStateEnum::Failed, $job->getJobState());

        $attributes = ['job-state' => new IppAttribute(IppTypeEnum::Enum, 'job-state', JobStateEnum::Completed->value)];
        $job        = new CupsIppResponse(IppStatusCodeEnum::SuccessfulOk, $attributes);
        static::assertSame(JobStateEnum::Completed, $job->getJobState());

        $job = new CupsIppResponse(IppStatusCodeEnum::SuccessfulOk, []);
        static::assertNull($job->getJobState());
    }

    public function testGetJobId(): void
    {
        $attributes = ['job-id' => new IppAttribute(IppTypeEnum::Int, 'job-id', 1)];
        $job        = new CupsIppResponse(IppStatusCodeEnum::SuccessfulOk, $attributes);
        static::assertSame(1, $job->getJobId());
    }

    public function testGetJobUri(): void
    {
        $attributes = ['job-uri' => new IppAttribute(IppTypeEnum::Int, 'job-uri', 'test')];
        $job        = new CupsIppResponse(IppStatusCodeEnum::SuccessfulOk, $attributes);
        static::assertSame('test', $job->getJobUri());
    }

    public function testGetStatusMessage(): void
    {
        $attributes = ['status-message' => new IppAttribute(IppTypeEnum::Int, 'status-message', 'test')];
        $job        = new CupsIppResponse(IppStatusCodeEnum::SuccessfulOk, $attributes);
        static::assertSame('test', $job->getStatusMessage());

        $job = new CupsIppResponse(IppStatusCodeEnum::ClientErrorGone, []);
        static::assertSame('Gone', $job->getStatusMessage());

        $job = new CupsIppResponse(IppStatusCodeEnum::SuccessfulOk, []);
        static::assertNull($job->getStatusMessage());
    }
}
