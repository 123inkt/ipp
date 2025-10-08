<?php

declare(strict_types=1);

namespace DR\Ipp\Tests\Unit\Protocol\Response;

use DateTime;
use DR\Ipp\Entity\IppResolution;
use DR\Ipp\Enum\IppTypeEnum;
use DR\Ipp\Protocol\Response\IppResponseState;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use RuntimeException;

#[CoversClass(IppResponseState::class)]
class IppResponseStateTest extends TestCase
{
    public function testGetNextByte(): void
    {
        $state = new IppResponseState('A');
        static::assertSame(65, $state->getNextByte());
    }

    public function testConsumeInt(): void
    {
        $state = new IppResponseState(pack('n', 0x04));
        static::assertSame(4, $state->consume(2, IppTypeEnum::Int));
    }

    public function testConsumeEnum(): void
    {
        $state = new IppResponseState(pack('N', 0x04));
        static::assertSame(4, $state->consume(4, IppTypeEnum::Enum));
    }

    public function testConsumeNoValue(): void
    {
        $state = new IppResponseState('');
        static::assertNull($state->consume(0, IppTypeEnum::NoValue));
    }

    public function testConsumeBool(): void
    {
        $state = new IppResponseState(pack('a', 0x01));
        static::assertTrue($state->consume(1, IppTypeEnum::Bool));

        $state = new IppResponseState(pack('a', 0x00));
        static::assertFalse($state->consume(1, IppTypeEnum::Bool));
    }

    public function testConsumeDate(): void
    {
        $expected = new DateTime('1990-02-03T14:01:02.000000+0100');

        $state = new IppResponseState(pack('nc6ac2', 1990, 2, 3, 14, 01, 02, 0, '+', 1, 0));
        static::assertEquals($expected, $state->consume(11, IppTypeEnum::DateTime));
    }

    public function testConsumeResolution(): void
    {
        $expected = new IppResolution(1, 2, 3);

        $state = new IppResponseState(pack('N2c', 1, 2, 3));
        static::assertEquals($expected, $state->consume(9, IppTypeEnum::Resolution));
    }

    public function testConsumeIntRange(): void
    {
        $state = new IppResponseState(pack('N2', 1, 2));
        static::assertSame([1, 2], $state->consume(8, IppTypeEnum::IntRange));
    }

    public function testConsumeWithoutType(): void
    {
        $state = new IppResponseState('unit');
        static::assertSame(ord('u'), $state->consume(1, null));
    }

    public function testConsumeDefault(): void
    {
        $state = new IppResponseState('unit');
        static::assertSame('unit', $state->consume(4, IppTypeEnum::MemberAttributeName));
    }

    public function testConsumeSingleException(): void
    {
        $this->expectException(RuntimeException::class);
        $state = new IppResponseState('');
        $state->consume(2, IppTypeEnum::Int);
    }

    public function testConsumeMultipleException(): void
    {
        $this->expectException(RuntimeException::class);
        $state = new IppResponseState('');
        $state->consume(2, IppTypeEnum::IntRange);
    }

    public function testConsumeInvalidDateException(): void
    {
        $this->expectException(RuntimeException::class);
        $state = new IppResponseState('invalid date unittest');
        $state->consume(11, IppTypeEnum::DateTime);
    }

    public function testConsumeWithLengthZero(): void
    {
        $state = new IppResponseState('unittest');
        static::assertSame('', $state->consume(0, IppTypeEnum::Int));
    }
}
