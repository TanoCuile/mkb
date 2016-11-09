<?php
/**
 * @global $app Application
 */

use Silex\Application;


// DB config
if ($app) {
    $app['db'] = function (Application $app) {
        $connection = new mysqli($app['db_host'], $app['db_user'], $app['db_password'], $app['db_database']);

        Service\DatabaseUtils::$prefix = $app['db_prefix'];
        Service\DatabaseUtils::setDrupalDB($connection);

        $connection->connect_errno && die("Database error");
        return $connection;
    };
    // Load service
//    $app['db'];
} else
    throw new RuntimeException("Application not initialized");