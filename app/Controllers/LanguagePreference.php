<?php

namespace App\Controllers;

class LanguagePreference extends BaseController
{
    public function update()
    {
        $locale = (string) $this->request->getPost('language');
        if (! in_array($locale, ['en', 'taglish', 'fil'], true)) {
            return redirect()->back()->with('error', lang('App.languageInvalid'));
        }

        $returnTo = (string) $this->request->getPost('return_to');
        $parts = parse_url($returnTo);
        $path = is_array($parts) ? (string) ($parts['path'] ?? '') : '';
        if ($path === '' || str_starts_with($path, '/') || str_contains($path, '\\')
            || isset($parts['scheme']) || isset($parts['host']) || preg_match('#(?:^|/)\.\.?(?:/|$)#', $path)) {
            $path = '';
            $parts = [];
        }

        $destination = site_url($path);
        if (isset($parts['query'])) {
            $destination .= '?' . $parts['query'];
        }
        if (isset($parts['fragment'])) {
            $destination .= '#' . $parts['fragment'];
        }

        return redirect()->to($destination)->setCookie(
            'duno_language',
            $locale,
            365 * 24 * 60 * 60,
            '',
            '/',
            '',
            $this->request->isSecure(),
            true,
            'Lax'
        );
    }
}
