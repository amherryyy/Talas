(() => {
    const dialog = document.querySelector('[data-level-up-dialog]');
    if (!(dialog instanceof HTMLDialogElement)) return;

    const closeButton = dialog.querySelector('[data-level-up-close]');
    closeButton?.addEventListener('click', () => dialog.close());
    dialog.addEventListener('click', (event) => {
        if (event.target === dialog) dialog.close();
    });

    dialog.showModal();
})();
