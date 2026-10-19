// Shows the steps of an `x-steps` component one at a time. The form still submits all the steps at once.
document.querySelectorAll("[data-steps]").forEach((container) => {
    const steps = [...container.querySelectorAll("[data-step]")];
    const titles = steps.map((step) => step.querySelector("[data-step-title]"));
    const form = container.closest("form");
    const scroller = container.querySelector("[data-steps-scroller]");
    const icons = {
        done: container.querySelector('[data-steps-icon="done"]'),
        error: container.querySelector('[data-steps-icon="error"]'),
    };
    const last = steps.length - 1;
    let current = 0;
    // The furthest step reached: the indicator opens any step up to it.
    let reached = 0;
    // Whether the user has typed something, to warn before leaving the page.
    let changed = false;
    let submitting = false;

    const fields = (index) => [
        ...steps[index].querySelectorAll("input, select, textarea"),
    ];
    const valid = (index) =>
        fields(index).every((field) => field.checkValidity());
    // Set by the server after a failed submit, until the field is changed.
    const rejected = (index) =>
        fields(index).some(
            (field) => field.getAttribute("aria-invalid") === "true",
        );

    // The indicator lists the titles of the steps, so that they always match.
    const indicators = titles.map((title, i) => {
        const indicator = document.createElement("li");
        const button = document.createElement("button");

        indicator.className = "step";
        button.type = "button";
        button.className =
            "cursor-pointer enabled:hover:underline focus-visible:underline disabled:cursor-default";
        button.textContent = title.textContent;
        button.addEventListener("click", () => goTo(i));
        indicator.append(button);
        container.querySelector("[data-steps-indicator]").append(indicator);

        return indicator;
    });

    // A step left behind is done, with a check, or has errors, with a cross. The current step and the furthest one
    // reached keep their number.
    const render = () => {
        indicators.forEach((indicator, i) => {
            const button = indicator.querySelector("button");
            const error =
                i !== current && (rejected(i) || (i < reached && !valid(i)));
            const done = !error && i !== current && i < reached;

            indicator.querySelector(".step-icon")?.remove();

            if (error || done) {
                indicator.prepend(
                    icons[error ? "error" : "done"].content.cloneNode(true),
                );
            }

            indicator.classList.toggle("step-error", error);
            indicator.classList.toggle("step-primary", !error && i <= reached);
            button.disabled = i > reached;

            if (i === current) {
                button.setAttribute("aria-current", "step");
            } else {
                button.removeAttribute("aria-current");
            }
        });

        // Keeps the current step visible when the indicator scrolls sideways.
        const indicator = indicators[current];

        scroller.scrollLeft =
            indicator.offsetLeft -
            (scroller.clientWidth - indicator.offsetWidth) / 2;
    };

    // `history` records the step in the URL ("push" or "replace"), so that the back button of the browser goes back
    // to the previous step and a reload keeps the step.
    const show = (index, { focus = true, history = "push" } = {}) => {
        current = index;
        reached = Math.max(reached, index);

        steps.forEach((step, i) => {
            step.hidden = i !== index;
            step.querySelector("[data-steps-previous]").hidden = i === 0;
            step.querySelector("[data-steps-next]").hidden = i === last;
        });

        render();

        const hash = `#${titles[index].id}`;

        if (history && location.hash !== hash) {
            window.history[`${history}State`](null, "", hash);
        }

        if (focus) {
            titles[index].focus();
        }
    };

    // Going back is always allowed. Going forward is allowed up to the furthest step reached, once the steps until
    // there are valid, so that no invalid field is left in a hidden step when the form is submitted. A step can have
    // become invalid after it was left, with Previous: it is then opened, with the browser's message.
    const goTo = (index, options = {}) => {
        if (index < current) {
            show(index, options);

            return;
        }

        if (index <= current || index > reached) {
            return;
        }

        for (let i = current; i < index; i++) {
            const invalid = fields(i).find((field) => !field.checkValidity());

            if (invalid) {
                if (i !== current) {
                    show(i, { ...options, focus: false });
                }

                invalid.reportValidity();

                return;
            }
        }

        show(index, options);
    };

    const next = () => {
        if (
            current < last &&
            fields(current).every((field) => field.reportValidity())
        ) {
            show(current + 1);
        }
    };

    const fromHash = () =>
        Math.max(
            titles.findIndex((title) => `#${title.id}` === location.hash),
            0,
        );

    steps.forEach((step) => {
        step.querySelector("[data-steps-previous]").addEventListener(
            "click",
            () => show(current - 1),
        );
        step.querySelector("[data-steps-next]").addEventListener("click", next);
    });

    // The back and forward buttons of the browser move between the steps. If the step cannot be opened, the URL
    // follows the step shown instead.
    window.addEventListener("popstate", () => {
        goTo(fromHash(), { history: null });

        if (location.hash !== `#${titles[current].id}`) {
            window.history.replaceState(null, "", `#${titles[current].id}`);
        }
    });

    form?.addEventListener("input", (event) => {
        changed = true;
        event.target.removeAttribute("aria-invalid");
    });

    // Enter in a field submits the form: go to the next step instead, until the last one.
    form?.addEventListener("submit", (event) => {
        if (current < last) {
            event.preventDefault();
            next();
        } else {
            submitting = true;
        }
    });

    // Warns before leaving the page with what was typed, unless the form is being submitted.
    window.addEventListener("beforeunload", (event) => {
        if (changed && !submitting) {
            event.preventDefault();
        }
    });

    // Coming back to the page with the back button of the browser, after a submit, warns again.
    window.addEventListener("pageshow", () => {
        submitting = false;
    });

    steps.forEach((step, i) => {
        const position = step.querySelector("[data-step-position]");

        position.textContent = container.dataset.stepsPosition
            .replace(":current", i + 1)
            .replace(":total", steps.length);
        position.hidden = false;
    });

    container.querySelector("[data-steps-progress]").hidden = false;

    // After a failed submit, all the steps were filled in, so they can all be opened: start on the first step with an
    // error. Otherwise, start on the step of the URL, once the steps before it are valid.
    const invalid = steps.findIndex((_, i) => rejected(i));

    if (invalid >= 0) {
        reached = last;
        show(invalid, { history: "replace" });
    } else {
        const target = fromHash();

        reached = target;
        show(0, { focus: false, history: null });
        goTo(target, { focus: false, history: "replace" });
    }
});
