<?php

declare(strict_types=1);

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;

/**
 * As invokable actions are considered as using a skeleton app for the task even though this is one of the standard ways for creating routes in SLIM they are switched to controllers
 */
return function (App $app) {
    $appBasePath = $app->getContainer()->get('settings')['basePath'];

    $app->options($appBasePath . '/{routes:.*}', function (Request $request, Response $response) {
        // CORS Pre-Flight OPTIONS Request Handler
        return $response;
    });

    $app->get($appBasePath . '/hellotest', 'TestsController:hello');
    $app->get($appBasePath . '/twigtest', 'TestsController:twig');

    $app->get($appBasePath . '[/]', 'IndexController:index');

    $app->group($appBasePath . '/api', function (App $group) {
        $group->get('/test', 'APITestsController:data');
        $group->get('/testerror', 'APITestsController:error');
        $group->get('/testprotected', 'APITestsController:protected');
        $group->post('/login', 'SecurityController:login'); 
        $group->get('/logout', 'SecurityController:logout');

        $group->group('/users', function (App $group) {
            $group->get('/list', 'APIUsersController:list');
            $group->put('[/]', 'APIUsersController:create');
            $group->delete('/{id}', 'APIUsersController:delete');
            $group->get('/{id}', 'APIUsersController:get');
            // $group->post('[/]', \App\Application\API\Users\UpdateAction::class);
            $group->post('[/[{id}]]', 'APIUsersController:update');
        });

        $group->group('/posts', function (App $group) {
            $group->get('/list', 'APIPostsController:list');
            $group->put('[/]', 'APIPostsController:create');
            $group->delete('/{id}', 'APIPostsController:delete');
            $group->get('/{id}', 'APIPostsController:get'); 
            $group->post('[/[{id}]]', 'APIPostsController:update');
        });
    });

    $app->group($appBasePath . '/admin', function (App $group) {
        $group->get('[/]', 'AdminController:index');
        $group->post('[/]', 'AdminController:index');

        $group->get('/posts', 'AdminController:posts');

        $group->group('/tpl', function (App $group) {
            $group->get('/edituser', 'AdminDialogsController:editUser');
            $group->get('/createuser', 'AdminDialogsController:createUser');
            $group->get('/editpost', 'AdminDialogsController:editPost');
            $group->get('/createpost', 'AdminDialogsController:createPost');
        });
    });
    
    $app->group($appBasePath . '/posts', function (App $group) {
        $group->get('/{id}', 'PostsController:view');
        $group->get('[/]', 'PostsController:list');
    });
};
