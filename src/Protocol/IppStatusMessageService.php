<?php

declare(strict_types=1);

namespace DR\Ipp\Protocol;

use DR\Ipp\Enum\IppStatusCodeEnum;

class IppStatusMessageService
{
    public static function getStatusMessage(IppStatusCodeEnum $statusCode): ?string
    {
        return match ($statusCode) {
            IppStatusCodeEnum::ClientErrorBadRequest                     => "Bad request",
            IppStatusCodeEnum::ClientErrorForbidden                      => "Forbidden",
            IppStatusCodeEnum::ClientErrorNotAuthenticated               => "Not authenticated",
            IppStatusCodeEnum::ClientErrorNotAuthorized                  => "Not authorized",
            IppStatusCodeEnum::ClientErrorNotPossible                    => "Not possible",
            IppStatusCodeEnum::ClientErrorTimeout                        => "Timeout",
            IppStatusCodeEnum::ClientErrorNotFound                       => "The configured printer could not be found.",
            IppStatusCodeEnum::ClientErrorGone                           => "Gone",
            IppStatusCodeEnum::ClientErrorRequestEntityTooLarge          => "Request entity too large",
            IppStatusCodeEnum::ClientErrorRequestValueTooLong            => "Request value too long",
            IppStatusCodeEnum::ClientErrorDocumentFormatNotSupported     => "Document format not supported",
            IppStatusCodeEnum::ClientErrorAttributesOrValuesNotSupported => "Attribute(s) or values not supported",
            IppStatusCodeEnum::ClientErrorUriSchemeNotSupported          => "Uri scheme not supported",
            IppStatusCodeEnum::ClientErrorCharsetNotSupported            => "Charset is not supported",
            IppStatusCodeEnum::ClientErrorConflictingAttributes          => "Conflicting attributes",
            IppStatusCodeEnum::ClientErrorCompressionNotSupported        => "Compression is not supported",
            IppStatusCodeEnum::ClientErrorCompressionError               => "Compression error",
            IppStatusCodeEnum::ClientErrorDocumentFormatError            => "Document format error",
            IppStatusCodeEnum::ClientErrorDocumentAccessError            => "Could not access document",
            IppStatusCodeEnum::ClientErrorAttributesNotSettable          => "Attribute(s) can not be set",
            IppStatusCodeEnum::ClientErrorIgnoredAllSubscriptions        => "Ignored all subscriptions",
            IppStatusCodeEnum::ClientErrorTooManySubscriptions           => "Too many subscriptions",
            IppStatusCodeEnum::ClientErrorIgnoredAllNotifications        => "Ignored all notifications",
            IppStatusCodeEnum::ClientErrorPrintSupportFileNotFound       => "Printer can not print this file type",
            IppStatusCodeEnum::ClientErrorDocumentPasswordError          => "Document password error",
            IppStatusCodeEnum::ClientErrorDocumentPermissionError        => "Do not have permission to print this document",
            IppStatusCodeEnum::ClientErrorDocumentSecurityError          => "Document security error",
            IppStatusCodeEnum::ClientErrorDocumentUnprintableError       => "Document can not be printed",
            IppStatusCodeEnum::ClientErrorAccountInfoNeeded              => "Account info needed",
            IppStatusCodeEnum::ClientErrorAccountClosed                  => "Account closed",
            IppStatusCodeEnum::ClientErrorAccountLimitReached            => "Account limit reached",
            IppStatusCodeEnum::ClientErrorAccountAuthorizationFailed     => "Account authorization failed",
            IppStatusCodeEnum::ClientErrorNotFetchable                   => "Not fetchable",
            IppStatusCodeEnum::ServerErrorInternalError                  => "Internal Error",
            IppStatusCodeEnum::ServerErrorOperationNotSupported          => "Operation not supported",
            IppStatusCodeEnum::ServerErrorServiceUnavailable             => "Service unavailable",
            IppStatusCodeEnum::ServerErrorVersionNotSupported            => "Version not supported",
            IppStatusCodeEnum::ServerErrorDeviceError                    => "Device Error",
            IppStatusCodeEnum::ServerErrorTemporaryError                 => "Temporary Error (try again)",
            IppStatusCodeEnum::ServerErrorNotAcceptingJobs               => "Not accepting jobs",
            IppStatusCodeEnum::ServerErrorBusy                           => "Printer is busy",
            IppStatusCodeEnum::ServerErrorJobCanceled                    => "Job canceled",
            IppStatusCodeEnum::ServerErrorMultiDocumentJobNotSupported   => "Printer does not support printing multiple documents",
            IppStatusCodeEnum::ServerErrorPrinterIsDeactivated           => "Printer is deactivated",
            IppStatusCodeEnum::ServerErrorTooManyJobs                    => "Too many jobs",
            IppStatusCodeEnum::ServerErrorTooManyDocuments               => "Too many documents",
            IppStatusCodeEnum::Unknown                                   => "Unknown error",
            default                                                      => null
        };
    }
}
