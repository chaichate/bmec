<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
header('Access-Control-Allow-Origin: *'); 


if (PHP_SAPI == 'cli-server') {
    // To help the built-in PHP dev server, check if the request was actually for
    // something which should probably be served as a static file
    $url  = parse_url($_SERVER['REQUEST_URI']);
    $file = __DIR__ . $url['path'];
    if (is_file($file)) {
        return false;
    }
}

require __DIR__ . '/../../vendor/autoload.php';

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response; 

session_start();

// Instantiate the app
$settings = require __DIR__ . '/../src/settings.php';
$app = new \Slim\App($settings);
$app->add(function ($request, $response, $next) {
    $newResponse = $response->withHeader('Access-Control-Allow-Origin', '*')
                         ->withHeader('Access-Control-Allow-Headers', array('Content-Type', 'X-Requested-With', 'Authorization'))
                         ->withHeader('Access-Control-Allow-Methods', array('GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'));

 if($request->isOptions()) {
     return $newResponse;
}

  return $next($request, $newResponse);
});


// Set up dependencies
require __DIR__ . '/../src/dependencies.php';

// Register middleware
require __DIR__ . '/../src/middleware.php';

// Register routes
require __DIR__ . '/../src/routes.php';

// Run app
$app->run();
