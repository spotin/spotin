document.querySelectorAll("[data-validate-submit]").forEach((form) => {
    const submitButton = form.querySelector('button[type="submit"]');

    if (!(submitButton instanceof HTMLButtonElement)) {
        return;
    }

    const updateSubmitState = () => {
        submitButton.disabled = !form.checkValidity();

        form.querySelectorAll("input, select, textarea").forEach((input) => {
            if (!(
                input instanceof HTMLInputElement ||
                input instanceof HTMLSelectElement ||
                input instanceof HTMLTextAreaElement
            )) {
                return;
            }

            const validator = input.closest(".validator");

            if (validator) {
                const serverInvalid =
                    validator.dataset.serverInvalid === "true";

                validator.setAttribute(
                    "aria-invalid",
                    serverInvalid ||
                        (input.value !== "" && !input.checkValidity())
                        ? "true"
                        : "false",
                );
            }
        });
    };

    form.addEventListener("input", (event) => {
        if (event.target instanceof HTMLInputElement) {
            const validator = event.target.closest(".validator");

            if (validator?.dataset.serverInvalid === "true") {
                const hint = validator.nextElementSibling;

                if (hint instanceof HTMLElement && hint.dataset.clientHint) {
                    hint.textContent = hint.dataset.clientHint;
                }
            }

            validator?.setAttribute("data-server-invalid", "false");
        }

        updateSubmitState();
    });
    form.addEventListener("change", updateSubmitState);
    window.addEventListener("pageshow", updateSubmitState);
    window.addEventListener("load", updateSubmitState);
    updateSubmitState();
});
