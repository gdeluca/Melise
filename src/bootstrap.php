<?php

use Paulyg\Autoloader;

error_reporting(E_ALL ^ E_NOTICE);

define('ROOT_PATH', dirname(__DIR__));
define('VENDOR_PATH', ROOT_PATH . '/vendor');

require_once VENDOR_PATH . '/paulyg/autoloader/src/Paulyg/Autoloader.php';
require_once 'config.php';

$loader = new Autoloader();
$loader->addPsr4('OAuth\\OAuth1', __DIR__ . '/OAuth/OAuth1');
$loader->addPsr4('Olapis', __DIR__ . '/Olapis');
$loader->addPsr4('Symfony\\Component', VENDOR_PATH . '/symfony/serializer/Symfony/Component');
$loader->addPsr4('Symfony\\Component\\Serializer', VENDOR_PATH . '/symfony/serializer/Symfony/Component/Serializer');
$loader->addPsr4('Symfony\\Component\\Serializer\\Normalizer', VENDOR_PATH . '/symfony/serializer/Symfony/Component/Serializer/Normalizer');
$loader->addPsr4('Silex', VENDOR_PATH . '/silex/silex/src/Silex');
$loader->addPsr0('Pimple', VENDOR_PATH . '/pimple/pimple/lib'); // backwards compatibility features for PEAR-style classname
$loader->addPsr4('Symfony\\Component\\HttpKernel', VENDOR_PATH . '/symfony/http-kernel/Symfony/Component/HttpKernel');
$loader->addPsr4('Symfony\\Component\\Routing', VENDOR_PATH . '/symfony/routing/Symfony/Component/Routing');
$loader->addPsr4('Symfony\\Component\\HttpFoundation', VENDOR_PATH . '/symfony/http-foundation/Symfony/Component/HttpFoundation');
$loader->addPsr4('Symfony\\Component\\EventDispatcher', VENDOR_PATH . '/symfony/event-dispatcher/Symfony/Component/EventDispatcher');
$loader->addPsr4('Symfony\\Component\\Debug', VENDOR_PATH . '/symfony/debug');
$loader->addPsr4('Httpful', VENDOR_PATH . '/nategood/httpful/src/Httpful');
return $loader;
