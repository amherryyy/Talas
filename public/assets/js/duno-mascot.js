(() => {
    const durations = { greeting: 1400, thinking: 1350, celebrating: 1100, solo: 1050 };
    const stateClasses = ['idle', ...Object.keys(durations)].map((state) => `is-duno-${state}`);
    const controllers = new Map();
    const supportsIntersectionObserver = 'IntersectionObserver' in window;

    function register(element) {
        if (!(element instanceof HTMLElement) || controllers.has(element)) return;
        const controller = { timer: 0, observer: null, click: null };
        controller.observer = supportsIntersectionObserver
            ? new IntersectionObserver(([entry]) => element.classList.toggle('is-duno-offscreen', !entry.isIntersecting), { threshold: 0.01 })
            : null;
        controller.observer?.observe(element);
        if (element.hasAttribute('data-duno-interactive')) {
            controller.click = () => window.Duno.play('solo', element);
            element.addEventListener('click', controller.click);
        }
        controllers.set(element, controller);
        setState(element, element.dataset.dunoState || 'idle');
    }

    function setState(element, state) {
        const controller = controllers.get(element);
        if (!controller) return;
        window.clearTimeout(controller.timer);
        element.classList.remove(...stateClasses);
        const nextState = durations[state] || state === 'idle' ? state : 'idle';
        element.dataset.dunoState = nextState;
        element.classList.add(`is-duno-${nextState}`);
        if (durations[nextState]) {
            controller.timer = window.setTimeout(() => setState(element, 'idle'), durations[nextState]);
        }
    }

    function targets(target) {
        if (target instanceof HTMLElement) return [target];
        if (typeof target === 'string') return [...document.querySelectorAll(target)].filter((element) => element instanceof HTMLElement);
        return [...document.querySelectorAll('[data-duno-mascot]')];
    }

    function destroy(element) {
        const controller = controllers.get(element);
        if (!controller) return;
        window.clearTimeout(controller.timer);
        controller.observer?.disconnect();
        if (controller.click) element.removeEventListener('click', controller.click);
        controllers.delete(element);
    }

    window.Duno = {
        play(state = 'idle', target) {
            const nextState = durations[state] || state === 'idle' ? state : 'idle';
            targets(target).forEach((element) => {
                register(element);
                setState(element, nextState);
            });
        },
        destroy(target) {
            targets(target).forEach(destroy);
        },
    };

    document.querySelectorAll('[data-duno-mascot]').forEach(register);
    document.querySelectorAll('[data-duno-plan-form], [data-agenda-progress-form]').forEach((form) => {
        form.addEventListener('submit', (event) => {
            if (event.defaultPrevented) return;
            window.Duno.play('thinking', '.plan-thinking-mascot');
        });
    });
    document.addEventListener('visibilitychange', () => {
        controllers.forEach((controller, element) => element.classList.toggle('is-duno-page-hidden', document.hidden));
    });
    new MutationObserver(() => {
        controllers.forEach((controller, element) => {
            if (!element.isConnected) destroy(element);
        });
    }).observe(document.documentElement, { childList: true, subtree: true });
})();
