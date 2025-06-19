<?php

declare(strict_types=1);

namespace DR\Ipp\Enum;

enum IppOperationEnum: int
{
    case PrintJob = 0x02;
    case PrintUri = 0x03;
    case ValidateJob = 0x04;
    case CreateJob = 0x05;
    case SendDocument = 0x06;
    case SendUri = 0x07;
    case CancelJob = 0x08;
    case GetJobAttributes = 0x09;
    case GetJobList = 0x0A;
    case GetPrinterAttributes = 0x0B;
    case HoldJob = 0x0C;
    case ReleaseJob = 0x0D;
    case RestartJob = 0x0E;

    case PausePrinter = 0x10;
    case ResumePrinter = 0x11;
    case PurgeJobs = 0x12;
    case SetPrinterAttributes = 0x13;
    case SetJobAttributes = 0x14;
    case GetPrinterSupportedValues = 0x15;
    case CreatePrinterSubscriptions = 0x16;
    case CreateJobSubscriptions = 0x17;
    case GetSubscriptionAttributes = 0x18;
    case GetSubscriptions = 0x19;
    case RenewSubscriptions = 0x1A;
    case CancelSubscriptions = 0x1B;
    case GetNotifications = 0x1C;
    case SendNotifications = 0x1D;
    case GetResourceAttributes = 0x1E;
    case GetResourceData = 0x1F;

    case GetResources = 0x20;
    case GetPrinterSupportFiles = 0x21;
    case EnablePrinter = 0x22;
    case DisablePrinter = 0x23;
    case PausePrinterAfterCurrentJob = 0x24;
    case HoldNewJobs = 0x25;
    case ReleaseHeldNewJobs = 0x26;
    case DeactivatePrinter = 0x27;
    case ActivatePrinter = 0x28;
    case RestartPrinter = 0x29;
    case ShutdownPrinter = 0x2A;
    case StartupPrinter = 0x2B;
    case ReprocessJob = 0x2C;
    case CancelCurrentJob = 0x2D;
    case SuspendCurrentJob = 0x2E;
    case ResumeJob = 0x2F;

    case PromoteJob = 0x30;
    case ScheduleJobAfter = 0x31;

    case CancelDocument = 0x33;
    case GetDocumentAttributes = 0x34;
    case GetDocuments = 0x35;
    case DeleteDocument = 0x36;
    case SetDocumentAttributes = 0x37;
    case CancelJobs = 0x38;
    case CancelMyJobs = 0x39;
    case ResubmitJob = 0x3A;
    case CloseJob = 0x3B;
    case IdentifyPrinter = 0x3C;
    case ValidateDocument = 0x3D;
    case AddDocumentImages = 0x3E;
    case AcknowledgeDocument = 0x3F;

    case AcknowledgeIdentifyPrinter = 0x40;
    case AcknowledgeJob = 0x41;
    case FetchDocument = 0x42;
    case FetchJob = 0x43;
    case GetOutputDeviceAttributes = 0x44;
    case UpdateActiveJobs = 0x45;
    case DeregisterOutputDevice = 0x46;
    case UpdateDocumentStatus = 0x47;
    case UpdateJobStatus = 0x48;
    case UpdateOutputDeviceAttributes = 0x49;
    case GetNextDocumentData = 0x4A;
    case AllocatePrinterResources = 0x4B;
    case CreatePrinter = 0x4C;
    case DeallocatePrinterResources = 0x4D;
    case DeletePrinter = 0x4E;
    case GetPrinters = 0x4F;

    case ShutdownOnePrinter = 0x50;
    case StartupOnePrinter = 0x51;
    case CancelResource = 0x52;
    case CreateResource = 0x53;
    case InstallResource = 0x54;
    case SendResourceData = 0x55;
    case SetResourceAttributes = 0x56;
    case CreateResourceSubscriptions = 0x57;
    case CreateSystemSubscriptions = 0x58;
    case DisableAllPrinters = 0x59;
    case EnableAllPrinters = 0x5A;
    case GetSystemAttributes = 0x5B;
    case GetSystemSupportedValues = 0x5C;
    case PauseAllPrinters = 0x5D;
    case PauseAllPrintersAfterCurrentJob = 0x5E;
    case RegisterOutputDevice = 0x5F;

    case RestartSystem = 0x60;
    case ResumeAllPrinters = 0x61;
    case SetSystemAttributes = 0x62;
    case ShutdownAllPrinters = 0x63;
    case StartupAllPrinters = 0x64;
    case GetPrinterResources = 0x65;
    case GetUserPrinterAttributes = 0x66;
    case RestartOnePrinter = 0x67;
    case AcknowledgeEncryptedJobAttributes = 0x68;
    case FetchEncryptedJobAttributes = 0x69;
    case GetEncryptedJobAttributes = 0x6A;

    case CupsGetDefault = 0x4001;
    case CupsGetPrinters = 0x4002;
    case CupsAddModifyPrinter = 0x4003;
    case CupsDeletePrinter = 0x4004;
    case CupsGetClasses = 0x4005;
    case CupsAddModifyClass = 0x4006;
    case CupsDeleteClass = 0x4007;
    case CupsAcceptJobs = 0x4008;
    case CupsRejectJobs = 0x4009;
    case CupsSetDefault = 0x400A;
    case CupsGetDevices = 0x400B;
    case CupsGetPpds = 0x400C;
    case CupsMoveJob = 0x400D;
    case CupsAuthenticateJob = 0x400E;
    case CupsGetPpd = 0x400F;

    case CupsGetDocument = 0x4027;
    case CupsCreateLocalPrinter = 0x4028;
}
