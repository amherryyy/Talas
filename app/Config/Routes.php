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
    $routes->get('subjects', 'Student\\Subjects::index', ['as' => 'student.subjects.index']);
    $routes->get('subjects/new', 'Student\\Subjects::newSubject', ['as' => 'student.subjects.new']);
    $routes->post('subjects', 'Student\\Subjects::create', ['as' => 'student.subjects.create']);
    $routes->get('subjects/(:num)', 'Student\\Subjects::show/$1', ['as' => 'student.subjects.show']);
    $routes->get('subjects/(:num)/edit', 'Student\\Subjects::edit/$1', ['as' => 'student.subjects.edit']);
    $routes->post('subjects/(:num)', 'Student\\Subjects::update/$1', ['as' => 'student.subjects.update']);
    $routes->post('subjects/(:num)/delete', 'Student\\Subjects::delete/$1', ['as' => 'student.subjects.delete']);
});

$routes->group('admin', ['filter' => ['session', 'exclusive-role:admin', 'permission:admin.access']], static function (RouteCollection $routes): void {
    $routes->get('/', 'Admin\\Dashboard::index', ['as' => 'admin.dashboard']);
});
