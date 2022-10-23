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
class AdminController extends AppController {

    public function index(Request $request, Response $response, array $args = []): Response {
        if ($this->checkLogin($request)) {
            $userService = new UserDataService();
            $data = $userService->getAll();

            $view = $this->fetchHTMLView('admin/users.html', ['users' => $data]);
            return $this->respondHTML($response, 'admin/index.html', ['viewHTML' => $view, 'currentLink' => 'users']);
        } else {
            return $this->respondHTML($response, 'admin/login.html', [], 403);
        }
    }
    
    public function posts(Request $request, Response $response, array $args = []): Response {
        if ($this->checkLogin($request)) {
              $dataService= new PostDataService();
              $data= $dataService->getAll();

              $view= $this->fetchHTMLView('admin/posts.html', ['posts'=>$data]);
              return $this->respondHTML($response,'admin/index.html',['viewHTML'=>$view,'currentLink'=>'posts']);
        } else {
            return $this->respondHTML($response, 'admin/login.html', [], 403);
        }
    }

}
