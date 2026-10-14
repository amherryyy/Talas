<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->setAutoRoute(false);
$routes->get('/', 'Home::index');

$routes->group('/', ['filter' => 'auth-rates'], static function (RouteCollection $routes): void {
    service('auth')->routes($routes, ['only' => ['register', 'login']]);
});

$routes->post('logout', '\\CodeIgniter\\Shield\\Controllers\\LoginController::logoutAction', ['as' => 'logout']);

$routes->group('student', ['filter' => ['session', 'exclusive-role:student']], static function (RouteCollection $routes): void {
    $routes->get('/', 'Student\\Dashboard::index', ['as' => 'student.dashboard']);
});

$routes->group('admin', ['filter' => ['session', 'exclusive-role:admin', 'permission:admin.access']], static function (RouteCollection $routes): void {
    $routes->get('/', 'Admin\\Dashboard::index', ['as' => 'admin.dashboard']);
});
