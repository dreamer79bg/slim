<?php

declare(strict_types=1);

namespace App\Application;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\RequestInterface as Request;
use Psr\Container\ContainerInterface;
use Slim\App;
use Slim\Views\Twig;
use SlimSession\Helper as SessionHelper;
use App\Application\Services\SecurityService;

/**
 * A custom controller class for MVC app
 * written by A. Markov
 * 
 * As invokable actions are considered as using a skeleton app for the task even though this is one of the standard ways for creating routes in SLIM they are switched to controllers.
 * 
 * There is no way to keep action classes as they will always produce code similar to the example application for SLIM
 */
abstract class Controller {

    protected App $app;
    protected ContainerInterface $container;
    protected Twig $view;
    protected SessionHelper $session;
    protected SecurityService $securityService;

    const ERROR_NOT_ALLOWED = 'Not allowed';
    const ERROR_BAD_DATA = 'Bad data';

    public function __construct(ContainerInterface $container) {
        $this->container = $container;
        $this->view = $this->container->get('view');
        $this->session = new SessionHelper();
        $this->securityService= SecurityService::getService();
    }

    protected function respondJSON(Response $response, array $payload = [], int $statusCode = 200): Response {
        $json = json_encode($payload, JSON_PRETTY_PRINT);

        $response->getBody()->write($json);

        return $response
                        ->withHeader('Content-Type', 'application/json')
                        ->withStatus($statusCode);
    }

    protected function respondHTML(Response $response, string $viewName, array $data = [], int $statusCode = 200) {
        $this->view->render($response, $viewName, $data + ['baseAppPath' => $this->container->get('settings')['basePath']]);
        return $response->withStatus($statusCode);
    }

    protected function fetchHTMLView(string $viewName, array $data = []) {
        return $this->view->fetch($viewName, $data + ['baseAppPath' => $this->container->get('settings')['basePath']]);
    }

    protected function respondJSONWithError(Response $response, string $error = Error, int $statusCode = 200): Response {
        return $this->respondJSON($response, array('error' => $error, 'statusCode' => $statusCode), $statusCode);
    }

    protected function checkLogin(Request $request = null): bool {
        if (is_object($request)) {
            $parsedBody = $request->getParsedBody();

            if ($request->getMethod() == 'POST') {
                if (!empty($parsedBody['doLogin']) && $parsedBody['doLogin'] == 'login') {
                    $this->securityService->doLogout();

                    if (isset($parsedBody['username']) && isset($parsedBody['password'])) {
                        $this->securityService->doLogin($parsedBody['username'], $parsedBody['password']);
                    }
                }
            }

            $parsedQuery = $request->getQueryParams();
            if (isset($parsedQuery['logout'])) {
                $this->doLogout();
            }
        }

        return $this->securityService->checkLogin();
    }

    protected function doLogout() {
        $this->securityService->doLogout();
    }

}
