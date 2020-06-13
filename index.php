<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");

/**
 * Front controller
 */

/**
 * Composer
 */
require ('vendor/autoload.php');

/**
 * Routing
 */
$router = new Core\Router();
$router->setBasePath('/Cex-assignment');

// Map the routes
$router->addRoutes([
    ['GET','/users/[i:id]', 'users#show', 'users_show'],
    ['POST','/users/login', 'users#login', 'users_login'],
    ['POST','/users/register', 'users#register', 'users_register'],
]);

$match = $router->dispatch();