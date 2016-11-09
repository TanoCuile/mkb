<?php
/**
 * @global $app Application
 */

use Silex\Application;

if ($app) {
    require_once 'parameters.php';
//    require_once 'db.php';
    require_once 'services.php';
    require_once 'controllers.php';
    require_once 'routes.php';
} else
    throw new RuntimeException("Application not initialized");