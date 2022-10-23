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
};
