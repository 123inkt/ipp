<?php

declare(strict_types=1);

namespace DR\Ipp\Enum;

enum FileTypeEnum: string
{
    case PCL = 'pcl';
    case PDF = 'pdf';
    case PNG = 'png';
    case PS = 'ps';
    case JPG = 'jpg';
    case ZPL = 'zpl';
}
