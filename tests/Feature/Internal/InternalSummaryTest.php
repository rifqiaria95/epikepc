<?php

use App\Queries\Internal\InternalSummaryQuery;

it('aggregates internal listing metrics in single queries', function () {
    $cards = app(InternalSummaryQuery::class)->cards('services');

    expect($cards)->toHaveCount(4)
        ->and($cards[0])->toHaveKeys(['label', 'value', 'icon', 'color']);
});

it('exposes cards for all internal cms listing pages', function (string $page) {
    $cards = app(InternalSummaryQuery::class)->cards($page);

    expect($cards)->toHaveCount(4);
})->with([
    'projects',
    'services',
    'about',
    'galeri',
    'service_types',
    'kategori',
    'tags',
    'news',
    'pricing',
    'coverage',
    'testimoni',
    'organisasi',
    'consultation',
    'users',
    'roles',
    'permissions',
    'menu_groups',
    'menu_details',
    'sub_menu_details',
    'knowledge',
    'kategori_galeri',
    'trash',
]);
