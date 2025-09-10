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