<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->setAutoRoute(false);
$routes->get('/', 'Home::index');
$routes->post('language', 'LanguagePreference::update', ['as' => 'language.update']);

$routes->group('/', ['filter' => 'auth-rates'], static function (RouteCollection $routes): void {
    service('auth')->routes($routes, ['only' => ['register', 'login', 'auth-actions']]);
    $routes->post('auth/a/resend-registration-code', 'RegistrationRecovery::resend', ['as' => 'auth-registration-resend']);
    $routes->post('auth/a/cancel-registration', 'RegistrationRecovery::cancel', ['as' => 'auth-registration-cancel']);
});

$routes->post('logout', 'Auth\\LogoutController::logoutAction', ['as' => 'logout']);

$routes->group('student', ['filter' => ['session', 'exclusive-role:student', 'student-streak']], static function (RouteCollection $routes): void {
    $routes->get('/', 'Student\\Dashboard::index', ['as' => 'student.dashboard']);
    $routes->get('profile', 'Student\\Profile::show', ['as' => 'student.profile']);
    $routes->post('profile', 'Student\\Profile::update', ['as' => 'student.profile.update']);
    $routes->get('subjects', 'Student\\Subjects::index', ['as' => 'student.subjects.index']);
    $routes->post('subjects/search', 'Student\\Subjects::search', ['as' => 'student.subjects.search']);
    $routes->get('subjects/templates', 'Student\\Subjects::templates', ['as' => 'student.subjects.templates']);
    $routes->post('subjects/templates/import', 'Student\\Subjects::importTemplate', ['as' => 'student.subjects.templates.import']);
    $routes->post('subjects/nvsu/import', 'Student\\Subjects::importNvsuCurriculum', ['as' => 'student.subjects.nvsu.import']);
    $routes->post('subjects/shs/import', 'Student\\Subjects::importShsSubjects', ['as' => 'student.subjects.shs.import']);
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
    $routes->get('plan', 'Student\\Plans::show', ['as' => 'student.plan']);
    $routes->post('plan/generate', 'Student\\Plans::generate', ['as' => 'student.plan.generate']);
    $routes->post('plan/items/(:num)/progress', 'Student\\Plans::updateProgress/$1', ['as' => 'student.plan.items.progress']);
    $routes->post('plan/items/(:num)/duration', 'Student\\Plans::updateDuration/$1', ['as' => 'student.plan.items.duration']);
    $routes->post('plan/items/(:num)/remove', 'Student\\Plans::removeItem/$1', ['as' => 'student.plan.items.remove']);
    $routes->post('plan/items/(:num)/move/(:alpha)', 'Student\\Plans::moveItem/$1/$2', ['as' => 'student.plan.items.move']);
    $routes->get('sessions', 'Student\\StudySessions::index', ['as' => 'student.sessions.index']);
    $routes->get('aralytics', 'Student\\Aralytics::index', ['as' => 'student.aralytics']);
    $routes->get('check-ins', 'Student\\CheckIns::index', ['as' => 'student.check-ins.index']);
    $routes->post('sessions', 'Student\\StudySessions::create', ['as' => 'student.sessions.create']);
    $routes->get('sessions/(:num)/edit', 'Student\\StudySessions::edit/$1', ['as' => 'student.sessions.edit']);
    $routes->post('sessions/(:num)', 'Student\\StudySessions::update/$1', ['as' => 'student.sessions.update']);
    $routes->post('sessions/(:num)/delete', 'Student\\StudySessions::delete/$1', ['as' => 'student.sessions.delete']);
    $routes->post('sessions/(:num)/delete/confirm', 'Student\\StudySessions::confirmDelete/$1', ['as' => 'student.sessions.delete.confirm']);
    $routes->post('check-ins', 'Student\\CheckIns::create', ['as' => 'student.check-ins.create']);
    $routes->get('check-ins/(:num)/edit', 'Student\\CheckIns::edit/$1', ['as' => 'student.check-ins.edit']);
    $routes->post('check-ins/(:num)', 'Student\\CheckIns::update/$1', ['as' => 'student.check-ins.update']);
    $routes->get('check-ins/(:num)/delete', 'Student\\CheckIns::delete/$1', ['as' => 'student.check-ins.delete']);
    $routes->post('check-ins/(:num)/delete/confirm', 'Student\\CheckIns::confirmDelete/$1', ['as' => 'student.check-ins.delete.confirm']);
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
