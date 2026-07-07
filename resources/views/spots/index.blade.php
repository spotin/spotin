@extends('layouts.app')
@section('title', __('ui.spots.index.title'))
@section('content')
<div class="flex items-center justify-between mb-4">
    <h1 class="text-2xl font-bold">{{ __('ui.spots.index.title') }}</h1>
    <a href="{{ route('spots.create') }}" class="btn btn-primary btn-sm">{{ __('ui.spots.index.button') }}</a>
</div>
<p class="text-base-content/60 mb-6 text-sm">{{ __('ui.spots.index.paragraphOne') }}</p>
<div class="card bg-base-100 shadow overflow-x-auto">
    <table class="table">
        <thead>
            <tr>
                <th class="grow">{{ __('ui.spots.index.name') }}</th>
                <th class="text-center">{{ __('ui.spots.form.public.label') }}</th>
                <th class="text-center">{{ __('ui.spots.form.configured.label') }}</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse ($spots as $spot)
                <tr class="hover">
                    <td>
                        <a href="{{ route('spots.show', $spot) }}" class="link font-medium">
                            {{ $spot->name ?? __('ui.spots.index.noName') }}
                        </a>
                    </td>
                    <td class="text-center">
                        @if ($spot->public)
                            <span class="badge badge-success badge-sm">{{ __('ui.spots.form.public.values.true') }}</span>
                        @else
                            <span class="badge badge-ghost badge-sm">{{ __('ui.spots.form.public.values.false') }}</span>
                        @endif
                    </td>
                    <td class="text-center">
                        @if ($spot->configured)
                            <span class="text-success">✓</span>
                        @else
                            <span class="text-warning">–</span>
                        @endif
                    </td>
                    <td class="flex gap-2 justify-end">
                        <a href="{{ route('spots.show', $spot) }}" class="btn btn-ghost btn-xs">{{ __('ui.spots.form.actions.view') }}</a>
                        <a href="{{ route('spots.edit', $spot) }}" class="btn btn-ghost btn-xs">{{ __('ui.spots.view.edit') }}</a>
                        <form method="POST" action="{{ route('spots.destroy', $spot) }}" onsubmit="return confirm('{{ __('ui.spots.form.actions.delete') }} ?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-ghost btn-xs text-error">{{ __('ui.spots.form.actions.delete') }}</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center text-base-content/50 py-8">{{ __('ui.spots.index.noName') }}</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
