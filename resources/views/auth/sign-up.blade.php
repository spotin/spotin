<x-default-layout :title="__('ui.auth.sign_up.title')" :description="__('ui.auth.sign_up.description')">
    <h1>{{ __('ui.auth.sign_up.title') }}</h1>

    <form method="POST" action="{{ url('/auth/register') }}">
        @csrf

        <fieldset class="fieldset mb-4">
            <legend class="fieldset-legend">{{ __('ui.common.required_fields') }}</legend>

            <label class="label font-bold" for="username">{{ __('ui.auth.sign_up.form.fields.username.label') }}</label>
            <label class="input w-full">
                <x-lucide-pencil-line class="h-[1em] opacity-50" />
                <input
                    id="username"
                    type="text"
                    class="input w-full"
                    placeholder="{{ __('ui.auth.sign_up.form.fields.username.placeholder') }}"
                />
            </label>

            <label class="label font-bold" for="name">{{ __('ui.auth.sign_up.form.fields.name.label') }}</label>
            <label class="input w-full">
                <x-lucide-pencil-line class="h-[1em] opacity-50" />
                <input
                    id="name"
                    type="text"
                    class="input w-full"
                    placeholder="{{ __('ui.auth.sign_up.form.fields.name.placeholder') }}"
                />
            </label>

            <label class="label font-bold" for="email">{{ __('ui.auth.sign_up.form.fields.email.label') }}</label>
            <label class="input w-full">
                <x-lucide-pencil-line class="h-[1em] opacity-50" />
                <input
                    id="email"
                    type="email"
                    class="input w-full"
                    placeholder="{{ __('ui.auth.sign_up.form.fields.email.placeholder') }}"
                />
            </label>

            <label class="label font-bold" for="password">{{ __('ui.auth.sign_up.form.fields.password.label') }}</label>
            <label class="input w-full">
                <x-lucide-pencil-line class="h-[1em] opacity-50" />
                <input
                    id="password"
                    type="password"
                    class="input w-full"
                    placeholder="{{ __('ui.auth.sign_up.form.fields.password.placeholder') }}"
                />
            </label>

            <label
                class="label font-bold"
                for="password_confirmation"
            >{{ __('ui.auth.sign_up.form.fields.confirm_password.label') }}</label>
            <label class="input w-full">
                <x-lucide-pencil-line class="h-[1em] opacity-50" />
                <input
                    id="password_confirmation"
                    type="password"
                    class="input w-full"
                    placeholder="{{ __('ui.auth.sign_up.form.fields.confirm_password.placeholder') }}"
                />
            </label>
        </fieldset>

        <footer>
            <button type="submit" class="btn btn-block">{{ __('ui.auth.sign_up.form.actions.submit') }}</button>

            <p class="text-center text-sm">
                {{ __('ui.auth.sign_up.already_have_an_account') }}
                <a href="{{ route('auth.sign_in') }}"> {{ __('ui.auth.sign_up.sign_in') }} </a>
            </p>
        </footer>
    </form>
</x-default-layout>
