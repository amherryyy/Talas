(() => {
    const eye = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7S2 12 2 12Z"></path><circle cx="12" cy="12" r="3"></circle></svg>';
    const eyeOff = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><path d="m3 3 18 18"></path><path d="M10.6 10.6a2 2 0 0 0 2.8 2.8"></path><path d="M9.9 5.2A10.7 10.7 0 0 1 12 5c6.4 0 10 7 10 7a15.7 15.7 0 0 1-3.1 3.9"></path><path d="M6.2 6.2C3.5 8 2 12 2 12s3.6 7 10 7a10.7 10.7 0 0 0 2.1-.2"></path></svg>';
    const labels = document.body.dataset;

    document.querySelectorAll('input[type="password"]').forEach((input) => {
        const field = input.closest('.form-floating') ?? input.parentElement;
        if (!field || field.querySelector('[data-password-toggle]')) return;

        const isConfirmation = input.name === 'password_confirm';
        const showLabel = isConfirmation ? labels.passwordConfirmShow : labels.passwordShow;
        const hideLabel = isConfirmation ? labels.passwordConfirmHide : labels.passwordHide;
        const button = document.createElement('button');
        button.className = 'password-visibility-toggle';
        button.type = 'button';
        button.dataset.passwordToggle = '';
        button.setAttribute('aria-label', showLabel);
        button.setAttribute('aria-pressed', 'false');
        if (input.id) button.setAttribute('aria-controls', input.id);
        button.innerHTML = eye;

        field.classList.add('has-password-toggle');
        field.append(button);

        button.addEventListener('click', () => {
            const visible = input.type === 'password';
            input.type = visible ? 'text' : 'password';
            button.innerHTML = visible ? eyeOff : eye;
            button.setAttribute('aria-label', visible ? hideLabel : showLabel);
            button.setAttribute('aria-pressed', String(visible));
            input.focus({ preventScroll: true });
        });
    });
})();
