<?php

declare(strict_types=1);

namespace DR\Ipp\Enum;

enum IppStatusCodeEnum: int
{
    case SuccessfulOk = 0x0000;
    case SuccessfulOkIgnoredAttributes = 0x0001;
    case SuccessfulOkConflictingAttributes = 0x0002;
    case SuccessfulOkIgnoredSubscriptions = 0x0003;
    case SuccessfulOkIgnoredNotifications = 0x0004;
    case SuccessfulOkTooManyEvents = 0x0005;
    case SuccessfulOkCancelSubscription = 0x0006;
    case SuccessfulOkEventsComplete = 0x0007;
    // 400
    case ClientErrorBadRequest = 0x0400;
    case ClientErrorForbidden = 0x0401;
    case ClientErrorNotAuthenticated = 0x0402;
    case ClientErrorNotAuthorized = 0x0403;
    case ClientErrorNotPossible = 0x0404;
    case ClientErrorTimeout = 0x0405;
    case ClientErrorNotFound = 0x0406;
    case ClientErrorGone = 0x0407;
    case ClientErrorRequestEntityTooLarge = 0x0408;
    case ClientErrorRequestValueTooLong = 0x0409;
    case ClientErrorDocumentFormatNotSupported = 0x040A;
    case ClientErrorAttributesOrValuesNotSupported = 0x040B;
    case ClientErrorUriSchemeNotSupported = 0x040C;
    case ClientErrorCharsetNotSupported = 0x040D;
    case ClientErrorConflictingAttributes = 0x040E;
    case ClientErrorCompressionNotSupported = 0x040F;
    case ClientErrorCompressionError = 0x0410;
    case ClientErrorDocumentFormatError = 0x0411;
    case ClientErrorDocumentAccessError = 0x0412;
    case ClientErrorAttributesNotSettable = 0x0413;
    case ClientErrorIgnoredAllSubscriptions = 0x0414;
    case ClientErrorTooManySubscriptions = 0x0415;
    case ClientErrorIgnoredAllNotifications = 0x0416;
    case ClientErrorPrintSupportFileNotFound = 0x0417;
    case ClientErrorDocumentPasswordError = 0x0418;
    case ClientErrorDocumentPermissionError = 0x0419;
    case ClientErrorDocumentSecurityError = 0x041A;
    case ClientErrorDocumentUnprintableError = 0x041B;
    case ClientErrorAccountInfoNeeded = 0x041C;
    case ClientErrorAccountClosed = 0x041D;
    case ClientErrorAccountLimitReached = 0x041E;
    case ClientErrorAccountAuthorizationFailed = 0x041F;
    case ClientErrorNotFetchable = 0x0420;
    // 500
    case ServerErrorInternalError = 0x0500;
    case ServerErrorOperationNotSupported = 0x0501;
    case ServerErrorServiceUnavailable = 0x0502;
    case ServerErrorVersionNotSupported = 0x0503;
    case ServerErrorDeviceError = 0x0504;
    case ServerErrorTemporaryError = 0x0505;
    case ServerErrorNotAcceptingJobs = 0x0506;
    case ServerErrorBusy = 0x0507;
    case ServerErrorJobCanceled = 0x0508;
    case ServerErrorMultiDocumentJobNotSupported = 0x0509;
    case ServerErrorPrinterIsDeactivated = 0x050A;
    case ServerErrorTooManyJobs = 0x050B;
    case ServerErrorTooManyDocuments = 0x050C;
    // 1000
    case CupsAuthenticationCanceled = 0x1000;
    case CupsPkiError = 0x1001;
    case CupsUpgradeRequired = 0x1002;

    case Unknown = 0xFFFF;
}
