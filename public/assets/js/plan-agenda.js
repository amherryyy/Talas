(() => {
    const agenda = document.querySelector('[data-plan-agenda]');
    const sheet = document.querySelector('[data-agenda-sheet]');
    if (!(agenda instanceof HTMLElement)) return;

    const openButton = agenda.querySelector('[data-agenda-open]');
    const dialogSupported = typeof HTMLDialogElement !== 'undefined' && sheet instanceof HTMLDialogElement;
    const closeButtons = dialogSupported ? [...sheet.querySelectorAll('[data-agenda-close]')] : [];
    const firstField = dialogSupported ? sheet.querySelector('select, input:not([type="hidden"])') : null;
    const choice = dialogSupported ? sheet.querySelector('[data-agenda-choice]') : null;
    const duration = dialogSupported ? sheet.querySelector('#agenda-duration') : null;

    const refreshCsrfTokens = (csrf) => {
        if (!csrf?.name || !csrf?.hash) return;
        document.getElementsByName(csrf.name).forEach((input) => {
            if (input instanceof HTMLInputElement) input.value = csrf.hash;
        });
    };

    agenda.addEventListener('change', async (event) => {
        const toggle = event.target;
        if (!(toggle instanceof HTMLInputElement) || !toggle.matches('[data-agenda-progress-toggle]')) return;
        const form = toggle.closest('[data-agenda-progress-form]');
        if (!(form instanceof HTMLFormElement)) return;
        const label = form.querySelector('label span');
        const previousProgress = form.dataset.progress === 'done' ? 'done' : 'in_progress';
        // FormData must capture the checked value before the input is disabled.
        const payload = new FormData(form);
        toggle.disabled = true;
        form.classList.add('is-saving');
        try {
            const response = await fetch(form.action, {
                method: 'POST',
                body: payload,
                credentials: 'same-origin',
                headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
            });
            const result = await response.json();
            refreshCsrfTokens(result.csrf);
            if (!response.ok || result.ok !== true) throw new Error(result.message || 'Could not save topic progress.');
            form.dataset.progress = result.progress;
            toggle.checked = result.progress === 'done';
            if (label instanceof HTMLElement) label.textContent = toggle.checked ? 'Done' : 'Mark done';
        } catch {
            toggle.checked = previousProgress === 'done';
            if (label instanceof HTMLElement) label.textContent = toggle.checked ? 'Done' : 'Mark done';
            form.setAttribute('data-save-error', 'Could not save. Try again.');
        } finally {
            toggle.disabled = false;
            form.classList.remove('is-saving');
        }
    });

    if (!dialogSupported) return;
    agenda.classList.add('is-enhanced');

    choice?.addEventListener('change', () => {
        const option = choice.selectedOptions[0];
        if (option instanceof HTMLOptionElement && duration instanceof HTMLInputElement && option.dataset.duration) {
            duration.value = option.dataset.duration;
        }
    });

    openButton?.addEventListener('click', () => {
        if (typeof sheet.showModal === 'function') sheet.showModal();
        else sheet.setAttribute('open', '');
        window.setTimeout(() => {
            if (firstField instanceof HTMLElement) firstField.focus({ preventScroll: true });
        }, 80);
    });

    closeButtons.forEach((button) => button.addEventListener('click', () => {
        if (typeof sheet.close === 'function') sheet.close();
        else sheet.removeAttribute('open');
    }));

    sheet.addEventListener('click', (event) => {
        if (event.target === sheet) {
            if (typeof sheet.close === 'function') sheet.close();
            else sheet.removeAttribute('open');
        }
    });

    const newBlock = agenda.querySelector('.plan-agenda-event.is-entering');
    if (newBlock instanceof HTMLElement) {
        const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        window.requestAnimationFrame(() => {
            newBlock.scrollIntoView({ behavior: reduceMotion ? 'auto' : 'smooth', block: 'center' });
            newBlock.focus({ preventScroll: true });
        });
    }
})();
