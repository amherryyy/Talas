<?php

namespace App\Controllers;

class Legal extends BaseController
{
    public function terms(): string
    {
        return $this->showPage('terms', 'Terms and Conditions', 'Terms for using Duno, an independent student study-organizer project.', [
            ['title' => 'About Duno', 'paragraphs' => [
                'Duno is a browser-based study organizer for subjects, topics, study plans, saved study sessions, and descriptive Aralytics. Its priorities, plans, progression, and curated study suggestions use deterministic rules. They are organizational aids; they do not assess health, predict grades, certify attendance, or guarantee academic results.',
                'Duno is an independent student project created and operated by Amher B. Bolante. It is not operated, sponsored, or endorsed by NVSU or another school.',
            ]],
            ['title' => 'Eligibility and accounts', 'paragraphs' => [
                'Duno is intended for adults aged 18 or older. By creating or using an account, you confirm that you are at least 18. The registration form asks for this confirmation; Duno does not collect your birth date or independently verify your age. If you are under 18, do not create or use a Duno account.',
                'Use an email address you are authorized to access, keep your sign-in details private, and use only your own account. Student accounts are created through public registration; administrative accounts are provisioned separately. Keep your school and program details accurate.',
            ]],
            ['title' => 'Acceptable use', 'paragraphs' => [
                'Use Duno only for lawful personal study organization. Do not try to access another person\'s records, interfere with the service, evade its security controls, or submit personal information that belongs to another person. Do not put passwords, government identifiers, health details, or other sensitive information in study fields or check-ins.',
                'Duno requires an internet connection for account pages and saved study data. The offline page and selected public files do not provide offline access to private records or save changes made offline.',
            ]],
            ['title' => 'Your records and account deletion', 'paragraphs' => [
                'Subjects, topics, plans, sessions, progression activity, and optional check-ins are associated with your account. Check-ins are optional and private to the student account in the current application; they are excluded from admin summaries and printed study reports.',
                'You can edit or delete supported records in the app. A subject or topic deletion may be blocked when saved study history depends on it. You can request permanent account deletion from Profile after confirming your password and typing DELETE. This removes the account and associated study records from the application database. A full self-service export is not currently available; use the privacy contact to request access to your information.',
            ]],
            ['title' => 'Availability and contact', 'paragraphs' => [
                'Access may be interrupted by network, hosting, or maintenance problems. Do not rely on Duno as your only copy of an important schedule. The project has not established a service-availability, backup-recovery, or backup-deletion deadline.',
                'For questions about these Terms, contact Amher B. Bolante at <a href="mailto:mhracads@gmail.com">mhracads@gmail.com</a>. Applicable privacy and consumer rights are not limited by these Terms.',
            ]],
        ]);
    }

