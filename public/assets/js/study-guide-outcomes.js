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
            : 'Choose a goal to get started';
        summary.textContent = approach
            ? approach.summary
            : 'Pick one small outcome for this session. You can change your choice any time.';
        empty.hidden = Boolean(approach);
        status.textContent = approach
            ? family !== ''
                ? `${outcomeCount} verified program outcome connection${outcomeCount === 1 ? '' : 's'} shown where available. Duno’s method-to-outcome connection is an editorial study aid.`
                : 'These curated methods are general study guidance. Verified outcome links are available for supported NVSU BSCS, BSIS, and BSIT subjects.'
            : 'Choose a subject or topic to see a tailored heading. Verified program outcomes appear for supported NVSU BSCS, BSIS, and BSIT programs. Suggestions remain general study guidance.';
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
