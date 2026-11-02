<?= $this->extend('student/subjects/layout') ?>

<?= $this->section('content') ?>
<section class="academic-program-setup" aria-labelledby="academic-program-heading">
    <p class="workspace-kicker">One-time account setup</p>
    <h1 id="academic-program-heading">What program are you taking now?</h1>
    <p class="academic-program-intro">Choose an NVSU Bayombong program, or enter your school and program if they are not listed. Duno keeps your choice with your account; you can manage your own subjects either way.</p>

    <?php if ($errors !== []): ?>
        <ul class="errors" role="alert">
            <?php foreach ($errors as $message): ?><li><?= esc($message) ?></li><?php endforeach ?>
        </ul>
    <?php endif ?>

    <form method="post" action="<?= esc(site_url('student/academic-program')) ?>" class="academic-program-form">
        <?= csrf_field() ?>
        <?php $manualEntrySelected = old('program_selection_mode') === 'manual'; ?>
        <input type="hidden" name="program_selection_mode" value="<?= $manualEntrySelected ? 'manual' : 'catalog' ?>" data-program-selection-mode>
        <div class="academic-program-catalog" data-program-catalog<?= $manualEntrySelected ? ' hidden' : '' ?>>
            <label for="academic-college-select">College</label>
            <select id="academic-college-select" name="college" required data-academic-college<?= $manualEntrySelected ? ' disabled' : '' ?>>
                <option value="">Choose your college</option>
                <?php foreach ($colleges as $college): ?>
                    <option value="<?= esc($college) ?>"<?= old('college') === $college ? ' selected' : '' ?>><?= esc($college) ?></option>
                <?php endforeach ?>
            </select>

            <label for="academic-program-select">Program</label>
            <select id="academic-program-select" name="academic_program_id" required data-academic-program<?= $manualEntrySelected ? ' disabled' : '' ?>>
                <option value="">Choose a program</option>
                <?php foreach ($colleges as $college): ?>
                    <optgroup label="<?= esc($college) ?>" data-academic-program-college="<?= esc($college) ?>">
                        <?php foreach ($programs as $program): ?>
                            <?php if ($program['college'] !== $college) { continue; } ?>
                            <option value="<?= esc($program['id']) ?>"<?= old('academic_program_id') === $program['id'] ? ' selected' : '' ?>><?= esc($program['title']) ?></option>
                        <?php endforeach ?>
                    </optgroup>
                <?php endforeach ?>
            </select>
            <p class="academic-program-note">NVSU Bayombong programs are listed here. Some have a Duno subject list; others let you type your subjects.</p>
        </div>

        <button class="academic-program-manual-toggle" type="button" data-program-mode-toggle aria-expanded="<?= $manualEntrySelected ? 'true' : 'false' ?>" aria-controls="manual-program-fields">
            <?= $manualEntrySelected ? 'Choose an NVSU program instead' : 'My school or program is not listed' ?>
        </button>

        <div id="manual-program-fields" class="academic-program-manual-fields" data-manual-program-fields<?= $manualEntrySelected ? '' : ' hidden' ?>>
            <p class="academic-program-note">Enter the school and program you currently attend. Duno will not attach an NVSU curriculum to this choice.</p>
            <label for="academic-school-name">School</label>
            <input id="academic-school-name" name="school_name" type="text" maxlength="160" autocomplete="organization" value="<?= esc(old('school_name', '')) ?>"<?= $manualEntrySelected ? ' required' : ' disabled' ?> data-manual-school>
            <label for="manual-academic-program">Program</label>
            <input id="manual-academic-program" name="manual_program_title" type="text" maxlength="180" autocomplete="off" value="<?= esc(old('manual_program_title', '')) ?>"<?= $manualEntrySelected ? ' required' : ' disabled' ?> data-manual-program>
        </div>

        <label class="academic-program-confirm">
            <input type="checkbox" name="confirm_current_program" value="1" required<?= old('confirm_current_program') === '1' ? ' checked' : '' ?>>
            <span>I confirm this is my current academic program.</span>
        </label>

        <p class="academic-program-note">This choice is saved once and shown read-only in your profile. If your program changes later, contact the project administrator.</p>
        <button type="submit">Save program and continue</button>
    </form>
</section>
<script src="<?= esc(duno_asset('assets/js/academic-program-selection.js')) ?>" defer></script>
<?= $this->endSection() ?>
