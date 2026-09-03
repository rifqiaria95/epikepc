<?php

use App\Support\Menu\MenuActiveMatcher;
use Illuminate\Http\Request;

beforeEach(function () {
    $this->matcher = new MenuActiveMatcher(Request::create('/'));
});

it('marks an exact route as active', function () {
    $this->matcher = new MenuActiveMatcher(Request::create('/internal/career/vacancies'));

    expect($this->matcher->isRouteActive('/internal/career/vacancies'))->toBeTrue()
        ->and($this->matcher->itemClass('/internal/career/vacancies'))->toBe('active');
});

it('marks nested routes as active without activating sibling menu items', function () {
    $this->matcher = new MenuActiveMatcher(Request::create('/internal/career/applications/abc-123'));

    $peers = [
        '/internal/career',
        '/internal/career/vacancies',
        '/internal/career/applications',
        '/internal/career/candidates',
    ];

    expect($this->matcher->isRouteActive('/internal/career/applications', $peers))->toBeTrue()
        ->and($this->matcher->isRouteActive('/internal/career', $peers))->toBeFalse()
        ->and($this->matcher->isRouteActive('/internal/career/vacancies', $peers))->toBeFalse();
});

it('opens a parent menu group when a child route is active', function () {
    $this->matcher = new MenuActiveMatcher(Request::create('/internal/career/candidates'));

    $class = $this->matcher->groupClass([
        '/internal/career',
        '/internal/career/vacancies',
        '/internal/career/applications',
        '/internal/career/candidates',
    ]);

    expect($class)->toBe('active open');
});

it('matches legacy frontend menu routes against internal paths', function () {
    $this->matcher = new MenuActiveMatcher(Request::create('/internal/project'));

    expect($this->matcher->isRouteActive('/frontend/project'))->toBeTrue();
});

it('marks dashboard as active only on dashboard path', function () {
    $this->matcher = new MenuActiveMatcher(Request::create('/dashboard'));

    expect($this->matcher->isRouteActive('/dashboard'))->toBeTrue()
        ->and($this->matcher->isRouteActive('/dashboard/settings'))->toBeFalse();
});
