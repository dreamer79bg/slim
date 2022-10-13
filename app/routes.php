<?php

declare(strict_types=1);

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;

return function (App $app) {
    $app->setBasePath('/slim');
    
    $app->options('/{routes:.*}', function (Request $request, Response $response) {
        // CORS Pre-Flight OPTIONS Request Handler
        return $response;
    });

    $app->get('/hellotest', \App\Application\Actions\TestActions\HelloWorldAction::class);
    $app->get('/twigtest', \App\Application\Actions\TestActions\TwigTestAction::class);

    $app->get('[/]', \App\Application\Actions\Index\IndexAction::class);
    
    $app->group('/api', function (Group $group) {
        $group->get('/test', \App\Application\API\TestActions\TestDataAction::class);
        $group->get('/testerror', \App\Application\API\TestActions\TestErrorAction::class);
        $group->get('/testprotected', \App\Application\API\TestActions\TestProtectedAction::class);
        $group->post('/login', \App\Application\API\Security\LoginAction::class);
        $group->get('/logout', \App\Application\API\Security\LogoutAction::class);
        
        $group->group('/users',   function (Group $group) {
            $group->get('/list', \App\Application\API\Users\ListAction::class);
            $group->put('[/]', \App\Application\API\Users\CreateAction::class);
            $group->delete('/{id}', \App\Application\API\Users\DeleteAction::class);
            $group->get('/{id}', \App\Application\API\Users\GetAction::class);
           // $group->post('[/]', \App\Application\API\Users\UpdateAction::class);
            $group->post('[/[{id}]]', \App\Application\API\Users\UpdateAction::class);
        });
        
        $group->group('/posts',   function (Group $group) {
            $group->get('/list', \App\Application\API\Posts\ListAction::class);
            $group->put('[/]', \App\Application\API\Posts\CreateAction::class);
            $group->delete('/{id}', \App\Application\API\Posts\DeleteAction::class);
            $group->get('/{id}', \App\Application\API\Posts\GetAction::class);
            $group->post('[/[{id}]]', \App\Application\API\Posts\UpdateAction::class);
        });
    });
    
    $app->group('/admin', function (Group $group) {
        $group->get('[/]', \App\Application\Actions\Admin\IndexAction::class);
        $group->post('[/]', \App\Application\Actions\Admin\IndexAction::class);
        $group->group('/tpl',   function (Group $group) {
            $group->get('/edituser', \App\Application\Actions\Admin\Dialogs\EditUserDialogAction::class);
            $group->get('/createuser', \App\Application\Actions\Admin\Dialogs\CreateUserDialogAction::class);
        });
    });
};
