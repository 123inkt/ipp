<?php

declare(strict_types=1);

namespace DR\Ipp\Tests\Unit\Factory;

use DateTimeImmutable;
use DR\Ipp\Enum\AuthenticationSupportEnum;
use DR\Ipp\Enum\IppTypeEnum;
use DR\Ipp\Enum\PrinterStateEnum;
use DR\Ipp\Enum\PrintQualityEnum;
use DR\Ipp\Factory\IppPrinterFactory;
use DR\Ipp\Protocol\IppAttribute;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(IppPrinterFactory::class)]
class IppPrinterFactoryTest extends TestCase
{
    public function testCreateNull(): void
    {
        $factory = new IppPrinterFactory();
        static::assertNull($factory->create([]));
    }

    public function testCreate(): void
    {
        $attributes                                 = [];
        $attributes['printer-name']                 = new IppAttribute(IppTypeEnum::Keyword, 'printer-name', 'unit');
        $attributes['printer-location']             = new IppAttribute(IppTypeEnum::Keyword, 'printer-location', 'test');
        $attributes['device-uri']                   = new IppAttribute(IppTypeEnum::Keyword, 'device-uri', 'foo');
        $attributes['printer-state']                = new IppAttribute(IppTypeEnum::Int, 'printer-state', PrinterStateEnum::Idle->value);
        $attributes['printer-state-reasons']        = new IppAttribute(IppTypeEnum::Int, 'printer-state', 'paused');
        $attributes['printer-make-and-model']       = new IppAttribute(IppTypeEnum::Keyword, 'printer-make-and-model', 'baz');
        $attributes['printer-up-time']              = new IppAttribute(IppTypeEnum::Int, 'printer-up-time', 0);
        $attributes['uri-authentication-supported'] = new IppAttribute(
            IppTypeEnum::Keyword,
            'uri-authentication-supported',
            AuthenticationSupportEnum::None->value,
        );
        $attributes['print-quality-supported']      = new IppAttribute(
            IppTypeEnum::Keyword,
            'print-quality-supported',
            [PrintQualityEnum::Normal->value, PrintQualityEnum::High->value],
        );

        $factory = new IppPrinterFactory();
        $printer = $factory->create($attributes);
        static::assertNotNull($printer);
        static::assertSame('unit', $printer->getHostname());
        static::assertSame('test', $printer->getLocation());
        static::assertSame('foo', $printer->getDeviceUri());
        static::assertSame(PrinterStateEnum::Idle, $printer->getPrinterState());
        static::assertSame('paused', $printer->getPrinterStateReason());
        static::assertSame('baz', $printer->getPrinterType());
        static::assertSame([AuthenticationSupportEnum::None], $printer->getUriAuthSupported());
        static::assertSame([PrintQualityEnum::Normal, PrintQualityEnum::High], $printer->getPrintQualitiesSupported());
        static::assertSame((new DateTimeImmutable('@0'))->getTimeStamp(), $printer->getUpSince()?->getTimeStamp());
    }
}
