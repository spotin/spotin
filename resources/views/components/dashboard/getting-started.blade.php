@props(['checklist'])

@php
    $percent = (int) round($checklist->completed() / $checklist->total() * 100);
@endphp

{{-- The first steps of the user, ticked from what they did; shown until hidden, here or from the profile settings --}}
<section aria-labelledby="getting-started" {{ $attributes->class('not-prose card card-border bg-base-100') }}>
    <div class="card-body">
        <header class="flex items-center gap-4">
            <div
                class="radial-progress text-primary shrink-0 text-sm font-bold"
                style="--value: {{ $percent }}; --size: 3.5rem"
                role="progressbar"
                aria-valuenow="{{ $checklist->completed() }}"
                aria-valuemin="0"
                aria-valuemax="{{ $checklist->total() }}"
                aria-label="{{ __('ui.dashboard.getting_started.progress', ['completed' => $checklist->completed(), 'total' => $checklist->total()]) }}"
            >
                {{ $checklist->completed() }}/{{ $checklist->total() }}
            </div>

            <div class="grow">
                <h2 id="getting-started" class="card-title">{{ __('ui.dashboard.getting_started.title') }}</h2>
                <p class="text-sm opacity-70">
                    {{ $checklist->isCompleted() ? __('ui.dashboard.getting_started.completed') : __('ui.dashboard.getting_started.description') }}
                </p>
            </div>

            <form method="POST" action="{{ route('user-profile-information.update') }}">
                @csrf
                @method('PUT')
                <input type="hidden" name="show_getting_started" value="0" />
                <button type="submit" class="btn btn-ghost btn-sm">
                    {{ __('ui.dashboard.getting_started.hide') }}
                </button>
            </form>
        </header>

        <ol class="mt-2 flex flex-col gap-3">
            @foreach ($checklist->steps as $step)
                <li class="flex items-center gap-3">
                    @if ($step['done'])
                        <x-lucide-circle-check class="text-success h-5 w-5 shrink-0" aria-hidden="true" />
                        <span class="grow opacity-70">
                            <span class="sr-only">{{ __('ui.dashboard.getting_started.done') }}</span>
                            {{ __("ui.dashboard.getting_started.steps.{$step['key']}.label") }}
                        </span>
                    @else
                        <x-lucide-circle class="h-5 w-5 shrink-0 opacity-50" aria-hidden="true" />
                        <span class="grow">{{ __("ui.dashboard.getting_started.steps.{$step['key']}.label") }}</span>
                        @if ($step['url'])
                            <a href="{{ $step['url'] }}" class="btn btn-sm">
                                {{ __("ui.dashboard.getting_started.steps.{$step['key']}.action") }}
                            </a>
                        @endif
                    @endif
                </li>
            @endforeach
        </ol>

        <p class="mt-2 text-xs opacity-60">{{ __('ui.dashboard.getting_started.hint') }}</p>
    </div>
</section>
