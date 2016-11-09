<?php
/**
 * @global $app Application
 */

use Silex\Application;

if ($app) {
    $app['db_host'] = 'localhost';
    $app['db_user'] = 'root';
    $app['db_password'] = 'master';
    $app['db_database'] = 'mykingdombook';
    $app['db_prefix'] = '';
} else
    throw new RuntimeException("Application not initialized");