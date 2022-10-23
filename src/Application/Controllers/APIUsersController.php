<?php

namespace App\Application\Controllers;

use App\Application\Controller as AppController;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\RequestInterface as Request;
use Exception;
use App\Application\DataServices\UserDataService;

class APIUsersController extends AppController {

    public function get(Request $request, Response $response, array $args = []): Response {
        //check security. as this is not an invokable action we can not use any inheritance here :( 

        if ($this->checkLogin()) {
            $id = $request->getAttribute('id');

            $userService = new UserDataService();

            try {
                $user = $userService->getById($request->getAttribute('id'));

                return $this->respondJSON($response, array('status' => 'ok', 'userName' => $user->userName, 'fullName' => $user->fullName, 'id' => $user->getId()), 200);
            } catch (Exception $e) {
                
            }
            return $this->respondJSONWithError($response, self::ERROR_BAD_DATA, 400);
        } else {
            return $this->respondJSONWithError($response, self::ERROR_NOT_ALLOWED, 403);
        }
    }

    public function list(Request $request, Response $response, array $args = []): Response {
        //check security. as this is not an invokable action we can not use any inheritance here :( 

        if ($this->checkLogin()) {
            $userService = new UserDataService();
            $data = $userService->getAll();
            return $this->respondJSON($response, $data, 200);
        } else {
            return $this->respondJSONWithError($response, self::ERROR_NOT_ALLOWED, 403);
        }
    }

    public function create(Request $request, Response $response, array $args = []): Response {
        //check security. as this is not an invokable action we can not use any inheritance here :( 

        if ($this->checkLogin()) {
            $payload = (string) $request->getBody();
            if ($payload != '') {
                $reqData = json_decode($payload, true);
                if (is_array($reqData)) {
                    try {
                        $userService = new UserDataService();
                        $id = $userService->createUser($reqData);
                        $user = $userService->getById($id);
                    } catch (Exception $e) {
                        return $this->respondJSONWithError($response, self::ERROR_BAD_DATA, 400);
                    }

                    return $this->respondJSON($response, array('status' => 'ok', 'id' => $user->id), 200);
                }
            }

            return $this->respondJSONWithError($response, self::ERROR_BAD_DATA, 400);
        } else {
            return $this->respondJSONWithError($response, self::ERROR_NOT_ALLOWED, 403);
        }
    }

    public function delete(Request $request, Response $response, array $args = []): Response {
        //check security. as this is not an invokable action we can not use any inheritance here :( 

        if ($this->checkLogin()) {
            $id = $request->getAttribute('id');

            $userService = new UserDataService();

            try {
                $userService->deleteUser($request->getAttribute('id'));

                return $this->respondJSON($response, array('status' => 'ok'), 200);
            } catch (Exception $e) {
                
            }
            return $this->respondJSONWithError($response, self::ERROR_BAD_DATA, 400);
        } else {
            return $this->respondJSONWithError($response, self::ERROR_NOT_ALLOWED, 403);
        }
    }

    public function update(Request $request, Response $response, array $args = []): Response {
        //check security. as this is not an invokable action we can not use any inheritance here :( 

        if ($this->checkLogin()) {
            $payload = (string) $request->getBody();

            if ($payload != '') {
                $reqData = json_decode($payload, true);

                if (is_array($reqData)) {
                    try {
                        if (empty($reqData['id'])) {
                            $reqData['id'] = $request->getAttribute('id', null);
                        }

                        $userService = new UserDataService();
                        $userService->updateUser($reqData);
                        $user = $userService->getById($reqData['id']);
                    } catch (Exception $e) {
                        return $this->respondJSONWithError($response, self::ERROR_BAD_DATA, 400);
                    }

                    return $this->respondJSON($response, array('status' => 'ok', 'id' => $user->id), 200);
                }
            }

            return $this->respondJSONWithError($response, self::ERROR_BAD_DATA, 400);
        } else {
            return $this->respondJSONWithError($response, self::ERROR_NOT_ALLOWED, 403);
        }
    }

}
