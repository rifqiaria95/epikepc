<?php

namespace App\Services\Career;

use App\Models\Career\JobApplication;
use App\Models\Career\JobVacancy;
use Illuminate\Support\Str;

class CareerReferenceNumberService
{
    public function nextVacancyCode(): string
    {
        $prefix = 'EPC-'.now()->format('ym');

        $latest = JobVacancy::query()
            ->withTrashed()
            ->where('code', 'like', $prefix.'%')
            ->orderByDesc('code')
            ->value('code');

        $seq = 1;
        if ($latest && preg_match('/(\d+)$/', $latest, $m)) {
            $seq = ((int) $m[1]) + 1;
        }

        return sprintf('%s-%03d', $prefix, $seq);
    }

    public function nextApplicationReference(): string
    {
        // Opaque, non-sequential: EPC-APP-{Ymd}-{random}
        do {
            $ref = 'EPC-APP-'.now()->format('ymd').'-'.Str::upper(Str::random(8));
        } while (JobApplication::query()->where('reference_number', $ref)->exists());

        return $ref;
    }
}
