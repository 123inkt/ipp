<?php

declare(strict_types=1);

namespace DR\Ipp\Factory;

use BackedEnum;
use DateTimeImmutable;
use DR\Ipp\Entity\IppPrinter;
use DR\Ipp\Enum\AuthenticationSupportEnum;
use DR\Ipp\Enum\PrinterStateEnum;
use DR\Ipp\Enum\PrintQualityEnum;
use DR\Ipp\Enum\SecuritySupportEnum;
use DR\Ipp\Protocol\IppAttribute;
use DR\Utils\Assert;

class IppPrinterFactory
{
    /**
     * @param IppAttribute[] $attributes
     */
    public function create(array $attributes): ?IppPrinter
    {
        $name = $this->getAttribute($attributes, 'printer-name');
        if ($name === null) {
            return null;
        }

        $stateReason           = Assert::string($this->getAttribute($attributes, 'printer-state-reasons')?->getValue());
        $printerType           = $this->getAttribute($attributes, 'printer-make-and-model')?->getValue();
        $queuedJobsCount       = $this->getAttribute($attributes, 'queued-job-count')?->getValue();
        $uriAuthSupported      = $this->getEnumValues($attributes, 'uri-authentication-supported', AuthenticationSupportEnum::class);
        $uriSecuritySupported  = $this->getEnumValues($attributes, 'uri-security-supported', SecuritySupportEnum::class);
        $documentFormatDefault = $this->getAttribute($attributes, 'document-format-default')?->getValue();
        /** @phpstan-var string[]|null $documentFormatsSupported */
        $documentFormatsSupported = $this->getAttribute($attributes, 'document-format-supported')?->getValue();
        $acceptingJobs            = $this->getAttribute($attributes, 'printer-is-accepting-jobs')?->getValue();
        $printerUri               = $this->getAttribute($attributes, 'printer-uri-supported')?->getValue();
        $upTime                   = $this->getAttribute($attributes, 'printer-up-time')?->getValue();
        $fileSizeLimit            = $this->getAttribute($attributes, 'job-k-limit')?->getValue();
        $pageLimit                = $this->getAttribute($attributes, 'job-page-limit')?->getValue();
        /** @phpstan-var string[]|null $mediaSupported */
        $mediaSupported          = $this->getAttribute($attributes, 'media-supported')?->getValue();
        $defaultMedia            = $this->getAttribute($attributes, 'media-default')?->getValue();
        $defaultPrintQuality     = $this->getAttribute($attributes, 'print-quality-default')?->getValue();
        $printQualitiesSupported = $this->getEnumValues($attributes, 'print-quality-supported', PrintQualityEnum::class);

        $printer = new IppPrinter();
        $printer->setHostname(Assert::string($name->getValue()));
        $printer->setLocation(Assert::string($this->getAttribute($attributes, 'printer-location')?->getValue()));
        $printer->setDeviceUri(Assert::string($this->getAttribute($attributes, 'device-uri')?->getValue()));
        $printer->setPrinterState(PrinterStateEnum::from(Assert::integer($this->getAttribute($attributes, 'printer-state')?->getValue())));
        $printer->setPrinterStateReason($stateReason);
        $printer->setPrinterType($printerType === null ? null : Assert::string($printerType));
        $printer->setUriAuthSupported($uriAuthSupported);
        $printer->setUriSecuritySupported($uriSecuritySupported);
        $printer->setDefaultDocumentFormat($documentFormatDefault === null ? null : Assert::string($documentFormatDefault));
        $printer->setDocumentFormatsSupported($documentFormatsSupported === null ? [] : Assert::isArray($documentFormatsSupported));
        $printer->setAcceptingJobs($acceptingJobs === null ? null : Assert::boolean($acceptingJobs));
        $printer->setQueuedJobsCount($queuedJobsCount === null ? null : Assert::integer($queuedJobsCount));
        $printer->setPrinterUri($printerUri === null ? null : Assert::string($printerUri));
        $printer->setUpSince($upTime === null ? null : (new DateTimeImmutable())->setTimeStamp(Assert::integer($upTime)));
        $printer->setFileSizeLimit($fileSizeLimit > 0 ? Assert::integer($fileSizeLimit) : null);
        $printer->setPageLimit($pageLimit > 0 ? Assert::integer($pageLimit) : null);
        $printer->setSupportedMedia($mediaSupported === null ? [] : Assert::isArray($mediaSupported));
        $printer->setDefaultMedia($defaultMedia === null ? null : Assert::string($defaultMedia));
        $printer->setDefaultQuality($defaultPrintQuality === null ? null : PrintQualityEnum::tryFrom(Assert::nonNegativeInt($defaultPrintQuality)));
        $printer->setPrintQualitiesSupported($printQualitiesSupported);

        return $printer;
    }

    /**
     * @param IppAttribute[] $attributes
     */
    private function getAttribute(array $attributes, string $name): ?IppAttribute
    {
        return $attributes[$name] ?? null;
    }

    /**
     * @param IppAttribute[]  $attributes
     * @template T of BackedEnum
     * @param class-string<T> $enumClass
     *
     * @return T[]
     */
    private function getEnumValues(array $attributes, string $name, string $enumClass): array
    {
        /** @phpstan-var int|string|null $value */
        $value = $this->getAttribute($attributes, $name)?->getValue();
        if ($value === null) {
            return [];
        }

        if (is_array($value) === false) {
            return $enumClass::tryFrom($value) === null ? [] : [$enumClass::from($value)];
        }

        $result = [];
        foreach ($value as $item) {
            if ($enumClass::tryFrom($item) !== null) {
                $result[] = $enumClass::from($item);
            }
        }

        return $result;
    }
}
