<?php

use App\Enums\TokenAbility;
use App\Enums\TokenExpiry;
use App\Support\Breadcrumbs;
use Illuminate\View\View;
use Laravel\Head\Facades\Head;

use function Laravel\Folio\middleware;
use function Laravel\Folio\name;
use function Laravel\Folio\render;

middleware(['auth', 'verified']);
name('settings.developer.tokens.create');

render(function (View $view) {
    Head::title(__('ui.settings.developer.create.title'))
        ->description(__('ui.settings.developer.create.description'));

    $breadcrumbs = [...Breadcrumbs::developer(), ['label' => __('ui.settings.developer.create.title')]];

    return $view->with([
        'breadcrumbs' => $breadcrumbs,
        'abilities' => TokenAbility::forUser(auth()->user()),
        'expiries' => TokenExpiry::cases(),
    ]);
});
?>

<x-default-layout :breadcrumbs="$breadcrumbs">
    <h1>{{ __('ui.settings.developer.create.title') }}</h1>

    <p>{{ __('ui.settings.developer.create.description') }}</p>

    <form method="POST" action="{{ route('settings.developer.tokens.store') }}">
        @csrf

        <fieldset class="fieldset">
            <legend class="fieldset-legend">{{ __('ui.settings.developer.common.forms.fields.name.label') }}</legend>
            <input
                name="name"
                type="text"
                class="input validator w-full"
                required
                placeholder="{{ __('ui.settings.developer.common.forms.fields.name.placeholder') }}"
                value="{{ old('name') }}"
                @error('name') aria-invalid="true" @enderror
            />
            @error('name')
                <span class="validator-hint mt-0">{{ $message }}</span>
            @enderror
        </fieldset>

        @php($abilitiesError = $errors->first('abilities') ?: $errors->first('abilities.*'))

        <fieldset class="fieldset validator">
            <legend class="fieldset-legend">
                {{ __('ui.settings.developer.common.forms.fields.abilities.label') }}
            </legend>

            @foreach ($abilities as $ability)
                <label class="label text-base-content text-sm">
                    <input
                        type="checkbox"
                        name="abilities[]"
                        value="{{ $ability->value }}"
                        class="checkbox"
                        @checked(in_array($ability->value, old('abilities', []), true))
                        @if ($abilitiesError) aria-invalid="true" @endif
                    />
                    <span class="sm:w-56"> {{ $ability->label() }} </span>
                    <code class="badge badge-sm badge-ghost not-prose font-mono">{{ $ability->value }}</code>
                </label>
            @endforeach
        </fieldset>
        @if ($abilitiesError)
            <span class="validator-hint mt-0">{{ $abilitiesError }}</span>
        @endif

        <fieldset class="fieldset">
            <legend class="fieldset-legend">{{ __('ui.settings.developer.common.forms.fields.expiry.label') }}</legend>
            <select
                name="expiry"
                class="select validator invalid:text-base-content/50! [&>option]:text-base-content w-full"
                required
                @error('expiry') aria-invalid="true" @enderror
            >
                <option value="" disabled @selected(old('expiry') === null)>
                    {{ __('ui.settings.developer.common.forms.fields.expiry.placeholder') }}
                </option>
                @foreach ($expiries as $expiry)
                    <option value="{{ $expiry->value }}" @selected(old('expiry') === $expiry->value)>
                        {{ $expiry->label() }}
                    </option>
                @endforeach
            </select>
            @error('expiry')
                <span class="validator-hint mt-0">{{ $message }}</span>
            @enderror
        </fieldset>

        <footer class="mt-4">
            <button type="submit" class="btn btn-block">
                {{ __('ui.settings.developer.create.form.actions.submit') }}
            </button>
        </footer>
    </form>
</x-default-layout>
