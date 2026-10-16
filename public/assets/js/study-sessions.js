(() => {
    const subjectSelect = document.querySelector('[data-subject-select]');
    const topicSelect = document.querySelector('[data-topic-select]');
    if (subjectSelect && topicSelect) {
        const filterTopics = () => {
            const subjectId = subjectSelect.value;
            for (const option of topicSelect.options) {
                if (!option.value) {
                    option.hidden = false;
                    continue;
                }

                const available = subjectId !== '' && option.dataset.subjectId === subjectId;
                option.hidden = !available;
                option.disabled = !available;
                if (!available && option.selected) {
                    topicSelect.value = '';
                }
            }
        };
        subjectSelect.addEventListener('change', filterTopics);
        filterTopics();
    }

    const timer = document.querySelector('[data-study-timer]');
    if (!timer) {
        return;
    }

    const form = timer.querySelector('[data-session-form]');
    const elapsedOutput = timer.querySelector('[data-elapsed]');
    const status = timer.querySelector('[data-timer-status]');
    const review = timer.querySelector('[data-review]');
    const durationInput = timer.querySelector('#duration-minutes');
    const startButton = timer.querySelector('[data-start]');
    const pauseButton = timer.querySelector('[data-pause]');
    const resumeButton = timer.querySelector('[data-resume]');
    const stopButton = timer.querySelector('[data-stop]');
    const discardButtons = timer.querySelectorAll('[data-discard]');
    const saveButton = timer.querySelector('[data-save]');
    const subject = timer.querySelector('[data-subject-select]');
    const topic = timer.querySelector('[data-topic-select]');
    const maxDurationMs = 480 * 60 * 1000;

    let accumulatedMs = 0;
    let startedAt = null;
    let intervalId = null;
    let state = timer.dataset.reviewing === 'true' ? 'stopped' : 'idle';
    let unsaved = state === 'stopped';

    if (state === 'stopped') {
        review.hidden = false;
        saveButton.disabled = false;
        for (const button of discardButtons) button.disabled = false;
        status.textContent = 'Review the duration and save the session, or discard this result.';
    }

    const elapsedMilliseconds = () => Math.min(maxDurationMs, accumulatedMs + (startedAt === null ? 0 : performance.now() - startedAt));

    const formatTime = (milliseconds) => {
        const seconds = Math.floor(milliseconds / 1000);
        const hours = Math.floor(seconds / 3600);
        const minutes = Math.floor((seconds % 3600) / 60);
        const remainder = seconds % 60;
        return [hours, minutes, remainder].map((value) => String(value).padStart(2, '0')).join(':');
    };

    const renderTime = () => {
        elapsedOutput.textContent = formatTime(elapsedMilliseconds());
        if (state === 'running' && elapsedMilliseconds() >= maxDurationMs) {
            stopTimer(true);
        }
    };

    const lockReferences = (locked) => {
        subject.disabled = locked;
        topic.disabled = locked;
    };

    const startTimer = () => {
        startedAt = performance.now();
        state = 'running';
        unsaved = true;
        lockReferences(true);
        startButton.disabled = true;
        pauseButton.disabled = false;
        resumeButton.disabled = true;
        stopButton.disabled = false;
        for (const button of discardButtons) button.disabled = false;
        status.textContent = 'Timer running. Stopping it will show a review; it will not save automatically.';
        intervalId = window.setInterval(renderTime, 250);
    };

    const pauseTimer = () => {
        accumulatedMs = elapsedMilliseconds();
        startedAt = null;
        state = 'paused';
        window.clearInterval(intervalId);
        intervalId = null;
        renderTime();
        pauseButton.disabled = true;
        resumeButton.disabled = false;
        stopButton.disabled = false;
        status.textContent = 'Timer paused. Resume or stop and review the session.';
    };

    function stopTimer(reachedMaximum = false) {
        if (state === 'running') {
            accumulatedMs = elapsedMilliseconds();
        }
        startedAt = null;
        state = 'stopped';
        unsaved = true;
        window.clearInterval(intervalId);
        intervalId = null;
        lockReferences(false);
        renderTime();
        const completedMinutes = Math.floor(accumulatedMs / 60000);
        durationInput.value = String(Math.max(1, Math.min(480, completedMinutes)));
        review.hidden = false;
        startButton.disabled = true;
        pauseButton.disabled = true;
        resumeButton.disabled = true;
        stopButton.disabled = true;
        saveButton.disabled = false;
        for (const button of discardButtons) button.disabled = false;
        status.textContent = reachedMaximum
            ? 'The timer reached the 480-minute limit. Review the duration before saving.'
            : 'Timer stopped. Review the duration and explicitly save or discard this session.';
        durationInput.focus();
    }

    const discardTimer = () => {
        window.clearInterval(intervalId);
        intervalId = null;
        accumulatedMs = 0;
        startedAt = null;
        state = 'idle';
        unsaved = false;
        review.hidden = true;
        durationInput.value = '';
        lockReferences(false);
        subject.value = '';
        topic.value = '';
        subject.disabled = false;
        topic.disabled = false;
        startButton.disabled = false;
        pauseButton.disabled = true;
        resumeButton.disabled = true;
        stopButton.disabled = true;
        saveButton.disabled = true;
        for (const button of discardButtons) button.disabled = true;
        renderTime();
        status.textContent = 'Timer discarded. No session was saved.';
        subject.focus();
    };

    startButton.addEventListener('click', () => {
        if (!subject.value) {
            subject.reportValidity();
            subject.focus();
            return;
        }
        if (state === 'paused') {
            startTimer();
            status.textContent = 'Timer resumed.';
            resumeButton.disabled = true;
        } else if (state === 'idle') {
            startTimer();
            status.textContent = 'Timer running. Stopping it will show a review; it will not save automatically.';
        }
    });
    pauseButton.addEventListener('click', pauseTimer);
    resumeButton.addEventListener('click', startTimer);
    stopButton.addEventListener('click', () => stopTimer());
    for (const button of discardButtons) button.addEventListener('click', discardTimer);

    form.addEventListener('submit', (event) => {
        if (state !== 'stopped' || !durationInput.value || !durationInput.checkValidity()) {
            event.preventDefault();
            status.textContent = 'Stop the timer and enter a valid duration before saving.';
            if (!durationInput.checkValidity()) durationInput.reportValidity();
            return;
        }
        unsaved = false;
    });

    window.addEventListener('beforeunload', (event) => {
        if (unsaved) {
            event.preventDefault();
            event.returnValue = '';
        }
    });
})();
