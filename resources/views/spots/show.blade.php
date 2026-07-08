@extends('layouts.app')
@section('title', $spot->name ?? __('ui.spots.view.noName'))
@section('content')
<div class="flex items-start justify-between mb-6">
    <div>
        <h1 class="text-2xl font-bold">{{ $spot->name ?? __('ui.spots.view.noName') }}</h1>
        <div class="flex gap-2 mt-2">
            @if ($spot->public) <span class="badge badge-success">{{ __('ui.spots.form.public.values.true') }}</span> @endif
            @if (!$spot->configured) <span class="badge badge-warning">{{ __('ui.spots.form.configured.values.false') }}</span> @endif
        </div>
    </div>
    <div class="flex gap-2">
        <a href="{{ route('spots.edit', $spot) }}" class="btn btn-sm btn-ghost">{{ __('ui.spots.view.edit') }}</a>
        <form method="POST" action="{{ route('spots.destroy', $spot) }}" onsubmit="return confirm('{{ __('ui.spots.form.actions.delete') }} ?')">
            @csrf @method('DELETE')
            <button class="btn btn-sm btn-error btn-outline">{{ __('ui.spots.form.actions.delete') }}</button>
        </form>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 flex flex-col gap-4">
        @if ($spot->description)
            <div class="card bg-base-100 shadow p-5">
                <h2 class="font-semibold mb-2 text-sm uppercase tracking-wide text-base-content/50">{{ __('ui.spots.form.description.label') }}</h2>
                <p class="text-sm leading-relaxed">{{ $spot->description }}</p>
            </div>
        @endif
        @if ($spot->website_target)
            <div class="card bg-base-100 shadow p-5">
                <h2 class="font-semibold mb-2 text-sm uppercase tracking-wide text-base-content/50">{{ __('ui.spots.form.websiteTarget.label') }}</h2>
                <a href="{{ $spot->website_target }}" target="_blank" class="link text-primary break-all">{{ $spot->website_target }}</a>
                @if ($spot->direct_access_to_website_target)
                    <span class="badge badge-info badge-sm ml-2">{{ __('ui.spots.form.directAccessToWebsiteTarget.values.true') }}</span>
                @endif
            </div>
        @endif
        @if ($spot->latitude && $spot->longitude)
            <div class="card bg-base-100 shadow p-5">
                <h2 class="font-semibold mb-2 text-sm uppercase tracking-wide text-base-content/50">{{ __('ui.spots.form.latitude.label') }}</h2>
                <p class="text-sm">{{ $spot->latitude }}, {{ $spot->longitude }}</p>
            </div>
        @endif
        @if ($spot->payload)
            <div class="card bg-base-100 shadow p-5">
                <h2 class="font-semibold mb-2 text-sm uppercase tracking-wide text-base-content/50">{{ __('ui.spots.form.optionsForAdvancedUsers.payload.label') }}</h2>
                <pre class="text-xs bg-base-200 rounded p-3 overflow-x-auto">{{ json_encode($spot->payload, JSON_PRETTY_PRINT) }}</pre>
            </div>
        @endif
    </div>
    <div class="flex flex-col gap-4">
        <div class="card bg-base-100 shadow p-5 text-center">
            <h2 class="font-semibold mb-3 text-sm uppercase tracking-wide text-base-content/50">QR Code</h2>
            <img src="https://api.qrserver.com/v1/create-qr-code/?size=180x180&data={{ urlencode(config('app.url') . '/s/' . $spot->id) }}" alt="QR Code" class="mx-auto rounded" />
            <p class="text-xs text-base-content/40 mt-2 break-all">{{ config('app.url') }}/s/{{ $spot->id }}</p>
            <a href="{{ config('app.url') }}/s/{{ $spot->id }}" target="_blank" class="btn btn-ghost btn-sm mt-3">{{ __('ui.spots.view.view') }}</a>
        </div>
    </div>
</div>
@endsection
