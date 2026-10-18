(() => {
    const menu = document.querySelector('[data-responsive-menu]');

    if (!menu || typeof window.matchMedia !== 'function') {
        return;
    }

    const mobileViewport = window.matchMedia('(max-width: 32rem)');
    const syncMenu = () => {
        if (mobileViewport.matches) {
            menu.removeAttribute('open');
        } else {
            menu.setAttribute('open', '');
        }
    };

    syncMenu();
    mobileViewport.addEventListener('change', syncMenu);
})();
