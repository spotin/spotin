@props(['name' => 'query', 'placeholder', 'value' => ''])

<label class="input join-item min-w-0 flex-1">
    <x-lucide-search class="h-[1em] opacity-50" aria-hidden="true" />
    <input
        type="search"
        name="{{ $name }}"
        value="{{ $value }}"
        placeholder="{{ $placeholder }}"
        aria-label="{{ $placeholder }}"
        class="grow"
    />
</label>
