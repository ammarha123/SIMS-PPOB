<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->get('/registration', 'Auth\RegistrationController::index');
$routes->post('registration', 'Auth\RegistrationController::submit');
$routes->get('/login', 'Auth\LoginController::index');
$routes->post('login', 'Auth\LoginController::submit');
$routes->get('/logout', 'Auth\LoginController::logout');
$routes->get('debug-session', function () {
    dd(session()->get());
});
$routes->get('/topup', 'TopUpController::index');
$routes->post('topup', 'TopUpController::store');

$routes->get('payment/(:segment)', 'PaymentController::index/$1');
$routes->post('payment/(:segment)', 'PaymentController::pay/$1');
$routes->get('transaction', 'TransactionController::index');
$routes->get('profile', 'ProfileController::index');
$routes->post('profile/update', 'ProfileController::update');
$routes->post('profile/image', 'ProfileController::uploadImage');
$routes->get('/transaction/more', 'TransactionController::more');
