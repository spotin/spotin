@props(['title', 'actions' => null])

{{-- Named by its title without JS: resources/js/steps.js names it by its position, `step-1`, for the URL --}}
@php($id = 'step-'.Str::slug($title))

<section data-step aria-labelledby="{{ $id }}" {{ $attributes->merge(['class' => 'mb-4']) }}>
    <header class="flex flex-wrap items-baseline gap-x-3">
        <h2 id="{{ $id }}" tabindex="-1" class="focus:outline-none" data-step-title>{{ $title }}</h2>
        {{-- "Step 1 of 2", filled by resources/js/steps.js, next to the title and not in it, as the indicator copies it --}}
        <span class="text-sm opacity-60" data-step-position hidden></span>
    </header>

    {{ $slot }}

    {{--
        Shown by resources/js/steps.js: Previous on every step but the first, Continue on every step but the last. The
        actions of the step, such as the submit button of the last one, come after them.
    --}}
    <footer class="mt-6 flex flex-wrap gap-2">
        <button type="button" class="btn" data-steps-previous hidden>{{ __('ui.common.steps.previous') }}</button>
        <div class="ml-auto flex flex-wrap gap-2">
            <button type="button" class="btn btn-primary" data-steps-next hidden>
                {{ __('ui.common.steps.next') }}
            </button>
            {{ $actions }}
        </div>
    </footer>
</section>
