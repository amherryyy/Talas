(() => {
    const mascot = document.querySelector('[data-auth-mascot]');
    const message = document.querySelector('[data-auth-mascot-message]');
    if (!(mascot instanceof HTMLButtonElement) || !(message instanceof HTMLElement)) return;
    const encouragements = [
        'You can start with one small step.',
        'A little progress still counts.',
        'Your study buddy is cheering you on.',
    ];
    let encouragementIndex = -1;
    mascot.addEventListener('click', () => {
        encouragementIndex = (encouragementIndex + 1) % encouragements.length;
        message.textContent = encouragements[encouragementIndex];
    });
})();
