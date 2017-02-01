<?php

$app = require_once __DIR__ . '/../src/app.php';

// in order to serve static files, you'll have to make sure your front controller returns false
$filename = __DIR__ . preg_replace('#(\?.*)$#', '', $_SERVER['REQUEST_URI']);
if (php_sapi_name() === 'cli-server' && is_file($filename)) {
    return false;
}

$app->run();
