<?php

namespace App\Services;

/** Normalize InfinityFree's terminal browser-check marker on query-style routes. */
final class InfinityFreeRouteMarker
{
    public static function stripTerminalMarker(string $requestUri, string $scriptName): string
    {
        $queryOffset = strpos($requestUri, '?');
        if ($queryOffset === false) {
            return $requestUri;
        }

        $requestPath = substr($requestUri, 0, $queryOffset);
        $scriptPath = parse_url($scriptName, PHP_URL_PATH);
        $routeQuery = substr($requestUri, $queryOffset + 1);

        if (! is_string($scriptPath)
            || $requestPath !== $scriptPath
            || ! str_starts_with($routeQuery, '/')
            || preg_match('/&i=\d+$/D', $routeQuery, $marker) !== 1) {
            return $requestUri;
        }

        return $requestPath . '?' . substr($routeQuery, 0, -strlen($marker[0]));
    }
}
