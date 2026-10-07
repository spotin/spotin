<x-default-layout>
    <h1>{{ __('ui.auth.login.title') }}</h1>

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <fieldset class="fieldset mb-4 gap-3">
            <legend class="fieldset-legend">{{ __('ui.common.fill_the_form') }}</legend>

            <div>
                <label class="floating-label input validator w-full">
                    <span>{{ __('ui.auth.login.form.fields.username_or_email.placeholder') }}</span>
                    <x-lucide-user-round class="h-[1em] opacity-50" />
                    <input
                        name="username"
                        type="text"
                        required
                        placeholder="{{ __('ui.auth.login.form.fields.username_or_email.label') }}"
                        value="{{ old('username') }}"
                        @error('username') aria-invalid="true" @enderror
                        class="input w-full"
                    />
                </label>
                @error('username')
                    <span class="validator-hint">{{ $message }}</span>
                @enderror
            </div>

            <div>
                <label class="floating-label input validator w-full">
                    <span>{{ __('ui.auth.login.form.fields.password.placeholder') }}</span>
                    <x-lucide-lock-keyhole class="h-[1em] opacity-50" />
                    <input
                        name="password"
                        type="password"
                        required
                        placeholder="{{ __('ui.auth.login.form.fields.password.label') }}"
                        @error('password') aria-invalid="true" @enderror
                        class="input w-full"
                    />
                </label>
                @error('password')
                    <span class="validator-hint">{{ $message }}</span>
                @enderror
            </div>
        </fieldset>

        <button type="submit" class="btn btn-block">{{ __('ui.auth.login.form.actions.submit') }}</button>
    </form>

    <p class="text-center text-sm">
        {{ __('ui.auth.login.dont_have_an_account') }}
        <a href="{{ route('register') }}"> {{ __('ui.auth.login.register') }} </a>
    </p>

    <p class="text-center text-sm">
        {{ __('ui.auth.login.forgot_your_password') }}
        <a href="{{ route('password.request') }}"> {{ __('ui.auth.login.reset_your_password') }} </a>
    </p>
</x-default-layout>
