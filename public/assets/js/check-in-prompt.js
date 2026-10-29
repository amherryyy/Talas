(() => {
    const prompt = document.querySelector('[data-check-in-prompt]');
    if (!(prompt instanceof HTMLDialogElement)) {
        return;
    }

    const date = prompt.dataset.promptDate;
    const storageKey = `duno-check-in-dismissed-${prompt.dataset.promptUser}-${date}`;
    const dismissButtons = prompt.querySelectorAll('[data-check-in-dismiss]');
    const notifyTourReady = () => {
        window.dunoCheckInPromptReady = true;
        window.dispatchEvent(new Event('duno:check-in-ready'));
    };

    const rememberToday = () => {
        try {
            window.localStorage.setItem(storageKey, '1');
        } catch {
            // The prompt still works when browser storage is unavailable.
        }
    };

    if (prompt.dataset.promptSatisfied === 'true') {
        rememberToday();
        notifyTourReady();
        return;
    }

    if (window.location.hash === '#app-tour') {
        notifyTourReady();
        return;
    }

    let dismissedToday = false;
    try {
        dismissedToday = window.localStorage.getItem(storageKey) === '1';
    } catch {
        // Fall back to showing the optional prompt for this page visit.
    }

    const closePrompt = () => {
        rememberToday();
        if (prompt.open) {
            prompt.close();
        }
    };

    for (const button of dismissButtons) {
        button.addEventListener('click', closePrompt);
    }

    prompt.addEventListener('cancel', rememberToday);
    prompt.addEventListener('close', notifyTourReady, { once: true });
    prompt.addEventListener('click', (event) => {
        if (event.target === prompt) {
            closePrompt();
        }
    });

    if (!dismissedToday) {
        window.setTimeout(() => {
            if (!prompt.open) {
                prompt.showModal();
            }
        }, 900);
    } else {
        notifyTourReady();
    }
})();
