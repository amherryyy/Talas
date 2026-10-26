<?= $this->extend('student/subjects/layout') ?>

<?= $this->section('content') ?>
<p><a href="<?= esc(site_url('student/subjects')) ?>">Back to subjects</a></p>
<h1>Program and curriculum catalog</h1>
<p>Browse NVSU undergraduate offerings, DepEd Senior High School subjects, and sourced CHED curriculum samples. Add selected course titles to your own subject list.</p>

<section aria-labelledby="nvsu-offerings-heading">
    <h2 id="nvsu-offerings-heading">Nueva Vizcaya State University undergraduate offerings</h2>
    <p class="notice" role="note">This directory follows the programs published on NVSU’s campus pages. It lists degree programs and majors, not the subjects in each school-approved curriculum. Check NVSU admissions for current availability. Full NVSU course plans will be added when official curriculum documents are available.</p>
    <?php foreach ($nvsuCampuses as $campus): ?>
        <section class="summary-card" aria-labelledby="<?= esc(url_title($campus['campus'])) ?>-heading">
            <h3 id="<?= esc(url_title($campus['campus'])) ?>-heading"><?= esc($campus['campus']) ?></h3>
            <p><a href="<?= esc($campus['source']) ?>" target="_blank" rel="noopener noreferrer">Check NVSU’s published program list (opens in a new tab)</a></p>
            <?php foreach ($campus['colleges'] as $college): ?>
                <h4><?= esc($college['name']) ?></h4>
                <ul class="subject-list">
                    <?php foreach ($college['programs'] as $offering): ?>
                        <li>
                            <strong><?= esc($offering['title']) ?></strong>
                            <?php if (($offering['majors'] ?? []) !== []): ?>
                                <span class="muted"> — <?= esc(implode(', ', $offering['majors'])) ?></span>
                            <?php endif ?>
                            <?php if (isset($offering['note'])): ?>
                                <p class="muted"><?= esc($offering['note']) ?></p>
                            <?php endif ?>
                        </li>
                    <?php endforeach ?>
                </ul>
            <?php endforeach ?>
        </section>
    <?php endforeach ?>
</section>

<section id="deped-shs" aria-labelledby="deped-shs-heading">
    <h2 id="deped-shs-heading">Philippine Senior High School subjects</h2>
    <section class="summary-card" aria-label="DepEd curriculum source and transition status">
        <p><strong><?= esc(\App\Services\DepedShsCatalog::VERSION) ?></strong></p>
        <p>Based on DepEd's published Strengthened SHS curriculum guides. Incoming Grade 11 learners use this curriculum in SY 2026-2027. Grade 12 learners from non-pilot schools continue the previous SHS curriculum during the transition; Grade 12 pilot schools continue the strengthened curriculum. The previous subject list is not included here.</p>
        <p>Core subjects are required. Academic and TechPro subjects are electives, and schools offer only approved subjects they can support. DepEd says only subjects with officially released and approved curriculum guides may be offered.</p>
        <p>The list follows the guides published on DepEd's page. It currently shows no subject guide under the Sports, Health, and Wellness cluster, so there are no subjects from that cluster here.</p>
        <p><a href="<?= esc(\App\Services\DepedShsCatalog::SOURCE) ?>" target="_blank" rel="noopener noreferrer">Open DepEd's Strengthened SHS curriculum guides and issuances (opens in a new tab)</a></p>
    </section>
    <?php if ($shsErrors !== []): ?>
        <ul class="errors" role="alert">
            <?php foreach ($shsErrors as $error): ?>
                <li><?= esc($error) ?></li>
            <?php endforeach ?>
        </ul>
    <?php endif ?>
    <form method="post" action="<?= esc(site_url('student/subjects/shs/import')) ?>">
        <?= csrf_field() ?>
        <div class="actions">
            <button type="button" class="secondary" data-shs-select-all>Select all listed SHS subjects</button>
            <button type="button" class="secondary" data-shs-select-none>Clear SHS selection</button>
        </div>
        <?php foreach ($shsGroups as $group): ?>
            <section class="summary-card" aria-labelledby="shs-<?= esc($group['id']) ?>-heading">
                <h3 id="shs-<?= esc($group['id']) ?>-heading"><?= esc($group['cluster']) ?></h3>
                <p class="muted"><?= esc($group['track']) ?> · <?= esc($group['grade']) ?></p>
                <ul class="subject-list">
                    <?php foreach ($group['subjects'] as $subject): ?>
                        <li>
                            <label>
                                <input type="checkbox" name="subject_ids[]" value="<?= esc($subject['id']) ?>"<?= $group['id'] === 'core' ? ' checked' : '' ?> data-shs-subject>
                                <?= esc($subject['title']) ?>
                            </label>
                        </li>
                    <?php endforeach ?>
                </ul>
            </section>
        <?php endforeach ?>
        <p class="muted">Selected items are added as editable subjects to your Duno list. Duplicate subject names are skipped. Duno does not assign a school, grade, term, track, or elective cluster to imported subjects.</p>
        <div class="actions"><button type="submit">Add selected SHS subjects to my list</button></div>
    </form>
