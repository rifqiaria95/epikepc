<?php

namespace App\Exceptions\Career;

class DuplicateApplicationException extends CareerDomainException
{
    public function __construct(string $message = 'Anda sudah pernah melamar lowongan ini.')
    {
        parent::__construct($message, 409, ['email' => [$message]]);
    }
}