    public function privacy(): string
    {
        return $this->showPage('privacy', 'Privacy Notice', 'What information Duno uses, why it is used, and how to make a privacy request.', [
            ['title' => 'Who is responsible', 'paragraphs' => [
                'Duno is an independent student project created and operated by Amher B. Bolante; it is not operated, sponsored, or endorsed by NVSU or another school. For privacy questions or requests, email <a href="mailto:mhracads@gmail.com?subject=Duno%20privacy%20request">mhracads@gmail.com</a> with the subject "Duno privacy request." Amher handles privacy requests for this project and is listed as its privacy contact, not as an institution-appointed Data Protection Officer.',
            ]],
            ['title' => 'Information Duno uses', 'paragraphs' => [
                'Duno uses your username and email address, a protected password credential, and account-security information needed for authentication. You may provide a school and academic program, subjects, topics, due dates, progress, plans, study sessions, and progression activity. You may also choose to record workload and energy ratings as optional check-ins. Do not submit another person\'s information or sensitive information in study fields.',
                'Registration verification and password recovery use short-lived numeric codes. Password-reset codes are stored as hashes and expire after 15 minutes or when used or replaced. The app also uses a language preference cookie and browser-storage values for tour completion and dismissal of the optional check-in prompt; those values do not contain saved study records or ratings.',
            ]],
            ['title' => 'Purposes and use', 'paragraphs' => [
                'Account information is used to create, verify, secure, and recover accounts. Study information is used to provide the tools and summaries you choose to use. Optional check-ins are used to show private summaries to the same student. Duno uses deterministic rules for priority, planning, progression, and curated suggestions. It does not use generative AI, predict grades, or make school admission, grading, or eligibility decisions.',
                'Core account and study information is processed as needed to provide the account-based service you request under these Terms, including authentication, saved records, and summaries. Optional check-in ratings are processed only when you choose to submit them, to provide your private summaries. The Terms and Privacy Notice explain these purposes; acknowledging the notice does not waive your privacy rights.',
            ]],
            ['title' => 'Access and service providers', 'paragraphs' => [
                'Students can access records associated with their own account. The current admin dashboard shows aggregates and does not show student names, individual study records, program breakdowns, or private check-ins. Small groups in trend summaries are withheld.',
                'Duno is deployed on InfinityFree hosting. The host may process information needed to serve and secure the site, including technical connection information. Verification and password-recovery messages are sent through the mail service configured for the deployment; its provider and processing locations have not been verified. If you email the privacy contact, Google may process that correspondence as the provider of the Gmail account. The locations where service providers store or process information have not been verified and may be outside the Philippines. The reviewed source code contains no advertising or third-party analytics tracker; deployed integrations have not been independently audited. Duno does not sell personal information.',
            ]],
            ['title' => 'School evaluation', 'paragraphs' => [
                'Creating a Duno account does not by itself enroll you in academic research or an evaluation study. Any separate evaluation must use its own adviser-approved information and consent process. Evaluation should use synthetic accounts and avoid real student records. Do not enter private or identifying study information for an evaluation task.',
            ]],
            ['title' => 'Retention and deletion', 'paragraphs' => [
                'Study records remain in the application until you delete them or delete your account. Optional check-ins remain until you delete them or your account. Account deletion removes the account and its associated study records from the application database. Inactive, unverified registrations are eligible for cleanup after 48 hours when registration is opened. The app is configured for session-based sign-in with a two-hour session expiration.',
                'Hosting-provider log retention and the timing of deletion from backups have not been verified. Deleting information in Duno should not be understood as a verified deletion from hosting backups. A full self-service data export is not available, but you may request access or a copy by email.',
            ]],
            ['title' => 'Your rights and how to make a request', 'paragraphs' => [
                'You can edit or delete supported records in Duno and permanently delete your account from Profile. You may also email <a href="mailto:mhracads@gmail.com?subject=Duno%20privacy%20request">mhracads@gmail.com</a> to ask whether Duno holds information about you, request access or correction, object to processing, or request erasure/blocking or a copy of your information where applicable. Include the account email so the request can be verified. Do not send your password. The project may ask for additional information only when reasonably needed to verify your identity. Requests are subject to applicable legal requirements and exceptions. You may also complain to the National Privacy Commission.',
                'For an overview of rights under Philippine privacy law, see the National Privacy Commission\'s <a href="https://privacy.gov.ph/data-subject-rights/" rel="noopener noreferrer">data subject rights</a> page. These rights include being informed, access, correction, objection, erasure or blocking, portability, damages, and filing a complaint, subject to the law.',
            ]],
        ]);
    }

    public function cookies(): string
    {
        return $this->showPage('cookies', 'Cookies and browser storage', 'What Duno stores in your browser and what those values do.', [
            ['title' => 'Cookies used by Duno', 'paragraphs' => [
                'Duno uses a first-party session cookie for signed-in sessions and session-based request protection. The app does not provide a "remember me" sign-in option.',
                'A first-party cookie named <code>duno_language</code> remembers English, Taglish, or Filipino for up to one year. The app sets it as HttpOnly and SameSite=Lax, and marks it Secure when the request uses HTTPS.',
            ]],
            ['title' => 'Browser storage', 'paragraphs' => [
                'The onboarding tour stores whether it was completed, using local storage when available and session storage as a fallback. A separate local-storage flag remembers whether the optional check-in prompt was dismissed for the current day. These flags contain interface state, not study records or check-in ratings.',
                'Duno\'s service worker caches the offline recovery page and selected public app-shell files. It does not cache authenticated pages or saved study data, and it does not queue changes made offline.',
            ]],
            ['title' => 'Your controls', 'paragraphs' => [
                'You can remove cookies and site storage in your browser settings. Blocking the session cookie can prevent sign-in or form submissions. Clearing the language preference returns Duno to its default language; clearing browser storage may cause the tour or check-in prompt to appear again.',
                'No cookie consent banner is currently implemented. A source review did not find an optional advertising or third-party analytics tracker. The production host and deployed integrations have not been independently audited, so provider-added cookies or retention settings are not confirmed. For questions, contact <a href="mailto:mhracads@gmail.com">mhracads@gmail.com</a>.',
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
