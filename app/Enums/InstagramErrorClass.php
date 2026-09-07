<?php

namespace App\Enums;

enum InstagramErrorClass: string
{
    case Authentication = 'authentication';
    case Permission = 'permission';
    case RateLimit = 'rate_limit';
    case Timeout = 'timeout';
    case Malformed = 'malformed';
    case Transient = 'transient';
    case Configuration = 'configuration';
    case Disabled = 'disabled';

    public function retryable(): bool
    {
        return $this === self::Timeout || $this === self::Transient;
    }

    public function tokenInvalid(): bool
    {
        return $this === self::Authentication;
    }
}
