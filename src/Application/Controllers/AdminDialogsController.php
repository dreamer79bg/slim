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
class AdminDialogsController extends AppController {

    public function createPost(Request $request, Response $response, array $args = []): Response {
        if ($this->checkLogin($request)) {
            return $this->respondHTML($response, 'admin/Dialogs/CreatePost.html');
        } else {
            return $this->respondHTML($response, 'admin/login.html', [], 403);
        }
    }

    public function editPost(Request $request, Response $response, array $args = []): Response {
        if ($this->checkLogin($request)) {
            return $this->respondHTML($response, 'admin/Dialogs/EditPost.html');
        } else {
            return $this->respondHTML($response, 'admin/login.html', [], 403);
        }
    }

    public function createUser(Request $request, Response $response, array $args = []): Response {
        if ($this->checkLogin($request)) {
            return $this->respondHTML($response, 'admin/Dialogs/CreateUser.html');
        } else {
            return $this->respondHTML($response, 'admin/login.html', [], 403);
        }
    }

    public function editUser(Request $request, Response $response, array $args = []): Response {
        if ($this->checkLogin($request)) {
            return $this->respondHTML($response, 'admin/Dialogs/EditUser.html');
        } else {
            return $this->respondHTML($response, 'admin/login.html', [], 403);
        }
    }

}
