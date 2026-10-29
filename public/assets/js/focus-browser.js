(() => {
    const browser = document.querySelector('[data-focus-browser]');
    if (!browser) return;

    const grid = browser.querySelector('[data-focus-grid]');
    const listPane = browser.querySelector('.focus-topic-list');
    const mobileSubjectFilter = browser.querySelector('.focus-mobile-subject-filter');
    const rows = [...browser.querySelectorAll('[data-focus-topic-row]')];
    const topicLinks = [...browser.querySelectorAll('[data-focus-topic]')];
    const details = [...browser.querySelectorAll('[data-focus-detail]')];
    const folderButtons = [...browser.querySelectorAll('[data-focus-folder]')];
    const subjectSelect = browser.querySelector('[data-focus-subject-select]');
    const searchInput = browser.querySelector('[data-focus-search]');
    const countOutput = browser.querySelector('[data-focus-visible-count]');
    const emptyList = browser.querySelector('[data-focus-empty]');
    const emptyDetail = browser.querySelector('[data-focus-detail-empty]');
    const backButton = browser.querySelector('[data-focus-back]');
    let selectedTopicId = browser.querySelector('[data-focus-topic][aria-current]')?.dataset.focusTopic ?? null;
    let selectedFolder = 'all';

    window.addEventListener('resize', () => {
        if (!window.matchMedia('(max-width: 62rem)').matches) {
            listPane.inert = false;
            mobileSubjectFilter.inert = false;
        }
    });

    const visibleLinks = () => topicLinks.filter((link) => !link.closest('[data-focus-topic-row]').hidden);

    const selectTopic = (topicId, animate = true, openDetail = false) => {
        const link = topicLinks.find((candidate) => candidate.dataset.focusTopic === topicId);
        const detail = details.find((candidate) => candidate.dataset.focusDetail === topicId);
        if (!link || !detail) return;

        selectedTopicId = topicId;
        for (const candidate of topicLinks) {
            const selected = candidate === link;
            candidate.classList.toggle('is-selected', selected);
            if (selected) candidate.setAttribute('aria-current', 'true');
            else candidate.removeAttribute('aria-current');
        }
        for (const candidate of details) {
            candidate.hidden = candidate !== detail;
            candidate.classList.toggle('is-current', candidate === detail);
            candidate.classList.remove('is-entering');
        }
        emptyDetail.hidden = true;
        if (animate) {
            void detail.offsetWidth;
            detail.classList.add('is-entering');
        }
        browser.querySelector('[data-focus-details]').scrollTop = 0;
        if (openDetail) {
            grid.classList.add('is-detail-open');
            if (window.matchMedia('(max-width: 62rem)').matches) {
                listPane.inert = true;
                mobileSubjectFilter.inert = true;
                backButton.focus({ preventScroll: true });
            }
        }
    };

    const applyFilters = ({ keepDetailOpen = false } = {}) => {
        const query = searchInput.value.trim().toLocaleLowerCase();
        for (const row of rows) {
            const subjectMatches = selectedFolder === 'all' || row.dataset.focusSubject === selectedFolder;
            const textMatches = query === '' || row.textContent.toLocaleLowerCase().includes(query);
            row.hidden = !subjectMatches || !textMatches;
        }

        const visible = visibleLinks();
        countOutput.textContent = String(visible.length);
        emptyList.hidden = visible.length > 0;
        for (const link of topicLinks) {
            const folderMatches = selectedFolder === 'all' || link.closest('[data-focus-topic-row]').dataset.focusSubject === selectedFolder;
            const textMatches = query === '' || link.textContent.toLocaleLowerCase().includes(query);
            link.tabIndex = folderMatches && textMatches ? 0 : -1;
        }

        if (visible.length === 0) {
            for (const detail of details) detail.hidden = true;
            emptyDetail.hidden = false;
            selectedTopicId = null;
            grid.classList.remove('is-detail-open');
            listPane.inert = false;
            mobileSubjectFilter.inert = false;
            return;
        }

        const selectionIsVisible = visible.some((link) => link.dataset.focusTopic === selectedTopicId);
        if (!selectionIsVisible) selectTopic(visible[0].dataset.focusTopic, false, keepDetailOpen);
    };

    for (const button of folderButtons) {
        button.addEventListener('click', () => {
            selectedFolder = button.dataset.focusFolder;
            for (const folderButton of folderButtons) {
                const selected = folderButton === button;
                folderButton.classList.toggle('is-active', selected);
                folderButton.setAttribute('aria-pressed', selected ? 'true' : 'false');
            }
            subjectSelect.value = selectedFolder;
            applyFilters();
        });
    }

    subjectSelect.addEventListener('change', () => {
        selectedFolder = subjectSelect.value;
        for (const button of folderButtons) {
            const selected = button.dataset.focusFolder === selectedFolder;
            button.classList.toggle('is-active', selected);
            button.setAttribute('aria-pressed', selected ? 'true' : 'false');
        }
        applyFilters();
    });

    searchInput.addEventListener('input', () => applyFilters({ keepDetailOpen: grid.classList.contains('is-detail-open') }));

    browser.querySelector('.focus-topic-rows').addEventListener('click', (event) => {
        const link = event.target.closest('[data-focus-topic]');
        if (!link || event.button !== 0 || event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) return;
        event.preventDefault();
        selectTopic(link.dataset.focusTopic, true, true);
    });

    backButton.addEventListener('click', () => {
        grid.classList.remove('is-detail-open');
        listPane.inert = false;
        mobileSubjectFilter.inert = false;
        topicLinks.find((link) => link.dataset.focusTopic === selectedTopicId)?.focus();
    });

    applyFilters();
})();
