<?= $this->extend('student/subjects/layout') ?>

<?= $this->section('content') ?>
<section class="academic-program-setup" aria-labelledby="academic-program-heading">
    <p class="workspace-kicker">One-time account setup</p>
    <h1 id="academic-program-heading">What program are you taking now?</h1>
    <p class="academic-program-intro">Choose your college at NVSU Bayombong, then your program. Duno keeps it with your account; your subjects and course choices stay yours to manage.</p>

    <?php if ($errors !== []): ?>
        <ul class="errors" role="alert">
            <?php foreach ($errors as $message): ?><li><?= esc($message) ?></li><?php endforeach ?>
        </ul>
    <?php endif ?>

    <form method="post" action="<?= esc(site_url('student/academic-program')) ?>" class="academic-program-form">
        <?= csrf_field() ?>
        <label for="academic-college-select">College</label>
        <select id="academic-college-select" name="college" required data-academic-college>
            <option value="">Choose your college</option>
            <?php foreach ($colleges as $college): ?>
                <option value="<?= esc($college) ?>"<?= old('college') === $college ? ' selected' : '' ?>><?= esc($college) ?></option>
            <?php endforeach ?>
        </select>

        <label for="academic-program-select">Program</label>
        <select id="academic-program-select" name="academic_program_id" required data-academic-program>
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
        <p class="academic-program-note">Only NVSU Bayombong programs are listed.</p>

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
