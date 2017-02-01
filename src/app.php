<?php

use Olapis\RoutesLoader;
use Olapis\ServicesLoader;
use Silex\Provider\ServiceControllerServiceProvider;
use Silex\Provider\SessionServiceProvider;
use Symfony\Component\HttpFoundation\Request;

require_once __DIR__ . '/bootstrap.php';

$app = new Silex\Application();
$app['debug'] = true;

//accepting JSON
$app->before(function (Request $request) {
    if (0 === strpos($request->headers->get('Content-Type'), 'application/json')) {
        $data = json_decode($request->getContent(), true);
        $request->request->replace(is_array($data) ? $data : array());
    }
});

// will be used to store tokens across sessions
$app->register(new SessionServiceProvider());

$app->register(new ServiceControllerServiceProvider());

//load services
$servicesLoader = new ServicesLoader($app);
$servicesLoader->load();

//load routes
$routesLoader = new RoutesLoader($app);
$routesLoader->load();

return $app;
