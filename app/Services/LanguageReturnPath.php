<?php

namespace App\Services;

class LanguageReturnPath
{
    public static function fromServerUri(string $uriPath, string $queryString, string $indexPage): string
    {
        $path = trim($uriPath, '/');
        $indexPage = trim($indexPage, '/?');
        $query = $queryString;
        $queryContainsRoute = str_starts_with($query, '/');
        if ($queryContainsRoute) {
            $query = ltrim($query, '/');
        }

        while ($indexPage !== '' && str_starts_with($query, $indexPage . '?/')) {
            $query = substr($query, strlen($indexPage) + 2);
            $queryContainsRoute = true;
        }

        if ($queryContainsRoute) {
            $routeQueryParts = explode('?', ltrim($query, '/'), 2);
            $path = $routeQueryParts[0];
            $query = $routeQueryParts[1] ?? '';
        } elseif ($path === $indexPage || $path === '') {
            $path = '';
            if (str_starts_with($query, '/')) {
                $routeQueryParts = explode('?', ltrim($query, '/'), 2);
                $path = $routeQueryParts[0];
                $query = $routeQueryParts[1] ?? '';
            }
        }

        return $path . ($query !== '' ? '?' . $query : '');
    }
}
