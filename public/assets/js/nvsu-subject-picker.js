(() => {
    const program = document.getElementById('program-id');
    const course = document.getElementById('course-id');
    const name = document.getElementById('name');
    if (!program || !course || !name) return;

    const groups = Array.from(course.querySelectorAll('optgroup[data-program-options]'));
    const syncCourses = () => {
        const selectedProgram = program.value;
        groups.forEach(group => {
            const enabled = selectedProgram !== '' && group.dataset.programOptions === selectedProgram;
            group.disabled = !enabled;
            Array.from(group.querySelectorAll('option')).forEach(option => {
                option.disabled = !enabled;
            });
        });

        const selectedCourse = course.selectedOptions[0];
        if (selectedCourse?.dataset.programId !== selectedProgram) {
            course.value = '';
        }
    };

    program.addEventListener('change', syncCourses);
    course.addEventListener('change', () => {
        const selectedCourse = course.selectedOptions[0];
        if (selectedCourse?.dataset.courseName) {
            name.value = selectedCourse.dataset.courseName;
        }
    });
    name.addEventListener('input', () => {
        if (course.value !== '' && name.value !== course.selectedOptions[0]?.dataset.courseName) {
            course.value = '';
        }
    });

    syncCourses();
})();
