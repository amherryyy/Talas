(() => {
    const labels = document.body.dataset;
    const svgNamespace = 'http://www.w3.org/2000/svg';

    function createEyeIcon(passwordVisible) {
        const svg = document.createElementNS(svgNamespace, 'svg');
        svg.setAttribute('viewBox', '0 0 24 24');
        svg.setAttribute('fill', 'none');
        svg.setAttribute('stroke', 'currentColor');
        svg.setAttribute('stroke-width', '1.8');
        svg.setAttribute('stroke-linecap', 'round');
        svg.setAttribute('stroke-linejoin', 'round');
        svg.setAttribute('aria-hidden', 'true');
        svg.setAttribute('focusable', 'false');

        const paths = passwordVisible
            ? ['m3 3 18 18', 'M10.6 10.6a2 2 0 0 0 2.8 2.8', 'M9.9 5.2A10.7 10.7 0 0 1 12 5c6.4 0 10 7 10 7a15.7 15.7 0 0 1-3.1 3.9', 'M6.2 6.2C3.5 8 2 12 2 12s3.6 7 10 7a10.7 10.7 0 0 0 2.1-.2']
            : ['M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7S2 12 2 12Z'];

        paths.forEach((data) => {
            const path = document.createElementNS(svgNamespace, 'path');
            path.setAttribute('d', data);
            svg.append(path);
        });

        if (!passwordVisible) {
            const pupil = document.createElementNS(svgNamespace, 'circle');
            pupil.setAttribute('cx', '12');
            pupil.setAttribute('cy', '12');
            pupil.setAttribute('r', '3');
            svg.append(pupil);
        }

        return svg;
    }

    document.querySelectorAll('input[type="password"]').forEach((input) => {
        const field = input.closest('.form-field') ?? input.parentElement;
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
        button.replaceChildren(createEyeIcon(false));

        field.classList.add('has-password-toggle');
        field.append(button);

        button.addEventListener('click', () => {
            const visible = input.type === 'password';
            input.type = visible ? 'text' : 'password';
            button.replaceChildren(createEyeIcon(visible));
            button.setAttribute('aria-label', visible ? hideLabel : showLabel);
            button.setAttribute('aria-pressed', String(visible));
            input.focus({ preventScroll: true });
        });
    });
})();
