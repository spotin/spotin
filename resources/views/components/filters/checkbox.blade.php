@props(['name', 'label', 'checked' => false])

<label class="label h-10 text-sm">
    <input type="checkbox" name="{{ $name }}" value="1" class="checkbox" @checked($checked) />
    {{ $label }}
</label>
