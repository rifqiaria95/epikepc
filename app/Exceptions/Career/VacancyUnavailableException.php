<?php

namespace App\Exceptions\Career;

class VacancyUnavailableException extends CareerDomainException
{
    public function __construct(string $message = 'Lowongan ini sudah ditutup dan tidak lagi menerima lamaran.')
    {
        parent::__construct($message, 409, ['job_vacancy' => [$message]]);
    }
}
