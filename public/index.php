<?php
use Slim\Factory\AppFactory;
use Slim\Views\Twig;
use Slim\Views\TwigMiddleware;

require __DIR__ . '/../vendor/autoload.php';

global $app;

global $publicRootDir;
$publicRootDir=__DIR__;

$app = AppFactory::create();

$app->addBodyParsingMiddleware();

$routes= require __DIR__ . '/../app/routes.php';

$routes($app);

// Create Twig
$twig = Twig::create(__DIR__.'/../src/Views', ['cache' => __DIR__.'/../twigcache']);

// Add Twig-View Middleware
$app->add(TwigMiddleware::create($app, $twig));


//create session
$app->add(
  new \Slim\Middleware\Session([
    'name' => 'slimblog_session',
    'autorefresh' => true,
    'lifetime' => '1 hour',
  ])
);


try {
    $app->run();
} catch (\Exception $e) {
    if (is_a($e, Slim\Exception\HttpMethodNotAllowedException::class)) {
        http_response_code(404);
        print file_get_contents(__DIR__.'/../src/Views/404.html');
    }
}