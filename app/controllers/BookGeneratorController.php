<?php
namespace Controller;

/**
 * @global $app Application
 */
use Exception\GenerationException;
use Service\BookGenerator;
use Service\NodeGenerator;
use Silex\Application;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Book generation controller
 *
 * Class BookGeneratorController
 * Author Stri <strifinder@gmail.com>
 * @package Controller
 */
class BookGeneratorController{
    /**
     * Generate book action
     *
     * @param Request $request
     * @param Application $app
     * @return JsonResponse
     */
   public function generate(Request $request, Application $app) {
       /**
        * @var $bookGenerator BookGenerator
        */
       $bookGenerator = $app['book_generator'];

       try {
           $data = $bookGenerator->generateBook(
               $request->query->get('first_name'),
               $request->query->get('last_name'),
               $request->query->get('gender', 'boy'),
               $request->query->get('age', 14),
               $request->query->get('unique_id', null)
           );

           return new JsonResponse($data);
       } catch (GenerationException $e) {
           return new JsonResponse([
               'message' => $e->getMessage(),
               'data' => $e->getData()
           ], 500);
       }
   }
}