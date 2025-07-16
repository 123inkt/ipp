<?php

declare(strict_types=1);

namespace DR\Ipp\Protocol;

use DateTime;
use DR\Ipp\Entity\Response\CupsIppResponse;
use DR\Ipp\Entity\Response\IppResponseInterface;
use DR\Ipp\Enum\IppOperationTagEnum;
use DR\Ipp\Enum\IppStatusCodeEnum;
use DR\Ipp\Enum\IppTypeEnum;
use RuntimeException;

/**
 * @internal
 */
class IppResponseParser implements IppResponseParserInterface
{
    public function getResponse(string $response): IppResponseInterface
    {
        [, $response] = $this->consume($response, 2, IppTypeEnum::Int);            // version   0x0101
        /** @var int $status */
        [$status, $response] = $this->consume($response, 2, IppTypeEnum::Int);     // status    0x0502
        [, $response] = $this->consume($response, 4, IppTypeEnum::Int);            // requestId 0x00000001
        [, $response] = $this->consume($response, 1, IppTypeEnum::Int);            // IPPOperationTag::OPERATION_ATTRIBUTE_START

        $attributesTags = [
            IppOperationTagEnum::JobAttributeStart->value,
            IppOperationTagEnum::PrinterAttributeStart->value,
            IppOperationTagEnum::UnsupportedAttributes->value,
        ];
        $attributes     = [];
        while ($this->unpack('c', $response) !== IppOperationTagEnum::AttributeEnd->value) {
            // look for attribute tag and remove it before parsing further attributes
            if (in_array($this->unpack('c', $response), $attributesTags, true)) {
                [, $response] = $this->consume($response, 1, null);
            }

            [$attribute, $response] = $this->consumeAttribute($response);
            $attributes[$attribute->getName()] = $attribute;
        }
        $statusCode = IppStatusCodeEnum::tryFrom($status) ?? IppStatusCodeEnum::Unknown;

        return new CupsIppResponse($statusCode, $attributes);
    }

    /**
     * Decodes an attribute from the response, and returns the decoded values and the rest of the response
     * @return array{IppAttribute, string}
     */
    private function consumeAttribute(string $response): array
    {
        /** @var int $type */
        [$type, $response] = $this->consume($response, 1, null);
        [$nameLength, $response] = $this->consume($response, 2, IppTypeEnum::Int);
        /** @var int $nameLength */
        [$attrName, $response] = $this->consume($response, $nameLength, IppTypeEnum::NameWithoutLang);
        /** @var string $attrName */
        [$valueLength, $response] = $this->consume($response, 2, IppTypeEnum::Int);
        /** @var int $valueLength */
        [$attrValue, $response] = $this->consume(
            $response,
            $valueLength,
            IppTypeEnum::tryFrom($type),
        );

        return [new IppAttribute(IppTypeEnum::tryFrom($type) ?? IppTypeEnum::Int, $attrName, $attrValue), $response];
    }

    /**
     * Decodes part of a binary string, and returns the decoded value and the rest of the binary string
     * @return array{int|string|DateTime, string}
     */
    private function consume(string $response, int $length, ?IppTypeEnum $type): array
    {
        switch ($type) {
            case IppTypeEnum::Int:
            case IppTypeEnum::Enum:
                $unpack = $length === 2 ? 'n' : 'N';
                break;
            case IppTypeEnum::DateTime:
                return [$this->unpackDateTime($response), substr($response, $length)];
            case null:
                $unpack = 'c' . $length;
                break;
            default:
                $unpack = 'a' . $length;
        }

        return [$this->unpack($unpack, $response), substr($response, $length)];
    }

    private function unpackDateTime(string $response): DateTime
    {
        // Datetime in rfc2579 format: https://datatracker.ietf.org/doc/html/rfc2579
        /** @var array{year: int, month: int, day: int, hour: int, min: int, sec: int, int, tz: string, tzhour:int, tzmin: int}|false $dateTime */
        $dateTime = @unpack('nyear/cmonth/cday/chour/cmin/csec/c/atz/ctzhour/ctzmin', $response);
        if ($dateTime === false) {
            throw new RuntimeException('Failed to unpack IPP datetime');
        }
        $date     = $dateTime['year'] . '-' . $dateTime['month'] . '-' . $dateTime['day'];
        $time     = $dateTime['hour'] . ':' . sprintf('%02d', $dateTime['min']) . ':' . sprintf('%02d', $dateTime['sec']);
        $timeZone = $dateTime['tz'] . sprintf('%02d', $dateTime['tzhour']) . sprintf('%02d', $dateTime['tzmin']);

        $converted = DateTime::createFromFormat('Y-n-j G:i:sO', $date . ' ' . $time . $timeZone);
        if ($converted === false) {
            throw new RuntimeException('Invalid DateTime in IPP attribute');
        }

        return $converted;
    }

    private function unpack(string $unpack, string $string): string|int
    {
        $data = @unpack($unpack, $string);
        if ($data === false || isset($data[1]) === false || (is_string($data[1]) === false && is_int($data[1]) === false)) {
            throw new RuntimeException();
        }

        return $data[1];
    }
}
