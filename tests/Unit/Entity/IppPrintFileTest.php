<?php

declare(strict_types=1);

namespace DR\Ipp\Tests\Unit\Entity;

use DR\Ipp\Entity\IppPrintFile;
use DR\Ipp\Enum\FileTypeEnum;
use DR\Ipp\Enum\IppAttributeTypeEnum;
use DR\Ipp\Enum\IppTypeEnum;
use DR\Ipp\Protocol\IppAttribute;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(IppPrintFile::class)]
class IppPrintFileTest extends TestCase
{
    public function testConstruct(): void
    {
        $file = new IppPrintFile('foo', FileTypeEnum::JPG, 'bar', 1);
        static::assertSame('foo', $file->getData());
        static::assertSame(FileTypeEnum::JPG, $file->getFileType());
        static::assertSame('bar', $file->getFileName());
        static::assertSame(1, $file->getNumberOfCopies());
    }

    public function testSetters(): void
    {
        $file = new IppPrintFile('foo', FileTypeEnum::JPG);

        $file->setData('bar');
        $file->setFileType(FileTypeEnum::PNG);
        $file->setFileName('unit');
        $file->setNumberOfCopies(2);

        static::assertSame('bar', $file->getData());
        static::assertSame(FileTypeEnum::PNG, $file->getFileType());
        static::assertSame('unit', $file->getFileName());
        static::assertSame(2, $file->getNumberOfCopies());
    }

    public function testExtraAttributes(): void
    {
        $attrA = new IppAttribute(IppTypeEnum::Int, 'bar1', 1);
        $attrB = new IppAttribute(IppTypeEnum::Int, 'bar2', 2);
        $attrC = new IppAttribute(IppTypeEnum::Int, 'bar3', 3);

        $file = new IppPrintFile('foo', FileTypeEnum::JPG);
        $file->addAttribute(IppAttributeTypeEnum::JobAttribute, $attrA);
        $file->addAttribute(IppAttributeTypeEnum::OperationAttribute, $attrB);
        $file->addAttribute(IppAttributeTypeEnum::PrinterAttribute, $attrC);

        static::assertSame([$attrA], $file->getIppAttributes(IppAttributeTypeEnum::JobAttribute));
        static::assertSame([$attrB], $file->getIppAttributes(IppAttributeTypeEnum::OperationAttribute));
        static::assertSame([$attrC], $file->getIppAttributes(IppAttributeTypeEnum::PrinterAttribute));
    }
}
