@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}" class="not-prose mt-4 flex justify-center">
        <div class="join">
            @if ($paginator->onFirstPage())
                <span class="join-item btn btn-disabled w-32 shrink-0" aria-disabled="true">
                    {!! __('pagination.previous') !!}
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="join-item btn w-32 shrink-0">
                    {!! __('pagination.previous') !!}
                </a>
            @endif

            <span class="join-item btn btn-disabled sm:hidden" aria-disabled="true">
                {{ __('ui.common.pagination.page', ['current' => $paginator->currentPage(), 'last' => $paginator->lastPage()]) }}
            </span>

            @foreach ($elements as $element)
                @if (is_string($element))
                    <span class="join-item btn btn-disabled hidden sm:inline-flex" aria-disabled="true">
                        {{ $element }}
                    </span>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span class="join-item btn btn-active hidden sm:inline-flex" aria-current="page">
                                {{ $page }}
                            </span>
                        @else
                            <a href="{{ $url }}" class="join-item btn hidden sm:inline-flex">{{ $page }}</a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="join-item btn w-32 shrink-0">
                    {!! __('pagination.next') !!}
                </a>
            @else
                <span class="join-item btn btn-disabled w-32 shrink-0" aria-disabled="true">
                    {!! __('pagination.next') !!}
                </span>
            @endif
        </div>
    </nav>
@endif
