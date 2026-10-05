<x-default-layout>
    <h1>{{ __('ui.auth.reset_password.title') }}</h1>

    <form method="POST" action="{{ route('password.update') }}" data-validate-submit>
        @csrf

        <input type="hidden" name="token" value="{{ request()->route('token') }}">

        <fieldset class="fieldset mb-4 gap-3">
            <legend class="fieldset-legend">{{ __('ui.common.fill_the_form') }}</legend>

            <div>
                <label
                    class="floating-label input validator w-full"
                    data-server-invalid="{{ $errors->has('email') ? 'true' : 'false' }}"
                >
                    <span>{{ __('ui.auth.reset_password.form.fields.email.placeholder') }}</span>
                    <x-lucide-mail class="h-[1em] opacity-50" />
                    <input
                        name="email"
                        type="email"
                        required
                        placeholder="{{ __('ui.auth.reset_password.form.fields.email.label') }}"
                        maxlength="254"
                        title="{{ __('ui.auth.reset_password.form.fields.email.hint') }}"
                        value="{{ request('email') }}"
                        class="input w-full"
                    />
                </label>
                <span
                    class="validator-hint mt-0 hidden"
                    data-client-hint="{{ __('ui.auth.reset_password.form.fields.email.hint') }}"
                    aria-live="polite"
                >
                    @error('email') {{ $message }}@else {{ __('ui.auth.reset_password.form.fields.email.hint') }}@enderror
                </span>
            </div>

            <div>
                <label
                    class="floating-label input validator w-full"
                    data-server-invalid="{{ $errors->has('password') ? 'true' : 'false' }}"
                >
                    <span>{{ __('ui.auth.reset_password.form.fields.password.placeholder') }}</span>
                    <x-lucide-lock-keyhole class="h-[1em] opacity-50" />
                    <input
                        name="password"
                        type="password"
                        required
                        placeholder="{{ __('ui.auth.reset_password.form.fields.password.label') }}"
                        minlength="8"
                        maxlength="128"
                        title="{{ __('ui.auth.reset_password.form.fields.password.hint') }}"
                        class="input w-full"
                    />
                </label>
                <span
                    class="validator-hint mt-0 hidden"
                    data-client-hint="{{ __('ui.auth.reset_password.form.fields.password.hint') }}"
                    aria-live="polite"
                >
                    @error('password') {{ $message }}@else {{ __('ui.auth.reset_password.form.fields.password.hint') }}@enderror
                </span>
            </div>

            <div>
                <label
                    class="floating-label input validator w-full"
                    data-server-invalid="{{ $errors->has('password') ? 'true' : 'false' }}"
                >
                    <span>{{ __('ui.auth.reset_password.form.fields.confirm_password.placeholder') }}</span>
                    <x-lucide-lock-keyhole-open class="h-[1em] opacity-50" />
                    <input
                        name="password_confirmation"
                        type="password"
                        required
                        placeholder="{{ __('ui.auth.reset_password.form.fields.confirm_password.label') }}"
                        minlength="8"
                        maxlength="128"
                        title="{{ __('ui.auth.reset_password.form.fields.confirm_password.hint') }}"
                        class="input w-full"
                    />
                </label>
                <span
                    class="validator-hint mt-0 hidden"
                    data-client-hint="{{ __('ui.auth.reset_password.form.fields.confirm_password.hint') }}"
                    aria-live="polite"
                >
                    @error('password') {{ $message }}@else {{ __('ui.auth.reset_password.form.fields.confirm_password.hint') }}@enderror
                </span>
            </div>
        </fieldset>

        <footer>
        <button type="submit" class="btn btn-block">{{ __('ui.auth.reset_password.form.actions.submit') }}</button>

            <p class="text-center text-sm">
                {{ __('ui.auth.reset_password.already_have_an_account') }}
                <a href="{{ route('login') }}"> {{ __('ui.auth.reset_password.login') }} </a>
            </p>
        </footer>
    </form>
</x-default-layout>
