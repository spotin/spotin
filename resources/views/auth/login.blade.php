<x-default-layout :title="__('ui.auth.login.title')" :description="__('ui.auth.login.description')">
    <h1>{{ __('ui.auth.login.title') }}</h1>

    <form method="POST" action="{{ route('login') }}" data-validate-submit>
        @csrf

        <fieldset class="fieldset mb-4 gap-3">
            <legend class="fieldset-legend">{{ __('ui.common.fill_the_form') }}</legend>

            @if ($errors->any())
                Error
            @endif

            <div>
                <label
                    class="floating-label input validator w-full"
                    data-server-invalid="{{ $errors->has('username') ? 'true' : 'false' }}"
                >
                    <span>{{ __('ui.auth.login.form.fields.username_or_email.placeholder') }}</span>
                    <x-lucide-user-round class="h-[1em] opacity-50" />
                    <input
                        name="username"
                        type="text"
                        required
                        placeholder="{{ __('ui.auth.login.form.fields.username_or_email.label') }}"
                        title="{{ __('ui.auth.login.form.fields.username_or_email.hint') }}"
                        value="{{ old('username') }}"
                        class="input w-full"
                    />
                </label>
                <span
                    class="validator-hint mt-0 hidden"
                    data-client-hint="{{ __('ui.auth.login.form.fields.username_or_email.hint') }}"
                    aria-live="polite"
                >
                    @error('username') {{ $message }}@else {{ __('ui.auth.login.form.fields.username_or_email.hint') }}@enderror
                </span>
            </div>

            <div>
                <label
                    class="floating-label input validator w-full"
                    data-server-invalid="{{ $errors->has('password') ? 'true' : 'false' }}"
                >
                    <span>{{ __('ui.auth.login.form.fields.password.placeholder') }}</span>
                    <x-lucide-lock-keyhole class="h-[1em] opacity-50" />
                    <input
                        name="password"
                        type="password"
                        required
                        placeholder="{{ __('ui.auth.login.form.fields.password.label') }}"
                        minlength="8"
                        maxlength="128"
                        title="{{ __('ui.auth.login.form.fields.password.hint') }}"
                        class="input w-full"
                    />
                </label>
                <span
                    class="validator-hint mt-0 hidden"
                    data-client-hint="{{ __('ui.auth.login.form.fields.password.hint') }}"
                    aria-live="polite"
                >
                    @error('password') {{ $message }}@else {{ __('ui.auth.login.form.fields.password.hint') }}@enderror
                </span>
            </div>
        </fieldset>

        <footer>
            <button type="submit" class="btn btn-block" disabled>{{ __('ui.auth.login.form.actions.submit') }}</button>

            <p class="text-center text-sm">
                {{ __('ui.auth.login.dont_have_an_account') }}
                <a href="{{ route('register') }}"> {{ __('ui.auth.login.register') }} </a>
            </p>
        </footer>
    </form>
</x-default-layout>
