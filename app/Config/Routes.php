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
    $routes->post('subjects/search', 'Student\\Subjects::search', ['as' => 'student.subjects.search']);
    $routes->get('subjects/new', 'Student\\Subjects::newSubject', ['as' => 'student.subjects.new']);
    $routes->post('subjects', 'Student\\Subjects::create', ['as' => 'student.subjects.create']);
    $routes->get('subjects/(:num)', 'Student\\Subjects::show/$1', ['as' => 'student.subjects.show']);
    $routes->get('subjects/(:num)/edit', 'Student\\Subjects::edit/$1', ['as' => 'student.subjects.edit']);
    $routes->post('subjects/(:num)', 'Student\\Subjects::update/$1', ['as' => 'student.subjects.update']);
    $routes->post('subjects/(:num)/delete', 'Student\\Subjects::delete/$1', ['as' => 'student.subjects.delete']);
    $routes->get('subjects/(:num)/topics', 'Student\\Topics::index/$1', ['as' => 'student.topics.index']);
    $routes->get('topics', 'Student\\Topics::all', ['as' => 'student.topics.all']);
    $routes->post('topics', 'Student\\Topics::all', ['as' => 'student.topics.filter']);
    $routes->get('priority', 'Student\\Topics::priority', ['as' => 'student.priority']);
    $routes->get('subjects/(:num)/topics/new', 'Student\\Topics::newTopic/$1', ['as' => 'student.topics.new']);
    $routes->post('subjects/(:num)/topics', 'Student\\Topics::create/$1', ['as' => 'student.topics.create']);
    $routes->get('subjects/(:num)/topics/(:num)', 'Student\\Topics::show/$1/$2', ['as' => 'student.topics.show']);
    $routes->get('subjects/(:num)/topics/(:num)/edit', 'Student\\Topics::edit/$1/$2', ['as' => 'student.topics.edit']);
    $routes->post('subjects/(:num)/topics/(:num)', 'Student\\Topics::update/$1/$2', ['as' => 'student.topics.update']);
    $routes->post('subjects/(:num)/topics/(:num)/delete', 'Student\\Topics::delete/$1/$2', ['as' => 'student.topics.delete']);
});

$routes->group('admin', ['filter' => ['session', 'exclusive-role:admin', 'permission:admin.access']], static function (RouteCollection $routes): void {
    $routes->get('/', 'Admin\\Dashboard::index', ['as' => 'admin.dashboard']);
});
