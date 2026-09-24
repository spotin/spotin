<x-default-layout :title="__('ui.welcome.title')" :description="__('ui.welcome.description')">
    <article class="prose lg:prose-xl">
        <h1>{{ __('ui.welcome.title') }}</h1>

        <p>{{ __('ui.welcome.first_paragraph') }}</p>

        <p>{{ __('ui.welcome.second_paragraph') }}</p>

        Test PR.

        <div>
            <button class="btn">{{ __('ui.welcome.button') }}</button>
        </div>
    </article>
</x-default-layout>
