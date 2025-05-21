<?php

declare(strict_types=1);

namespace DR\Ipp\Enum;

enum JobStateEnum: int
{
    case Failed = 0;
    case Pending = 3;
    case PendingHeld = 4;
    case Processing = 5;
    case ProcessingStopped = 6;
    case Canceled = 7;
    case Aborted = 8;
    case Completed = 9;
}
