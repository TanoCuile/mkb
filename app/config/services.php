<?php
/**
 * @global $app Application
 */

use Service\FieldGenerator;
use Service\NodeGenerator;
use Silex\Application;

if ($app) {
    $app['field_generator'] = function (Application $app) {
        return new FieldGenerator($app['db']);
    };

    $app['node_generator'] = function (Application $app) {
        return new NodeGenerator($app['field_generator'], $app['db']);
    };

    $app['book_generator'] = function(Application $app){
        return new Service\BookGenerator($app['node_generator'], $app['db']);
    };

    $app['book_repository'] = function(Application $app){
        return new Service\BookRepository();
    };
} else
    throw new RuntimeException("Application not initialized");