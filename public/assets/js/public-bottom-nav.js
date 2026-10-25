const nav = document.querySelector('[data-guest-bottom-nav]');

if (nav) {
    const links = [...nav.querySelectorAll('[data-nav-section]')];
    const sections = links
        .map((link) => ({ link, section: document.getElementById(link.dataset.navSection) }))
        .filter(({ section }) => section !== null);

    const updateCurrentSection = () => {
        const marker = Math.min(window.innerHeight * 0.35, 320);
        let current = sections[0];

        for (const item of sections) {
            if (item.section.getBoundingClientRect().top <= marker) current = item;
        }

        for (const item of sections) {
            if (item === current) {
                item.link.setAttribute('aria-current', 'location');
            } else {
                item.link.removeAttribute('aria-current');
            }
        }
    };

    window.addEventListener('scroll', updateCurrentSection, { passive: true });
    window.addEventListener('resize', updateCurrentSection);
    window.addEventListener('hashchange', updateCurrentSection);
    updateCurrentSection();
}
