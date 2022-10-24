<?php

use Psr\Container\ContainerInterface;

return function (ContainerInterface $container) {
    $container['SecurityController'] = function ($c) {
        return new \App\Application\Controllers\SecurityController($c);
    };

    $container['APIPostsController'] = function ($c) {
        return new \App\Application\Controllers\APIPostsController($c);
    };

    $container['APIUsersController'] = function ($c) {
        return new \App\Application\Controllers\APIUsersController($c);
    };

    $container['APITestsController'] = function ($c) {
        return new \App\Application\Controllers\APITestsController($c);
    };

    $container['IndexController'] = function ($c) {
        return new \App\Application\Controllers\IndexController($c);
    };

    $container['AdminController'] = function ($c) {
        return new \App\Application\Controllers\AdminController($c);
    };

    $container['AdminDialogsController'] = function ($c) {
        return new \App\Application\Controllers\AdminDialogsController($c);
    };

    $container['TestsController'] = function ($c) {
        return new \App\Application\Controllers\TestsController($c);
    };

    $container['PostsController'] = function ($c) {
        return new \App\Application\Controllers\PostsController($c);
    };
    
    //Override the default Not Found Handler
    $container['notFoundHandler'] = function ($c) {
        return function ($request, $response) use ($c) {
            return $c['view']->render($response->withStatus(404), '404.html', [
            ]);
        };
    };
    
    //Override the default Not Allowed Handler
    $container['notAllowedHandler'] = function ($c) {
        return function ($request, $response) use ($c) {
            return $c['view']->render($response->withStatus(404), '404.html', [
            ]);
        };
    };
};
