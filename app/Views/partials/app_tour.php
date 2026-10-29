<div class="app-tour-shade" data-tour-shade aria-hidden="true" hidden>
    <span data-shade-top></span>
    <span data-shade-left></span>
    <span data-shade-right></span>
    <span data-shade-bottom></span>
</div>
<aside
    class="app-tour"
    data-app-tour
    data-tour-user="<?= esc($appTourUserId) ?>"
    data-auto-start="<?= $appTourAutoStart ? 'true' : 'false' ?>"
    aria-labelledby="app-tour-title"
    aria-describedby="app-tour-copy"
    aria-modal="true"
    role="dialog"
    hidden
>
    <div class="app-tour-topline">
        <p class="workspace-kicker" data-tour-progress aria-live="polite"><?= esc(lang('App.tourProgress')) ?></p>
        <button class="app-tour-skip" type="button" data-tour-skip><?= esc(lang('App.skipTour')) ?></button>
    </div>
    <section class="app-tour-carousel" data-tour-carousel aria-labelledby="welcome-carousel-title" aria-describedby="welcome-carousel-copy" hidden>
        <div class="welcome-carousel-layout">
            <div class="welcome-carousel-art" aria-hidden="true">
                <div class="welcome-art-track" data-welcome-track>
                    <div class="welcome-art-slide welcome-art-first">
                        <span class="welcome-art-glow"></span>
                        <span class="welcome-art-orbit welcome-art-orbit-one"></span>
                        <span class="welcome-art-orbit welcome-art-orbit-two"></span>
                        <img class="welcome-art-mascot" src="<?= esc(duno_asset('assets/images/duno-mascot-welcome.webp')) ?>" alt="" width="512" height="536">
                        <span class="welcome-art-float welcome-art-note"><i aria-hidden="true">✦</i><span>one step at a time</span></span>
                        <span class="welcome-art-float welcome-art-check"><i aria-hidden="true">✓</i><span>ready when you are</span></span>
                    </div>
                    <div class="welcome-art-slide">
                        <svg viewBox="0 0 540 420" focusable="false">
                            <ellipse cx="270" cy="365" rx="172" ry="18" fill="#dedaf2" opacity=".48" />
                            <g class="welcome-plan-board">
                                <rect x="92" y="46" width="356" height="310" rx="28" fill="#fff" />
                                <rect x="92" y="46" width="356" height="82" rx="28" fill="#f0edff" />
                                <path d="M92 100h356v28H92z" fill="#f0edff" />
                                <rect x="128" y="76" width="44" height="38" rx="12" fill="#7465d8" />
                                <path d="M140 70v12m20-12v12m-20 12h20m-20 8h15" fill="none" stroke="#fff" stroke-width="3" stroke-linecap="round" />
                                <rect x="190" y="73" width="132" height="10" rx="5" fill="#5144ae" opacity=".86" />
                                <rect x="190" y="92" width="86" height="7" rx="3.5" fill="#afa7e7" />
                                <rect x="126" y="151" width="292" height="52" rx="15" fill="#faf9ff" stroke="#ece9f7" />
                                <circle cx="153" cy="177" r="10" fill="#7465d8" />
                                <path d="m148 177 4 4 7-8" fill="none" stroke="#fff" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round" />
                                <rect x="176" y="166" width="138" height="8" rx="4" fill="#7465d8" opacity=".83" />
                                <rect x="176" y="181" width="93" height="6" rx="3" fill="#d9d5e9" />
                                <rect x="126" y="217" width="292" height="52" rx="15" fill="#fff" stroke="#ece9f7" />
                                <circle cx="153" cy="243" r="10" fill="#fff" stroke="#cbc6dd" stroke-width="2" />
                                <rect x="176" y="232" width="156" height="8" rx="4" fill="#555266" opacity=".65" />
                                <rect x="176" y="247" width="112" height="6" rx="3" fill="#e2dfeb" />
                                <rect x="126" y="283" width="292" height="52" rx="15" fill="#fff" stroke="#ece9f7" />
                                <circle cx="153" cy="309" r="10" fill="#fff" stroke="#cbc6dd" stroke-width="2" />
                                <rect x="176" y="298" width="121" height="8" rx="4" fill="#555266" opacity=".65" />
                                <rect x="176" y="313" width="78" height="6" rx="3" fill="#e2dfeb" />
                            </g>
                            <g class="welcome-art-plan-spark">
                                <circle cx="430" cy="108" r="30" fill="#eaf8ef" />
                                <path d="m416 108 10 10 19-21" fill="none" stroke="#3c8b61" stroke-width="5" stroke-linecap="round" stroke-linejoin="round" />
                            </g>
                            <path d="M103 294c-28-13-37-33-28-51" fill="none" stroke="#ffda92" stroke-width="5" stroke-linecap="round" />
                            <path d="m68 245 7-11 9 9" fill="none" stroke="#ffda92" stroke-width="5" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </div>
                    <div class="welcome-art-slide">
                        <svg viewBox="0 0 540 420" focusable="false">
                            <ellipse cx="270" cy="364" rx="180" ry="18" fill="#dedaf2" opacity=".48" />
                            <g class="welcome-session-card">
                                <rect x="62" y="68" width="250" height="280" rx="28" fill="#fff" />
                                <rect x="90" y="98" width="128" height="9" rx="4.5" fill="#77718e" opacity=".55" />
                                <circle cx="187" cy="221" r="82" fill="#f3f0ff" />
                                <circle cx="187" cy="221" r="66" fill="none" stroke="#e3dffa" stroke-width="12" />
                                <path d="M187 155a66 66 0 1 1-59 95" fill="none" stroke="#7465d8" stroke-width="12" stroke-linecap="round" />
                                <path d="M187 180v43l29 18" fill="none" stroke="#5144ae" stroke-width="7" stroke-linecap="round" stroke-linejoin="round" />
                                <circle cx="187" cy="221" r="7" fill="#5144ae" />
                                <rect x="131" y="318" width="112" height="10" rx="5" fill="#d8f0e1" />
                                <rect x="131" y="318" width="72" height="10" rx="5" fill="#4c9b6c" />
                            </g>
                            <g class="welcome-session-chart">
                                <rect x="284" y="155" width="190" height="178" rx="24" fill="#fff" stroke="#eeeaf7" />
                                <rect x="310" y="180" width="80" height="8" rx="4" fill="#7465d8" opacity=".78" />
                                <path d="M309 297h140" stroke="#e5e1f0" stroke-width="2" />
                                <rect x="315" y="250" width="19" height="47" rx="7" fill="#c8c1f2" />
                                <rect x="345" y="228" width="19" height="69" rx="7" fill="#a79ce9" />
                                <rect x="375" y="238" width="19" height="59" rx="7" fill="#c8c1f2" />
                                <rect x="405" y="204" width="19" height="93" rx="7" fill="#7465d8" />
                                <rect x="435" y="220" width="9" height="77" rx="4.5" fill="#b9e8d1" />
                                <circle cx="427" cy="119" r="34" fill="#ffda92" />
                                <path d="m412 119 10 10 20-22" fill="none" stroke="#6d5830" stroke-width="5" stroke-linecap="round" stroke-linejoin="round" />
                            </g>
                            <path class="welcome-session-spark" d="M95 70c3 13 8 18 21 21-13 3-18 8-21 21-3-13-8-18-21-21 13-3 18-8 21-21Z" fill="#ffda92" />
                        </svg>
                    </div>
                </div>
            </div>
            <div class="welcome-carousel-copy">
                <div class="welcome-carousel-brand"><span class="welcome-brand-mark" aria-hidden="true">✦</span><span><strong>duno.</strong><small>Your Little Study Buddy.</small></span></div>
                <p class="workspace-kicker" data-welcome-kicker>WELCOME TO DUNO</p>
                <h2 id="welcome-carousel-title" data-welcome-title tabindex="-1">Make room for your next study step.</h2>
                <p id="welcome-carousel-copy" class="welcome-carousel-lead" data-welcome-copy>Duno keeps your subjects, plans, and study time together so it’s easier to know where to begin.</p>
                <div class="welcome-carousel-indicators">
                    <div class="welcome-carousel-dots" role="group" aria-label="Choose a welcome slide" data-welcome-dots>
                        <button type="button" aria-label="Show welcome slide 1" aria-current="step" data-welcome-slide="0"></button>
                        <button type="button" aria-label="Show welcome slide 2" data-welcome-slide="1"></button>
                        <button type="button" aria-label="Show welcome slide 3" data-welcome-slide="2"></button>
                        <span class="welcome-carousel-dot-indicator" data-welcome-indicator aria-hidden="true"></span>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <div class="app-tour-intro">
        <img class="app-tour-mascot" src="<?= esc(duno_asset('assets/images/duno-mascot-welcome.webp')) ?>" alt="" width="512" height="536" aria-hidden="true">
        <div>
            <h2 id="app-tour-title" data-tour-title tabindex="-1"><?= esc(lang('App.welcomeDuno')) ?></h2>
            <p id="app-tour-copy" class="app-tour-copy" data-tour-copy></p>
        </div>
    </div>
    <div class="app-tour-actions">
        <button class="button secondary" type="button" data-tour-back disabled><?= esc(lang('App.back')) ?></button>
        <button class="button" type="button" data-tour-next><?= esc(lang('App.next')) ?></button>
    </div>
</aside>
