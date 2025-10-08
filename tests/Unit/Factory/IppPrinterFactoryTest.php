<?php

declare(strict_types=1);

namespace DR\Ipp\Tests\Unit\Factory;

use DR\Ipp\Enum\IppTypeEnum;
use DR\Ipp\Enum\PrinterStateEnum;
use DR\Ipp\Enum\PrinterStateReasonEnum;
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
        $attributes                           = [];
        $attributes['printer-name']           = new IppAttribute(IppTypeEnum::Keyword, 'printer-name', 'unit');
        $attributes['printer-location']       = new IppAttribute(IppTypeEnum::Keyword, 'printer-location', 'test');
        $attributes['device-uri']             = new IppAttribute(IppTypeEnum::Keyword, 'device-uri', 'foo');
        $attributes['printer-state']          = new IppAttribute(IppTypeEnum::Int, 'printer-state', PrinterStateEnum::Idle->value);
        $attributes['printer-state-reasons']  = new IppAttribute(IppTypeEnum::Int, 'printer-state', PrinterStateReasonEnum::Paused->value);
        $attributes['printer-make-and-model'] = new IppAttribute(IppTypeEnum::Keyword, 'printer-make-and-model', 'baz');

        $factory = new IppPrinterFactory();
        $printer = $factory->create($attributes);
        static::assertNotNull($printer);
        static::assertSame('unit', $printer->getHostname());
        static::assertSame('test', $printer->getLocation());
        static::assertSame('foo', $printer->getDeviceUri());
        static::assertSame(PrinterStateEnum::Idle, $printer->getPrinterState());
        static::assertSame(PrinterStateReasonEnum::Paused, $printer->getPrinterStateReason());
        static::assertSame('baz', $printer->getPrinterType());
    }
}
