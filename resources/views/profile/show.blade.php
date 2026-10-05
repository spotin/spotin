<x-default-layout>
    <h1>{{ __('ui.profile.title') }}</h1>

    @if ($errors->any())
        <div class="alert alert-error mb-4">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('user-profile-information.update') }}" data-validate-submit>
        @csrf
        @method('PUT')

        <fieldset class="fieldset mb-4 gap-3">
            <legend class="fieldset-legend">{{ __('ui.common.fill_the_form') }}</legend>

            <div>
                <label
                    class="floating-label input validator w-full"
                    data-server-invalid="{{ $errors->has('username') ? 'true' : 'false' }}"
                >
                    <span>{{ __('ui.auth.profile.form.fields.username.placeholder') }}</span>
                    <x-lucide-user-round class="h-[1em] opacity-50" />
                    <input
                        name="username"
                        type="text"
                        required
                        placeholder="{{ __('ui.auth.profile.form.fields.username.label') }}"
                        pattern="[A-Za-z][A-Za-z0-9\-_]*"
                        minlength="3"
                        maxlength="30"
                        title="{{ __('ui.auth.profile.form.fields.username.hint') }}"
                        value="{{ old('username', auth()->user()->username) }}"
                        class="input w-full"
                    />
                </label>
                <span
                    class="validator-hint mt-0 hidden"
                    data-client-hint="{{ __('ui.auth.profile.form.fields.username.hint') }}"
                    aria-live="polite"
                >
                    @error('username') {{ $message }}@else {{ __('ui.auth.profile.form.fields.username.hint') }}@enderror
                </span>
            </div>

            <div>
                <label
                    class="floating-label input validator w-full"
                    data-server-invalid="{{ $errors->has('name') ? 'true' : 'false' }}"
                >
                    <span>{{ __('ui.auth.profile.form.fields.name.placeholder') }}</span>
                    <x-lucide-id-card class="h-[1em] opacity-50" />
                    <input
                        name="name"
                        type="text"
                        required
                        placeholder="{{ __('ui.auth.profile.form.fields.name.label') }}"
                        pattern="[A-Za-z][A-Za-z' -]*"
                        minlength="2"
                        maxlength="50"
                        title="{{ __('ui.auth.profile.form.fields.name.hint') }}"
                        value="{{ old('name', auth()->user()->name) }}"
                        class="input w-full"
                    />
                </label>
                <span
                    class="validator-hint mt-0 hidden"
                    data-client-hint="{{ __('ui.auth.profile.form.fields.name.hint') }}"
                    aria-live="polite"
                >
                    @error('name') {{ $message }}@else {{ __('ui.auth.profile.form.fields.name.hint') }}@enderror
                </span>
            </div>

            <div>
                <label
                    class="floating-label input validator w-full"
                    data-server-invalid="{{ $errors->has('email') ? 'true' : 'false' }}"
                >
                    <span>{{ __('ui.auth.profile.form.fields.email.placeholder') }}</span>
                    <x-lucide-mail class="h-[1em] opacity-50" />
                    <input
                        name="email"
                        type="email"
                        required
                        placeholder="{{ __('ui.auth.profile.form.fields.email.label') }}"
                        maxlength="254"
                        title="{{ __('ui.auth.profile.form.fields.email.hint') }}"
                        value="{{ old('email', auth()->user()->email) }}"
                        class="input w-full"
                    />
                </label>
                <span
                    class="validator-hint mt-0 hidden"
                    data-client-hint="{{ __('ui.auth.profile.form.fields.email.hint') }}"
                    aria-live="polite"
                >
                    @error('email') {{ $message }}@else {{ __('ui.auth.profile.form.fields.email.hint') }}@enderror
                </span>
            </div>

            <div>
                <label
                    class="floating-label input validator w-full"
                    data-server-invalid="{{ $errors->has('password') ? 'true' : 'false' }}"
                >
                    <span>{{ __('ui.auth.profile.form.fields.password.placeholder') }}</span>
                    <x-lucide-lock-keyhole class="h-[1em] opacity-50" />
                    <input
                        name="password"
                        type="password"
                        required
                        placeholder="{{ __('ui.auth.profile.form.fields.password.label') }}"
                        minlength="8"
                        maxlength="128"
                        title="{{ __('ui.auth.profile.form.fields.password.hint') }}"
                        class="input w-full"
                    />
                </label>
                <span
                    class="validator-hint mt-0 hidden"
                    data-client-hint="{{ __('ui.auth.profile.form.fields.password.hint') }}"
                    aria-live="polite"
                >
                    @error('password') {{ $message }}@else {{ __('ui.auth.profile.form.fields.password.hint') }}@enderror
                </span>
            </div>

            <div>
                <label
                    class="floating-label input validator w-full"
                    data-server-invalid="{{ $errors->has('password') ? 'true' : 'false' }}"
                >
                    <span>{{ __('ui.auth.profile.form.fields.confirm_password.placeholder') }}</span>
                    <x-lucide-lock-keyhole-open class="h-[1em] opacity-50" />
                    <input
                        name="password_confirmation"
                        type="password"
                        required
                        placeholder="{{ __('ui.auth.profile.form.fields.confirm_password.label') }}"
                        minlength="8"
                        maxlength="128"
                        title="{{ __('ui.auth.profile.form.fields.confirm_password.hint') }}"
                        class="input w-full"
                    />
                </label>
                <span
                    class="validator-hint mt-0 hidden"
                    data-client-hint="{{ __('ui.auth.profile.form.fields.confirm_password.hint') }}"
                    aria-live="polite"
                >
                    @error('password') {{ $message }}@else {{ __('ui.auth.profile.form.fields.confirm_password.hint') }}@enderror
                </span>
            </div>
        </fieldset>

        <footer>
            <button type="submit" class="btn btn-block" disabled>
                {{ __('ui.auth.profile.form.actions.submit') }}
            </button>
        </footer>
    </form>
    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="btn btn-block btn-secondary mt-4">{{ __('ui.common.logout') }}</button>
    </form>
</x-default-layout>
