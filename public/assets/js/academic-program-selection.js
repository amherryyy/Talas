(() => {
  const collegeSelect = document.querySelector('[data-academic-college]');
  const programSelect = document.querySelector('[data-academic-program]');
  const modeInput = document.querySelector('[data-program-selection-mode]');
  const modeToggle = document.querySelector('[data-program-mode-toggle]');
  const catalogFields = document.querySelector('[data-program-catalog]');
  const manualFields = document.querySelector('[data-manual-program-fields]');
  const manualSchool = document.querySelector('[data-manual-school]');
  const manualProgram = document.querySelector('[data-manual-program]');

  if (!(collegeSelect instanceof HTMLSelectElement) || !(programSelect instanceof HTMLSelectElement)
    || !(modeInput instanceof HTMLInputElement) || !(modeToggle instanceof HTMLButtonElement)
    || !(catalogFields instanceof HTMLElement) || !(manualFields instanceof HTMLElement)
    || !(manualSchool instanceof HTMLInputElement) || !(manualProgram instanceof HTMLInputElement)) return;

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

  const setMode = (mode, moveFocus = true) => {
    const manual = mode === 'manual';
    modeInput.value = manual ? 'manual' : 'catalog';
    modeToggle.setAttribute('aria-expanded', String(manual));
    modeToggle.textContent = manual ? 'Choose an NVSU program instead' : 'My school or program is not listed';
    catalogFields.hidden = manual;
    manualFields.hidden = !manual;
    collegeSelect.disabled = manual;
    programSelect.disabled = manual;
    manualSchool.disabled = !manual;
    manualProgram.disabled = !manual;
    manualSchool.required = manual;
    manualProgram.required = manual;
    if (!manual) {
      updatePrograms();
      if (moveFocus) collegeSelect.focus();
    } else if (moveFocus) {
      manualSchool.focus();
    }
  };

  modeToggle.addEventListener('click', () => {
    setMode(modeInput.value === 'manual' ? 'catalog' : 'manual');
  });

  setMode(modeInput.value, false);
})();
