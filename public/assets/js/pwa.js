(() => {
    'use strict';

    const status = document.querySelector('[data-pwa-status]');
    const statusMessage = status?.querySelector('[data-pwa-status-message]');
    const installButtons = document.querySelectorAll('[data-pwa-install]');
    const offlineText = document.documentElement.lang.startsWith('fil')
        ? 'Walang internet. Kailangan ng koneksyon para buksan o i-save ang account at study data mo.'
        : document.documentElement.lang.startsWith('en-PH')
            ? 'Offline ka ngayon. Kailangan ng internet para buksan o i-save ang account at study data mo.'
            : 'You are offline. An internet connection is needed to open or save your account and study data.';
    const onlineText = document.documentElement.lang.startsWith('fil')
        ? 'Bumalik na ang koneksyon sa internet.'
        : document.documentElement.lang.startsWith('en-PH')
            ? 'Online ka na ulit. Maaari ka nang magpatuloy.'
            : 'You are back online. You can continue.';

    let installPrompt = null;
    let statusTimeout = 0;

    function showStatus(state, message) {
        if (!status || !statusMessage) return;
        window.clearTimeout(statusTimeout);
        status.dataset.state = state;
        statusMessage.textContent = message;
        status.hidden = false;
        if (state === 'online') {
            statusTimeout = window.setTimeout(() => { status.hidden = true; }, 5000);
        }
    }

    function updateNetworkStatus() {
        if (!navigator.onLine) {
            showStatus('offline', offlineText);
            return;
        }
        if (status && !status.hidden && status.dataset.state === 'offline') {
            showStatus('online', onlineText);
        }
    }

    window.addEventListener('offline', updateNetworkStatus);
    window.addEventListener('online', updateNetworkStatus);
    if (!navigator.onLine) updateNetworkStatus();

    if ('serviceWorker' in navigator && window.isSecureContext) {
        const manifest = document.querySelector('link[rel="manifest"]');
        if (manifest) {
            const workerUrl = new URL('sw.js', manifest.href);
            navigator.serviceWorker.register(workerUrl.href, { scope: new URL('./', workerUrl).pathname })
                .catch(() => {
                    // Duno remains usable online if a browser or host blocks service workers.
                });
        }
    }

    window.addEventListener('beforeinstallprompt', (event) => {
        event.preventDefault();
        installPrompt = event;
        installButtons.forEach((button) => { button.hidden = false; });
    });

    installButtons.forEach((button) => {
        button.addEventListener('click', async () => {
            if (!installPrompt) return;
            installPrompt.prompt();
            await installPrompt.userChoice;
            installPrompt = null;
            installButtons.forEach((installButton) => { installButton.hidden = true; });
        });
    });

    window.addEventListener('appinstalled', () => {
        installPrompt = null;
        installButtons.forEach((button) => { button.hidden = true; });
    });
})();
