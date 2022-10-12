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
            $app = AppFactory::create();
            $app->addBodyParsingMiddleware();

            $routes = require __DIR__ . '/../app/routes.php';

            $routes($app);

            //override the production route for testing
            $app->setBasePath('');

            // Create Twig
            $twig = Twig::create(__DIR__ . '/../src/Views', ['cache' => __DIR__ . '/../twigcache']);

            // Add Twig-View Middleware
            $app->add(TwigMiddleware::create($app, $twig));
        }

        return $app;
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
        $uri = new Uri('', '', 80, $path);
        $handle = fopen('php://temp', 'w+');
        $stream = (new StreamFactory())->createStreamFromResource($handle);

        $h = new Headers();
        foreach ($headers as $name => $value) {
            $h->addHeader($name, $value);
        }

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