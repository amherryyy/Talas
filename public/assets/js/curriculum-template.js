document.querySelector('[data-template-select-all]')?.addEventListener('click', () => {
    document.querySelectorAll('[data-template-course]').forEach((course) => {
        course.checked = true;
    });
});

document.querySelector('[data-template-select-none]')?.addEventListener('click', () => {
    document.querySelectorAll('[data-template-course]').forEach((course) => {
        course.checked = false;
    });
});

document.querySelector('[data-shs-select-all]')?.addEventListener('click', () => {
    document.querySelectorAll('[data-shs-subject]').forEach((subject) => {
        subject.checked = true;
    });
});

document.querySelector('[data-shs-select-none]')?.addEventListener('click', () => {
    document.querySelectorAll('[data-shs-subject]').forEach((subject) => {
        subject.checked = false;
    });
});
