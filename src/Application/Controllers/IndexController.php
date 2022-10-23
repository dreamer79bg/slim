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

class IndexController extends AppController {

    public function index(Request $request, Response $response, array $args = []): Response {
        $dataService = new PostDataService();
        $data = $dataService->getAll();

        try {
            $html = '';
            foreach ($data as $row) {
                $html .= $this->fetchHTMLView('datablocks/post.html', ['data' => $row]);
            }
        } catch (\Exception $e) {
            
        }
        return $this->respondHTML($response,'index.html', ['viewHTML' => $html]);
    }
}
