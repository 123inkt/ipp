<?php

declare(strict_types=1);

namespace DR\Ipp\Enum;

enum IppOperationTagEnum: int
{
    case OperationAttributeStart = 0x01;
    case JobAttributeStart = 0x02;
    case AttributeEnd = 0x03;
    case PrinterAttributeStart = 0x04;
    case UnsupportedAttributes = 0x05;
}
