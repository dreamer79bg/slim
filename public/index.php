<?php
use Slim\Factory\AppFactory;
use Slim\Views\Twig;
use Slim\Views\TwigMiddleware;

require __DIR__ . '/../vendor/autoload.php';

$app = AppFactory::create();

$routes= require __DIR__ . '/../app/routes.php';

$routes($app);

// Create Twig
$twig = Twig::create(__DIR__.'/../src/Views', ['cache' => __DIR__.'/../twigcache']);

// Add Twig-View Middleware
$app->add(TwigMiddleware::create($app, $twig));

$app->run();