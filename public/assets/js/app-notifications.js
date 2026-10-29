(() => {
    const stack = document.querySelector('[data-notification-stack]');
    if (!stack) return;

    const dismissLabel = stack.dataset.dismissLabel || 'Dismiss notification';
    const mascotSource = stack.dataset.mascotSrc;
    const titles = {
        success: stack.dataset.titleSuccess || 'Nice work!',
        info: stack.dataset.titleInfo || 'A little note',
        warning: stack.dataset.titleWarning || 'Quick heads-up',
        error: stack.dataset.titleError || "Let's sort this out",
    };
    const emotions = {
        success: String.fromCodePoint(0x1f973),
        info: String.fromCodePoint(0x1f914),
        warning: String.fromCodePoint(0x1f62e),
        error: String.fromCodePoint(0x1f61f),
    };
    const selector = '.notice[role="status"], .notice[role="alert"], .errors[role="alert"], .duno-auth .alert';

    function dismiss(card, delay = 0) {
        const timeout = window.setTimeout(() => {
            if (!card.isConnected) return;
            card.classList.add('is-leaving');
            window.setTimeout(() => card.remove(), 190);
        }, delay);
        card.addEventListener('pointerenter', () => window.clearTimeout(timeout), { once: true });
        card.addEventListener('focusin', () => window.clearTimeout(timeout), { once: true });
    }

    function wireCard(card) {
        const mood = card.dataset.mood || 'info';
        card.querySelector('.duno-toast-dismiss')?.addEventListener('click', event => {
            event.preventDefault();
            dismiss(card);
        });
        if (mood === 'success' || mood === 'info') {
            dismiss(card, mood === 'success' ? 7000 : 9000);
        }
    }

    function addNotification(source) {
        const isList = source.matches('ul.errors, ol.errors');
        const requestedType = source.dataset.notificationType;
        const mood = ['success', 'info', 'warning', 'error'].includes(requestedType)
            ? requestedType
            : source.classList.contains('error') || source.classList.contains('errors') || source.classList.contains('alert-danger')
                ? 'error'
                : source.classList.contains('alert-warning')
                    ? 'warning'
                    : source.classList.contains('alert-info')
                        ? 'info'
                        : 'success';
        const card = document.createElement('article');
        card.className = 'duno-toast';
        card.dataset.mood = mood;
        card.setAttribute('role', mood === 'error' || mood === 'warning' ? 'alert' : 'status');
        card.setAttribute('aria-live', mood === 'error' || mood === 'warning' ? 'assertive' : 'polite');

        const mascot = document.createElement('span');
        mascot.className = 'duno-toast-mascot';
        mascot.setAttribute('aria-hidden', 'true');
        const image = document.createElement('img');
        image.src = mascotSource;
        image.alt = '';
        mascot.append(image);
        const emotion = document.createElement('span');
        emotion.className = 'duno-toast-emotion';
        emotion.textContent = emotions[mood];
        mascot.append(emotion);

        const copy = document.createElement('div');
        copy.className = 'duno-toast-copy';
        const title = document.createElement('strong');
        title.className = 'duno-toast-title';
        title.textContent = titles[mood];
        copy.append(title);

        if (isList && source.children.length > 0) {
            const list = document.createElement('ul');
            list.className = 'duno-toast-message-list';
            Array.from(source.children).forEach(item => {
                const entry = document.createElement('li');
                entry.textContent = item.textContent.trim();
                if (entry.textContent) list.append(entry);
            });
            copy.append(list);
        } else {
            const message = document.createElement('p');
            message.className = 'duno-toast-message';
            message.textContent = source.textContent.trim();
            copy.append(message);
        }

        const close = document.createElement('button');
        close.className = 'duno-toast-dismiss';
        close.type = 'button';
        close.setAttribute('aria-label', dismissLabel);
        close.textContent = '\u00d7';

        card.append(mascot, copy, close);
        stack.append(card);
        source.remove();
        wireCard(card);
    }

    stack.querySelectorAll('.duno-toast').forEach(wireCard);

    document.querySelectorAll(selector).forEach(source => {
        if (!source.closest('[data-notification-stack]') || !source.matches('.duno-toast')) {
            addNotification(source);
        }
    });
})();
