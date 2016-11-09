<?php
/**
 * @global $app Application
 */

use Controller\BookGeneratorController;
use Silex\Application;

if ($app) {
    $app->get('/mkb/generate', 'controller.book_generator:generate');
} else
    throw new RuntimeException("Application not initialized");