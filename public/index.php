<?php

use CodeIgniter\Boot;
use Config\Paths;

require_once __DIR__ . '/../app/Services/InfinityFreeRouteMarker.php';

/*
 *---------------------------------------------------------------
 * CHECK PHP VERSION
 *---------------------------------------------------------------
 */

$minPhpVersion = '8.2'; // If you update this, don't forget to update `spark`.
if (version_compare(PHP_VERSION, $minPhpVersion, '<')) {
    $message = sprintf(
        'Your PHP version must be %s or higher to run CodeIgniter. Current version: %s',
        $minPhpVersion,
        PHP_VERSION,
    );

    header('HTTP/1.1 503 Service Unavailable.', true, 503);
    echo $message;

    exit(1);
}

// InfinityFree's browser check can append its `&i=1` marker to the query-style
// route form used by this host (`index.php?/student/...`). In that URL shape,
// the marker is otherwise parsed as part of the route and CI4 rejects `&` as a
// disallowed URI character. Remove only the terminal host marker from a route
// query; leave normal app query parameters and path-style URLs untouched.
$requestUri = $_SERVER['REQUEST_URI'] ?? null;
$scriptName = $_SERVER['SCRIPT_NAME'] ?? null;
if (is_string($requestUri) && is_string($scriptName)) {
    $_SERVER['REQUEST_URI'] = InfinityFreeRouteMarker::stripTerminalMarker($requestUri, $scriptName);
}

/*
 *---------------------------------------------------------------
 * SET THE CURRENT DIRECTORY
 *---------------------------------------------------------------
 */

// Path to the front controller (this file)
define('FCPATH', __DIR__ . DIRECTORY_SEPARATOR);

// Ensure the current directory is pointing to the front controller's directory
if (getcwd() . DIRECTORY_SEPARATOR !== FCPATH) {
    chdir(FCPATH);
}

/*
 *---------------------------------------------------------------
 * BOOTSTRAP THE APPLICATION
 *---------------------------------------------------------------
 * This process sets up the path constants, loads and registers
 * our autoloader, along with Composer's, loads our constants
 * and fires up an environment-specific bootstrapping.
 */

// LOAD OUR PATHS CONFIG FILE
// This is the line that might need to be changed, depending on your folder structure.
require FCPATH . '../app/Config/Paths.php';
// ^^^ Change this line if you move your application folder

$paths = new Paths();

// LOAD THE FRAMEWORK BOOTSTRAP FILE
require $paths->systemDirectory . '/Boot.php';

exit(Boot::bootWeb($paths));
