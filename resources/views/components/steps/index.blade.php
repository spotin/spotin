@props(['complete' => false])

{{--
    Splits a form into steps, shown one at a time by resources/js/steps.js, which also lists the titles of the steps in
    the indicator. Without JS, all the steps are shown. `complete` opens every step from the start, for a form whose
    fields are all filled, such as an edit form.
--}}
<div
    data-steps
    data-steps-position="{{ __('ui.common.steps.position') }}"
    @if ($complete) data-steps-complete @endif
    {{ $attributes }}
>
    {{ $slot }}

    <div data-steps-progress hidden>
        <div class="divider"></div>

        {{--
            Scrolls sideways when the steps do not fit, on small screens. The titles wrap, in steps wide enough to stay
            readable. The padding below them keeps the scrollbar, drawn over the content by some systems, off them.
        --}}
        <div class="not-prose relative mb-2 overflow-x-auto pb-2" data-steps-scroller>
            <ul class="steps [&>.step]:min-w-28 w-full" data-steps-indicator></ul>
        </div>
    </div>
</div>
