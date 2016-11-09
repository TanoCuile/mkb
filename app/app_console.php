<?php

define('SITE_ROOT', __DIR__ . '/../web');

require_once __DIR__.'/../vendor/autoload.php';

$app = new Silex\Application();

$app->register(new Silex\Provider\ServiceControllerServiceProvider());


require_once 'config/parameters.php';
require_once 'config/db.php';
require_once 'config/services.php';
require_once 'config/routes.php';

$app->run();