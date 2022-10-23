<?php

declare(strict_types=1);

namespace Tests;

use Exception;
use PHPUnit\Framework\TestCase as PHPUnit_TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Factory\AppFactory;
use Slim\Psr7\Factory\StreamFactory;
use Slim\Psr7\Headers;
use Slim\Psr7\Request as SlimRequest;
use Slim\Psr7\Response as SlimResponse;
use Slim\Psr7\Uri;
use Slim\Views\Twig;
use Slim\Views\TwigMiddleware;
use DI\Container;

class TestCase extends PHPUnit_TestCase {

    use ProphecyTrait;

    /**
     * @return App
     * @throws Exception
     */
    protected function getAppInstance(): App {

        global $app;
        if (!is_object($app)) {
            if (session_status() === PHP_SESSION_NONE || session_id() === null) {
                session_start();
            }

            $mainConfig = require __DIR__ . '/../app/mainconfig.php';

            $config = [
                'settings' => $mainConfig + [
            'displayErrorDetails' => true,
                ]
            ];
            $app = new \Slim\App($config);

            $app->add(
                    new \Slim\Middleware\Session([
                        'name' => 'slimblog_session',
                        'autorefresh' => true,
                        'lifetime' => 600,
                            ])
            );

// Get container
            $container = $app->getContainer();

// Register component on container
            $container['view'] = function ($container) {
                $view = new \Slim\Views\Twig(__DIR__ . '/../src/Views', ['cache' => __DIR__ . '/../twigcache']);

                // Instantiate and add Slim specific extension
                $router = $container->get('router');
                $uri = \Slim\Http\Uri::createFromEnvironment(new \Slim\Http\Environment($_SERVER));
                $view->addExtension(new \Slim\Views\TwigExtension($router, $uri));

                return $view;
            };

            $routes = require __DIR__ . '/../app/routes.php';

            $routes($app);
        }

        return $app;
    }

    protected function handleRequest($request) {
        $app = $this->getAppInstance();

        /** @var Container $container */
        $container = $app->getContainer();
        $container['request'] = $request;
        $container['response'] = new SlimResponse();
        return $app->process($request, new SlimResponse());
    }

    /**
     * @param string $method
     * @param string $path
     * @param array  $headers
     * @param array  $cookies
     * @param array  $serverParams
     * @return Request
     */
    protected function createRequest(
            string $method,
            string $path,
            array $headers = [], //'HTTP_ACCEPT' => '*;application/json'
            array $cookies = [],
            array $serverParams = []
    ): Request {
        $base = $this->getAppInstance()->getContainer()->get('settings')['basePath'];

        $uri = new Uri('', '', 80, $base . $path);
        $handle = fopen('php://temp', 'w+');
        $stream = (new StreamFactory())->createStreamFromResource($handle);

        $h = new Headers();
        foreach ($headers as $name => $value) {
            $h->addHeader($name, $value);
        }

        //add base path to URI :D 

        return new SlimRequest($method, $uri, $h, $cookies, $serverParams, $stream);
    }

    protected function createJsonRequest(string $method, $uri, array $data = null): SlimRequest {
        $request = $this->createRequest($method, $uri);

        if ($data !== null) {
            $request->getBody()->write((string) json_encode($data));
            $request->getBody()->rewind();
        }

        return $request->withHeader('Content-Type', 'application/json');
    }

}

/*
 * tests do not have $_SESSION and the session middleware breaks. start a session.
 */
if (!session_id()) {
    session_start();
}