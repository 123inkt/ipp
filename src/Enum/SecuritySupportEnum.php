<?php

declare(strict_types=1);

namespace DR\Ipp\Enum;

/**
 * @see https://datatracker.ietf.org/doc/html/rfc8011#page-149
 */
enum SecuritySupportEnum: string
{
    case None = 'none';
    case Tls = 'tls';
}
