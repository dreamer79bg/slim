<?php

namespace App\Application\Controllers;

use App\Application\Controller as AppController;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\RequestInterface as Request;
use Exception;
use App\Application\DataServices\PostDataService;

/**
 * As invokable actions are considered as using a skeleton app for the task even though this is one of the standard ways for creating routes in SLIM they are switched to controllers.
 * 
 * There is no way to keep action classes as they will always produce code similar to the example application for SLIM
 */
class TestsController extends AppController {

    public function hello(Request $request, Response $response, array $args = []): Response {
        $response->getBody()->write('Hello world!');
        return $response;
    }

     public function twig(Request $request, Response $response, array $args = []): Response {
        return $this->respondHTML($response,'hello.html');
    }

}
