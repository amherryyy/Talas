<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class LanguagePreferenceFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $locale = $request->getCookie('duno_language');
        if (! in_array($locale, ['en', 'taglish', 'fil'], true)) {
            $locale = 'en';
        }

        $request->setLocale($locale);

        return null;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
    }
}
