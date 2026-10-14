<?php

use Illuminate\View\View;
use Laravel\Head\Facades\Head;

use function Laravel\Folio\middleware;
use function Laravel\Folio\name;
use function Laravel\Folio\render;

middleware(['auth', 'verified']);
name('spots.create');

render(function (View $view) {
    Head::title(__('ui.spots.create.title'))->description(__('ui.spots.create.description'));

    $breadcrumbs = [
        ['label' => __('ui.spots.index.title'), 'url' => route('spots.index')],
        ['label' => __('ui.spots.create.title')],
    ];

    return $view->with([
        'breadcrumbs' => $breadcrumbs,
        'organizations' => auth()->user()->spotOrganizations()->orderBy('organizations.name')->get(),
        'selected' => request()->query('organization'),
    ]);
});
?>

<x-default-layout :breadcrumbs="$breadcrumbs">
    <h1>{{ __('ui.spots.create.title') }}</h1>

    @if ($organizations->isEmpty())
        <p>{{ __('ui.spots.create.no-organization') }}</p>
    @else
        <form method="POST" action="{{ route('spots.store') }}">
            @csrf

            <fieldset class="fieldset mb-4 gap-3">
                <legend class="fieldset-legend">{{ __('ui.common.fill-the-form') }}</legend>

                <div>
                    <label class="select w-full">
                        <span class="label">
                            <x-lucide-building-2 class="h-[1em]" />
                        </span>
                        <select
                            name="organization"
                            required
                            @error('organization') aria-invalid="true" @enderror
                            class="invalid:text-base-content/50! [&>option]:text-base-content"
                        >
                            <option value="" disabled @selected(! old('organization', $selected))>
                                {{ __('ui.spots.common.forms.fields.organization.placeholder') }}
                            </option>
                            @foreach ($organizations as $organization)
                                <option
                                    value="{{ $organization->uuid }}"
                                    @selected(old('organization', $selected) === $organization->uuid)
                                >
                                    {{ $organization->name }}
                                </option>
                            @endforeach
                        </select>
                    </label>
                    @error('organization')
                        <span class="validator-hint">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label class="floating-label input validator w-full">
                        <span>{{ __('ui.spots.common.forms.fields.name.placeholder') }}</span>
                        <x-lucide-map-pin class="h-[1em] opacity-50" />
                        <input
                            name="name"
                            type="text"
                            required
                            placeholder="{{ __('ui.spots.common.forms.fields.name.label') }}"
                            value="{{ old('name') }}"
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
                <button type="submit" class="btn btn-block">{{ __('ui.spots.create.form.actions.submit') }}</button>
            </footer>
        </form>
    @endif
</x-default-layout>
