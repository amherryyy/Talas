document.querySelectorAll('[data-template-select-all]').forEach((button) => {
    button.addEventListener('click', (event) => {
        event.currentTarget.closest('form')?.querySelectorAll('[data-template-course]').forEach((course) => {
            course.checked = true;
        });
    });
});

document.querySelectorAll('[data-template-select-none]').forEach((button) => {
    button.addEventListener('click', (event) => {
        event.currentTarget.closest('form')?.querySelectorAll('[data-template-course]').forEach((course) => {
            course.checked = false;
        });
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
