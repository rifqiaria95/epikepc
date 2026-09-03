<?php

namespace App\Exceptions\Career;

class TokenException extends CareerDomainException
{
    public function __construct(string $message = 'Tautan tidak valid atau sudah tidak berlaku.', int $status = 410)
    {
        parent::__construct($message, $status, ['token' => [$message]]);
    }
}
