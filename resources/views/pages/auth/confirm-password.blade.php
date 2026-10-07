<x-default-layout>
    <h1>{{ __('ui.auth.password_confirmation.title') }}</h1>
    <p>{{ __('ui.auth.password_confirmation.explanation') }}</p>

    <form method="POST" action="{{ route('password.confirm') }}">
        @csrf

        <fieldset class="fieldset mb-4 gap-3">
            <legend class="fieldset-legend">{{ __('ui.common.fill_the_form') }}</legend>

            <div>
                <label class="floating-label input validator w-full">
                    <span>{{ __('ui.auth.password_confirmation.form.fields.password.placeholder') }}</span>
                    <x-lucide-lock-keyhole class="h-[1em] opacity-50" />
                    <input
                        name="password"
                        type="password"
                        required
                        autocomplete="current-password"
                        placeholder="{{ __('ui.auth.password_confirmation.form.fields.password.label') }}"
                        @error('password') aria-invalid="true" @enderror
                        class="input w-full"
                    />
                </label>
                @error('password')
                    <span class="validator-hint">{{ $message }}</span>
                @enderror
            </div>
        </fieldset>

        <button type="submit" class="btn btn-block">
            {{ __('ui.auth.password_confirmation.form.actions.submit') }}
        </button>
    </form>
</x-default-layout>
