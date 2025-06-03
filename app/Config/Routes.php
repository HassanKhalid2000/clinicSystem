<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// Auth routes
$routes->get('/', 'Auth::login');
$routes->get('login', 'Auth::login');
$routes->post('login', 'Auth::attemptLogin');
$routes->get('auth/logout', 'Auth::logout');
$routes->get('auth/forgot-password', 'Auth::forgotPassword');
$routes->post('auth/forgot-password', 'Auth::forgotPassword');
$routes->get('auth/change-password', 'Auth::changePassword', ['filter' => 'auth']);
$routes->post('auth/change-password', 'Auth::changePassword', ['filter' => 'auth']);

// Dashboard
$routes->get('dashboard', 'Dashboard::index', ['filter' => 'auth']);

// Patients
$routes->group('patients', ['filter' => 'auth'], function($routes) {
    $routes->get('/', 'Patients::index');
    $routes->get('create', 'Patients::create');
    $routes->post('store', 'Patients::store');
    $routes->get('edit/(:num)', 'Patients::edit/$1');
    $routes->post('update/(:num)', 'Patients::update/$1');
    $routes->get('delete/(:num)', 'Patients::delete/$1');
    $routes->get('view/(:num)', 'Patients::view/$1');
});

// Doctors
$routes->group('doctors', ['filter' => 'auth'], function($routes) {
    $routes->get('/', 'Doctors::index');
    $routes->get('create', 'Doctors::create');
    $routes->post('store', 'Doctors::store');
    $routes->get('edit/(:num)', 'Doctors::edit/$1');
    $routes->post('update/(:num)', 'Doctors::update/$1');
    $routes->get('delete/(:num)', 'Doctors::delete/$1');
    $routes->get('view/(:num)', 'Doctors::view/$1');
    $routes->get('schedule/(:num)', 'Doctors::schedule/$1');
});

// Appointments
$routes->group('appointments', ['filter' => 'auth'], function($routes) {
    $routes->get('/', 'Appointments::index');
    $routes->get('create', 'Appointments::create');
    $routes->post('store', 'Appointments::store');
    $routes->get('edit/(:num)', 'Appointments::edit/$1');
    $routes->post('update/(:num)', 'Appointments::update/$1');
    $routes->get('delete/(:num)', 'Appointments::delete/$1');
    $routes->get('view/(:num)', 'Appointments::view/$1');
    $routes->get('calendar', 'Appointments::calendar');
    $routes->get('calendar-data', 'Appointments::getCalendarData');
});

// Medical Records
$routes->group('medical-records', ['filter' => 'auth'], function($routes) {
    $routes->get('/', 'MedicalRecords::index');
    $routes->get('create', 'MedicalRecords::create');
    $routes->post('store', 'MedicalRecords::store');
    $routes->get('edit/(:num)', 'MedicalRecords::edit/$1');
    $routes->post('update/(:num)', 'MedicalRecords::update/$1');
    $routes->get('delete/(:num)', 'MedicalRecords::delete/$1');
    $routes->get('view/(:num)', 'MedicalRecords::view/$1');
    $routes->get('download/(:num)/(:num)', 'MedicalRecords::download/$1/$2');
});

// Billings
$routes->group('billings', ['filter' => 'auth'], function($routes) {
    $routes->get('/', 'Billings::index');
    $routes->get('create', 'Billings::create');
    $routes->post('store', 'Billings::store');
    $routes->get('edit/(:num)', 'Billings::edit/$1');
    $routes->post('update/(:num)', 'Billings::update/$1');
    $routes->get('delete/(:num)', 'Billings::delete/$1');
    $routes->get('view/(:num)', 'Billings::view/$1');
    $routes->get('print/(:num)', 'Billings::print/$1');
    $routes->get('report', 'Billings::report');
});
