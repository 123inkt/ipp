<?php

declare(strict_types=1);

namespace DR\Ipp\Enum;

enum JobStateReasonEnum: string
{
    case None = 'none';
    case JobIncoming = 'job-incoming';
    case JobDataInsufficient = 'job-data-insufficient';
    case JobOutgoing = 'job-outgoing';
    case JobHoldUntilSpecified = 'job-hold-until-specified';
    case JobInterpreting = 'job-interpreting';
    case JobQueued = 'job-queued';
    case JobFetchable = 'job-fetchable';
    case JobSpooling = 'job-spooling';
    case JobTransforming = 'job-transforming';
    case JobQueuedForMarker = 'job-queued-for-marker';
    case JobPrinting = 'job-printing';
    case JobCanceledByUser = 'job-canceled-by-user';
    case JobCanceledByOperator = 'job-canceled-by-operator';
    case JobCanceledAtDevice = 'job-canceled-at-device';
    case JobCompletedSuccessfully = 'job-completed-successfully';
    case JobCompletedWithWarnings = 'job-completed-with-warnings';
    case JobCompletedWithErrors = 'job-completed-with-errors';
    case JobRestartable = 'job-restartable';
    case PrinterStoppedPartly = 'printer-stopped-partly';
    case PrinterStopped = 'printer-stopped';
    case ResourcesAreNotReady = 'resources-are-not-ready';
    case UnsupportedDocumentFormat = 'unsupported-document-format';
    case DocumentAccessError = 'document-access-error';
    case DocumentFormatError = 'document-format-error';
    case DocumentPasswordError = 'document-password-error';
    case DocumentPermissionError = 'document-permission-error';
    case DocumentUnprintableError = 'document-unprintable-error';
    case CompressionError = 'compression-error';
    case UnsupportedCompression = 'unsupported-compression';
    case ProcessingToStopPoint = 'processing-to-stop-point';
    case SubmissionInterrupted = 'submission-interrupted';
    case AbortedBySystem = 'aborted-by-system';
    case ServiceOffLine = 'service-off-line';
    case QueuedInDevice = 'queued-in-device';
    case ErrorsDetected = 'errors-detected';
    case WarningsDetected = 'warnings-detected';
    case Unknown = 'unknown';
}
