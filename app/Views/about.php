<!doctype html>
<?php $htmlLocale = match (service('request')->getLocale()) { 'fil' => 'fil-PH', 'taglish' => 'en-PH', default => 'en' }; ?>
<html lang="<?= esc($htmlLocale) ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#f7f6fc">
    <meta name="description" content="Learn how to use Duno Focus, Plan, Guide, Timer, and Aralytics, and find answers to common questions.">
    <?= view('partials/pwa_head') ?>
    <title>Duno: A Personalized Study Planning, Tracking, and Academic Analytics Application</title>
    <link rel="stylesheet" href="<?= esc(duno_asset('assets/css/student-navigation.css')) ?>">
    <link rel="stylesheet" href="<?= esc(duno_asset('assets/css/duno-ui.css')) ?>">
</head>
<body class="duno-landing duno-about-page">
<a class="skip-link" href="#main-content"><?= esc(lang('App.skipMain')) ?></a>
<?= view('partials/pwa_network_status') ?>
<div class="about-shell">
    <header class="about-header">
        <a class="about-brand" href="<?= esc(site_url('/')) ?>" aria-label="duno. — Your Little Study Buddy. Home">
            <span>duno.</span><small>Your Little Study Buddy.</small>
        </a>
        <a class="about-header-link" href="<?= esc($workspaceUrl) ?>"><?= esc($workspaceAction) ?> <span aria-hidden="true">↗</span></a>
    </header>
    <nav class="info-page-nav" aria-label="Duno information pages">
        <a href="<?= esc(site_url('about')) ?>" aria-current="page"><?= esc(lang('App.about')) ?></a>
        <a href="<?= esc(site_url('terms')) ?>"><?= esc(lang('App.terms')) ?></a>
        <a href="<?= esc(site_url('privacy')) ?>"><?= esc(lang('App.privacy')) ?></a>
        <a href="<?= esc(site_url('cookies')) ?>"><?= esc(lang('App.cookies')) ?></a>
    </nav>

    <main id="main-content">
        <section class="about-hero" aria-labelledby="about-title">
            <div class="about-hero-copy">
                <p class="about-eyebrow">A little context, then your next step</p>
                <h1 id="about-title">A study space that helps you decide what to do next.</h1>
                <p>Duno brings your subjects, unfinished topics, study plans, and saved sessions into one place. You choose the work; Duno helps you organize it.</p>
                <p class="about-project-title"><span>PROJECT TITLE</span>Duno: A Personalized Study Planning, Tracking, and Academic Analytics Application</p>
                <a class="about-primary-link" href="#how-to-use">See how it works <span aria-hidden="true">↓</span></a>
            </div>
            <figure class="about-hero-art">
                <img src="<?= esc(duno_asset('assets/images/duno-mascot-curious.webp')) ?>" alt="Duno, the study buddy mascot, thinking about the next step." width="280" height="280">
                <figcaption><span>FOCUS · PLAN · STUDY · REFLECT</span></figcaption>
            </figure>
            <nav class="about-section-nav" aria-label="About page sections">
                <a href="#how-to-use">How to use Duno</a>
                <a href="#why-duno">Why it works this way</a>
                <a href="#faq">FAQs</a>
                <a href="#people-and-contact">People and contact</a>
            </nav>
        </section>

        <section class="about-section about-how" id="how-to-use" aria-labelledby="how-heading">
            <header class="about-section-heading">
                <p class="about-eyebrow">A simple route through the app</p>
                <h2 id="how-heading">From your subject list to a saved study session.</h2>
                <p>Start with the work you already have. Each tool supports one part of the routine.</p>
            </header>
            <ol class="about-steps">
                <li class="about-step">
                    <span class="about-step-number">01</span>
                    <div><h3>Set up your subjects</h3><p>On your first student sign-in, choose your current NVSU Bayombong program. If Duno has a subject list for it, add subjects from that list. If it has no list yet, type in your own subject names.</p><a href="<?= esc(site_url('student/subjects')) ?>">Go to Subjects <span aria-hidden="true">↗</span></a></div>
                </li>
                <li class="about-step">
                    <span class="about-step-number">02</span>
                    <div><h3>Add the topics you need to study</h3><p>Give each topic a title and, when useful, a due date, importance, difficulty, and progress status. These details help Focus explain its order.</p><a href="<?= esc(site_url('student/topics')) ?>">Go to Topics <span aria-hidden="true">↗</span></a></div>
                </li>
                <li class="about-step">
                    <span class="about-step-number">03</span>
                    <div><h3>Choose a next topic in Duno Focus</h3><p>Focus orders unfinished topics using the details you entered. Open a topic’s explanation to see the factors behind its position.</p><a href="<?= esc(site_url('student/priority')) ?>">Open Duno Focus <span aria-hidden="true">↗</span></a></div>
                </li>
                <li class="about-step">
                    <span class="about-step-number">04</span>
                    <div><h3>Fit study blocks into your day</h3><p>Generate a Duno Plan from the time you have, then edit, move, or schedule its blocks to suit your day.</p><a href="<?= esc(site_url('student/plan')) ?>">Open Duno Plan <span aria-hidden="true">↗</span></a></div>
                </li>
                <li class="about-step">
                    <span class="about-step-number">05</span>
                    <div><h3>Study, review, and choose whether to save</h3><p>Use Duno Timer for a session. Review the elapsed time before deciding to save it. Aralytics then summarizes the sessions you saved.</p><div class="about-step-links"><a href="<?= esc(site_url('student/sessions')) ?>">Open Duno Timer <span aria-hidden="true">↗</span></a><a href="<?= esc(site_url('student/aralytics')) ?>">Open Aralytics <span aria-hidden="true">↗</span></a></div></div>
                </li>
            </ol>
            <aside class="about-guide-note"><span class="about-guide-mark" aria-hidden="true">G</span><p><strong>Need a study approach?</strong> In Duno Guide, select a subject or topic, choose a goal, and browse a short sequence of curated study methods.</p><a href="<?= esc(site_url('student#guide-heading')) ?>">Find Duno Guide <span aria-hidden="true">↗</span></a></aside>
        </section>

        <section class="about-section about-rationale" id="why-duno" aria-labelledby="why-heading">
            <div class="about-rationale-intro">
                <p class="about-eyebrow">The thinking behind Duno</p>
                <h2 id="why-heading">Clear reasons. Student choice.</h2>
                <p>Duno is a focused course project for making day-to-day study organization easier to see and adjust.</p>
            </div>
            <dl class="about-principles">
                <div><dt>Recommendations you can inspect</dt><dd>Focus uses the topic details you set and shows why an unfinished topic appears where it does. Guide methods are curated and selected by you; Duno does not generate personal advice.</dd></div>
                <div><dt>Plans you can change</dt><dd>A plan starts from your available time and unfinished topics. You can shape the blocks around your actual day.</dd></div>
                <div><dt>Progress based on saved sessions</dt><dd>Aralytics describes sessions you chose to save. Its summaries are not predictions, grades, or attendance records.</dd></div>
                <div><dt>Private by design</dt><dd>Subjects, topics, plans, and sessions belong to your account. Optional workload and energy check-ins stay in your own view and are excluded from admin summaries.</dd></div>
            </dl>
            <p class="about-curriculum-note">Duno’s current program setup focuses on NVSU Bayombong. Some course lists are historical reference curricula; confirm your current courses with NVSU.</p>
        </section>

        <section class="about-section about-faq" id="faq" aria-labelledby="faq-heading">
            <header class="about-section-heading">
                <p class="about-eyebrow">Quick answers</p>
                <h2 id="faq-heading">Frequently asked questions</h2>
            </header>
            <div class="about-faq-list">
                <details><summary>Why can’t I type a subject name?</summary><p>When Duno has a subject list for your selected program, add a subject from that list. Typing is available for programs without a sourced list.</p></details>
                <details><summary>Can I change my academic program later?</summary><p>The program choice is a one-time account setup and appears read-only in Profile. If it changes, contact the project administrator through the approved project channel.</p></details>
                <details><summary>Does Duno choose what I study?</summary><p>Focus ranks unfinished topics using your saved details and shows the reasons. You decide which topic to open. In Guide, you choose a subject or topic and a study goal before viewing methods.</p></details>
                <details><summary>Does the timer save a session automatically?</summary><p>No. Review the elapsed time and save only when you choose.</p></details>
                <details><summary>Can I use my study pages offline?</summary><p>No. Account pages and saved study data need an internet connection. The offline page and selected public files may still open, but Duno does not save offline changes.</p></details>
                <details><summary>Who can see my optional check-ins?</summary><p>They are shown in your account only. The current admin dashboard does not display individual check-in values or patterns.</p></details>
            </div>
        </section>

        <section class="about-section about-people" id="people-and-contact" aria-labelledby="people-heading">
            <div class="about-section-heading">
                <p class="about-eyebrow">CONTACT &amp; COMMUNITY</p>
                <h2 id="people-heading">Reach out, report, connect.</h2>
                <p>Choose the right channel for a question, a problem, or a developer profile.</p>
            </div>
            <div class="about-contact-grid">
                <section class="about-contact-item about-contact-email">
                    <p class="about-contact-kind">EMAIL &middot; QUESTIONS</p>
                    <h3>Contact Duno</h3>
                    <p>Questions or feedback? Send a note.</p>
                    <a class="about-contact-link" href="mailto:mhracads@gmail.com" aria-label="Email Duno at mhracads@gmail.com"><span>duno@gmail.com</span><span aria-hidden="true">&#8599;</span></a>
                </section>
                <section class="about-contact-item about-contact-report">
                    <p class="about-contact-kind">GITHUB &middot; SUPPORT</p>
                    <h3>Report a problem</h3>
                    <p>Open an issue for bugs, access problems, or suggestions. GitHub sign-in is required; leave out passwords and private study records.</p>
                    <a class="about-contact-link" href="https://github.com/amherryyy/Talas/issues/new" target="_blank" rel="noopener noreferrer"><span>Open GitHub Issues</span><span aria-hidden="true">&#8599;</span></a>
                </section>
                <section class="about-contact-item about-contact-social">
                    <div class="about-social-copy">
                        <p class="about-contact-kind">PERSONAL PROFILES</p>
                        <h3>Connect with the developer</h3>
                        <p>Follow along on LinkedIn or Facebook.</p>
                    </div>
                    <nav class="about-social-links" aria-label="Developer social profiles">
                        <a class="about-contact-link" href="https://www.linkedin.com/in/amher-bolante-637443387" target="_blank" rel="noopener noreferrer"><span class="about-social-mark" aria-hidden="true">in</span><span>LinkedIn</span><span aria-hidden="true">&#8599;</span></a>
                        <a class="about-contact-link" href="https://www.facebook.com/share/1CcC5xkLrn/" target="_blank" rel="noopener noreferrer"><span class="about-social-mark" aria-hidden="true">f</span><span>Facebook</span><span aria-hidden="true">&#8599;</span></a>
                    </nav>
                </section>
            </div>
        </section>
    </main>
</div>
<?= view('partials/app_footer', ['footerGroups' => $footerGroups]) ?>
<script src="<?= esc(duno_asset('assets/js/pwa.js')) ?>" defer></script>
</body>
</html>
