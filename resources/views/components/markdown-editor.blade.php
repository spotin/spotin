@props(['name', 'label', 'value' => null, 'placeholder' => null])

@php($id = 'markdown-editor-'.$name)

<fieldset data-markdown-editor class="fieldset">
    <legend id="{{ $id }}-label" class="fieldset-legend">{{ $label }}</legend>

    <div
        class="not-prose flex flex-wrap items-center gap-1"
        role="toolbar"
        aria-label="{{ __('ui.common.markdown_editor.toolbar') }}"
        aria-controls="{{ $id }}-content"
        data-markdown-editor-toolbar
        hidden
    >
        <select
            class="select select-sm w-auto"
            aria-label="{{ __('ui.common.markdown_editor.block.label') }}"
            data-markdown-editor-block
            data-format
        >
            <option value="paragraph">{{ __('ui.common.markdown_editor.block.paragraph') }}</option>
            {{-- Shifted under the first heading of the page when the description is shown --}}
            @foreach (range(1, 5) as $level)
                <option value="{{ $level }}">
                    {{ __('ui.common.markdown_editor.block.heading', ['level' => $level]) }}
                </option>
            @endforeach
        </select>

        @foreach ([
            ['bold' => 'bold', 'italic' => 'italic', 'strike' => 'strikethrough'],
            ['bullet_list' => 'list', 'ordered_list' => 'list-ordered', 'blockquote' => 'text-quote', 'horizontal_rule' => 'separator-horizontal'],
            ['link' => 'link', 'image' => 'image-plus'],
        ] as $group)
            <span class="bg-base-content/10 mx-1 h-6 w-px" aria-hidden="true"></span>
            @foreach ($group as $command => $icon)
                <button
                    type="button"
                    class="btn btn-sm btn-ghost btn-square"
                    data-command="{{ $command }}"
                    data-format
                    aria-label="{{ __("ui.common.markdown_editor.commands.{$command}") }}"
                    title="{{ __("ui.common.markdown_editor.commands.{$command}") }}"
                >
                    <x-dynamic-component :component="'lucide-'.$icon" class="h-4 w-4" aria-hidden="true" />
                </button>
            @endforeach
        @endforeach

        {{-- Switches between the formatted text and its Markdown code: pressed shows the code --}}
        <button
            type="button"
            class="btn btn-sm btn-ghost btn-square swap ml-auto"
            data-markdown-editor-mode
            aria-pressed="false"
            aria-label="{{ __('ui.common.markdown_editor.markdown') }}"
            title="{{ __('ui.common.markdown_editor.markdown') }}"
        >
            <x-lucide-code-xml class="swap-off h-4 w-4" aria-hidden="true" />
            <x-lucide-eye class="swap-on h-4 w-4" aria-hidden="true" />
        </button>
    </div>

    <div
        id="{{ $id }}-content"
        data-markdown-editor-content
        data-link-prompt="{{ __('ui.common.markdown_editor.prompts.link') }}"
        data-image-prompt="{{ __('ui.common.markdown_editor.prompts.image') }}"
        data-image-alt-prompt="{{ __('ui.common.markdown_editor.prompts.image_alt') }}"
        hidden
    ></div>

    <textarea
        id="{{ $id }}"
        name="{{ $name }}"
        placeholder="{{ $placeholder }}"
        aria-labelledby="{{ $id }}-label"
        @error($name) aria-invalid="true" @enderror
        class="textarea validator h-64 w-full font-mono text-sm"
    >{{ old($name, $value) }}</textarea>
    <p class="label m-0" data-markdown-editor-hint>{{ __('ui.common.markdown_editor.hint') }}</p>

    @error($name)
        <span class="validator-hint mt-0">{{ $message }}</span>
    @enderror
</fieldset>
