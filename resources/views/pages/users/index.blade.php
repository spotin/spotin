<?php

use App\Http\Requests\ListFiltersRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\View\View;
use Laravel\Head\Facades\Head;

use function Laravel\Folio\middleware;
use function Laravel\Folio\name;
use function Laravel\Folio\render;

middleware(['auth', 'verified', 'can:viewAny,'.User::class]);
name('users.index');

render(function (View $view, ListFiltersRequest $request) {
    Head::title(__('ui.users.index.title'))->description(__('ui.users.index.description'));

    $searchQuery = $request->searchQuery();

    $adminIds = User::where('is_admin', true)->pluck('id');
    $lastAdminId = $adminIds->count() === 1 ? $adminIds->first() : null;

    return $view->with([
        'users' => User::withCount('organizations')
            ->when($searchQuery !== '', fn (Builder $query) => $query->where(
                fn (Builder $query) => $query->whereLike('name', "%$searchQuery%")->orWhereLike('email', "%$searchQuery%"),
            ))
            ->orderBy('name')
            ->paginate(25)
            ->withQueryString(),
        'searchQuery' => $searchQuery,
        'lastAdminId' => $lastAdminId,
        'isLastAdmin' => $lastAdminId === auth()->id(),
    ]);
});

?>

<x-default-layout>
    <x-page-heading class="mb-6">{{ __('ui.users.index.title') }}</x-page-heading>

    {{-- Above the list, seen however long it is --}}
    @if ($isLastAdmin)
        <p>{{ __('ui.users.index.last_admin') }}</p>
    @endif

    <x-filters :action="route('users.index')" :label="__('ui.users.index.filter.label')" :active="$searchQuery !== ''">
        <x-slot:search>
            <x-filters.search :placeholder="__('ui.users.index.filter.search')" :value="$searchQuery" />
        </x-slot:search>
    </x-filters>

    @error('user')
        <x-alert type="error" closable>{{ $message }}</x-alert>
    @enderror

    @if ($users->isEmpty())
        <p>{{ __('ui.users.index.no_result') }}</p>
    @else
        <div class="not-prose rounded-box border-base-content/5 bg-base-100 overflow-x-auto border">
            <table class="table-zebra table-sm table">
                <thead>
                    <tr>
                        <th>{{ __('ui.users.index.table.columns.name') }}</th>
                        <th>{{ __('ui.users.index.table.columns.organizations') }}</th>
                        <th>{{ __('ui.users.index.table.columns.role') }}</th>
                        <th>{{ __('ui.users.index.table.columns.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($users as $user)
                        <tr class="group/row">
                            <td>
                                <div class="flex flex-wrap items-center gap-x-2 gap-y-1">
                                    {{ $user->name }}
                                    @if ($user->is(auth()->user()))
                                        <span class="badge badge-outline badge-info">
                                            {{ __('ui.users.index.table.you') }}
                                        </span>
                                    @endif
                                </div>
                                <x-email :address="$user->email" class="block text-xs break-words opacity-60" />
                            </td>

                            <td>{{ trans_choice('ui.organizations.index.count', $user->organizations_count) }}</td>

                            <td>
                                <select
                                    name="is_admin"
                                    form="role-{{ $user->id }}"
                                    class="select select-sm w-32 shrink-0 sm:w-40"
                                    aria-label="{{ __('ui.users.index.table.columns.role') }}"
                                    @disabled($user->id === $lastAdminId)
                                >
                                    <option value="0" @selected(! $user->is_admin)>
                                        {{ __('ui.users.common.roles.user') }}
                                    </option>
                                    <option value="1" @selected($user->is_admin)>
                                        {{ __('ui.users.common.roles.admin') }}
                                    </option>
                                </select>
                            </td>

                            <td>
                                @if ($user->id !== $lastAdminId)
                                    <form
                                        id="role-{{ $user->id }}"
                                        method="POST"
                                        action="{{ route('users.update', ['user' => $user->id]) }}"
                                        class="hidden group-has-[option:checked:not([selected])]/row:block"
                                    >
                                        @csrf
                                        @method('PUT')
                                        <button type="submit" class="btn btn-sm w-full sm:w-auto">
                                            {{ __('ui.users.index.table.actions.save') }}
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    {{ $users->links() }}
</x-default-layout>
