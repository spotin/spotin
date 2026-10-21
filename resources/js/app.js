import "./steps.js";

// The heavier scripts are only loaded on the pages that use them, in their own files.
const maps = document.querySelectorAll("[data-map]");

if (maps.length > 0) {
    // MapLibre is only loaded once a map is about to be seen, such as at the bottom of a page.
    const observer = new IntersectionObserver(
        (entries) => {
            if (entries.some((entry) => entry.isIntersecting)) {
                observer.disconnect();
                import("./map.js");
            }
        },
        { rootMargin: "200px" },
    );

    maps.forEach((map) => {
        map.hidden = false;
        observer.observe(map);
    });
}

const markdownEditors = document.querySelectorAll("[data-markdown-editor]");

if (markdownEditors.length > 0) {
    import("./markdown-editor.js");
}

const prefersDark = matchMedia("(prefers-color-scheme: dark)");

document.querySelectorAll(".theme-controller").forEach((controller) => {
    const syncWithOs = () => {
        if (!document.documentElement.dataset.theme) {
            controller.checked = prefersDark.matches;
        }
    };

    syncWithOs();
    prefersDark.addEventListener("change", syncWithOs);

    controller.addEventListener("change", () => {
        const theme = controller.checked ? controller.value : "light";

        document.documentElement.dataset.theme = theme;
        document.cookie = `theme=${theme}; path=/; max-age=31536000; SameSite=Lax`;
    });
});
