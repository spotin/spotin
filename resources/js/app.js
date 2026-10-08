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
