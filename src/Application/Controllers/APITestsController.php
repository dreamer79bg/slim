<?php

namespace App\Application\Controllers;

use App\Application\Controller as AppController;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\RequestInterface as Request;
use Exception;
use App\Application\DataServices\UserDataService;

class APITestsController extends AppController {

    public function data(Request $request, Response $response, array $args = []): Response {
        $data = array('test' => 1);
        return $this->respondJSON($response, $data, 200);
    }

    public function error(Request $request, Response $response, array $args = []): Response {
        return $this->respondJSONWithError($response, self::ERROR_BAD_DATA, 404);
    }

    public function protected(Request $request, Response $response, array $args = []): Response {
        //check security. as this is not an invokable action we can not use any inheritance here :( 

        if ($this->checkLogin()) {
            $data = array('test' => 1);
            return $this->respondJSON($response, $data, 200);
        } else {
            return $this->respondJSONWithError($response, self::ERROR_NOT_ALLOWED, 403);
        }
    }

}
