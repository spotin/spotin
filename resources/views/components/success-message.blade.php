{{--
    The message of an action that succeeded, for the request after it. Shown by `x-default-layout` above the page, or by
    a page that places it itself, next to what the action changed.
--}}
@if (session('success'))
    <p role="alert" class="alert alert-success">
        <x-lucide-check-circle class="h-6 w-6" />
        <span>{{ session('success') }}</span>
    </p>
@endif
