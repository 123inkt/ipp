<?php

declare(strict_types=1);

namespace DR\Ipp\Enum;

enum IppAttributeTypeEnum: int
{
    case OperationAttribute = 0x00;
    case PrinterAttribute = 0x01;
    case JobAttribute = 0x02;
}
