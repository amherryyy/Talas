<?= $this->extend('student/subjects/layout') ?>

<?= $this->section('content') ?>
<style {csp-style-nonce}>
    .dashboard-welcome { display: grid; grid-template-columns: minmax(0, 1fr) 7rem; align-items: center; gap: .75rem; margin: -.3rem 0 1.2rem; padding: .8rem 1rem; border: 1px solid var(--duno-line); border-radius: .9rem; background: linear-gradient(115deg, #f0edff, #fffaf0); }
    .dashboard-welcome h1 { margin: 0; }
    .dashboard-intro { max-width: 42rem; margin-bottom: 0; }
    .dashboard-mascot { display: block; width: 7rem; height: auto; }
    .suggestion-list { display: grid; grid-template-columns: repeat(auto-fit, minmax(min(100%, 20rem), 1fr)); gap: .75rem; padding: 0; list-style: none; }
    .suggestion-card { height: 100%; padding: .8rem; border: 1px solid var(--duno-line); border-radius: .7rem; background: #fff; }
    .suggestion-card h3 { margin-top: 0; }
    .suggestion-card p { margin: .4rem 0; }
    @media (max-width: 32rem) { .dashboard-welcome { grid-template-columns: minmax(0, 1fr) 5rem; gap: .35rem; padding: .7rem; } .dashboard-mascot { width: 5rem; } }
</style>

<section class="dashboard-welcome" aria-labelledby="dashboard-heading">
    <div>
        <h1 id="dashboard-heading">Your study space</h1>
        <p class="dashboard-intro">Plan study time, choose a topic, and track sessions.</p>
    </div>
    <img class="dashboard-mascot" src="<?= esc(base_url('assets/images/duno-mascot.png')) ?>" alt="Duno, your friendly study buddy" width="1228" height="1285" loading="lazy">
</section>
<p><a class="button" href="<?= esc(site_url('student/subjects')) ?>">Manage subjects</a></p>

<section aria-labelledby="study-ideas-heading">
    <h2 id="study-ideas-heading">Duno Guide</h2>
    <p class="muted">General study ideas. Choose what fits your course.</p>
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
