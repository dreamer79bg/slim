<?php

declare(strict_types=1);

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;

return function (App $app) {
    $appBasePath = $app->getContainer()->get('settings')['basePath'];

    $app->options($appBasePath . '/{routes:.*}', function (Request $request, Response $response) {
        // CORS Pre-Flight OPTIONS Request Handler
        return $response;
    });

    $app->get($appBasePath . '/hellotest', \App\Application\Actions\TestActions\HelloWorldAction::class);
    $app->get($appBasePath . '/twigtest', \App\Application\Actions\TestActions\TwigTestAction::class);

    $app->get($appBasePath . '[/]', \App\Application\Actions\Index\IndexAction::class);

    $app->group($appBasePath . '/api', function (App $group) {
        $group->get('/test', \App\Application\API\TestActions\TestDataAction::class);
        $group->get('/testerror', \App\Application\API\TestActions\TestErrorAction::class);
        $group->get('/testprotected', \App\Application\API\TestActions\TestProtectedAction::class);
        $group->post('/login', 'SecurityController:login'); //\App\Application\API\Security\LoginAction::class);
        $group->get('/logout', 'SecurityController:logout'); //\App\Application\API\Security\LogoutAction::class);

        $group->group('/users', function (App $group) {
            $group->get('/list', \App\Application\API\Users\ListAction::class);
            $group->put('[/]', \App\Application\API\Users\CreateAction::class);
            $group->delete('/{id}', \App\Application\API\Users\DeleteAction::class);
            $group->get('/{id}', \App\Application\API\Users\GetAction::class);
            // $group->post('[/]', \App\Application\API\Users\UpdateAction::class);
            $group->post('[/[{id}]]', \App\Application\API\Users\UpdateAction::class);
        });

        $group->group('/posts', function (App $group) {
            $group->get('/list', 'APIPostsController:list');
            $group->put('[/]', 'APIPostsController:create');
            $group->delete('/{id}', 'APIPostsController:delete'); //\App\Application\API\Posts\DeleteAction::class);
            $group->get('/{id}', 'APIPostsController:get'); //\App\Application\API\Posts\GetAction::class);
            $group->post('[/[{id}]]', 'APIPostsController:update');
        });
    });

    $app->group($appBasePath . '/admin', function (App $group) {
        $group->get('[/]', \App\Application\Actions\Admin\IndexAction::class);
        $group->post('[/]', \App\Application\Actions\Admin\IndexAction::class);

        $group->get('/posts', \App\Application\Actions\Admin\PostsAction::class);

        $group->group('/tpl', function (App $group) {
            $group->get('/edituser', \App\Application\Actions\Admin\Dialogs\EditUserDialogAction::class);
            $group->get('/createuser', \App\Application\Actions\Admin\Dialogs\CreateUserDialogAction::class);
            $group->get('/editpost', \App\Application\Actions\Admin\Dialogs\EditPostDialogAction::class);
            $group->get('/createpost', \App\Application\Actions\Admin\Dialogs\CreatePostDialogAction::class);
        });
    });
};
