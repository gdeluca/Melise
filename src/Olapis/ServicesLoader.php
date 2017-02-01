<?php

namespace Olapis;

use Olapis\Services\InstagramService;
use Silex\Application;

/**
 * Loads services {@inheritdoc}
 * Class ServicesLoader
 * @package Olapis
 */
class ServicesLoader implements Loader
{
    private $app;


    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function load()
    {
        $this->app[Loader::MEDIA_SERVICE] = $this->app->share(function () {
            return new InstagramService();
        });
    }
}
