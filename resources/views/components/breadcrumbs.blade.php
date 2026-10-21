@props(['items' => []])

@auth
    @if (count($items) > 0)
        <nav aria-label="{{ __('ui.common.breadcrumbs') }}" class="breadcrumbs my-4 text-sm">
            <ul>
                @foreach ($items as $item)
                    <li>
                        @if ($loop->last)
                            <span
                                aria-current="page"
                                class="cursor-default no-underline hover:no-underline"
                            >{{ $item['label'] }}</span>
                        @elseif (! empty($item['url']))
                            <a href="{{ $item['url'] }}">{{ $item['label'] }}</a>
                        @else
                            <span>{{ $item['label'] }}</span>
                        @endif
                    </li>
                @endforeach
            </ul>
        </nav>
    @endif
@endauth
