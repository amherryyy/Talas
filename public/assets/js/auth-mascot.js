(() => {
    const mascot = document.querySelector('[data-auth-mascot]');
    const message = document.querySelector('[data-auth-mascot-message]');
    if (!(mascot instanceof HTMLButtonElement) || !(message instanceof HTMLElement)) return;
    const image = mascot.querySelector('img');
    const restingSource = image?.src;
    const cheeringSource = mascot.dataset.cheerSrc;
    if (cheeringSource) {
        const cheeringImage = new Image();
        cheeringImage.fetchPriority = 'low';
        cheeringImage.src = cheeringSource;
    }

    const encouragements = [
        'You can start with one small step.',
        'A little progress still counts.',
        'Your study buddy is cheering you on.',
    ];
    let encouragementIndex = -1;
    let animationTimer = 0;

    mascot.addEventListener('click', () => {
        encouragementIndex = (encouragementIndex + 1) % encouragements.length;
        message.textContent = encouragements[encouragementIndex];

        mascot.classList.remove('is-responding');
        window.clearTimeout(animationTimer);
        if (image && cheeringSource) image.src = cheeringSource;
        window.requestAnimationFrame(() => mascot.classList.add('is-responding'));
        animationTimer = window.setTimeout(() => {
            mascot.classList.remove('is-responding');
            if (image && restingSource) image.src = restingSource;
        }, 760);
    });
})();
