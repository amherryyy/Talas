<?= $this->extend('student/subjects/layout') ?>

<?= $this->section('content') ?>
<p class="back-navigation"><a class="button secondary" href="<?= esc(site_url('student/subjects')) ?>"><span aria-hidden="true">←</span> <?= esc(lang('App.backToSubjects')) ?></a></p>
<h1>Program and curriculum catalog</h1>
<p>This page has NVSU's program directory and nine source-linked reference course lists, plus importable DepEd Senior High School subjects and CHED curriculum samples. To add subjects to Duno, jump to <a href="#nvsu-curricula">NVSU course lists</a>, <a href="#deped-shs">DepEd SHS subjects</a>, or <a href="#ched-curriculum">CHED templates</a>.</p>

<section aria-labelledby="nvsu-offerings-heading">
    <h2 id="nvsu-offerings-heading">Nueva Vizcaya State University undergraduate offerings</h2>
    <p class="notice" role="note"><strong>Program directory:</strong> This directory lists NVSU degree programs and majors. Use each campus source link to view published programs and check current availability with admissions. Selected course lists that have an NVSU source are available separately below.</p>
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
                            <?php if (isset($offering['course_source'])): ?>
                                <p><a href="<?= esc($offering['course_source']) ?>" target="_blank" rel="noopener noreferrer">Open NVSU CAS course page and curriculum links for this program (opens in a new tab)</a></p>
                            <?php endif ?>
                        </li>
                    <?php endforeach ?>
                </ul>
            <?php endforeach ?>
        </section>
    <?php endforeach ?>
</section>

<section id="nvsu-curricula" aria-labelledby="nvsu-curricula-heading">
    <h2 id="nvsu-curricula-heading">NVSU reference course lists</h2>
    <section class="summary-card" aria-label="NVSU course-list source status">
        <p>These course titles are transcribed from NVSU source documents. The Forestry, Agroforestry, and Ecotourism course-map PDF is undated; the CAS curriculum PDFs are effective S.Y. 2019–2020. None confirms a currently approved curriculum.</p>
        <p><a href="<?= esc(\App\Services\NvsuCurriculumCatalog::SOURCE) ?>" target="_blank" rel="noopener noreferrer">Read the undated Forestry, Agroforestry, and Ecotourism source document (opens in a new tab)</a></p>
        <p>Use a list as a personal starting point only. Confirm the current program curriculum with NVSU. Programs without sourced course lists remain browse-only in Duno.</p>
    </section>

    <?php if ($nvsuErrors !== []): ?>
        <ul class="errors" role="alert">
            <?php foreach ($nvsuErrors as $error): ?>
                <li><?= esc($error) ?></li>
            <?php endforeach ?>
        </ul>
    <?php endif ?>

    <form method="get" action="<?= esc(site_url('student/subjects/templates')) ?>#nvsu-curricula">
        <label for="nvsu-curriculum-program">NVSU degree program</label>
        <select id="nvsu-curriculum-program" name="nvsu" required>
            <option value="">Choose a course list</option>
            <?php foreach ($nvsuCurricula as $option): ?>
                <option value="<?= esc($option['id']) ?>"<?= $option['id'] === $nvsuProgramId ? ' selected' : '' ?>><?= esc($option['title']) ?> — <?= esc($option['campus']) ?></option>
            <?php endforeach ?>
        </select>
        <div class="actions"><button type="submit">Preview NVSU course list</button></div>
    </form>

    <?php if ($nvsuProgram === null && $nvsuProgramId !== ''): ?>
        <p class="notice" role="status">There is no sourced NVSU course list for that selection yet.</p>
    <?php endif ?>

    <?php if ($nvsuProgram !== null): ?>
        <section class="summary-card" aria-labelledby="nvsu-source-heading">
            <h3 id="nvsu-source-heading"><?= esc($nvsuProgram['title']) ?></h3>
            <p><strong><?= esc($nvsuProgram['source_label']) ?></strong> · <?= esc($nvsuProgram['campus']) ?></p>
            <p><?= esc($nvsuProgram['publication_date']) ?> · Source reviewed <?= esc($nvsuProgram['verified_on']) ?></p>
            <p><?= esc($nvsuProgram['note']) ?></p>
            <p><a href="<?= esc($nvsuProgram['source']) ?>" target="_blank" rel="noopener noreferrer">Read the NVSU curriculum source (opens in a new tab)</a></p>
        </section>
        <p>Select the course titles to add to your editable Duno subject list. Duno does not assign a school term. Existing subjects are kept and duplicate names are skipped.</p>
        <form method="post" action="<?= esc(site_url('student/subjects/nvsu/import')) ?>">
            <?= csrf_field() ?>
            <input type="hidden" name="nvsu_program_id" value="<?= esc($nvsuProgram['id']) ?>">
            <div class="actions">
                <button type="button" class="secondary" data-template-select-all>Select all courses</button>
                <button type="button" class="secondary" data-template-select-none>Clear selection</button>
            </div>
            <section class="summary-card" aria-label="<?= esc($nvsuProgram['title']) ?> course titles">
                <ul class="subject-list">
                    <?php foreach ($nvsuProgram['courses'] as $course): ?>
                        <li><label><input type="checkbox" name="course_ids[]" value="<?= esc($course['id']) ?>" data-template-course> <?= esc($course['title']) ?></label></li>
                    <?php endforeach ?>
                </ul>
            </section>
            <div class="actions"><button type="submit">Add selected NVSU courses to my list</button></div>
        </form>
    <?php endif ?>
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
    <p>Select or clear individual subjects below. Core subjects are preselected; electives are optional. Submit the list to add the selected titles to your Duno subjects.</p>
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

<section id="ched-curriculum" aria-labelledby="ched-template-heading">
<h2 id="ched-template-heading">Start from a CHED template</h2>
<p>Choose one of the listed programs and select <strong>Preview template</strong>. Then choose the courses to add to your subjects. These templates are not NVSU-specific course plans.</p>
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
        </div>
    </form>
<?php endif ?>

</section>

<script src="<?= esc(duno_asset('assets/js/curriculum-template.js')) ?>" defer></script>
<?= $this->endSection() ?>
