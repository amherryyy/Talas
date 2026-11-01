<?php $footerGroups = $footerGroups ?? []; ?>
<footer class="icon-credit app-footer">
    <div class="app-footer-main">
        <a class="app-footer-brand-lockup" href="<?= esc(site_url('/')) ?>" aria-label="duno. — Your Little Study Buddy. Home">
            <span class="app-footer-brand">duno.</span>
            <span class="app-footer-tagline">Your Little Study Buddy.</span>
        </a>
        <nav class="app-footer-columns" aria-label="<?= esc(lang('App.footerNavigation')) ?>">
            <?php foreach ($footerGroups as $group): ?>
                <details class="app-footer-column" open data-footer-disclosure>
                    <summary><h2 class="app-footer-column-title" id="<?= esc($group['id']) ?>"><?= esc($group['title']) ?></h2></summary>
                    <ul>
                        <?php foreach ($group['links'] as $link): ?>
                            <li><a class="app-footer-link" href="<?= esc($link['href']) ?>"><span><?= esc($link['label']) ?></span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><path d="M7 17 17 7M8 7h9v9" /></svg></a></li>
                        <?php endforeach ?>
                    </ul>
                </details>
            <?php endforeach ?>
        </nav>
    </div>
    <div class="app-footer-bottom">
        <nav class="app-footer-legal" aria-label="<?= esc(lang('App.footerLegal')) ?>">
            <a href="<?= esc(site_url('about')) ?>"><?= esc(lang('App.about')) ?></a>
            <a href="<?= esc(site_url('terms')) ?>"><?= esc(lang('App.terms')) ?></a>
            <a href="<?= esc(site_url('privacy')) ?>"><?= esc(lang('App.privacy')) ?></a>
            <a href="<?= esc(site_url('cookies')) ?>"><?= esc(lang('App.cookies')) ?></a>
        </nav>
        <p class="app-footer-copyright"><?= esc(lang('App.footerCopyright')) ?></p>
        <small>Icons: <a href="https://solar-icons.vercel.app/" rel="noopener noreferrer">Solar Icons</a> icon set by 480 Design, licensed under <a href="https://creativecommons.org/licenses/by/4.0/" rel="noopener noreferrer">CC BY 4.0</a>.</small>
    </div>
</footer>
<script src="<?= esc(duno_asset('assets/js/footer-disclosures.js')) ?>" defer></script>
