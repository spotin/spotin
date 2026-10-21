// Makes the textarea of `x-markdown-editor` a WYSIWYG editor, with Tiptap, which can switch back to the Markdown code.
// The textarea stays the field of the form: the editor writes Markdown back into it. Loaded by app.js only when an
// editor is on the page, then makes all the editors of the page.
import { Editor } from "@tiptap/core";
import Image from "@tiptap/extension-image";
import { Markdown } from "@tiptap/markdown";
import StarterKit from "@tiptap/starter-kit";

// The commands of the toolbar: what they do, and, for the ones that can be on or off, when their button is pressed.
const commands = {
    bold: [(chain) => chain.toggleBold(), (editor) => editor.isActive("bold")],
    italic: [
        (chain) => chain.toggleItalic(),
        (editor) => editor.isActive("italic"),
    ],
    strike: [
        (chain) => chain.toggleStrike(),
        (editor) => editor.isActive("strike"),
    ],
    code: [(chain) => chain.toggleCode(), (editor) => editor.isActive("code")],
    bullet_list: [
        (chain) => chain.toggleBulletList(),
        (editor) => editor.isActive("bulletList"),
    ],
    ordered_list: [
        (chain) => chain.toggleOrderedList(),
        (editor) => editor.isActive("orderedList"),
    ],
    blockquote: [
        (chain) => chain.toggleBlockquote(),
        (editor) => editor.isActive("blockquote"),
    ],
    code_block: [
        (chain) => chain.toggleCodeBlock(),
        (editor) => editor.isActive("codeBlock"),
    ],
    horizontal_rule: [(chain) => chain.setHorizontalRule()],
};

document.querySelectorAll("[data-markdown-editor]").forEach((container) => {
    const textarea = container.querySelector("textarea");
    const content = container.querySelector("[data-markdown-editor-content]");
    const toolbar = container.querySelector("[data-markdown-editor-toolbar]");
    const block = toolbar.querySelector("[data-markdown-editor-block]");
    const buttons = [...toolbar.querySelectorAll("[data-command]")];
    const toggle = toolbar.querySelector("[data-markdown-editor-mode]");
    const prompts = content.dataset;
    let mode = "visual";

    const editor = new Editor({
        element: content,
        extensions: [
            Markdown,
            // Five levels, from `#`: the page shifts them under its own first heading when it shows the description
            // (`Spot::descriptionHtml()`). Markdown has no underline.
            StarterKit.configure({
                heading: { levels: [1, 2, 3, 4, 5] },
                underline: false,
                link: {
                    openOnClick: false,
                    protocols: ["http", "https", "mailto"],
                    defaultProtocol: "https",
                },
            }),
            Image,
        ],
        content: textarea.value,
        contentType: "markdown",
        editorProps: {
            attributes: {
                id: `${textarea.id}-editable`,
                role: "textbox",
                "aria-multiline": "true",
                "aria-labelledby": `${textarea.id}-label`,
                // The images as on the page of the spot: kept to a reasonable height, centered.
                class: "textarea prose prose-sm prose-img:mx-auto prose-img:block prose-img:max-h-80 prose-img:w-auto prose-img:rounded-box block h-64 w-full max-w-none resize-y overflow-auto",
            },
        },
    });

    // Writes the Markdown into the textarea, as if typed, so that the form knows it changed.
    const save = () => {
        textarea.value = editor.isEmpty ? "" : editor.getMarkdown();
        textarea.dispatchEvent(new Event("input", { bubbles: true }));
    };

    const refresh = () => {
        buttons.forEach((button) => {
            const command = button.dataset.command;
            const isActive =
                command === "link" || command === "image"
                    ? (editor) => editor.isActive(command)
                    : commands[command][1];

            if (isActive) {
                const active = isActive(editor);

                button.setAttribute("aria-pressed", String(active));
                button.classList.toggle("btn-active", active);
            }
        });

        const heading = [1, 2, 3, 4, 5].find((level) =>
            editor.isActive("heading", { level }),
        );

        block.value = heading ? String(heading) : "paragraph";
    };

    // Asks for the address of a link: empty removes the link.
    const link = (chain) => {
        const href = window.prompt(
            prompts.linkPrompt,
            editor.getAttributes("link").href ?? "https://",
        );

        if (href === null) {
            return;
        }

        if (href.trim() === "" || href.trim() === "https://") {
            chain.extendMarkRange("link").unsetLink().run();
        } else {
            chain.extendMarkRange("link").setLink({ href: href.trim() }).run();
        }
    };

    // Asks for the address of an image, then its description for the people who cannot see it.
    const image = (chain) => {
        const src = window.prompt(prompts.imagePrompt, "https://");

        if (src === null || src.trim() === "" || src.trim() === "https://") {
            return;
        }

        const alt = window.prompt(prompts.imageAltPrompt, "") ?? "";

        chain.setImage({ src: src.trim(), alt: alt.trim() }).run();
    };

    buttons.forEach((button) =>
        button.addEventListener("click", () => {
            const command = button.dataset.command;
            const chain = editor.chain().focus();

            if (command === "link") {
                link(chain);
            } else if (command === "image") {
                image(chain);
            } else {
                commands[command][0](chain).run();
            }
        }),
    );

    block.addEventListener("change", () => {
        const chain = editor.chain().focus();

        if (block.value === "paragraph") {
            chain.setParagraph().run();
        } else {
            chain.setHeading({ level: Number(block.value) }).run();
        }
    });

    // Shows the formatted text, or its Markdown code in the textarea. The formatting tools only apply to the formatted
    // text.
    const switchTo = (next, { focus = true } = {}) => {
        mode = next;

        if (mode === "visual") {
            editor.commands.setContent(textarea.value, {
                contentType: "markdown",
                emitUpdate: false,
            });
        }

        // The height given to the formatted text or to the Markdown code, by its resize handle, carries to the other.
        const [shown, hidden] =
            mode === "visual"
                ? [editor.view.dom, textarea]
                : [textarea, editor.view.dom];

        shown.style.height = hidden.style.height;

        textarea.hidden = mode === "visual";
        content.hidden = mode !== "visual";
        toolbar
            .querySelectorAll("[data-format]")
            .forEach((control) => (control.disabled = mode !== "visual"));
        toggle.setAttribute("aria-pressed", String(mode === "markdown"));
        toggle.classList.toggle("btn-active", mode === "markdown");
        toggle.classList.toggle("swap-active", mode === "markdown");
        refresh();

        if (focus) {
            (mode === "visual" ? editor.commands : textarea).focus();
        }
    };

    toggle.addEventListener("click", () =>
        switchTo(mode === "visual" ? "markdown" : "visual"),
    );

    // While the Markdown code is edited, the formatted text follows it.
    textarea.addEventListener("input", (event) => {
        if (mode === "markdown" && event.isTrusted) {
            editor.commands.setContent(textarea.value, {
                contentType: "markdown",
                emitUpdate: false,
            });
        }
    });

    editor.on("update", save);
    editor.on("transaction", refresh);

    container.querySelector("[data-markdown-editor-hint]").hidden = true;
    toolbar.hidden = false;
    switchTo("visual", { focus: false });
});
