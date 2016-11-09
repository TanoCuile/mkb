<?php

define('SITE_ROOT', __DIR__);
define('SITE_DOMAIN', $_SERVER['SERVER_NAME']);

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

try {
    require_once __DIR__.'/../vendor/autoload.php';

    require_once __DIR__.'/../app/app.php';
} catch (Exception $e) {
    print $e->getMessage();
}