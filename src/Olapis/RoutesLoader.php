<?php

namespace Olapis;

use Olapis\Controllers\MediaController;
use Silex\Application;

/**
 * Loads controllers and maps routes to controllers {@inheritdoc}
 * Class RoutesLoader
 * @package Olapis
 */
class RoutesLoader implements Loader
{
    /**
     * @var Application
     */
    private $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
        $this->createControllers();
    }


    private function createControllers()
    {
        $this->app[Loader::MEDIA_CONTROLLER] = $this->app->share(function () {
            $mediaController = new MediaController();
            $mediaController->setInstagramService($this->app[Loader::MEDIA_SERVICE]);
            return $mediaController;
        });
    }

    public function load()
    {
        $api = $this->app[Loader::CONTROLLERS_FACTORY];
        $api->get('/media/{id}', Loader::MEDIA_CONTROLLER . ":getLocationResponse");
        $api->get('/media/{id}/regional', Loader::MEDIA_CONTROLLER . ":getRegionalLocationResponse");

        $api->get('/', function () {
            return 'The App is alive';
        });

        $this->app->mount('/', $api);
    }
}
