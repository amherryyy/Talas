(() => {
    const form = document.querySelector('[data-profile-form]');
    const saveBar = document.querySelector('[data-profile-savebar]');
    const username = document.querySelector('[data-profile-username]');
    const discard = document.querySelector('[data-profile-discard]');
    const submit = document.querySelector('[data-profile-submit]');
    const readyLabel = document.querySelector('[data-profile-ready]');
    const savingLabel = document.querySelector('[data-profile-saving]');

    if (!form || !saveBar || !username || !discard || !submit || !readyLabel || !savingLabel) return;

    const originalUsername = username.value;
    let saving = false;

    const updateSaveBar = () => {
        const dirty = username.value !== originalUsername;
        saveBar.hidden = !dirty;
        discard.disabled = !dirty || saving;
        submit.disabled = !dirty || saving;
    };

    username.addEventListener('input', updateSaveBar);
    username.addEventListener('change', updateSaveBar);
    form.addEventListener('reset', () => requestAnimationFrame(updateSaveBar));
    form.addEventListener('submit', () => {
        saving = true;
        submit.disabled = true;
        readyLabel.hidden = true;
        savingLabel.hidden = false;
    });

    updateSaveBar();
})();
