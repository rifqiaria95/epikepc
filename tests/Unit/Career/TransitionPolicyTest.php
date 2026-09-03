<?php

use App\Enums\Career\ApplicationStatus;
use App\Services\Career\JobApplicationTransitionService;

it('defines the locked career transition matrix', function () {
    $service = app(JobApplicationTransitionService::class);

    expect($service->isAllowed(ApplicationStatus::Submitted, ApplicationStatus::Screening))->toBeTrue()
        ->and($service->isAllowed(ApplicationStatus::Submitted, ApplicationStatus::Hired))->toBeFalse()
        ->and($service->isAllowed(ApplicationStatus::Hired, ApplicationStatus::Screening))->toBeFalse()
        ->and($service->allowedTargets(ApplicationStatus::Offered))->toBe(['HIRED', 'REJECTED', 'WITHDRAWN']);
});
