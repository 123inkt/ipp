<?php

declare(strict_types=1);

namespace DR\Ipp\Enum;

enum PrinterStateEnum: int
{
    case Idle = 3;
    case Processing = 4;
    case Stopped = 5;
}
