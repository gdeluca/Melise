<?php

namespace Olapis;

/**
 * Load elements in application context
 */
interface Loader
{
    const MEDIA_SERVICE = 'media.service';
    const MEDIA_CONTROLLER = 'media.controller';
    const CONTROLLERS_FACTORY = "controllers_factory";

    public function load();
}
