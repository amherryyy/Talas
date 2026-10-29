<?php

namespace App\Services;

class LanguageReturnPath
{
    public static function fromServerUri(string $uriPath, string $queryString, string $indexPage): string
    {
        $path = trim((string) parse_url($uriPath, PHP_URL_PATH), '/');
        $indexPage = trim($indexPage, '/?');
        $query = ltrim($queryString, '/');
        $queryCarriesRoute = str_starts_with($queryString, '/');

        if ($indexPage !== '' && ($path === $indexPage || str_starts_with($path, $indexPage . '/'))) {
            $path = ltrim(substr($path, strlen($indexPage)), '/');
        }

        // InfinityFree may expose the rewritten route in QUERY_STRING either as
        // /index.php?/student or index.php?/student. Collapse every repeated
        // front-controller prefix before building a return URL with site_url().
        while ($indexPage !== '' && str_starts_with($query, $indexPage . '?/')) {
            $query = substr($query, strlen($indexPage) + 2);
            $queryCarriesRoute = true;
        }

        if ($queryCarriesRoute) {
            [$path, $query] = self::splitRouteAndQuery($query);
        } elseif ($path === '' && $indexPage !== '' && str_starts_with($query, $indexPage . '/')) {
            $query = substr($query, strlen($indexPage) + 1);
            [$path, $query] = self::splitRouteAndQuery($query);
        }

        return $path . ($query !== '' ? '?' . $query : '');
    }

    /** @return array{string, string} */
    private static function splitRouteAndQuery(string $route): array
    {
        $parts = explode('?', ltrim($route, '/'), 2);

        return [$parts[0], $parts[1] ?? ''];
    }
}
