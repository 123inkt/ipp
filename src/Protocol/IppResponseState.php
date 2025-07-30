<?php

declare(strict_types=1);

namespace DR\Ipp\Protocol;

use DateTime;
use DR\Ipp\Enum\IppTypeEnum;
use RuntimeException;

/**
 * @internal
 */
class IppResponseState
{
    public function __construct(private string $response)
    {
    }

    public function getNextByte(): int
    {
        /** @phpstan-return int */
        return $this->unpackSingleValue('c');
    }

    /**
     * Decodes part of a binary string, returns the decoded value and removes $length bytes from the start of the binary string
     */
    public function consume(int $length, ?IppTypeEnum $type): mixed
    {
        return match ($type) {
            IppTypeEnum::Int, IppTypeEnum::Enum => $this->consumeBytes($length === 2 ? 'n' : 'N', $length),
            IppTypeEnum::DateTime               => $this->consumeDateTime(),
            IppTypeEnum::NoValue                => '',
            IppTypeEnum::Resolution             => $this->consumeResolution(),
            IppTypeEnum::Bool                   => (bool)$this->consumeBytes('a', 1),
            IppTypeEnum::IntRange               => $this->consumeIntRange(),
            null                                => $this->consumeBytes('c' . $length, $length),
            default                             => $this->consumeBytes('a' . $length, $length),
        };
    }

    /**
     * @return int|string|array<int|string, int|string>
     */
    private function consumeBytes(string $unpack, int $byteCount): int|string|array
    {
        $data           = str_contains($unpack, '/') ? $this->unpackArray($unpack) : $this->unpackSingleValue($unpack);
        $this->response = substr($this->response, $byteCount);

        return $data;
    }

    private function consumeDateTime(): DateTime
    {
        // Datetime in rfc2579 format: https://datatracker.ietf.org/doc/html/rfc2579
        /** @var array{year: int, month: int, day: int, hour: int, min: int, sec: int, int, tz: string, tzhour:int, tzmin: int}|false $dateTime */
        $dateTime = $this->consumeBytes('nyear/cmonth/cday/chour/cmin/csec/c/atz/ctzhour/ctzmin', 11);

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
    private function consumeIntRange(): array
    {
        /** @var array{start: int, end: int} $data */
        $data = $this->consumeBytes('Nstart/Nend', 4);

        return [(int)$data['start'], (int)$data['end']];
    }

    private function consumeResolution(): IppResolution
    {
        /** @var array{cross: int, feed: int, unit: int} $data */
        $data = $this->consumeBytes('Ncross/Nfeed/cunit', 9);

        return new IppResolution($data['cross'], $data['feed'], $data['unit']);
    }

    /**
     * @return array<int|string, mixed>
     */
    private function unpackArray(string $unpack): array
    {
        $data = @unpack($unpack, $this->response);
        if ($data === false) {
            throw new RuntimeException();
        }

        return $data;
    }

    private function unpackSingleValue(string $unpack): string|int
    {
        $data = @unpack($unpack, $this->response);
        if ($data === false || isset($data[1]) === false || (is_string($data[1]) === false && is_int($data[1]) === false)) {
            throw new RuntimeException();
        }

        return $data[1];
    }
}
