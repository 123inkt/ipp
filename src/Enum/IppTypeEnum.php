<?php

declare(strict_types=1);

namespace DR\Ipp\Enum;

enum IppTypeEnum: int
{
    case Unsupported = 0x10;
    case Default = 0x11;
    case Unknown = 0x12;
    case NoValue = 0x13;
    case NotSettable = 0x15;
    case DeleteAttribute = 0x16;
    case AdminDefine = 0x17;
    case Int = 0x21;
    case Bool = 0x22;
    case Enum = 0x23;
    case OctetString = 0x30;
    case DateTime = 0x31;
    case Resolution = 0x32;
    case IntRange = 0x33;
    case Collection = 0x34;
    case TextWithLang = 0x35;
    case NameWithLang = 0x36;
    case EndCollection = 0x37;
    case TextWithoutLang = 0x41;
    case NameWithoutLang = 0x42;
    case Keyword = 0x44;
    case Uri = 0x45;
    case UriScheme = 0x46;
    case Charset = 0x47;
    case NaturalLanguage = 0x48;
    case MimeType = 0x49;
    case MemberAttributeName = 0x4A;
}
