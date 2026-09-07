<?php

namespace App\Support\Instagram;

use App\Exceptions\Instagram\InstagramApiException;
use Illuminate\Support\Facades\Log;

class InstagramLogger
{
    /**
     * @param  array<string, mixed>  $context
     */
    public function info(string $message, array $context = []): void
    {
        $this->write('info', $message, $context);
    }

    /**
     * @param  array<string, mixed>  $context
     */
    public function warning(string $message, array $context = []): void
    {
        $this->write('warning', $message, $context);
    }

    /**
     * @param  array<string, mixed>  $context
     */
    public function error(string $message, array $context = []): void
    {
        $this->write('error', $message, $context);
    }

    /**
     * @param  array<string, mixed>  $context
     */
    protected function write(string $level, string $message, array $context): void
    {
        $payload = $this->safe($context);

        try {
            Log::channel($this->channel())->{$level}($message, $payload);
        } catch (\Throwable) {
            Log::{$level}($message, $payload);
        }
    }

    public function exception(InstagramApiException $exception, string $operation): void
    {
        $this->error('Instagram API operation failed.', [
            'operation' => $operation,
            'error_class' => $exception->errorClass->value,
            'http_status' => $exception->httpStatus,
            'graph_code' => $exception->graphCode,
        ]);
    }

    /**
     * @param  array<string, mixed>  $context
     * @return array<string, mixed>
     */
    public function safe(array $context): array
    {
        $encoded = json_encode($context, JSON_UNESCAPED_SLASHES) ?: '';
        $token = (string) config('instagram.access_token');

        if ($token !== '') {
            $encoded = str_replace($token, '[redacted]', $encoded);
        }

        $encoded = preg_replace(
            '/(access_token|authorization)([\"\'=\s:]+)[^\"\'&\s,}]+/i',
            '$1$2[redacted]',
            $encoded
        ) ?: $encoded;

        $decoded = json_decode($encoded, true);

        return is_array($decoded) ? $decoded : ['context' => '[unserializable]'];
    }

    public function containsSecret(string $haystack): bool
    {
        $token = (string) config('instagram.access_token');

        return $token !== '' && str_contains($haystack, $token);
    }

    protected function channel(): string
    {
        return config('logging.channels.instagram') ? 'instagram' : config('logging.default', 'stack');
    }
}
