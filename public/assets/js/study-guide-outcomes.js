(() => {
    const guide = document.querySelector('[data-study-guide]');
    if (!guide) return;

    const subjectSelect = guide.querySelector('[data-guide-subject]');
    const topicSelect = guide.querySelector('[data-guide-topic]');
    const goalButtons = [...guide.querySelectorAll('[data-guide-goal]')];
    const methodCards = [...guide.querySelectorAll('[data-guide-method]')];
    const title = guide.querySelector('[data-guide-approach-title]');
    const summary = guide.querySelector('[data-guide-approach-summary]');
    const status = guide.querySelector('[data-guide-outcome-status]');
    const empty = guide.querySelector('[data-guide-empty]');

    const createPicker = (select, kind) => {
        const wrapper = document.createElement('div');
        wrapper.className = `guide-select-picker guide-select-picker-${kind}`;
        wrapper.dataset.picker = kind;

        const label = guide.querySelector(`label[for="${select.id}"]`);
        const trigger = document.createElement('button');
        trigger.type = 'button';
        trigger.id = `${select.id}-picker-trigger`;
        trigger.className = 'guide-select-trigger';
        trigger.setAttribute('aria-haspopup', 'dialog');
        trigger.setAttribute('aria-expanded', 'false');
        trigger.setAttribute('aria-controls', `${select.id}-picker-panel`);
        if (label?.id) trigger.setAttribute('aria-labelledby', `${label.id} ${select.id}-picker-value`);

        const value = document.createElement('span');
        value.className = 'guide-select-value';
        value.id = `${select.id}-picker-value`;
        const chevron = document.createElement('span');
        chevron.className = 'guide-select-chevron';
        chevron.setAttribute('aria-hidden', 'true');
        chevron.textContent = '';
        trigger.append(value, chevron);

        const panel = document.createElement('div');
        panel.className = 'guide-select-panel';
        panel.id = `${select.id}-picker-panel`;
        panel.setAttribute('role', 'dialog');
        panel.setAttribute('aria-label', kind === 'subject' ? 'Choose a subject' : 'Choose a topic');
        panel.hidden = true;

        const searchWrap = document.createElement('label');
        searchWrap.className = 'guide-select-search';
        const searchIcon = document.createElement('span');
        searchIcon.setAttribute('aria-hidden', 'true');
        searchIcon.textContent = '';
        const search = document.createElement('input');
        search.type = 'search';
        search.autocomplete = 'off';
        search.placeholder = kind === 'subject' ? 'Search subjects' : 'Search topics';
        search.setAttribute('aria-label', search.placeholder);
        searchWrap.append(searchIcon, search);

        const options = document.createElement('div');
        options.className = 'guide-select-options';
        options.setAttribute('role', 'group');
        options.setAttribute('aria-label', kind === 'subject' ? 'Available subjects' : 'Available topics');
        const noResults = document.createElement('p');
        noResults.className = 'guide-select-empty';
        noResults.textContent = kind === 'subject' ? 'No subjects match that search.' : 'No topics match that search.';
        noResults.setAttribute('role', 'status');
        noResults.setAttribute('aria-live', 'polite');
        noResults.hidden = true;
        panel.append(searchWrap, options, noResults);

        select.before(wrapper);
        wrapper.append(select, trigger, panel);
        if (label) label.htmlFor = trigger.id;
        select.classList.add('guide-select-native-enhanced');
        select.tabIndex = -1;
        select.setAttribute('aria-hidden', 'true');

        const visibleOptions = () => [...options.querySelectorAll('button:not([hidden])')];
        const filterOptions = () => {
            const query = search.value.trim().toLocaleLowerCase();
            for (const option of options.querySelectorAll('button')) {
                option.hidden = !option.textContent.toLocaleLowerCase().includes(query);
            }
            noResults.hidden = visibleOptions().length > 0;
        };
        const syncValue = () => {
            const selected = select.selectedOptions[0];
            value.textContent = selected?.textContent.trim() || '';
            trigger.disabled = select.disabled;
            for (const option of options.querySelectorAll('button')) {
                option.setAttribute('aria-pressed', option.dataset.value === select.value ? 'true' : 'false');
            }
        };
        const refreshOptions = () => {
            options.replaceChildren();
            for (const option of select.options) {
                if (option.hidden || option.disabled) continue;
                const choice = document.createElement('button');
                choice.type = 'button';
                choice.className = 'guide-select-option';
                choice.dataset.value = option.value;
                choice.textContent = option.textContent.trim();
                choice.setAttribute('aria-pressed', option.selected ? 'true' : 'false');
                choice.addEventListener('click', () => {
                    select.value = option.value;
                    select.dispatchEvent(new Event('change', { bubbles: true }));
                    syncValue();
                    closePicker(true);
                });
                choice.addEventListener('keydown', (event) => {
                    const choices = visibleOptions();
                    const index = choices.indexOf(choice);
                    if (event.key === 'ArrowDown' || event.key === 'ArrowUp') {
                        event.preventDefault();
                        const offset = event.key === 'ArrowDown' ? 1 : -1;
                        choices[(index + offset + choices.length) % choices.length]?.focus();
                    } else if (event.key === 'Home' || event.key === 'End') {
                        event.preventDefault();
                        choices[event.key === 'Home' ? 0 : choices.length - 1]?.focus();
                    } else if (event.key === 'Escape') {
                        event.preventDefault();
                        closePicker(true);
                    }
                });
                options.append(choice);
            }
            filterOptions();
            syncValue();
        };
        const closePicker = (restoreFocus = false) => {
            panel.hidden = true;
            trigger.setAttribute('aria-expanded', 'false');
            wrapper.classList.remove('is-open');
            if (restoreFocus) trigger.focus();
        };
        const openPicker = () => {
            if (select.disabled) return;
            panel.hidden = false;
            trigger.setAttribute('aria-expanded', 'true');
            wrapper.classList.add('is-open');
            search.value = '';
            filterOptions();
            search.focus();
        };

        trigger.addEventListener('click', () => panel.hidden ? openPicker() : closePicker());
        trigger.addEventListener('keydown', (event) => {
            if (event.key === 'ArrowDown') {
                event.preventDefault();
                openPicker();
            }
        });
        search.addEventListener('input', filterOptions);
        search.addEventListener('keydown', (event) => {
            const choices = visibleOptions();
            if (event.key === 'ArrowDown' && choices.length > 0) {
                event.preventDefault();
                choices[0].focus();
            } else if (event.key === 'Escape') {
                event.preventDefault();
                closePicker(true);
            } else if (event.key === 'Enter' && choices.length === 1) {
                event.preventDefault();
                choices[0].click();
            }
        });
        wrapper.addEventListener('focusout', () => {
            window.setTimeout(() => {
                if (!wrapper.contains(document.activeElement)) closePicker();
            }, 0);
        });
        document.addEventListener('pointerdown', (event) => {
            if (!wrapper.contains(event.target)) closePicker();
        });
        select.addEventListener('change', syncValue);
        refreshOptions();

        return { refresh: refreshOptions };
    };

    const subjectPicker = createPicker(subjectSelect, 'subject');
    const topicPicker = createPicker(topicSelect, 'topic');

    const approaches = {
        understand: {
            title: 'Build understanding by explaining it',
            summary: 'Recall what you know, explain the idea, then revisit it later.',
            methods: ['recall-from-memory', 'explain-the-idea', 'space-reviews'],
        },
        remember: {
            title: 'Practice remembering without your notes',
            summary: 'Try to retrieve key points first, check what you missed, and return to them later.',
            methods: ['recall-from-memory', 'space-reviews'],
        },
        solve: {
            title: 'Move from an example to your own solution',
            summary: 'Study a worked solution, try a similar problem, and explain any step that differs.',
            methods: ['example-then-practice', 'explain-the-idea', 'space-reviews'],
        },
    };

    let selectedGoal = '';

    const selectedSubjectName = () => subjectSelect.selectedOptions[0]?.textContent.trim() || '';

    const updateTopics = () => {
        const subjectId = subjectSelect.selectedOptions[0]?.dataset.guideSubjectId || '';
        const hasSubject = subjectId !== '';
        topicSelect.disabled = !hasSubject;
        topicSelect.options[0].textContent = hasSubject ? 'No specific topic' : 'Choose a subject first';

        let selectedStillAvailable = topicSelect.value === '';
        for (const option of [...topicSelect.options].slice(1)) {
            const visible = hasSubject && option.dataset.subjectId === subjectId;
            option.hidden = !visible;
            option.disabled = !visible;
            if (visible && option.selected) selectedStillAvailable = true;
        }

        if (!selectedStillAvailable) topicSelect.value = '';
        topicPicker.refresh();
    };

    const updateApproach = () => {
        const approach = approaches[selectedGoal];
        const subjectName = subjectSelect.value ? selectedSubjectName() : '';
        const topicName = topicSelect.value ? topicSelect.selectedOptions[0]?.dataset.topicTitle || '' : '';
        const focusName = topicName || subjectName;
        const family = ['cs', 'is', 'it'].includes(subjectSelect.value) ? subjectSelect.value : '';
        let stepNumber = 0;
        let outcomeCount = 0;

        for (const button of goalButtons) {
            button.setAttribute('aria-pressed', button.dataset.guideGoal === selectedGoal ? 'true' : 'false');
        }

        for (const card of methodCards) {
            const methodIndex = approach?.methods.indexOf(card.dataset.guideMethod) ?? -1;
            const visible = methodIndex >= 0;
            card.hidden = !visible;
            card.closest('li').hidden = !visible;
            if (visible) {
                stepNumber += 1;
                card.querySelector('.guide-method-index').textContent = String(stepNumber).padStart(2, '0');
            }

            let cardOutcomeCount = 0;
            for (const outcome of card.querySelectorAll('[data-guide-outcome]')) {
                const showOutcome = visible && family !== '' && outcome.dataset.guideOutcome === family;
                outcome.hidden = !showOutcome;
                if (showOutcome) {
                    cardOutcomeCount += 1;
                    outcomeCount += 1;
                }
            }
            card.querySelector('[data-guide-outcome-label]').hidden = cardOutcomeCount === 0;
            card.querySelector('.guide-outcome-list').hidden = cardOutcomeCount === 0;
        }

        title.textContent = approach
            ? (focusName ? `${approach.title}: ${focusName}` : approach.title)
            : 'Pick a goal to begin';
        summary.textContent = approach
            ? approach.summary
            : 'Choose one outcome for this session. Change it anytime.';
        empty.hidden = Boolean(approach);
        status.textContent = approach
            ? family !== ''
                ? `Showing ${outcomeCount} verified outcome link${outcomeCount === 1 ? '' : 's'}, where available. Duno’s connections are editorial.`
                : 'General study guidance. Verified outcome links are available for supported NVSU BSCS, BSIS, and BSIT subjects.'
            : 'Select a subject or topic for tailored guidance. Verified outcomes appear for supported NVSU BSCS, BSIS, and BSIT programs.';
    };

    for (const button of goalButtons) {
        button.addEventListener('click', () => {
            selectedGoal = button.dataset.guideGoal;
            updateApproach();
        });
    }

    subjectSelect.addEventListener('change', () => {
        updateTopics();
        updateApproach();
    });
    topicSelect.addEventListener('change', updateApproach);
    updateTopics();
    updateApproach();
})();
