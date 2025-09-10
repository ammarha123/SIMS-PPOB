<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->get('/hello', 'Hello::index');
$routes->get('/registration', 'Auth\RegistrationController::index');
$routes->post('registration', 'Auth\RegistrationController::submit');