<?php

use App\Models\Spot;
use App\Support\SpotBreadcrumbs;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Laravel\Head\Facades\Head;

use function Laravel\Folio\middleware;
use function Laravel\Folio\name;
use function Laravel\Folio\render;

middleware(['auth', 'verified']);
name('spots.edit');

render(function (View $view, Spot $spot) {
    Gate::authorize('update', $spot);

    Head::title(__('ui.spots.edit.title'))->description(__('ui.spots.edit.description'));

    $breadcrumbs = [...SpotBreadcrumbs::for($spot), ['label' => __('ui.spots.edit.title')]];

    return $view->with('breadcrumbs', $breadcrumbs);
});
?>

<x-default-layout :breadcrumbs="$breadcrumbs">
    <h1>{{ __('ui.spots.edit.title') }}</h1>

    @if (session('status') === 'spot-updated')
        <p role="alert" class="alert alert-success">
            <x-lucide-check-circle class="h-6 w-6" />
            <span>{{ __('ui.spots.feedback.spot-updated') }}</span>
        </p>
    @endif

    <form method="POST" action="{{ route('spots.update', $spot) }}">
        @csrf
        @method('PUT')

        <fieldset class="fieldset mb-4 gap-3">
            <legend class="fieldset-legend">{{ __('ui.common.fill-the-form') }}</legend>

            <div>
                <label class="floating-label input validator w-full">
                    <span>{{ __('ui.spots.common.forms.fields.name.placeholder') }}</span>
                    <x-lucide-map-pin class="h-[1em] opacity-50" />
                    <input
                        name="name"
                        type="text"
                        required
                        placeholder="{{ __('ui.spots.common.forms.fields.name.label') }}"
                        value="{{ old('name', $spot->name) }}"
                        @error('name') aria-invalid="true" @enderror
                        class="input w-full"
                    />
                </label>
                @error('name')
                    <span class="validator-hint">{{ $message }}</span>
                @enderror
            </div>
        </fieldset>

        <footer>
            <button type="submit" class="btn btn-block">{{ __('ui.spots.edit.form.actions.submit') }}</button>
        </footer>
    </form>
</x-default-layout>