</section>

<section aria-labelledby="ched-template-heading">
<h2 id="ched-template-heading">Start from a CHED template</h2>
<p>Choose a program to preview a CHED curriculum reference. These templates are not NVSU-specific course plans.</p>
<p class="notice" role="note">Three CHED sample curricula currently have linked sources. This is not a complete list of programs across the Philippines.</p>

<?php if ($errors !== []): ?>
    <ul class="errors" role="alert">
        <?php foreach ($errors as $error): ?>
            <li><?= esc($error) ?></li>
        <?php endforeach ?>
    </ul>
<?php endif ?>

<form method="get" action="<?= esc(site_url('student/subjects/templates')) ?>">
    <label for="curriculum-program">Degree program</label>
    <select id="curriculum-program" name="program" required>
        <option value="">Choose a program</option>
        <?php foreach ($programs as $option): ?>
            <option value="<?= esc($option['id']) ?>"<?= $option['id'] === $programId ? ' selected' : '' ?>><?= esc($option['title']) ?></option>
        <?php endforeach ?>
    </select>
    <div class="actions"><button type="submit">Preview template</button></div>
</form>

<?php if ($program === null && $programId !== ''): ?>
    <p class="notice" role="status">There is no verified curriculum template for that selection yet.</p>
<?php endif ?>

<?php if ($program !== null): ?>
    <section class="summary-card" aria-labelledby="curriculum-source-heading">
        <h2 id="curriculum-source-heading"><?= esc($program['title']) ?></h2>
        <p><strong><?= esc($program['source_label']) ?></strong> · <?= esc($program['cmo']) ?></p>
        <p>Published <?= esc($program['publication_date']) ?> · Reviewed <?= esc($program['verified_on']) ?> · Status: sample reference</p>
        <p><?= esc($program['note']) ?></p>
        <p><a href="<?= esc($program['source']) ?>" target="_blank" rel="noopener noreferrer">Read the CHED source (opens in a new tab)</a></p>
        <p><a href="<?= esc($program['cmo_source']) ?>" target="_blank" rel="noopener noreferrer">Read the program standards (<?= esc($program['cmo']) ?>)</a></p>
    </section>

    <p class="notice">CHED samples are starting references. Your university or college may use a different approved curriculum. Check with your school before relying on a course list. Imported courses are added to your subjects; existing subjects are kept.</p>

    <form method="post" action="<?= esc(site_url('student/subjects/templates/import')) ?>">
        <?= csrf_field() ?>
        <input type="hidden" name="program_id" value="<?= esc($program['id']) ?>">
        <div class="actions">
            <button type="button" class="secondary" data-template-select-all>Select all courses</button>
            <button type="button" class="secondary" data-template-select-none>Clear selection</button>
        </div>
        <?php foreach ($program['terms'] as $term): ?>
            <section class="summary-card" aria-labelledby="<?= esc($term['id']) ?>-heading">
                <h2 id="<?= esc($term['id']) ?>-heading"><?= esc($term['label']) ?></h2>
                <ul class="subject-list">
                    <?php foreach ($term['courses'] as $course): ?>
                        <?php
                        $courseStatus = match ($course['status']) {
                            'core' => 'Core course in the source',
                            'required-slot' => 'Required course slot; choose the course used by your school',
                            'elective' => 'Elective slot',
                            default => 'Listed in the sample program of study',
                        };
                        ?>
                        <li>
                            <label>
                                <input type="checkbox" name="course_ids[]" value="<?= esc($course['id']) ?>"<?= in_array($course['status'], ['core', 'sample'], true) ? ' checked' : '' ?> data-template-course>
                                <?= esc($course['title']) ?>
                                <span class="muted">· <?= esc($courseStatus) ?></span>
                            </label>
                        </li>
                    <?php endforeach ?>
                </ul>
            </section>
        <?php endforeach ?>
        <p class="muted">Duno adds each course as an editable subject. It skips courses with a name already in your subject list. The year and semester shown above are from the CHED sample; Duno does not assign a school term to your subjects.</p>
        <div class="actions">
            <button type="submit">Add selected courses to my subjects</button>
            <a class="button secondary" href="<?= esc(site_url('student/subjects')) ?>">Cancel</a>
        </div>
    </form>
<?php endif ?>

</section>

<script src="<?= esc(duno_asset('assets/js/curriculum-template.js')) ?>" defer></script>
<?= $this->endSection() ?>
