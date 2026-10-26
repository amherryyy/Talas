(() => {
    const tour = document.querySelector('[data-app-tour]');
    if (!(tour instanceof HTMLElement)) return;

    const userKey = tour.dataset.tourUser;
    if (!userKey) return;

    const storageKey = `duno-app-tour-v1-${userKey}`;
    const steps = [
        {
            target: 'start',
            title: 'Your study space',
            copy: 'This is your starting point. Use the cards and navigation to find the tool that fits your next study step.',
        },
        {
            target: 'focus',
            title: 'Duno Focus',
            copy: 'Find unfinished topics in a clear order using due dates, importance, and progress.',
        },
        {
            target: 'plan',
            title: 'Duno Plan',
            copy: 'Make a plan from the time you have, then adjust or reorder its blocks to fit your day.',
        },
        {
            target: 'timer',
            title: 'Duno Timer',
            copy: 'Time a study session, review the elapsed time, and save it only when you choose.',
        },
        {
            target: 'aralytics',
            title: 'Aralytics',
            copy: 'Look back at the study sessions you saved through totals, patterns, and simple charts.',
        },
        {
            target: 'guide',
            title: 'Duno Guide',
            copy: 'Browse short, general study suggestions and choose the ones that fit your course.',
        },
        {
            target: 'more',
            title: 'More pages',
            copy: 'Open More for subjects, topics, and private check-ins. Your check-ins stay private. Replay this tour from the same menu.',
        },
    ];

    const progress = tour.querySelector('[data-tour-progress]');
    const title = tour.querySelector('[data-tour-title]');
    const copy = tour.querySelector('[data-tour-copy]');
    const back = tour.querySelector('[data-tour-back]');
    const next = tour.querySelector('[data-tour-next]');
    const skip = tour.querySelector('[data-tour-skip]');
    if (!(progress instanceof HTMLElement)
        || !(title instanceof HTMLElement)
        || !(copy instanceof HTMLElement)
        || !(back instanceof HTMLButtonElement)
        || !(next instanceof HTMLButtonElement)
        || !(skip instanceof HTMLButtonElement)) return;

    let stepIndex = 0;
    let activeTarget = null;
    let startRequested = false;
    let started = false;

    const hasCompleted = () => {
        try {
            if (window.localStorage.getItem(storageKey) === 'complete') return true;
        } catch {}
        try {
            return window.sessionStorage.getItem(storageKey) === 'complete';
        } catch {
            return false;
        }
    };

    const rememberCompletion = () => {
        try {
            window.localStorage.setItem(storageKey, 'complete');
        } catch {
            try {
                window.sessionStorage.setItem(storageKey, 'complete');
            } catch {
                // The tour still closes if browser storage is unavailable.
            }
        }
    };

    const removeHighlight = () => {
        if (activeTarget instanceof HTMLElement) {
            activeTarget.classList.remove('is-tour-highlighted');
        }
        activeTarget = null;
    };

    const findStepTarget = (step) => {
        const preferred = {
            start: ['.welcome[data-tour-target="start"]', '.workspace-hero[data-tour-target="start"]'],
            focus: ['.tool-card[data-tour-target="focus"]', '.dashboard-tool-card[data-tour-target="focus"]'],
            plan: ['.tool-card[data-tour-target="plan"]', '.dashboard-tool-card[data-tour-target="plan"]'],
            timer: ['.tool-card[data-tour-target="timer"]', '.dashboard-tool-card[data-tour-target="timer"]'],
            aralytics: ['.tool-card[data-tour-target="aralytics"]', '.dashboard-tool-card[data-tour-target="aralytics"]'],
            guide: ['.guide-note[data-tour-target="guide"]', '.guide-section[data-tour-target="guide"]'],
        }[step.target] ?? [];

        for (const selector of preferred) {
            const preferredTarget = [...document.querySelectorAll(selector)].find((element) => element instanceof HTMLElement && element.getClientRects().length > 0);
            if (preferredTarget instanceof HTMLElement) return preferredTarget;
        }

        return [...document.querySelectorAll(`[data-tour-target="${step.target}"]`)]
            .find((element) => element instanceof HTMLElement && element.getClientRects().length > 0) ?? null;
    };

    const closeTour = (completed) => {
        if (completed) rememberCompletion();
        const targetToFocus = activeTarget;
        removeHighlight();
        tour.hidden = true;
        started = false;

        if (targetToFocus instanceof HTMLElement) {
            if (!targetToFocus.matches('a, button, summary, input, select, textarea, [tabindex]')) {
                targetToFocus.tabIndex = -1;
            }
            targetToFocus.focus({ preventScroll: true });
        }
    };

    const showStep = (index) => {
        stepIndex = Math.max(0, Math.min(index, steps.length - 1));
        const step = steps[stepIndex];
        const target = findStepTarget(step);
        if (!(target instanceof HTMLElement)) return;

        removeHighlight();
        activeTarget = target;
        activeTarget.classList.add('is-tour-highlighted');

        const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        activeTarget.scrollIntoView({ behavior: reduceMotion ? 'auto' : 'smooth', block: 'center' });
        progress.textContent = `Getting started · Step ${stepIndex + 1} of ${steps.length}`;
        title.textContent = step.title;
        copy.textContent = step.copy;
        back.disabled = stepIndex === 0;
        next.textContent = stepIndex === steps.length - 1 ? 'Finish' : 'Next';
        title.focus({ preventScroll: true });
    };

    const beginTour = () => {
        if (!startRequested || started || tour.hidden === false) return;
        started = true;
        tour.hidden = false;
        showStep(0);

        if (window.location.hash === '#app-tour') {
            window.history.replaceState(null, '', `${window.location.pathname}${window.location.search}`);
        }
    };

    const requestStart = () => {
        startRequested = true;
        if (!document.querySelector('[data-check-in-prompt]') || window.dunoCheckInPromptReady === true) {
            window.setTimeout(beginTour, 250);
            return;
        }
        window.addEventListener('duno:check-in-ready', () => window.setTimeout(beginTour, 250), { once: true });
    };

    back.addEventListener('click', () => showStep(stepIndex - 1));
    next.addEventListener('click', () => {
        if (stepIndex === steps.length - 1) {
            closeTour(true);
            return;
        }
        showStep(stepIndex + 1);
    });
    skip.addEventListener('click', () => closeTour(true));

    document.addEventListener('keydown', (event) => {
        if (tour.hidden || event.key !== 'Escape') return;
        event.preventDefault();
        closeTour(true);
    });

    document.addEventListener('click', (event) => {
        if (tour.hidden || !(event.target instanceof Element)) return;
        const link = event.target.closest('a[href]');
        if (link) closeTour(true);
    }, true);

    if (window.location.hash === '#app-tour') {
        requestStart();
    } else if (tour.dataset.autoStart === 'true' && !hasCompleted()) {
        requestStart();
    }
})();
