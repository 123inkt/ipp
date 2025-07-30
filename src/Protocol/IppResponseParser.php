<?php

declare(strict_types=1);

namespace DR\Ipp\Protocol;

use DateTime;
use DR\Ipp\Entity\IppResolution;
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
    private IppAttribute $lastAttribute;

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
        if ($nameLength === 0x0000) {
            // additional value
            [$attrValue, $response] = $this->getAttributeValue($type, $response);
            $this->lastAttribute->addAdditionalValue($attrValue);

            return [$this->lastAttribute, $response];
        }
        /** @var int $nameLength */
        [$attrName, $response] = $this->consume($response, $nameLength, IppTypeEnum::NameWithoutLang);
        /** @var string $attrName */
        [$attrValue, $response] = $this->getAttributeValue($type, $response);

        $this->lastAttribute = new IppAttribute(IppTypeEnum::tryFrom($type) ?? IppTypeEnum::Int, $attrName, $attrValue);

        return [$this->lastAttribute, $response];
    }

    /**
     * @return array{mixed, string}
     */
    private function getAttributeValue(int $type, string $response): array
    {
        [$valueLength, $response] = $this->consume($response, 2, IppTypeEnum::Int);

        /** @var int $valueLength */
        return $this->consume(
            $response,
            $valueLength,
            IppTypeEnum::tryFrom($type),
        );
    }

    /**
     * Decodes part of a binary string, and returns the decoded value and the rest of the binary string
     * @return array{bool|int|string|int[]|DateTime|IppResolution, string}
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
            case IppTypeEnum::NoValue:
                return ['', $response];
            case IppTypeEnum::Resolution:
                return [$this->unpackResolution($response), substr($response, $length)];
            case IppTypeEnum::Bool:
                return [(bool)$this->unpack('a', $response), substr($response, $length)];
            case IppTypeEnum::IntRange:
                return [$this->unpackIntRange($response), substr($response, $length)];
            case null:
                $unpack = 'c' . $length;
                break;
            default:
                $unpack = 'a' . $length;
        }

        return [$this->unpack($unpack, $response), substr($response, $length)];
    }

    /**
     * @return int[]
     */
    private function unpackIntRange(string $response): array
    {
        return [(int)$this->unpack('N', $response), (int)$this->unpack('N', substr($response, 4, 4))];
    }

    private function unpackResolution(string $response): IppResolution
    {
        /** @var array{cross: int, feed: int, unit: int} $data */
        $data = @unpack('Ncross/Nfeed/cunit', $response);
        if ($data === false) {
            throw new RuntimeException('Failed to unpack IPP resolution');
        }

        return new IppResolution($data['cross'], $data['feed'], $data['unit']);
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
