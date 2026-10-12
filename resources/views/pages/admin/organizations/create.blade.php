<?php

use App\Models\Organization;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Laravel\Head\Facades\Head;

use function Laravel\Folio\middleware;
use function Laravel\Folio\name;
use function Laravel\Folio\render;

middleware(['auth', 'verified']);
name('admin.organizations.create');

render(function (View $view) {
    Head::title(__('ui.admin.organizations.create.title'))
        ->description(__('ui.admin.organizations.create.description'));

    Gate::authorize('create', Organization::class);

    $breadcrumbs = [['label' => __('ui.admin.title'), 'url' => route('admin.index')], ['label' => __('ui.admin.organizations.index.title'), 'url' => route('admin.organizations.index')], ['label' => __('ui.admin.organizations.create.title')]];

    return $view->with('breadcrumbs', $breadcrumbs);
});
?>

<x-default-layout :breadcrumbs="$breadcrumbs">
    <h1>{{ __('ui.admin.organizations.create.title') }}</h1>

    <form method="POST" action="{{ route('admin.organizations.store') }}">
        @csrf

        <fieldset class="fieldset mb-4 gap-3">
            <legend class="fieldset-legend">{{ __('ui.common.fill-the-form') }}</legend>

            <div>
                <label class="floating-label input validator w-full">
                    <span>{{ __('ui.organizations.common.forms.fields.name.placeholder') }}</span>
                    <x-lucide-building-complex class="h-[1em] opacity-50" />
                    <input
                        name="name"
                        type="text"
                        required
                        placeholder="{{ __('ui.organizations.common.forms.fields.name.label') }}"
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
            <button type="submit" class="btn btn-block">
                {{ __('ui.admin.organizations.create.form.actions.submit') }}
            </button>
        </footer>
    </form>
</x-default-layout>
