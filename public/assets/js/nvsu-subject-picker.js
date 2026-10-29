(() => {
    const program = document.getElementById('program-id');
    const course = document.getElementById('course-id');
    const name = document.getElementById('name');
    if (!program || !course || !name) return;

    const groups = Array.from(course.querySelectorAll('optgroup[data-program-options]'));
    const placeholder = course.querySelector(':scope > option');
    const courseGroups = new Map(groups.map(group => [group.dataset.programOptions, group.cloneNode(true)]));
    let previousCourseName = course.selectedOptions[0]?.dataset.courseName || '';

    const syncCourses = () => {
        const selectedProgram = program.value;
        const selectedCourse = course.selectedOptions[0];
        const selectedCourseId = selectedCourse?.dataset.programId === selectedProgram ? selectedCourse.value : '';
        course.replaceChildren();
        if (placeholder) course.append(placeholder.cloneNode(true));

        const activeGroup = courseGroups.get(selectedProgram);
        if (activeGroup) {
            const group = activeGroup.cloneNode(true);
            group.querySelectorAll('option').forEach(option => {
                option.selected = false;
                option.removeAttribute('selected');
            });
            if (selectedCourseId) {
                const option = Array.from(group.querySelectorAll('option')).find(item => item.value === selectedCourseId);
                if (option) option.selected = true;
            }
            course.append(group);
        }
        course.disabled = !activeGroup;
        previousCourseName = course.selectedOptions[0]?.dataset.courseName || '';
    };

    program.addEventListener('change', () => {
        if (previousCourseName && name.value.trim() === previousCourseName) name.value = '';
        syncCourses();
    });
    course.addEventListener('change', () => {
        const selectedCourse = course.selectedOptions[0];
        if (selectedCourse?.dataset.courseName) {
            name.value = selectedCourse.dataset.courseName;
            previousCourseName = selectedCourse.dataset.courseName;
        }
    });
    name.addEventListener('input', () => {
        if (course.value !== '' && name.value !== course.selectedOptions[0]?.dataset.courseName) {
            course.value = '';
        }
    });

    syncCourses();
})();
