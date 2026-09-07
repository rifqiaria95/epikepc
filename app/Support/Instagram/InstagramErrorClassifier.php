<?php

namespace App\Support\Instagram;

use App\Enums\InstagramErrorClass;
use App\Exceptions\Instagram\InstagramApiException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Response;
use Throwable;

class InstagramErrorClassifier
{
    public function fromResponse(Response $response): InstagramApiException
    {
        $payload = $response->json();
        $graphCode = is_array($payload) ? (int) data_get($payload, 'error.code', 0) : 0;
        $subcode = is_array($payload) ? (int) data_get($payload, 'error.error_subcode', 0) : 0;
        $status = $response->status();

        $class = $this->classify($status, $graphCode, $subcode);

        return new InstagramApiException($class, $class->name, $status, $graphCode ?: null);
    }

    public function fromThrowable(Throwable $exception): InstagramApiException
    {
        if ($exception instanceof InstagramApiException) {
            return $exception;
        }

        if ($exception instanceof ConnectionException) {
            return new InstagramApiException(
                InstagramErrorClass::Timeout,
                'Instagram API connection timed out.',
                previous: $exception,
            );
        }

        return new InstagramApiException(
            InstagramErrorClass::Transient,
            'Instagram API request failed.',
            previous: $exception,
        );
    }

    public function classify(int $status, int $graphCode = 0, int $subcode = 0): InstagramErrorClass
    {
        if (in_array($graphCode, [190, 102, 467], true) || in_array($subcode, [463, 467, 460], true)) {
            return InstagramErrorClass::Authentication;
        }

        if (in_array($graphCode, [10, 200, 294], true) || $status === 403) {
            return InstagramErrorClass::Permission;
        }

        if (in_array($graphCode, [4, 17, 32, 613, 80001], true) || $status === 429) {
            return InstagramErrorClass::RateLimit;
        }

        if ($status === 400 || $status === 404) {
            return InstagramErrorClass::Malformed;
        }

        if ($status >= 500) {
            return InstagramErrorClass::Transient;
        }

        return InstagramErrorClass::Malformed;
    }
}
