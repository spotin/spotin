<x-default-layout :title="__('ui.auth.sign_up.title')" :description="__('ui.auth.sign_up.description')">
    <h1>{{ __('ui.auth.sign_up.title') }}</h1>

    <form method="POST" action="{{ url('/auth/register') }}">
        @csrf

        <fieldset class="fieldset mb-4 gap-3">
            <legend class="fieldset-legend">{{ __('ui.common.fill_the_form') }}</legend>

            <div>
                <label class="floating-label input w-full validator">
                <span>{{ __('ui.auth.sign_up.form.fields.username.placeholder') }}</span>
                <x-lucide-user-round class="h-[1em] opacity-50" />
                <input
                    type="text"
                    required
                    placeholder="{{ __('ui.auth.sign_up.form.fields.username.label') }}"
                    pattern="[A-Za-z][A-Za-z0-9\-]*"
                    minlength="3"
                    maxlength="30"
                    title="{{ __('ui.auth.sign_up.form.fields.username.hint') }}"
                    class="input w-full"
                />
                </label>
                <span class="validator-hint hidden mt-0">{{ __('ui.auth.sign_up.form.fields.username.hint') }}</span>
            </div>

            <div>
                <label class="floating-label input w-full validator">
                <span>{{ __('ui.auth.sign_up.form.fields.name.placeholder') }}</span>
                <x-lucide-id-card class="h-[1em] opacity-50" />
                <input
                    type="text"
                    required
                    placeholder="{{ __('ui.auth.sign_up.form.fields.name.label') }}"
                    pattern="[A-Za-z][A-Za-z' -]*"
                    minlength="2"
                    maxlength="50"
                    title="{{ __('ui.auth.sign_up.form.fields.name.hint') }}"
                    class="input w-full"
                />
                </label>
                <span class="validator-hint hidden mt-0">{{ __('ui.auth.sign_up.form.fields.name.hint') }}</span>
            </div>

            <div>
                <label class="floating-label input w-full validator">
                <span>{{ __('ui.auth.sign_up.form.fields.email.placeholder') }}</span>
                <x-lucide-mail class="h-[1em] opacity-50" />
                <input
                    type="email"
                    required
                    placeholder="{{ __('ui.auth.sign_up.form.fields.email.label') }}"
                    maxlength="254"
                    title="{{ __('ui.auth.sign_up.form.fields.email.hint') }}"
                    class="input w-full"
                />
                </label>
                <span class="validator-hint hidden mt-0">{{ __('ui.auth.sign_up.form.fields.email.hint') }}</span>
            </div>

            <div>
                <label class="floating-label input w-full validator">
                <span>{{ __('ui.auth.sign_up.form.fields.password.placeholder') }}</span>
                <x-lucide-lock-keyhole class="h-[1em] opacity-50" />
                <input
                    type="password"
                    required
                    placeholder="{{ __('ui.auth.sign_up.form.fields.password.label') }}"
                    minlength="8"
                    maxlength="72"
                    title="{{ __('ui.auth.sign_up.form.fields.password.hint') }}"
                    class="input w-full"
                />
                </label>
                <span class="validator-hint hidden mt-0">{{ __('ui.auth.sign_up.form.fields.password.hint') }}</span>
            </div>

            <div>
                <label class="floating-label input w-full validator">
                <span>{{ __('ui.auth.sign_up.form.fields.confirm_password.placeholder') }}</span>
                <x-lucide-lock-keyhole-open class="h-[1em] opacity-50" />
                <input
                    type="password"
                    required
                    placeholder="{{ __('ui.auth.sign_up.form.fields.confirm_password.label') }}"
                    minlength="8"
                    maxlength="72"
                    title="{{ __('ui.auth.sign_up.form.fields.confirm_password.hint') }}"
                    class="input w-full"
                />
                </label>
                <span class="validator-hint hidden mt-0">{{ __('ui.auth.sign_up.form.fields.confirm_password.hint') }}</span>
            </div>
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
