<?php
/**
 * @global $app Application
 */

use Controller\BookGeneratorController;
use Controller\SynchronizeController;
use Silex\Application;

if ($app) {
    $app['controller.book_generator'] = $app->factory(function() use ($app){
        return new BookGeneratorController($app['book_service'], $app['book']);
    });
} else
    throw new RuntimeException("Application not initialized");