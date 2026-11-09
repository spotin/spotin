<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

pest()
    ->extend(TestCase::class)
    ->use(RefreshDatabase::class)
    ->in('Feature');

/**
 * An email address as the <x-email> component prints it, with its line break hints.
 */
function emailHtml(string $email): string
{
    return str_replace(['@', '.'], ['<wbr>@', '.<wbr>'], $email);
}

/**
 * Whether a checkbox of the filters of a list page, such as "mine", is checked.
 */
function filterIsChecked(string $html, string $name = 'mine'): bool
{
    preg_match('/<input[^>]*name="'.$name.'"[^>]*>/s', $html, $input);

    return isset($input[0]) && preg_match('/\schecked(\s|=|>|\/)/', $input[0]) === 1;
}

/**
 * The fields of a valid spot, to create one through a form or the API.
 */
function spotData(array $overrides = []): array
{
    return [
        'name' => 'Lighthouse',
        'latitude' => 46.5197,
        'longitude' => 6.6323,
        ...$overrides,
    ];
}
