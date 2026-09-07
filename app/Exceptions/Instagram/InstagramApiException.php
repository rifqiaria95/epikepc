<?php

namespace App\Exceptions\Instagram;

use App\Enums\InstagramErrorClass;
use RuntimeException;
use Throwable;

class InstagramApiException extends RuntimeException
{
    public function __construct(
        public readonly InstagramErrorClass $errorClass,
        string $message,
        public readonly ?int $httpStatus = null,
        public readonly ?int $graphCode = null,
        ?Throwable $previous = null,
    ) {
        parent::__construct($message, 0, $previous);
    }

    public function retryable(): bool
    {
        return $this->errorClass->retryable();
    }

    public function safeMessage(): string
    {
        return match ($this->errorClass) {
            InstagramErrorClass::Authentication => 'Instagram access token is invalid or expired.',
            InstagramErrorClass::Permission => 'Instagram API permission is missing or insufficient.',
            InstagramErrorClass::RateLimit => 'Instagram API rate limit was reached.',
            InstagramErrorClass::Timeout => 'Instagram API request timed out.',
            InstagramErrorClass::Malformed => 'Instagram API returned an unexpected response.',
            InstagramErrorClass::Transient => 'Instagram API is temporarily unavailable.',
            InstagramErrorClass::Configuration => 'Instagram integration is not configured.',
            InstagramErrorClass::Disabled => 'Instagram integration is disabled.',
        };
    }
}
