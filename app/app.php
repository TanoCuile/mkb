<?php

require_once __DIR__.'/../vendor/autoload.php';

$app = new Silex\Application();

$app['debug'] = true;

$app->register(new Silex\Provider\ServiceControllerServiceProvider());

require_once 'config/index.php';

$app->run();