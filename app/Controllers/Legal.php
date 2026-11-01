<?php

namespace App\Controllers;

class Legal extends BaseController
{
    public function terms(): string
    {
        return $this->showPage('terms', 'Terms and conditions', 'How Duno is intended to be used and what the current study tools provide.', [
            ['title' => 'Using Duno', 'paragraphs' => [
                'Duno is a browser-based study organizer for subjects, topics, study plans, saved study sessions, and descriptive Aralytics. Its rule-based priorities and general study suggestions are organizational aids. They do not assess health, predict grades, certify attendance, or guarantee academic results.',
                'Account pages and saved study data need an internet connection. The offline page and selected public design files do not make private study pages or saved data available offline.',
            ]],
            ['title' => 'Accounts and responsible use', 'paragraphs' => [
                'Use an email address you are authorized to access, keep your sign-in details private, and use only your own account. Do not try to access another person’s records, disrupt the service, or submit information you are not allowed to share.',
                'Student accounts are created through public registration. Administrative accounts are provisioned separately. Keep academic details accurate and use the available account controls to manage your records.',
            ]],
            ['title' => 'Your study records', 'paragraphs' => [
                'Subjects, topics, plans, sessions, and optional check-ins are associated with the signed-in account. Optional workload and energy check-ins are private to the student account in the current app and are excluded from admin summaries and printed study reports.',
                'Some records can be edited or deleted in the app. A subject or topic deletion may be blocked when saved study history depends on it. You can permanently delete your account from Profile after confirming your current password and typing DELETE. This removes the account and its saved study data from the application. A complete data export is not currently available.',
            ]],
            ['title' => 'Availability and changes', 'paragraphs' => [
                'Access can be interrupted by network, hosting, or maintenance problems. Keep any schedule you rely on somewhere appropriate. The project has not established a published service availability, backup recovery, or account-closure commitment.',
                'These preliminary terms need review by the project owner and the relevant school or institution before they are treated as final. Applicable rights are not limited by this notice.',
            ]],
        ]);
    }

    public function privacy(): string
    {
        return $this->showPage('privacy', 'Privacy notice', 'A plain-language summary of information used by the current Duno app.', [
            ['title' => 'Information Duno uses', 'paragraphs' => [
                'Duno uses account details such as username and email address, plus the authentication information needed to protect sign-in. Students may save an academic program, subjects, topics, plans, study sessions, progression activity, and optional workload and energy ratings.',
                'The app also uses a language preference cookie and small browser-storage values for tour completion and whether the optional daily check-in prompt was dismissed. Those browser values do not contain saved subjects, topics, plans, sessions, or check-in ratings.',
            ]],
            ['title' => 'Why it is used', 'paragraphs' => [
                'Account and study information supports sign-in, student-owned study tools, saved records, and each student’s own summaries. Limited operational information may also be needed to keep accounts and the service working. The project still needs to document and approve the responsible organization, purposes, and lawful basis before this notice is final.',
            ]],
            ['title' => 'Who can see it', 'paragraphs' => [
                'Students can access records associated with their own account. The current admin dashboard shows system-wide aggregates; it does not show student names, individual study records, program breakdowns, or private check-ins. Small groups in trend summaries are withheld.',
                'The app is intended to run on a hosting provider, and account verification may involve an email provider. The exact production providers, their locations, logs, backups, and data handling have not been verified for this notice.',
            ]],
            ['title' => 'Storage and retention', 'paragraphs' => [
                'Study records remain in the app until supported delete actions remove them; optional check-ins are kept until the student deletes them or deletes their account. Account deletion removes the account and its associated study data from the application. The project has not confirmed hosting backup expiry or a general retention schedule, so no deletion deadline for backups is promised here. A full data export is not currently available.',
                'The app is configured for session-based sign-in and a two-hour session expiration. Actual production hosting behavior and provider log retention have not been independently verified.',
            ]],
            ['title' => 'Your privacy choices and requests', 'paragraphs' => [
                'You can manage supported study records in the app and clear browser cookies or site storage through your browser. The project owner still needs to publish a monitored privacy contact and a process for access, correction, objection, deletion or blocking, portability, and other requests that may apply.',
                'For an overview of rights under Philippine privacy law, see the National Privacy Commission’s <a href="https://privacy.gov.ph/data-subject-rights/" rel="noopener noreferrer">data subject rights</a> page and its <a href="https://privacy.gov.ph/the-right-to-be-informed/" rel="noopener noreferrer">right to be informed</a> page. These links do not replace a Duno privacy contact or request process.',
            ]],
        ]);
    }

    public function cookies(): string
    {
        return $this->showPage('cookies', 'Cookies and browser storage', 'What Duno stores in your browser and what those values do.', [
            ['title' => 'Cookies used by Duno', 'paragraphs' => [
                'Duno uses a first-party session cookie for signed-in sessions and session-based request protection. These support sign-in and secure form submissions. The app does not provide a “remember me” sign-in option.',
                'A first-party cookie named <code>duno_language</code> remembers English, Taglish, or Filipino for up to one year. The app sets it as HttpOnly and SameSite=Lax, and marks it Secure when the request uses HTTPS.',
            ]],
            ['title' => 'Browser storage', 'paragraphs' => [
                'The onboarding tour stores whether it was completed, using local storage when available and session storage as a fallback. A separate local-storage flag remembers whether the optional check-in prompt was dismissed for the current day. These flags contain interface state, not study records or check-in ratings.',
                'Duno’s service worker caches the offline recovery page and selected public app-shell files. It does not cache authenticated pages or saved study data, and it does not queue changes made offline.',
            ]],
            ['title' => 'Your controls', 'paragraphs' => [
                'You can remove cookies and site storage in your browser settings. Blocking the session cookie can prevent sign-in or form submissions. Clearing the language preference returns Duno to its default language; clearing browser storage may cause the tour or check-in prompt to appear again.',
                'No cookie consent banner is currently implemented. A source review did not find an optional advertising or third-party analytics tracker, but the production host and deployed integrations have not been audited. The project owner should review actual deployment requirements before treating this page as final.',
            ]],
        ]);
    }

    /** @param list<array{title: string, paragraphs: list<string>}> $sections */
    private function showPage(string $slug, string $title, string $intro, array $sections): string
    {
        return view('legal/page', [
            'slug' => $slug,
            'pageTitle' => $title,
            'pageIntro' => $intro,
            'sections' => $sections,
            'footerGroups' => [
                ['id' => 'legal-footer-home', 'title' => 'Duno', 'links' => [
                    ['label' => lang('App.home'), 'href' => site_url('/')],
                    ['label' => lang('App.signIn'), 'href' => site_url('login')],
                ]],
                ['id' => 'legal-footer-tools', 'title' => lang('App.footerTools'), 'links' => [
                    ['label' => lang('App.focus'), 'href' => site_url('/#tools')],
                    ['label' => lang('App.guide'), 'href' => site_url('/#guide')],
                ]],
            ],
        ]);
    }
}
