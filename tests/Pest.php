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
