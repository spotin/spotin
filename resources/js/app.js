// Plain cookie (not encrypted) so the server can render the theme.
document.querySelectorAll(".theme-controller").forEach((controller) => {
    controller.addEventListener("change", () => {
        const theme = controller.checked ? controller.value : "light";

        document.documentElement.dataset.theme = theme;
        document.cookie = `theme=${theme}; path=/; max-age=31536000; SameSite=Lax`;
    });
});
