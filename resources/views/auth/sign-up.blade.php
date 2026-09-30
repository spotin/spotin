<x-default-layout :title="__('ui.auth.sign_up.title')" :description="__('ui.auth.sign_up.description')">
    <fieldset class="fieldset bg-base-200 border-base-300 rounded-box w-xs border p-4">
        <legend class="fieldset-legend">Login</legend>

        <label class="label">Email</label>
        <input type="email" class="input" placeholder="Email" />

        <label class="label">Password</label>
        <input type="password" class="input" placeholder="Password" />

        <button class="btn btn-neutral mt-4">Login</button>
    </fieldset>


    <form method="POST" action="{{ url('/auth/register') }}">
        @csrf

        <fieldset class="fieldset">
            <legend class="fieldset-legend">Se créer un compte</legend>

            <label class="label" for="name">Name</label>
            <label class="input w-full">
                <svg class="h-[1em] opacity-50" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                    <g
                    stroke-linejoin="round"
                    stroke-linecap="round"
                    stroke-width="2.5"
                    fill="none"
                    stroke="currentColor"
                    >
                    <path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"></path>
                    <path d="M14 2v4a2 2 0 0 0 2 2h4"></path>
                    </g>
                </svg>
                <input type="text" class="input w-full " placeholder="Type here" />
            </label>
            <p class="label">Optional</p>
        </fieldset>

        <div class="mb-4">
            <label for="username" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                {{ __('ui.auth.register.form.fields.username.label') }}
            </label>
            <input
                id="username"
                type="text"
                name="username"
                value="{{ old('username') }}"
                required
                autofocus
                placeholder="{{ __('ui.auth.register.form.fields.username.placeholder') }}"
                class="w-full px-3 py-2 border rounded-md bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:ring-2 focus:border-transparent @error('username') border-red-500 focus:ring-red-500 @else border-gray-300 dark:border-gray-600 focus:ring-teal-500 dark:focus:ring-purple-500 @enderror"
            />
            @error('username')
                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label for="email" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                {{ __('ui.auth.register.form.fields.email.label') }}
            </label>
            <input
                id="email"
                type="email"
                name="email"
                value="{{ old('email') }}"
                required
                placeholder="{{ __('ui.auth.register.form.fields.email.placeholder') }}"
                class="w-full px-3 py-2 border rounded-md bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:ring-2 focus:border-transparent @error('email') border-red-500 focus:ring-red-500 @else border-gray-300 dark:border-gray-600 focus:ring-teal-500 dark:focus:ring-purple-500 @enderror"
            />
            @error('email')
                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label for="first_name" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                {{ __('ui.auth.register.form.fields.first_name.label') }}
            </label>
            <input
                id="first_name"
                type="text"
                name="first_name"
                value="{{ old('first_name') }}"
                required
                placeholder="{{ __('ui.auth.register.form.fields.first_name.placeholder') }}"
                class="w-full px-3 py-2 border rounded-md bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:ring-2 focus:border-transparent @error('first_name') border-red-500 focus:ring-red-500 @else border-gray-300 dark:border-gray-600 focus:ring-teal-500 dark:focus:ring-purple-500 @enderror"
            />
            @error('first_name')
                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label for="last_name" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                {{ __('ui.auth.register.form.fields.last_name.label') }}
            </label>
            <input
                id="last_name"
                type="text"
                name="last_name"
                value="{{ old('last_name') }}"
                required
                placeholder="{{ __('ui.auth.register.form.fields.last_name.placeholder') }}"
                class="w-full px-3 py-2 border rounded-md bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:ring-2 focus:border-transparent @error('last_name') border-red-500 focus:ring-red-500 @else border-gray-300 dark:border-gray-600 focus:ring-teal-500 dark:focus:ring-purple-500 @enderror"
            />
            @error('last_name')
                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-6">
            <label for="password" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                {{ __('ui.auth.register.form.fields.password.label') }}
            </label>
            <input
                id="password"
                type="password"
                name="password"
                required
                placeholder="{{ __('ui.auth.register.form.fields.password.placeholder') }}"
                class="w-full px-3 py-2 border rounded-md bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:ring-2 focus:border-transparent @error('password') border-red-500 focus:ring-red-500 @else border-gray-300 dark:border-gray-600 focus:ring-teal-500 dark:focus:ring-purple-500 @enderror"
            />
            @error('password')
                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-6">
            <label for="password_confirmation" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                {{ __('ui.auth.register.form.fields.password_confirmation.label') }}
            </label>
            <input
                id="password_confirmation"
                type="password"
                name="password_confirmation"
                required
                placeholder="{{ __('ui.auth.register.form.fields.password_confirmation.placeholder') }}"
                class="w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-gray-900 focus:border-transparent focus:ring-2 focus:ring-teal-500 dark:border-gray-600 dark:bg-slate-700 dark:text-white dark:focus:ring-purple-500"
            />
        </div>

        <footer class="border-t border-gray-200 pt-4 dark:border-gray-700">
            <div class="flex flex-col gap-4">
                <button
                    type="submit"
                    class="w-full cursor-pointer rounded-md bg-teal-600 px-4 py-2 text-white hover:bg-teal-700 dark:bg-purple-900 dark:hover:bg-purple-800"
                >
                    {{ __('ui.auth.register.form.actions.submit') }}
                </button>

                <p class="text-center text-sm text-gray-600 dark:text-gray-400">
                    {{ __('ui.auth.register.already_have_account') }}
                    <a href="{{ url('/auth/login') }}" class="text-teal-600 hover:underline dark:text-purple-400">
                        {{ __('ui.auth.register.login') }}
                    </a>
                </p>
            </div>
        </footer>
    </form>
</x-default-layout>
