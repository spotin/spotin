<?php

use Illuminate\View\View;
use Laravel\Head\Facades\Head;

use function Laravel\Folio\name;
use function Laravel\Folio\render;

name('index');

render(function (View $view) {
    Head::title(__('ui.welcome.title'))
        ->description(__('ui.welcome.description'));

    return $view;
});
?>

<x-default-layout>
    <article class="prose lg:prose-xl">
        <h1>{{ __('ui.welcome.title') }}</h1>

        <p>{{ __('ui.welcome.first-paragraph') }}</p>

        <p>{{ __('ui.welcome.second-paragraph') }}</p>

        Test PR.

        <div>
            <button class="btn">{{ __('ui.welcome.button') }}</button>
        </div>
    </article>
</x-default-layout>
