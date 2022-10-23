<?php

namespace App\Application\Controllers;

use App\Application\Controller as AppController;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\RequestInterface as Request;
use Exception;
use App\Application\DataServices\PostDataService;

class APIPostsController extends AppController {

    public function get(Request $request, Response $response, array $args = []): Response {
        //check security. as this is not an invokable action we can not use any inheritance here :( 

        if ($this->checkLogin()) {
            $id = $request->getAttribute('id');

            $dataService = new PostDataService();

            try {
                $data = $dataService->getById($request->getAttribute('id'));

                return $this->respondJSON($response, array('status' => 'ok', 'title' => $data->title, 'shortDesc' => $data->shortDesc, 'id' => $data->getId()
                            , 'content' => $data->content, 'userId' => $data->userId
                            , 'categoryId' => $data->categoryId, 'imageFile' => $data->imageFile
                            , 'lastUpdated' => $data->lastUpdated, 'featuredPos' => $data->featuredPos
                                ), 200);
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
            $dataService = new PostDataService();
            $data = $dataService->getAll();
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
                        $dataService = new PostDataService();
                        if ($this->session->exists('userId')) {
                            $reqData['userId'] = $this->session->get('userId');
                        }
                        $id = $dataService->createPost($reqData);
                        $data = $dataService->getById($id);
                    } catch (Exception $e) {
                        return $this->respondJSONWithError($response, self::ERROR_BAD_DATA, 400);
                    }

                    return $this->respondJSON($response, array('status' => 'ok', 'id' => $data->id), 200);
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

            $dataService = new PostDataService();

            try {
                $dataService->deletePost($request->getAttribute('id'));

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

                        $dataService = new PostDataService();
                        $dataService->updatePost($reqData);
                        $data = $dataService->getById($reqData['id']);
                    } catch (Exception $e) {
                        return $this->respondJSONWithError($response,self::ERROR_BAD_DATA.$e->getMessage(), 400);
                    }

                    return $this->respondJSON($response, array('status' => 'ok', 'id' => $data->id), 200);
                }
            }

            return $this->respondJSONWithError($response, new ActionError(ActionError::BAD_REQUEST), 400);
        } else {
            return $this->respondJSONWithError($response, self::ERROR_NOT_ALLOWED, 403);
        }
    }

}
