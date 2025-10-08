<?php

declare(strict_types=1);

namespace DR\Ipp\Protocol;

use DR\Ipp\Enum\IppOperationTagEnum;
use DR\Ipp\Enum\IppTypeEnum;
use DR\Utils\Assert;

/**
 * @internal
 */
class IppEncoder
{
    public static function encodeOperation(IppOperation $operation): string
    {
        $versionMajorMinor = explode('.', $operation->getVersion());

        $binary = pack('c', $versionMajorMinor[0]) . pack('c', $versionMajorMinor[1]);  // version   0x0200
        $binary .= pack('n', $operation->getOperation()->value);                        // operation 0x0003
        $binary .= pack('N', $operation->getRequestId());                               // requestId 0x00000001

        if (count($operation->getOperationAttributes()) > 0) {
            $binary .= pack('c', IppOperationTagEnum::OperationAttributeStart->value) . implode('', $operation->getOperationAttributes());
        }
        if (count($operation->getPrinterAttributes()) > 0) {
            $binary .= pack('c', IppOperationTagEnum::PrinterAttributeStart->value) . implode('', $operation->getPrinterAttributes());
        }
        if (count($operation->getJobAttributes()) > 0) {
            $binary .= pack('c', IppOperationTagEnum::JobAttributeStart->value) . implode('', $operation->getJobAttributes());
        }
        $binary .= pack('c', IppOperationTagEnum::AttributeEnd->value);

        if ($operation->getFileData() !== null) {
            $binary .= $operation->getFileData();
        }

        return $binary;
    }

    public static function encodeAttribute(IppAttribute $attribute): string
    {
        // 1 byte type
        $binary = pack('c', $attribute->getType()->value);
        // 2 bytes length of attribute name, followed by attribute name.
        $binary .= pack('n', strlen($attribute->getName())) . $attribute->getName();

        // value
        if (is_array($attribute->getValue())) {
            /** @phpstan-var list<string|int> $value */
            $value  = $attribute->getValue();
            $binary .= self::encodeMultiValue($attribute->getType(), $value);
        } else {
            $binary .= self::encodeValue($attribute->getType(), $attribute->getValue());
        }

        return $binary;
    }

    /**
     * @param list<string|int> $values
     */
    private static function encodeMultiValue(IppTypeEnum $type, array $values): string
    {
        $binary = '';
        foreach ($values as $i => $value) {
            if ($i > 0) {
                // encode name length = 0 to signal additional value of this type
                $binary .= pack('c', $type->value) . pack('n', 0);
            }
            $binary .= self::encodeValue($type, $value);
        }

        return $binary;
    }

    private static function encodeValue(IppTypeEnum $type, mixed $value): string
    {
        // 2 bytes value length, followed by the value.
        return match ($type) {
            IppTypeEnum::Int, IppTypeEnum::Enum => pack('n', 4) . pack('N', $value),
            IppTypeEnum::Bool                   => pack('n', 1) . pack('c', (int)Assert::boolean($value)),
            default                             => pack('n', strlen(Assert::string($value))) . Assert::string($value),
        };
    }
}
