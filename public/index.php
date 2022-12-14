<?php
use Slim\App;
use Slim\Views\Twig;

require __DIR__ . '/../vendor/autoload.php';

global $app;

global $publicRootDir;
$publicRootDir=__DIR__;

$mainConfig= require __DIR__ . '/../app/mainconfig.php';

$config = [
    'settings' => $mainConfig + [
        'displayErrorDetails' => true,
    ] 
];
$app = new \Slim\App($config);

// Get container
$container = $app->getContainer();

// Register TWIG component on container
$container['view'] = function ($container) {
    $view = new \Slim\Views\Twig(__DIR__.'/../src/Application/Views', ['cache' => __DIR__.'/../twigcache']);

    // Instantiate and add Slim specific extension
    $router = $container->get('router');
    $uri = \Slim\Http\Uri::createFromEnvironment(new \Slim\Http\Environment($_SERVER));
    $view->addExtension(new \Slim\Views\TwigExtension($router, $uri));

    return $view;
};

/**
 * Initialize container elements for controllers used in routes
 */
$controllers= require __DIR__ . '/../app/controllers.php';
$controllers($container);

$routes= require __DIR__ . '/../app/routes.php';

$routes($app);

//create session middleware
$app->add(
  new \Slim\Middleware\Session([
    'name' => 'slimblog_session',
    'autorefresh' => true,
    'lifetime' => '1 hour',
  ])
);


//run application
try {
    $app->run();
} catch (\Exception $e) {
    if (is_a($e, Slim\Exception\HttpMethodNotAllowedException::class)) {
        http_response_code(404);
        print file_get_contents(__DIR__.'/../src/Views/404.html');
    }
}





