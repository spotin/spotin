<x-default-layout>
    <h1>{{ __('ui.auth.forgot_password.title') }}</h1>
    <p>{{ __('ui.auth.forgot_password.description') }}</p>

    @if (session('status'))
        <p role="alert" class="alert alert-success">
            <x-lucide-check-circle class="h-6 w-6" />
            <span>{{ session('status') }}</span>
        </p>
    @endif

    <form method="POST" action="{{ route('password.email') }}" data-validate-submit>
        @csrf

        <fieldset class="fieldset mb-4 gap-3">
            <legend class="fieldset-legend">{{ __('ui.common.fill_the_form') }}</legend>

            <div>
                <label
                    class="floating-label input validator w-full"
                    data-server-invalid="{{ $errors->has('email') ? 'true' : 'false' }}"
                >
                    <span>{{ __('ui.auth.forgot_password.form.fields.email.placeholder') }}</span>
                    <x-lucide-mail class="h-[1em] opacity-50" />
                    <input
                        name="email"
                        type="email"
                        required
                        placeholder="{{ __('ui.auth.forgot_password.form.fields.email.label') }}"
                        maxlength="254"
                        title="{{ __('ui.auth.forgot_password.form.fields.email.hint') }}"
                        value="{{ old('email') }}"
                        class="input w-full"
                    />
                </label>
                <span
                    class="validator-hint mt-0 hidden"
                    data-client-hint="{{ __('ui.auth.forgot_password.form.fields.email.hint') }}"
                    aria-live="polite"
                >
                    @error('email') {{ $message }}@else {{ __('ui.auth.register.form.fields.email.hint') }}@enderror
                </span>
            </div>
        </fieldset>

        <footer>
            <button type="submit" class="btn btn-block" disabled>
                {{ __('ui.auth.forgot_password.form.actions.submit') }}
            </button>
        </footer>
    </form>
</x-default-layout>
