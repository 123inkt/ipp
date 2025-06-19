<?php

declare(strict_types=1);

namespace DR\Ipp\Operations;

use DR\Ipp\Client\IppHttpClientInterface;
use DR\Ipp\Entity\IppPrinter;
use DR\Ipp\Entity\IppPrintFile;
use DR\Ipp\Entity\IppServer;
use DR\Ipp\Entity\Response\IppResponseInterface;
use DR\Ipp\Enum\FileTypeEnum;
use DR\Ipp\Enum\IppAttributeTypeEnum;
use DR\Ipp\Enum\IppOperationEnum;
use DR\Ipp\Enum\IppTypeEnum;
use DR\Ipp\Protocol\IppAttribute;
use DR\Ipp\Protocol\IppOperation;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;

/**
 * @internal
 */
class PrintOperation implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    public function __construct(private readonly IppServer $server, private readonly IppHttpClientInterface $client)
    {
    }

    /**
     * @throws ClientExceptionInterface
     */
    public function print(IppPrinter $printer, IppPrintFile $file): IppResponseInterface
    {
        $printerUri = $this->server->getUri() . '/printers/' . $printer->getHostname();

        $operation = new IppOperation(IppOperationEnum::PrintJob);
        $operation->addOperationAttribute(new IppAttribute(IppTypeEnum::Charset, 'attributes-charset', 'utf-8'));
        $operation->addOperationAttribute(new IppAttribute(IppTypeEnum::NaturalLanguage, 'attributes-natural-language', 'en'));
        $operation->addOperationAttribute(new IppAttribute(IppTypeEnum::Uri, 'printer-uri', $printerUri));
        $operation->addOperationAttribute(new IppAttribute(IppTypeEnum::MimeType, 'document-format', $this->fileTypeLookup($file->getFileType())));
        if ($printer->getTrayName() !== null) {
            $operation->addOperationAttribute(new IppAttribute(IppTypeEnum::Keyword, 'InputSlot', $printer->getTrayName()));
        }
        foreach ($file->getIppAttributes(IppAttributeTypeEnum::OperationAttribute) as $attribute) {
            $operation->addJobAttribute($attribute);
        }

        if ($file->getFileName() !== null) {
            $operation->addJobAttribute(new IppAttribute(IppTypeEnum::NameWithoutLang, 'job-name', $file->getFileName()));
        }
        $operation->addJobAttribute(new IppAttribute(IppTypeEnum::Int, 'copies', $file->getNumberOfCopies()));
        foreach ($file->getIppAttributes(IppAttributeTypeEnum::JobAttribute) as $attribute) {
            $operation->addJobAttribute($attribute);
        }

        $operation->setFileData($file->getData());

        return $this->client->sendRequest($operation);
    }

    public function fileTypeLookup(FileTypeEnum $fileType): string
    {
        return match ($fileType) {
            FileTypeEnum::PDF => 'application/pdf',
            FileTypeEnum::PCL => 'application/vnd.hp-pcl',
            FileTypeEnum::ZPL => 'application/octet-stream',
            FileTypeEnum::PS  => 'application/postscript',
            FileTypeEnum::JPG => 'image/jpeg',
            FileTypeEnum::PNG => 'image/png',
        };
    }
}
