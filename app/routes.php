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

    $app->get('/hellotest', \App\Application\Actions\HelloWorldAction::class);
    $app->get('/twigtest', \App\Application\Actions\TwigTestAction::class);
    
    $app->group('/api', function (Group $group) {
        $group->get('/test', \App\Application\API\TestDataAction::class);
        $group->get('/testerror', \App\Application\API\TestErrorAction::class);
        $group->get('/testprotected', \App\Application\API\TestProtectedAction::class);
        $group->get('/login', \App\Application\API\LoginAction::class);
        $group->get('/logout', \App\Application\API\LogoutAction::class);
    });
};
