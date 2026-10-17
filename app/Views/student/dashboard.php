<?= $this->extend('student/subjects/layout') ?>

<?= $this->section('content') ?>
<style {csp-style-nonce}>
    .dashboard-intro { max-width: 42rem; }
    .suggestion-list { display: grid; grid-template-columns: repeat(auto-fit, minmax(min(100%, 20rem), 1fr)); gap: 1rem; padding: 0; list-style: none; }
    .suggestion-card { height: 100%; padding: 1rem; border: 1px solid var(--duno-line); border-radius: .7rem; background: #fff; }
    .suggestion-card h3 { margin-top: 0; }
    .suggestion-card p { margin-bottom: .75rem; }
</style>

<h1>Your study space</h1>
<p class="dashboard-intro">Organize your subjects and topics, plan study time, and record sessions. Start by choosing what you want to work on.</p>
<p><a class="button" href="<?= esc(site_url('student/subjects')) ?>">Manage subjects</a></p>

<section aria-labelledby="study-ideas-heading">
    <h2 id="study-ideas-heading">Duno Guide</h2>
    <p class="muted">These general suggestions are adapted from an education practice guide. Choose what fits your course and study task.</p>
    <ul class="suggestion-list">
        <?php foreach ($suggestions as $suggestion): ?>
            <li>
                <article class="suggestion-card" aria-labelledby="suggestion-<?= esc($suggestion['id']) ?>">
                    <h3 id="suggestion-<?= esc($suggestion['id']) ?>"><?= esc($suggestion['title']) ?></h3>
                    <p><?= esc($suggestion['body']) ?></p>
                    <p class="muted"><small><?= esc($suggestion['recommendation']) ?></small></p>
                    <a href="<?= esc($suggestionSourceUrl) ?>" rel="noopener noreferrer"><?= esc($suggestionSourceName) ?></a>
                </article>
            </li>
        <?php endforeach ?>
    </ul>
</section>
<?= $this->endSection() ?>
