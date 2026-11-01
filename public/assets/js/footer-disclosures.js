(() => {
    const groups = [...document.querySelectorAll('[data-footer-disclosure]')];
    if (groups.length === 0 || typeof window.matchMedia !== 'function') return;

    const mobile = window.matchMedia('(max-width: 44rem)');
    const sync = () => {
        groups.forEach((group) => {
            group.open = !mobile.matches;
        });
    };

    sync();
    if (typeof mobile.addEventListener === 'function') mobile.addEventListener('change', sync);
    else mobile.addListener(sync);
})();
