<?php

namespace App\Exceptions\Career;

class InvalidTransitionException extends CareerDomainException
{
    public function __construct(string $message = 'Perubahan status tidak diizinkan.')
    {
        parent::__construct($message, 422, ['status' => [$message]]);
    }
}
