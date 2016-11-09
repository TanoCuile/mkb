<?php
/**
 * @global $app Application
 */

use Service\FieldGenerator;
use Service\NodeGenerator;
use Silex\Application;

if ($app) {
    $app['book_generator'] = function(Application $app){
        return new Service\BookGenerator();
    };

    $app['book_repository'] = function(Application $app){
        return new Service\BookRepository();
    };
} else
    throw new RuntimeException("Application not initialized");