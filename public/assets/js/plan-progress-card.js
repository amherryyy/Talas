(() => {
    const card = document.querySelector('[data-plan-progress-card]');
    if (!(card instanceof HTMLElement)) return;

    card.classList.add('is-enhanced');

    const progressBar = card.querySelector('[data-plan-progress-bar]');
    const progressFill = card.querySelector('[data-plan-progress-fill]');
    const progressPercent = card.querySelector('[data-plan-progress-percent]');
    const progressSummary = card.querySelector('[data-plan-progress-summary]');
    const doneBadge = card.querySelector('[data-plan-progress-done]');
    const liveStatus = card.querySelector('[data-plan-progress-status]');

    const refreshCsrfTokens = (csrf) => {
        if (!csrf?.name || !csrf?.hash) return;
        document.getElementsByName(csrf.name).forEach((input) => {
            if (input instanceof HTMLInputElement) input.value = csrf.hash;
        });
    };

    const refreshTotals = (message = '') => {
        const toggles = [...card.querySelectorAll('[data-plan-progress-toggle]')]
            .filter((input) => input instanceof HTMLInputElement);
        const complete = toggles.filter((input) => input.checked).length;
        const total = toggles.length;
        const percent = total === 0 ? 0 : Math.round((complete / total) * 100);

        if (progressFill instanceof HTMLElement) progressFill.style.transform = `scaleX(${percent / 100})`;
        if (progressPercent instanceof HTMLElement) progressPercent.textContent = `${percent}%`;
        if (progressBar instanceof HTMLElement) {
            progressBar.setAttribute('aria-valuemax', String(Math.max(1, total)));
            progressBar.setAttribute('aria-valuenow', String(complete));
            progressBar.setAttribute('aria-valuetext', total === 0 ? 'No linked topics available to track' : `${complete} of ${total} topics done`);
        }
        if (progressSummary instanceof HTMLElement) {
            progressSummary.textContent = total === 0 ? 'No linked topics available to track.' : `${complete} of ${total} topics done`;
        }
        if (doneBadge instanceof HTMLElement) doneBadge.hidden = total === 0 || complete !== total;
        if (liveStatus instanceof HTMLElement && message) liveStatus.textContent = `${message} ${complete} of ${total} topics done.`;
    };

    card.addEventListener('change', async (event) => {
        const toggle = event.target;
        if (!(toggle instanceof HTMLInputElement) || !toggle.matches('[data-plan-progress-toggle]')) return;

        const form = toggle.closest('[data-plan-progress-form]');
        const row = toggle.closest('[data-plan-progress-item]');
        if (!(form instanceof HTMLFormElement) || !(row instanceof HTMLElement)) return;

        const previousProgress = form.dataset.progress === 'done' ? 'done' : 'in_progress';
        toggle.disabled = true;
        row.classList.toggle('is-saving', true);

        try {
            const response = await fetch(form.action, {
                method: 'POST',
                body: new FormData(form),
                credentials: 'same-origin',
                headers: {
                    Accept: 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
            });
            const result = await response.json();
            refreshCsrfTokens(result.csrf);

            if (!response.ok || result.ok !== true) throw new Error(result.message || 'The topic progress could not be saved.');

            form.dataset.progress = result.progress;
            toggle.checked = result.progress === 'done';
            row.classList.toggle('is-done', toggle.checked);
            refreshTotals(result.message);
        } catch {
            toggle.checked = previousProgress === 'done';
            if (liveStatus instanceof HTMLElement) liveStatus.textContent = 'The topic progress could not be saved. Try again.';
        } finally {
            toggle.disabled = false;
            row.classList.remove('is-saving');
        }
    });
})();
