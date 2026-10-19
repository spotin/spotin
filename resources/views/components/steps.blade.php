{{--
    Splits a form into steps, shown one at a time by resources/js/steps.js, which also lists the titles of the steps in
    the indicator. Without JS, all the steps are shown.
--}}
<div data-steps data-steps-position="{{ __('ui.common.steps.position') }}" {{ $attributes }}>
    {{ $slot }}

    <div data-steps-progress hidden>
        <div class="divider"></div>

        {{-- Scrolls sideways when the steps do not fit, on small screens --}}
        <div class="not-prose relative mb-4 overflow-x-auto" data-steps-scroller>
            <ul class="steps w-full min-w-max" data-steps-indicator></ul>
        </div>

        {{-- The icons of the steps that are done or have errors, copied by resources/js/steps.js --}}
        <template data-steps-icon="done">
            <span class="step-icon">
                <x-lucide-check class="h-4 w-4" aria-hidden="true" />
                <span class="sr-only">{{ __('ui.common.steps.done') }}</span>
            </span>
        </template>
        <template data-steps-icon="error">
            <span class="step-icon">
                <x-lucide-x class="h-4 w-4" aria-hidden="true" />
                <span class="sr-only">{{ __('ui.common.steps.error') }}</span>
            </span>
        </template>
    </div>
</div>
