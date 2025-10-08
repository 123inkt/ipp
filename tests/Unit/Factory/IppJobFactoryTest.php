<?php

declare(strict_types=1);

namespace DR\Ipp\Tests\Unit\Factory;

use DateTime;
use DR\Ipp\Enum\IppTypeEnum;
use DR\Ipp\Enum\JobStateEnum;
use DR\Ipp\Enum\JobStateReasonEnum;
use DR\Ipp\Factory\IppJobFactory;
use DR\Ipp\Protocol\IppAttribute;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(IppJobFactory::class)]
class IppJobFactoryTest extends TestCase
{
    public function testCreateNull(): void
    {
        $factory = new IppJobFactory();
        static::assertNull($factory->create([]));
    }

    public function testCreateMinimal(): void
    {
        $attributes                      = [];
        $attributes['job-id']            = new IppAttribute(IppTypeEnum::Int, 'job-id', 1);
        $attributes['job-state']         = new IppAttribute(IppTypeEnum::Enum, 'job-state', JobStateEnum::Canceled->value);
        $attributes['job-uri']           = new IppAttribute(IppTypeEnum::Uri, 'job-uri', 'test');
        $attributes['job-state-reasons'] = new IppAttribute(IppTypeEnum::Enum, 'job-state-reasons', JobStateReasonEnum::AbortedBySystem->value);

        $factory = new IppJobFactory();
        $job     = $factory->create($attributes);
        static::assertNotNull($job);
        static::assertSame($job->getId(), 1);
        static::assertSame($job->getJobState(), JobStateEnum::Canceled);
        static::assertSame($job->getUri(), 'test');
        static::assertSame($job->getJobStateReason(), JobStateReasonEnum::AbortedBySystem);
    }

    public function testCreate(): void
    {
        $date = new DateTime();

        $attributes                              = [];
        $attributes['job-id']                    = new IppAttribute(IppTypeEnum::Int, 'job-id', 1);
        $attributes['job-state']                 = new IppAttribute(IppTypeEnum::Enum, 'job-state', JobStateEnum::Canceled->value);
        $attributes['job-uri']                   = new IppAttribute(IppTypeEnum::Uri, 'job-uri', 'test');
        $attributes['job-state-reasons']         = new IppAttribute(IppTypeEnum::Enum, 'job-state-reasons', JobStateReasonEnum::JobIncoming->value);
        $attributes['job-originating-user-name'] = new IppAttribute(IppTypeEnum::Uri, 'job-uri', 'user');
        $attributes['number-of-documents']       = new IppAttribute(IppTypeEnum::Uri, 'job-uri', 1);
        $attributes['job-k-octets']              = new IppAttribute(IppTypeEnum::Uri, 'job-uri', 42);
        $attributes['copies']                    = new IppAttribute(IppTypeEnum::Uri, 'job-uri', 1);
        $attributes['date-time-at-creation']     = new IppAttribute(IppTypeEnum::Uri, 'job-uri', $date);
        $attributes['date-time-at-completed']    = new IppAttribute(IppTypeEnum::Uri, 'job-uri', $date);
        $attributes['document-format']           = new IppAttribute(IppTypeEnum::Uri, 'job-uri', 'application/pdf');

        $factory = new IppJobFactory();
        $job     = $factory->create($attributes);
        static::assertNotNull($job);
        static::assertSame($job->getId(), 1);
        static::assertSame($job->getJobState(), JobStateEnum::Canceled);
        static::assertSame($job->getJobStateReason(), JobStateReasonEnum::JobIncoming);
        static::assertSame($job->getUri(), 'test');
        static::assertSame($job->getNumberOfDocuments(), 1);
        static::assertSame($job->getFileSize(), 42);
        static::assertSame($job->getCopies(), 1);
        static::assertSame($job->getCreationDate(), $date);
        static::assertSame($job->getCompletionDate(), $date);
        static::assertSame($job->getDocumentFormat(), 'application/pdf');
    }
}
