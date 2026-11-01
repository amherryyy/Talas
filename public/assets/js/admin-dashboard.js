(() => {
    const printButton = document.querySelector('[data-admin-print]');

    if (!printButton) {
        return;
    }

    printButton.addEventListener('click', () => window.print());
})();
