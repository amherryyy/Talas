(() => {
    document.querySelectorAll('[data-page-back]').forEach((link) => {
        if (window.history.length < 2 || document.referrer === '') return;

        let previousPage;
        try {
            previousPage = new URL(document.referrer);
        } catch {
            return;
        }

        if (previousPage.origin !== window.location.origin) return;

        link.addEventListener('click', (event) => {
            if (event.button !== 0 || event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) return;
            event.preventDefault();
            window.history.back();
        });
    });
})();
