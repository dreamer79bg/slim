<?php

namespace App\Application\Controllers;

use App\Application\Controller as AppController;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\RequestInterface as Request;
use Exception;
use App\Application\DataServices\PostDataService;
use App\Application\DataServices\UserDataService;

/**
 * As invokable actions are considered as using a skeleton app for the task even though this is one of the standard ways for creating routes in SLIM they are switched to controllers.
 * 
 * There is no way to keep action classes as they will always produce code similar to the example application for SLIM
 */
class PostsController extends AppController {

    public function view(Request $request, Response $response, array $args = []): Response {
        $dataService = new PostDataService();
        $id = $request->getAttribute('id');

        $statusCode = 200;

        $htmlPost = '';
        if ($id > 0) {
            try {
                $data = $dataService->getById($id)->asArray();

                $userService = new UserDataService();
                $user = $userService->getFullById($data['userId']);

                $data['userFullName'] = $user->fullName;

                $htmlPost = $this->fetchHTMLView('datablocks/fullpost.html', ['data' => $data]);
            } catch (Exception $e) {
                
            }
        }

        if (empty($htmlPost)) {
            $htmlPost = $this->fetchHTMLView('datablocks/postnotfound.html', []);
            $statusCode = 404;
        }

        $dataService = new PostDataService();
        $data = $dataService->getAll(3);

        try {
            $htmlNew = '';
            foreach ($data as $row) {
                $htmlNew .= $this->fetchHTMLView('datablocks/post.html', ['data' => $row]);
            }
        } catch (\Exception $e) {
            
        }

        return $this->respondHTML($response, 'index.html', ['postHTML' => $htmlPost, 'featured' => '', 'viewHTML' => $htmlNew], $statusCode);
    }

    public function list(Request $request, Response $response, array $args = []): Response {
        $dataService = new PostDataService();
        
        $dataService = new PostDataService();
        $data = $dataService->getAll();

        try {
            $htmlNew = '';
            foreach ($data as $row) {
                $htmlNew .= $this->fetchHTMLView('datablocks/post.html', ['data' => $row]);
            }
        } catch (\Exception $e) {
            
        }

        return $this->respondHTML($response, 'index.html', ['postHTML' => '', 'featured' => '', 'viewHTML' => $htmlNew, 'morePostsText'=>'All Posts','hideMoreButton'=>true]);
    }
    
}
