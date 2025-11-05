<?php

declare(strict_types=1);

namespace DR\Ipp\Enum;

/**
 * @see https://datatracker.ietf.org/doc/html/rfc8011#page-148
 */
enum AuthenticationSupportEnum: string
{
    case None = 'none';
    case RequestingUserName = 'requesting-user-name';
    case Basic = 'basic';
    case Digest = 'digest';
    case Certificate = 'certificate';
}
