(() => {
    const tour = document.querySelector('[data-app-tour]');
    if (!(tour instanceof HTMLElement)) return;
    const shade = document.querySelector('[data-tour-shade]');
    const shadePanels = {
        top: shade?.querySelector('[data-shade-top]'),
        left: shade?.querySelector('[data-shade-left]'),
        right: shade?.querySelector('[data-shade-right]'),
        bottom: shade?.querySelector('[data-shade-bottom]'),
    };
    const pageRegions = [...document.querySelectorAll('.skip-link, .shell, .app-footer, .student-bottom-nav')]
        .filter((element) => element instanceof HTMLElement);
    const originalInertState = new Map(pageRegions.map((element) => [element, element.inert]));

    const userKey = tour.dataset.tourUser;
    if (!userKey) return;

    const storageKey = `duno-app-tour-v1-${userKey}`;
    const tourCopy = document.documentElement.lang.startsWith('fil') ? {
        progress: 'Pagsisimula · Hakbang {step} sa {total}', skip: 'Laktawan ang gabay', next: 'Susunod', finish: 'Tapos na',
        steps: [
            ['Lugar mo sa pag-aaral', 'Dito ka magsisimula. Gamitin ang mga card at menu para piliin ang tool na kailangan mo sa susunod na pag-aaral.'],
            ['Duno Pokus', 'Makikita rito ang mga paksang hindi pa tapos, nakaayos batay sa takdang petsa, halaga, at progreso.'],
            ['Duno Plano', 'Gumawa ng plano batay sa oras na mayroon ka, at ayusin ang mga gawain ayon sa araw mo.'],
            ['Duno Orasan', 'Orasan ang iyong pag-aaral, tingnan ang lumipas na oras, at i-save lamang kapag handa ka na.'],
            ['Aralytics', 'Balikan ang mga na-save mong sesyon gamit ang mga kabuuan, pattern, at simpleng chart.'],
            ['Duno Gabay', 'Tingnan ang maiikling pangkalahatang mungkahi sa pag-aaral at piliin ang angkop sa kurso mo.'],
            ['Iba pang pahina', 'Nasa menu ang mga asignatura, paksa, at pribadong tala. Pribado ang mga tala mo. Maaari mong ulitin ang gabay dito.'],
        ],
    } : document.documentElement.lang.startsWith('en-PH') ? {
        progress: 'Getting started · Step {step} of {total}', skip: 'I-skip ang tour', next: 'Next', finish: 'Tapos na',
        steps: [
            ['Study space mo', 'Dito ka magsisimula. Gamitin ang cards at navigation para mahanap ang tool na bagay sa next study step mo.'],
            ['Duno Focus', 'Hanapin ang unfinished topics mo sa clear na order gamit ang due dates, importance, at progress.'],
            ['Duno Plan', 'Gumawa ng plan based sa available mong time, tapos i-adjust ang blocks para fit sa araw mo.'],
            ['Duno Timer', 'I-time ang study session mo, tingnan ang elapsed time, at i-save lang kapag gusto mo.'],
            ['Aralytics', 'Balikan ang mga saved study session mo gamit ang totals, patterns, at simple charts.'],
            ['Duno Guide', 'Mag-browse ng short, general study suggestions at piliin ang swak sa course mo.'],
            ['More pages', 'Nasa More ang subjects, topics, at private check-ins. Private ang check-ins mo. Puwede mong ulitin ang tour mula rito.'],
        ],
    } : {
        progress: 'Getting started · Step {step} of {total}', skip: 'Skip tour', next: 'Next', finish: 'Finish',
        steps: [
            ['Your study space', 'This is your starting point. Use the cards and navigation to find the tool that fits your next study step.'],
            ['Duno Focus', 'Find unfinished topics in a clear order using due dates, importance, and progress.'],
            ['Duno Plan', 'Make a plan from the time you have, then adjust or reorder its blocks to fit your day.'],
            ['Duno Timer', 'Time a study session, review the elapsed time, and save it only when you choose.'],
            ['Aralytics', 'Look back at the study sessions you saved through totals, patterns, and simple charts.'],
            ['Duno Guide', 'Browse short, general study suggestions and choose the ones that fit your course.'],
            ['More pages', 'More contains subjects, topics, and private check-ins. Your check-ins stay private. You can replay this tour from the same menu after you finish.'],
        ],
    };
    const steps = tourCopy.steps.map(([title, copy], index) => ({ target: ['start', 'focus', 'plan', 'timer', 'aralytics', 'guide', 'more'][index], title, copy }));
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

    skip.textContent = tourCopy.skip;

    let stepIndex = 0;
    let activeTarget = null;
    let activeTargetParent = null;
    let startRequested = false;
    let started = false;

    const setBackgroundUnavailable = (unavailable) => {
        pageRegions.forEach((element) => {
            element.inert = unavailable ? true : originalInertState.get(element) === true;
        });
    };

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
        if (activeTargetParent instanceof HTMLElement) {
            activeTargetParent.classList.remove('is-tour-target-parent');
        }
        activeTarget = null;
        activeTargetParent = null;
    };

    const setPanelRect = (panel, left, top, width, height) => {
        if (!(panel instanceof HTMLElement)) return;
        Object.assign(panel.style, {
            left: `${left}px`,
            top: `${top}px`,
            width: `${Math.max(0, width)}px`,
            height: `${Math.max(0, height)}px`,
        });
    };

    const updateSpotlight = () => {
        if (!(shade instanceof HTMLElement) || tour.hidden || !(activeTarget instanceof HTMLElement)) return;

        const rect = activeTarget.getBoundingClientRect();
        const padding = 12;
        const left = Math.max(0, rect.left - padding);
        const top = Math.max(0, rect.top - padding);
        const right = Math.min(window.innerWidth, rect.right + padding);
        const bottom = Math.min(window.innerHeight, rect.bottom + padding);
        const width = window.innerWidth;
        const height = window.innerHeight;

        setPanelRect(shadePanels.top, 0, 0, width, top);
        setPanelRect(shadePanels.left, 0, top, left, Math.max(0, bottom - top));
        setPanelRect(shadePanels.right, right, top, Math.max(0, width - right), Math.max(0, bottom - top));
        setPanelRect(shadePanels.bottom, 0, bottom, width, Math.max(0, height - bottom));
        shade.hidden = false;
    };

    const placeTourPanel = () => {
        if (tour.hidden || !(activeTarget instanceof HTMLElement)) return;

        const viewportWidth = window.innerWidth;
        const viewportHeight = window.innerHeight;
        const isMobileNav = window.matchMedia('(max-width: 52rem)').matches;
        const isPhone = viewportWidth <= 36 * 16;
        const margin = 12;
        const width = isPhone ? Math.max(0, viewportWidth - margin * 2) : Math.min(416, viewportWidth - margin * 2);
        const bottomReserve = isMobileNav ? 92 : margin;
        const panelHeight = Math.min(tour.offsetHeight, viewportHeight - margin * 2);
        const leftPositions = isPhone ? [margin] : [margin, Math.max(margin, viewportWidth - width - margin)];
        const topPositions = [margin, Math.max(margin, viewportHeight - panelHeight - bottomReserve)];
        const target = activeTarget.getBoundingClientRect();
        const candidates = [];

        for (const left of leftPositions) {
            for (const top of topPositions) {
                const overlapWidth = Math.max(0, Math.min(left + width, target.right + 16) - Math.max(left, target.left - 16));
                const overlapHeight = Math.max(0, Math.min(top + panelHeight, target.bottom + 16) - Math.max(top, target.top - 16));
                const distance = Math.hypot((left + width / 2) - (target.left + target.width / 2), (top + panelHeight / 2) - (target.top + target.height / 2));
                candidates.push({ left, top, score: overlapWidth * overlapHeight * 1000 + distance });
            }
        }

        candidates.sort((a, b) => a.score - b.score);
        const placement = candidates[0];
        if (!placement) return;

        tour.style.right = 'auto';
        tour.style.bottom = 'auto';
        tour.style.left = `${placement.left}px`;
        tour.style.top = `${placement.top}px`;
        tour.style.width = `${width}px`;
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
        if (shade instanceof HTMLElement) shade.hidden = true;
        setBackgroundUnavailable(false);
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
        activeTargetParent = activeTarget.closest('.app-header, .student-bottom-nav');
        if (activeTargetParent instanceof HTMLElement) {
            activeTargetParent.classList.add('is-tour-target-parent');
        }

        const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        activeTarget.scrollIntoView({ behavior: reduceMotion ? 'auto' : 'smooth', block: 'center' });
        progress.textContent = tourCopy.progress.replace('{step}', String(stepIndex + 1)).replace('{total}', String(steps.length));
        title.textContent = step.title;
        copy.textContent = step.copy;
        back.disabled = stepIndex === 0;
        next.textContent = stepIndex === steps.length - 1 ? tourCopy.finish : tourCopy.next;
        title.focus({ preventScroll: true });
        window.requestAnimationFrame(() => {
            updateSpotlight();
            placeTourPanel();
        });
        window.setTimeout(placeTourPanel, reduceMotion ? 0 : 320);
    };

    const beginTour = () => {
        if (!startRequested || started || tour.hidden === false) return;
        started = true;
        tour.hidden = false;
        setBackgroundUnavailable(true);
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
        if (tour.hidden) return;
        if (event.key === 'Escape') {
            event.preventDefault();
            closeTour(true);
            return;
        }
        if (event.key !== 'Tab') return;

        const focusable = [...tour.querySelectorAll('button:not(:disabled), a[href], input:not(:disabled), select:not(:disabled), textarea:not(:disabled), [tabindex]:not([tabindex="-1"])')]
            .filter((element) => element instanceof HTMLElement && element.getClientRects().length > 0);
        const first = focusable[0];
        const last = focusable[focusable.length - 1];
        if (!(first instanceof HTMLElement) || !(last instanceof HTMLElement)) return;

        if (event.shiftKey && (document.activeElement === first || document.activeElement === title)) {
            event.preventDefault();
            last.focus();
        } else if (!event.shiftKey && (document.activeElement === last || document.activeElement === title)) {
            event.preventDefault();
            first.focus();
        }
    });

    window.addEventListener('scroll', updateSpotlight, { passive: true });
    window.addEventListener('resize', () => {
        updateSpotlight();
        placeTourPanel();
    }, { passive: true });

    if (window.location.hash === '#app-tour') {
        requestStart();
    } else if (tour.dataset.autoStart === 'true' && !hasCompleted()) {
        requestStart();
    }
})();
