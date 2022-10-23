<?php

use Psr\Container\ContainerInterface;

return function (ContainerInterface $container) {
    $container['SecurityController'] = function ($c) {
        return new \App\Application\Controllers\SecurityController($c);
    };
};
