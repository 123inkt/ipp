<?php

declare(strict_types=1);

namespace DR\Ipp\Enum;

enum PrintQualityEnum: int
{
    case Draft = 3;
    case Normal = 4;
    case High = 5;
}
