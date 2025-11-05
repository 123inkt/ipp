<?php

declare(strict_types=1);

namespace DR\Ipp\Factory;

use DateTimeImmutable;
use DateTimeInterface;
use DR\Ipp\Entity\IppJob;
use DR\Ipp\Enum\JobStateEnum;
use DR\Ipp\Enum\JobStateReasonEnum;
use DR\Ipp\Protocol\IppAttribute;
use DR\Utils\Assert;

class IppJobFactory
{
    /**
     * @param IppAttribute[] $attributes
     */
    public function create(array $attributes): ?IppJob
    {
        $jobId = $this->getAttribute($attributes, 'job-id')?->getValue();
        if ($jobId === null) {
            return null;
        }

        $jobUri         = $this->getAttribute($attributes, 'job-uri')?->getValue();
        $jobName        = $this->getAttribute($attributes, 'job-name')?->getValue();
        $jobState       = $this->getAttribute($attributes, 'job-state')?->getValue();
        $stateReason    = $this->getAttribute($attributes, 'job-state-reasons')?->getValue();
        $nrOfDocuments  = $this->getAttribute($attributes, 'number-of-documents')?->getValue();
        $user           = $this->getAttribute($attributes, 'job-originating-user-name')?->getValue();
        $fileSize       = $this->getAttribute($attributes, 'job-k-octets')?->getValue();
        $copies         = $this->getAttribute($attributes, 'copies')?->getValue();
        $creationDate   = $this->getAttribute($attributes, 'date-time-at-creation')?->getValue();
        $processingDate = $this->getAttribute($attributes, 'date-time-at-processing')?->getValue();
        $completionDate = $this->getAttribute($attributes, 'date-time-at-completed')?->getValue();
        $documentFormat = $this->getAttribute($attributes, 'document-format')?->getValue();
        $documentName   = $this->getAttribute($attributes, 'document-name')?->getValue();
        $printerUri     = $this->getAttribute($attributes, 'job-printer-uri')?->getValue();
        $printerUptime  = $this->getAttribute($attributes, 'job-printer-up-time')?->getValue();
        $priority       = $this->getAttribute($attributes, 'job-priority')?->getValue();

        $job = new IppJob();
        $job->setId(Assert::integer($jobId));
        $job->setUri(Assert::string($jobUri));
        $job->setName($jobName === null ? null : Assert::string($jobName));
        $job->setJobState(JobStateEnum::from(Assert::integer($jobState)));
        $job->setJobStateReason(JobStateReasonEnum::tryFrom(Assert::string($stateReason)) ?? JobStateReasonEnum::Unknown);
        $job->setNumberOfDocuments($nrOfDocuments === null ? null : Assert::integer($nrOfDocuments));
        $job->setUserName($user === null ? null : Assert::string($user));
        $job->setFileSize($fileSize === null ? null : Assert::integer($fileSize));
        $job->setCopies($copies === null ? null : Assert::integer($copies));
        $job->setCreationDate($creationDate instanceof DateTimeInterface ? $creationDate : null);
        $job->setProcessingDate($processingDate instanceof DateTimeInterface ? $processingDate : null);
        $job->setCompletionDate($completionDate instanceof DateTimeInterface ? $completionDate : null);
        $job->setDocumentFormat($documentFormat === null ? null : Assert::string($documentFormat));
        $job->setDocumentName($documentName === null ? null : Assert::string($documentName));
        $job->setPrinterUri($printerUri === null ? null : Assert::string($printerUri));
        $job->setPrinterUpSince($printerUptime === null ? null : new DateTimeImmutable('@' . Assert::nonNegativeInt($printerUptime)));
        $job->setPriority($priority === null ? null : Assert::integer($priority));

        return $job;
    }

    /**
     * @param IppAttribute[] $attributes
     */
    private function getAttribute(array $attributes, string $name): ?IppAttribute
    {
        return $attributes[$name] ?? null;
    }
}
