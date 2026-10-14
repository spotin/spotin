@props(['count'])

@if ($count > 0)
    <p role="alert" class="alert alert-info alert-vertical sm:alert-horizontal">
        <x-lucide-mail class="h-6 w-6" />
        <span>{{ trans_choice('ui.organizations.index.pending-invitations', $count) }}</span>
        <a href="{{ route('organizations.invitations.pending') }}" class="btn btn-sm">
            {{ __('ui.organizations.index.view-pending-invitations') }}
        </a>
    </p>
@endif
