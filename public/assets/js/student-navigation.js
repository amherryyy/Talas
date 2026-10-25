(() => {
    const menu = document.querySelector('[data-student-menu]');
    const closeMenu = () => {
        if (menu instanceof HTMLDetailsElement && menu.open) {
            menu.open = false;
        }
    };

    if (menu instanceof HTMLDetailsElement) {
        document.addEventListener('pointerdown', (event) => {
            if (!menu.contains(event.target)) closeMenu();
        });
        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape' && menu.open) {
                closeMenu();
                menu.querySelector('summary')?.focus();
            }
        });
        menu.querySelectorAll('a').forEach((link) => link.addEventListener('click', closeMenu));
    }

    const guideLinks = document.querySelectorAll('[data-guide-nav], [data-nav-section="guide"]');
    const homeLinks = document.querySelectorAll('[data-home-nav]');
    const setGuideCurrent = () => {
        const onGuide = window.location.hash === '#guide-heading';
        const onStudentHome = /(?:^|\/)student\/?$/.test(window.location.pathname);
        homeLinks.forEach((link) => {
            if (onGuide || !onStudentHome) link.removeAttribute('aria-current');
            else link.setAttribute('aria-current', 'page');
        });
        guideLinks.forEach((link) => {
            if (onGuide) link.setAttribute('aria-current', 'location');
            else link.removeAttribute('aria-current');
        });
    };
    setGuideCurrent();
    window.addEventListener('hashchange', setGuideCurrent);
})();
