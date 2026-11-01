(() => {
  const collegeSelect = document.querySelector('[data-academic-college]');
  const programSelect = document.querySelector('[data-academic-program]');

  if (!(collegeSelect instanceof HTMLSelectElement) || !(programSelect instanceof HTMLSelectElement)) return;

  const groups = [...programSelect.querySelectorAll('optgroup[data-academic-program-college]')];
  const placeholder = programSelect.querySelector('option[value=""]');

  const updatePrograms = (reset = false) => {
    const college = collegeSelect.value;
    let matchingGroup = null;

    for (const group of groups) {
      const matches = college !== '' && group.dataset.academicProgramCollege === college;
      group.hidden = !matches;
      group.disabled = !matches;
      if (matches) matchingGroup = group;
    }

    programSelect.disabled = matchingGroup === null;
    if (placeholder) placeholder.textContent = matchingGroup === null ? 'Choose a college first' : 'Choose a program';

    const selectedOption = programSelect.selectedOptions[0];
    if (reset || (selectedOption?.parentElement !== matchingGroup && selectedOption?.value !== '')) {
      programSelect.value = '';
    }
  };

  collegeSelect.addEventListener('change', () => updatePrograms(true));
  updatePrograms();
})();
