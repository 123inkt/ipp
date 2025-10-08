<?php

declare(strict_types=1);

namespace DR\Ipp\Protocol\Response;

use DateTime;
use DR\Ipp\Entity\IppResolution;
use DR\Ipp\Enum\IppTypeEnum;
use DR\Utils\Assert;
use Nyholm\Psr7\Stream;
use Psr\Http\Message\StreamInterface;
use RuntimeException;

/**
 * @internal
 */
class IppResponseState
{
    private StreamInterface $stream;

    public function __construct(string $response)
    {
        $this->stream = new Stream(Assert::notFalse(fopen('php://memory', 'rb+')));
        $this->stream->write($response);
        $this->stream->rewind();
    }

    public function getNextByte(): int
    {
        $nextByte = (int)$this->unpackSingleValue('c', 1);
        $this->stream->seek(-1, SEEK_CUR);

        return $nextByte;
    }

    /**
     * Decodes part of a binary string, returns the decoded value and removes $length bytes from the start of the binary string
     */
    public function consume(int $length, ?IppTypeEnum $type): mixed
    {
        return match ($type) {
            IppTypeEnum::Int, IppTypeEnum::Enum => $this->consumeBytes($length === 2 ? 'n' : 'N', $length),
            IppTypeEnum::DateTime               => $this->consumeDateTime($length),
            IppTypeEnum::NoValue                => null,
            IppTypeEnum::Resolution             => $this->consumeResolution($length),
            IppTypeEnum::Bool                   => (bool)$this->consumeBytes('a', $length),
            IppTypeEnum::IntRange               => $this->consumeIntRange($length),
            null                                => $this->consumeBytes('c' . $length, $length),
            default                             => $this->consumeBytes('a' . $length, $length),
        };
    }

    /**
     * @return array<int|string, mixed>|int|string
     */
    private function consumeBytes(string $unpack, int $byteCount): int|string|array
    {
        if ($byteCount < 1) {
            return '';
        }

        return str_contains($unpack, '/') ? $this->unpackArray($unpack, $byteCount) : $this->unpackSingleValue($unpack, $byteCount);
    }

    private function consumeDateTime(int $length): DateTime
    {
        // Datetime in rfc2579 format: https://datatracker.ietf.org/doc/html/rfc2579
        /** @var array{year: int, month: int, day: int, hour: int, min: int, sec: int, int, tz: string, tzhour:int, tzmin: int} $dateTime */
        $dateTime = $this->consumeBytes('nyear/cmonth/cday/chour/cmin/csec/c/atz/ctzhour/ctzmin', $length);

        $date     = $dateTime['year'] . '-' . $dateTime['month'] . '-' . $dateTime['day'];
        $time     = $dateTime['hour'] . ':' . sprintf('%02d', $dateTime['min']) . ':' . sprintf('%02d', $dateTime['sec']);
        $timeZone = $dateTime['tz'] . sprintf('%02d', $dateTime['tzhour']) . sprintf('%02d', $dateTime['tzmin']);

        $converted = DateTime::createFromFormat('Y-n-j G:i:sO', $date . ' ' . $time . $timeZone);
        if ($converted === false) {
            throw new RuntimeException('Invalid DateTime in IPP attribute');
        }

        return $converted;
    }

    /**
     * @return int[]
     */
    private function consumeIntRange(int $length): array
    {
        /** @var array{start: int, end: int} $data */
        $data = $this->consumeBytes('Nstart/Nend', $length);

        return [(int)$data['start'], (int)$data['end']];
    }

    private function consumeResolution(int $length): IppResolution
    {
        /** @var array{cross: int, feed: int, unit: int} $data */
        $data = $this->consumeBytes('Ncross/Nfeed/cunit', $length);

        return new IppResolution($data['cross'], $data['feed'], $data['unit']);
    }

    /**
     * @return array<int|string, mixed>
     */
    private function unpackArray(string $unpack, int $byteCount): array
    {
        $data = @unpack($unpack, $this->stream->read($byteCount));
        if ($data === false) {
            throw new RuntimeException('Failed to parse IPP array data');
        }

        return $data;
    }

    private function unpackSingleValue(string $unpack, int $byteCount): string|int
    {
        $data = @unpack($unpack, $this->stream->read($byteCount));
        if ($data === false) {
            throw new RuntimeException('Failed to parse IPP data');
        }

        /** @phpstan-var string|int */
        return $data[1];
    }
}
