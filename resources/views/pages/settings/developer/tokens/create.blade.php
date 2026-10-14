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

        <fieldset class="fieldset mb-4 gap-3">
            <legend class="fieldset-legend">{{ __('ui.common.fill_the_form') }}</legend>

            <div>
                <label class="floating-label input validator w-full">
                    <span>{{ __('ui.settings.developer.common.forms.fields.name.placeholder') }}</span>
                    <x-lucide-key-round class="h-[1em] opacity-50" />
                    <input
                        name="name"
                        type="text"
                        required
                        maxlength="100"
                        placeholder="{{ __('ui.settings.developer.common.forms.fields.name.label') }}"
                        value="{{ old('name') }}"
                        @error('name') aria-invalid="true" @enderror
                        class="input w-full"
                    />
                </label>
                @error('name')
                    <span class="validator-hint">{{ $message }}</span>
                @enderror
            </div>

            @php($abilitiesError = $errors->first('abilities') ?: $errors->first('abilities.*'))

            <fieldset class="fieldset validator rounded-box border-base-content/5 bg-base-100 border p-4">
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

            <div>
                <label class="select validator w-full">
                    <span class="label">
                        <x-lucide-clock-fading class="h-[1em]" />
                    </span>
                    <select
                        name="expiry"
                        required
                        aria-label="{{ __('ui.settings.developer.common.forms.fields.expiry.label') }}"
                        @error('expiry') aria-invalid="true" @enderror
                        class="invalid:text-base-content/50! [&>option]:text-base-content"
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
                </label>
                @error('expiry')
                    <span class="validator-hint">{{ $message }}</span>
                @enderror
            </div>
        </fieldset>

        <footer>
            <button type="submit" class="btn btn-block">
                {{ __('ui.settings.developer.create.form.actions.submit') }}
            </button>
        </footer>
    </form>
</x-default-layout>
